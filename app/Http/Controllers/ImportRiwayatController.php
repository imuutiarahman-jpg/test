<?php

namespace App\Http\Controllers;

use App\Models\ImportRiwayat;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImportRiwayatController extends Controller
{
    public function index()
    {
        $imports = ImportRiwayat::latest()->paginate(15);

        return view('qmas.impor-riwayat', compact('imports'));
    }

    public function prosesImport(Request $request)
    {
        $data = $request->validate([
            'tahun_default' => 'required|integer|min:2000|max:2100',
            'file' => 'nullable|file|mimes:csv,tsv,txt|max:10240',
            'teks' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        $tahunDefault = (int) $data['tahun_default'];
        $raw = '';
        $sumber = 'teks';

        if ($request->hasFile('file')) {
            $sumber = 'file';
            $raw = (string) file_get_contents($request->file('file')->getRealPath());
        } elseif (! empty($data['link'])) {
            $sumber = 'google_sheets';
            $raw = $this->fetchGoogleSheet($data['link']);
            if ($raw === null) {
                return redirect()->route('qmas.imporRiwayat')
                    ->withErrors(['link' => 'Link Google Sheet tidak dapat diambil. Tempel data secara manual.']);
            }
        } else {
            $raw = (string) ($data['teks'] ?? '');
        }

        ImportRiwayat::create([
            'tahun_default' => $tahunDefault,
            'sumber_data' => $sumber,
            'konten_mentah' => $raw,
        ]);

        [$inserted, $skipped, $errors] = $this->importRows($raw, $tahunDefault);

        $message = "Impor selesai: {$inserted} nota masuk, {$skipped} dilewati.";
        if ($errors > 0) {
            $message .= " {$errors} baris bermasalah.";
        }

        return redirect()->route('qmas.imporRiwayat')->with('success', $message);
    }

    public function destroy(ImportRiwayat $import_riwayat)
    {
        $import_riwayat->delete();

        return redirect()->route('qmas.imporRiwayat')->with('success', 'Impor dibatalkan.');
    }

    protected function fetchGoogleSheet(string $link): ?string
    {
        if (! preg_match('#/spreadsheets/d/([a-zA-Z0-9-_]+)#', $link, $m)) {
            return null;
        }
        $id = $m[1];
        $gid = '0';
        if (preg_match('#gid=(\d+)#', $link, $g)) {
            $gid = $g[1];
        }
        $url = "https://docs.google.com/spreadsheets/d/{$id}/export?format=csv&gid={$gid}";

        try {
            $response = Http::timeout(20)->get($url);
            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    protected function importRows(string $raw, int $tahunDefault): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $raw);

        // Deteksi format sheet "list orderan QMAS"
        $isSheet = false;
        foreach ($lines as $line) {
            if (stripos($line, 'list orderan QMAS') !== false) {
                $isSheet = true;
                break;
            }
        }

        if ($isSheet) {
            return $this->importSheetRows($lines, $tahunDefault);
        }

        return $this->importPosRows($lines, $tahunDefault);
    }

    protected function importSheetRows(array $lines, int $tahunDefault): array
    {
        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        $lastDate = null;
        $products = ['QMAS GL 19L', 'QMAS 240 ML', 'QMAS 330ML', 'QMAS 600 ML', 'QMAS 1.5L'];
        $qtyCols = [4, 7, 10, 13, 16];

        foreach ($lines as $line) {
            $cols = explode("\t", $line);
            // normalisasi jumlah kolom
            if (count($cols) < 22) {
                continue;
            }

            $dateRaw = trim($cols[0] ?? '');
            $pelanggan = trim($cols[1] ?? '');
            $isSummary = stripos(trim($cols[0] ?? ''), 'TOTAL') !== false
                || stripos($pelanggan, 'TOTAL') !== false
                || trim($cols[4] ?? '') === 'QTT';
            if ($pelanggan === '' && $isSummary) {
                continue;
            }
            if ($pelanggan === '') {
                $pelanggan = 'Umum';
            }
            $isDonation = stripos($pelanggan, 'sumbangan') !== false || stripos($pelanggan, 'donasi') !== false;
            $tglLunas = trim($cols[2] ?? '');
            $tglSetor = trim($cols[3] ?? '');
            $cashRaw = trim($cols[20] ?? '');
            $bankRaw = trim($cols[21] ?? '');
            $tagihanRaw = trim($cols[22] ?? '');
            $ketRaw = trim($cols[23] ?? '');

            if ($isDonation) {
                $total = $this->parseMoney($ketRaw);
                if ($total <= 0) {
                    $total = $this->parseMoney($cashRaw) + $this->parseMoney($bankRaw) + $this->parseMoney($tagihanRaw);
                }
            } else {
                $total = $this->parseMoney($cashRaw) + $this->parseMoney($bankRaw) + $this->parseMoney($tagihanRaw);
            }

            if ($pelanggan === '' || $total <= 0) {
                // mungkin baris header/total/bulan
                if ($dateRaw !== '') {
                    $parsed = $this->parseSheetDate($dateRaw, $tahunDefault);
                    if ($parsed) {
                        $lastDate = $parsed;
                    }
                }
                continue;
            }

            if ($dateRaw !== '') {
                $parsed = $this->parseSheetDate($dateRaw, $tahunDefault);
                if ($parsed) {
                    $lastDate = $parsed;
                }
            }

            if ($lastDate === null) {
                $errors++;
                continue;
            }

            // item pesanan dari qty > 0
            $items = [];
            foreach ($qtyCols as $i => $col) {
                $qtyRaw = trim($cols[$col] ?? '');
                $qty = (int) preg_replace('/[^0-9]/', '', $qtyRaw);
                if ($qty > 0) {
                    $items[] = $products[$i].' x'.$qty;
                }
            }
            $itemPesanan = $items ? implode(', ', $items) : 'Penjualan';

            if ($total <= 0) {
                $errors++;
                continue;
            }

            $hasPayment = ($this->parseMoney($cashRaw) + $this->parseMoney($bankRaw) + $this->parseMoney($tagihanRaw)) > 0;
            if ($isDonation && ! $hasPayment) {
                $metode = 'Donasi';
            } elseif ($cashRaw !== '' && $bankRaw === '' && $tagihanRaw === '') {
                $metode = 'Tunai';
            } elseif ($bankRaw !== '') {
                $metode = 'Bank';
            } elseif ($tagihanRaw !== '') {
                $metode = 'Tagihan';
            } else {
                $metode = $ketRaw !== '' ? $ketRaw : 'Tunai';
            }

            if ($isDonation && ! $hasPayment) {
                $status = 'Lunas';
            } else {
                $status = 'Belum Bayar';
                if ($cashRaw !== '' || $bankRaw !== '') {
                    $status = 'Lunas';
                } elseif (stripos($tagihanRaw, 'Rp') !== false && $tagihanRaw !== '') {
                    $status = 'Belum Bayar';
                }
                if ($tglLunas !== '' && (stripos($tglLunas, 'lunas') !== false || preg_match('/\d/', $tglLunas))) {
                    $status = 'Lunas';
                }
            }

            $jatuhTempo = null;
            if ($status === 'Belum Bayar') {
                if ($tglLunas !== '' && preg_match('/\d{1,2}[\/\-]\d{1,2}(?:[\/\-]\d{2,4})?/', $tglLunas)) {
                    $jatuhTempo = $this->parseSheetDate($tglLunas, $tahunDefault);
                } elseif ($tglSetor !== '' && preg_match('/\d{1,2}[\/\-]\d{1,2}(?:[\/\-]\d{2,4})?/', $tglSetor)) {
                    $jatuhTempo = $this->parseSheetDate($tglSetor, $tahunDefault);
                }
            }


            Penjualan::create([
                'tanggal' => $lastDate,
                'pelanggan' => $pelanggan,
                'item_pesanan' => $itemPesanan,
                'metode_pembayaran' => $metode,
                'jatuh_tempo' => $jatuhTempo,
                'no_hp' => null,
                'kategori_lokasi' => $isDonation ? 'Donasi' : 'Food Court',
                'total_harga' => $total,
                'status_pembayaran' => $status,
            ]);

            $inserted++;
        }

        return [$inserted, $skipped, $errors];
    }

    protected function importPosRows(array $lines, int $tahunDefault): array
    {
        $blocks = [];
        $current = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                if ($current !== null && count($current) > 0) {
                    $current[] = '';
                }
                continue;
            }
            if (preg_match('/^POS-\S+\t\d{1,2}/', $trimmed) || preg_match('/^\S+\t\d{4}-\d{2}-\d{2}/', $trimmed)) {
                if ($current !== null) {
                    $blocks[] = $current;
                }
                $current = [$trimmed];
            } elseif ($current !== null) {
                $current[] = $trimmed;
            }
        }
        if ($current !== null) {
            $blocks[] = $current;
        }

        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($blocks as $block) {
            $block = array_values(array_filter($block, fn ($v) => trim($v) !== ''));
            if (count($block) < 2) {
                $errors++;
                continue;
            }

            $firstCols = preg_split('/\t+/', $block[0]);
            $pelanggan = trim($firstCols[2] ?? '');
            $tanggalRaw = trim($firstCols[1] ?? '');

            if ($pelanggan === '' || $tanggalRaw === '') {
                $errors++;
                continue;
            }

            $item = trim($block[1] ?? '');
            $detailLine = trim($block[count($block) - 1] ?? '');
            $detailCols = preg_split('/\t+/', $detailLine);
            $detailText = trim($detailCols[0] ?? '');

            $allText = implode(' ', $block);

            if (! preg_match('/Rp\s*[\d\.,]+/', $allText, $tm)) {
                $errors++;
                continue;
            }
            $totalRaw = $tm[0];

            $statusRaw = trim($detailCols[2] ?? '');
            if (stripos($statusRaw, 'belum') !== false) {
                $statusRaw = 'Belum Bayar';
            } elseif (stripos($statusRaw, 'lunas') !== false) {
                $statusRaw = 'Lunas';
            } else {
                $statusRaw = 'Belum Bayar';
            }

            $metode = 'Tunai';
            if (preg_match('/^([A-Za-z]+)\s*•/', $detailText, $mm)) {
                $metode = trim($mm[1]);
            } elseif (stripos($allText, 'Tagihan') !== false) {
                $metode = 'Tagihan';
            }

            $jatuhTempo = null;
            if (preg_match('/tempo\s+(\d{4}-\d{2}-\d{2})/i', $allText, $jm)) {
                $jatuhTempo = $jm[1];
            }

            $noHp = null;
            if (preg_match('/HP\s*(\d{6,})/i', $allText, $hm)) {
                $noHp = $hm[1];
            }

            $lokasi = 'Food Court';
            if (preg_match('/📍\s*(.+?)(?:\t|$)/u', $allText, $lm)) {
                $lokasi = trim($lm[1]);
            }

            $tanggal = $this->parseDate($tanggalRaw, $tahunDefault);
            if ($tanggal === null) {
                $errors++;
                continue;
            }

            if ($total <= 0) {
                $errors++;
                continue;
            }

            $exists = Penjualan::whereDate('tanggal', $tanggal)
                ->where('pelanggan', $pelanggan)
                ->where('item_pesanan', $item)
                ->where('total_harga', $total)
                ->exists();

            if ($exists) {
                $existing = Penjualan::whereDate('tanggal', $lastDate)
                    ->where('pelanggan', $pelanggan)
                    ->where('item_pesanan', $itemPesanan)
                    ->where('total_harga', $total)
                    ->first();
                if ($existing) {
                    $existing->update([
                        'metode_pembayaran' => $metode,
                        'status_pembayaran' => $status,
                        'jatuh_tempo' => $jatuhTempo,
                        'total_harga' => $total,
                    ]);
                }
                $skipped++;
                continue;
            }

            Penjualan::create([
                'tanggal' => $tanggal,
                'pelanggan' => $pelanggan,
                'item_pesanan' => $item,
                'metode_pembayaran' => $metode,
                'jatuh_tempo' => $jatuhTempo,
                'no_hp' => $noHp,
                'kategori_lokasi' => $lokasi,
                'total_harga' => $total,
                'status_pembayaran' => $statusRaw === 'Lunas' ? 'Lunas' : 'Belum Bayar',
            ]);

            $inserted++;
        }

        return [$inserted, $skipped, $errors];
    }

    protected function parseSheetDate(string $raw, int $tahunDefault): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        if (! preg_match('/^(\d{1,2})\s*([A-Za-z]+)\s*(\d{2,4})?$/', $raw, $m)) {
            return $this->parseDate($raw, $tahunDefault);
        }

        $day = (int) $m[1];
        $monthKey = strtolower($m[2]);
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'may' => 5,
            'jun' => 6, 'jul' => 7, 'agu' => 8, 'ags' => 8, 'aug' => 8, 'agus' => 8,
            'sep' => 9, 'okt' => 10, 'oct' => 10, 'nov' => 11, 'des' => 12, 'dec' => 12,
        ];

        // dukung singkatan/typo bulan (mis. "Agus")
        $short = substr($monthKey, 0, 3);
        if (! isset($months[$monthKey]) && isset($months[$short])) {
            $monthKey = $short;
        }

        if (! isset($months[$monthKey])) {
            return $this->parseDate($raw, $tahunDefault);
        }

        $month = $months[$monthKey];

        $year = $tahunDefault;
        if (isset($m[3]) && $m[3] !== '') {
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
        }
        if ($year < $tahunDefault) {
            $year = $tahunDefault;
        }

        if ($day < 1 || $day > 31) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    protected function parseDate(string $raw, int $tahunDefault): ?string
    {
        $raw = trim($raw);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return $raw;
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', $tahunDefault, (int) $m[2], (int) $m[1]);
        }

        return null;
    }

    protected function parseMoney(string $raw): float
    {
        $num = str_replace(['Rp', 'rp', ' '], '', $raw);
        $hasComma = strpos($num, ',') !== false;
        $hasDot = strpos($num, '.') !== false;

        if ($hasComma && $hasDot) {
            if (strrpos($num, ',') > strrpos($num, '.')) {
                $num = str_replace('.', '', $num);
                $num = str_replace(',', '.', $num);
            } else {
                $num = str_replace(',', '', $num);
            }
        } elseif ($hasComma) {
            if (preg_match('/,\d{1,2}$/', $num)) {
                $num = str_replace('.', '', $num);
                $num = str_replace(',', '.', $num);
            } else {
                $num = str_replace(',', '', $num);
            }
        } elseif ($hasDot) {
            if (preg_match('/\.\d{1,2}$/', $num)) {
                // keep decimal dot
            } else {
                $num = str_replace('.', '', $num);
            }
        }

        return (float) $num;
    }
}
