<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $defaultDari = Pembelian::min('tgl_beli');
        $defaultDari = $defaultDari ? \Carbon\Carbon::parse($defaultDari)->format('Y-m-d') : now()->startOfYear()->format('Y-m-d');
        $defaultSampai = Pembelian::max('tgl_beli');
        $defaultSampai = $defaultSampai ? \Carbon\Carbon::parse($defaultSampai)->format('Y-m-d') : now()->format('Y-m-d');

        $dari = $request->get('dari', $defaultDari);
        $sampai = $request->get('sampai', $defaultSampai);

        $pembelian = Pembelian::with('details.produk')
            ->whereDate('tgl_beli', '>=', $dari)
            ->whereDate('tgl_beli', '<=', $sampai)
            ->latest('tgl_beli')
            ->latest('id')
            ->get();
        $produk = Produk::orderBy('nama_produk')->get();

        return view('qmas.pembelian', compact('pembelian', 'produk', 'dari', 'sampai'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tgl_beli' => 'required|date',
            'jatuh_tempo' => 'nullable|date',
            'lokasi_penyimpanan' => 'required|string|max:255',
            'status_pembayaran' => 'required|string|in:Lunas,Belum Bayar',
            'akun_pembayaran' => 'nullable|string|max:255',
            'ongkir' => 'nullable|numeric|min:0',
            'produk_id' => 'required|array',
            'produk_id.*' => 'required|exists:produk,id',
            'harga_beli_satuan' => 'required|array',
            'harga_beli_satuan.*' => 'required|numeric|min:0',
            'qty' => 'required|array',
            'qty.*' => 'required|integer|min:1',
        ]);

        $ongkir = (float) ($data['ongkir'] ?? 0);
        $total = $ongkir;

        $pembelian = Pembelian::create([
            'no_invoice' => 'INV-' . now()->format('Ymd-His'),
            'nama_supplier' => $data['nama_supplier'],
            'keterangan' => $data['keterangan'] ?? null,
            'tgl_beli' => $data['tgl_beli'],
            'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
            'lokasi_penyimpanan' => $data['lokasi_penyimpanan'],
            'status_pembayaran' => $data['status_pembayaran'],
            'akun_pembayaran' => $data['akun_pembayaran'] ?? null,
            'ongkir' => $ongkir,
            'total_biaya' => 0,
        ]);

        foreach ($data['produk_id'] as $i => $pid) {
            $harga = (float) $data['harga_beli_satuan'][$i];
            $qty = (int) $data['qty'][$i];
            $subtotal = $harga * $qty;
            $total += $subtotal;

            DetailPembelian::create([
                'pembelian_id' => $pembelian->id,
                'produk_id' => $pid,
                'harga_beli_satuan' => $harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ]);

            $produk = Produk::find($pid);
            if ($produk) {
                $produk->increment('stok', $qty);
            }
        }

        $pembelian->update(['total_biaya' => $total]);

        return redirect()->route('qmas.pembelian.index')->with('success', 'Pembelian berhasil disimpan, stok diperbarui.');
    }

    public function importStokMasuk(Request $request)
    {
        $data = $request->validate([
            'tahun_default' => 'required|integer|min:2000|max:2100',
            'file' => 'nullable|file|mimes:csv,tsv,txt|max:10240',
            'teks' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        $tahunDefault = (int) $data['tahun_default'];
        $raw = '';
        if ($request->hasFile('file')) {
            $raw = (string) file_get_contents($request->file('file')->getRealPath());
        } elseif (! empty($data['link'])) {
            $raw = $this->fetchGoogleSheet($data['link']);
            if ($raw === null) {
                return redirect()->route('qmas.pembelian.index')
                    ->withErrors(['link' => 'Link Google Sheet tidak dapat diambil. Tempel data secara manual.']);
            }
        } else {
            $raw = (string) ($data['teks'] ?? '');
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $products = ['QMAS GL 19L', 'QMAS 240 ML', 'QMAS 330ML', 'QMAS 600 ML', 'QMAS 1.5L'];
        $masukCols = [1, 2, 3, 4, 5];

        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($lines as $line) {
            $cols = explode("\t", $line);
            if (count($cols) < 6) {
                continue;
            }

            $tanggal = $this->parseStockDate(trim($cols[0] ?? ''), $tahunDefault);
            if ($tanggal === null) {
                continue;
            }

            // deteksi baris data: kolom masuk berisi angka
            $hasMasuk = false;
            $masukValues = [];
            foreach ($masukCols as $i => $col) {
                $qty = (int) preg_replace('/[^0-9]/', '', trim($cols[$col] ?? ''));
                $masukValues[$i] = $qty;
                if ($qty > 0) {
                    $hasMasuk = true;
                }
            }
            if (! $hasMasuk) {
                continue;
            }

            foreach ($masukCols as $i => $col) {
                $qty = $masukValues[$i];
                if ($qty <= 0) {
                    continue;
                }

                $produk = $this->findOrCreateProduk($products[$i]);

                // idempotensi: lewati jika sudah ada pembelian dengan tanggal + keterangan + produk + qty
                $exists = Pembelian::whereDate('tgl_beli', $tanggal)
                    ->where('keterangan', 'Import Stok Masuk')
                    ->whereHas('details', function ($q) use ($produk, $qty) {
                        $q->where('produk_id', $produk->id)->where('qty', $qty);
                    })
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $pembelian = Pembelian::create([
                    'no_invoice' => 'IMP-STOK-' . date('Ymd', strtotime($tanggal)) . '-' . $produk->id,
                    'nama_supplier' => 'Import Stok',
                    'keterangan' => 'Import Stok Masuk',
                    'tgl_beli' => $tanggal,
                    'jatuh_tempo' => null,
                    'lokasi_penyimpanan' => 'Daqam',
                    'status_pembayaran' => 'Belum Bayar',
                    'akun_pembayaran' => null,
                    'ongkir' => 0,
                    'total_biaya' => 0,
                ]);

                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'produk_id' => $produk->id,
                    'harga_beli_satuan' => 0,
                    'qty' => $qty,
                    'subtotal' => 0,
                ]);

                $inserted++;
            }
        }

        $message = "Impor stok masuk selesai: {$inserted} pembelian dicatat, {$skipped} dilewati.";
        if ($errors > 0) {
            $message .= " {$errors} baris bermasalah.";
        }

        return redirect()->route('qmas.pembelian.index')->with('success', $message);
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

    protected function parseStockDate(string $raw, int $tahunDefault): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'may' => 5,
            'jun' => 6, 'jul' => 7, 'agu' => 8, 'ags' => 8, 'aug' => 8, 'agus' => 8,
            'sep' => 9, 'okt' => 10, 'oct' => 10, 'nov' => 11, 'des' => 12, 'dec' => 12,
        ];

        if (preg_match('/^(\d{1,2})[\s\-]+([A-Za-z]+)[\s\-]+(\d{2,4})$/', $raw, $m)) {
            $day = (int) $m[1];
            $monthKey = strtolower($m[2]);
            $short = substr($monthKey, 0, 3);
            if (! isset($months[$monthKey]) && isset($months[$short])) {
                $monthKey = $short;
            }
            if (! isset($months[$monthKey])) {
                return null;
            }
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
            if ($year < $tahunDefault) {
                $year = $tahunDefault;
            }
            return sprintf('%04d-%02d-%02d', $year, $months[$monthKey], $day);
        }

        if (preg_match('/^(\d{1,2})[\s\-]+([A-Za-z]+)$/', $raw, $m)) {
            $day = (int) $m[1];
            $monthKey = strtolower($m[2]);
            $short = substr($monthKey, 0, 3);
            if (! isset($months[$monthKey]) && isset($months[$short])) {
                $monthKey = $short;
            }
            if (! isset($months[$monthKey])) {
                return null;
            }
            return sprintf('%04d-%02d-%02d', $tahunDefault, $months[$monthKey], $day);
        }

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', $raw, $m)) {
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
            if ($year < $tahunDefault) {
                $year = $tahunDefault;
            }
            return sprintf('%04d-%02d-%02d', $year, (int) $m[2], (int) $m[1]);
        }

        return null;
    }

    protected function findOrCreateProduk(string $nama): Produk
    {
        $produk = Produk::whereRaw('LOWER(nama_produk) = ?', [strtolower($nama)])->first();
        if ($produk) {
            return $produk;
        }

        return Produk::create([
            'nama_produk' => $nama,
            'kategori' => 'Import',
            'harga' => 0,
            'stok' => 0,
            'batas_menipis' => 0,
        ]);
    }

    public function destroy(Pembelian $pembelian)
    {
        foreach ($pembelian->details as $d) {
            $produk = Produk::find($d->produk_id);
            if ($produk) {
                $produk->decrement('stok', $d->qty);
            }
        }
        $pembelian->delete();

        return redirect()->route('qmas.pembelian.index')->with('success', 'Pembelian berhasil dihapus.');
    }
}
