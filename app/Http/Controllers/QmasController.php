<?php

namespace App\Http\Controllers;

use App\Models\StokQmas;
use App\Models\Produk;
use App\Models\StokLokasi;
use App\Models\Penjualan;
use App\Models\AkunKas;
use App\Models\TransaksiKas;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class QmasController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        $omsetHariIni = (float) Penjualan::whereDate('tanggal', $today)->where('metode_pembayaran', '!=', 'Donasi')->sum('total_harga');
        $countHariIni = Penjualan::whereDate('tanggal', $today)->where('metode_pembayaran', '!=', 'Donasi')->count();

        $totalStok = (int) StokLokasi::sum('daqam') + (int) StokLokasi::sum('food_court') + (int) StokLokasi::sum('kantor');
        if ($totalStok === 0) {
            $totalStok = (int) Produk::sum('stok');
        }

        $totalKas = (float) AkunKas::sum('saldo_saat_ini');
        $kasAkun = AkunKas::count();

        $tagihan = (float) Penjualan::where('status_pembayaran', 'Belum Bayar')->where('metode_pembayaran', '!=', 'Donasi')->sum('total_harga');
        $tagihanJumlah = Penjualan::where('status_pembayaran', 'Belum Bayar')->where('metode_pembayaran', '!=', 'Donasi')->count();

        $stats = [
            'omset_hari_ini' => $omsetHariIni,
            'omset_label' => $countHariIni > 0 ? $countHariIni.' nota hari ini' : 'belum ada penjualan',
            'total_stok' => $totalStok,
            'total_kas' => $totalKas,
            'kas_akun' => $kasAkun,
            'tagihan' => $tagihan,
            'tagihan_jumlah' => $tagihanJumlah,
        ];

        $kas = AkunKas::orderBy('nama_kas')->get()->map(function ($a) {
            return ['nama' => $a->nama_kas, 'uraian' => $a->uraian, 'saldo' => (float) $a->saldo_saat_ini];
        })->toArray();

        // Penjualan 7 hari terakhir
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $labels = $days->map(fn ($d) => \Carbon\Carbon::parse($d)->format('D'))->toArray();
        $penjualanData = $days->map(fn ($d) => (float) Penjualan::whereDate('tanggal', $d)->where('metode_pembayaran', '!=', 'Donasi')->sum('total_harga'))->toArray();
        $penjualanAir = ['labels' => $labels, 'data' => $penjualanData];

        // Arus kas 7 hari (dari transaksi kas)
        $masuk = $days->map(fn ($d) => (float) TransaksiKas::whereDate('tanggal', $d)->where('jenis_transaksi', 'Masuk')->sum('nominal'))->toArray();
        $keluar = $days->map(fn ($d) => (float) TransaksiKas::whereDate('tanggal', $d)->where('jenis_transaksi', 'Keluar')->sum('nominal'))->toArray();
        $arusKas = ['labels' => $labels, 'masuk' => $masuk, 'keluar' => $keluar];

        // Stok produk
        $stokEntries = StokLokasi::with('produk')->get();
        $stokProduk = [
            'labels' => $stokEntries->map(fn ($s) => $s->produk->nama_produk ?? 'Produk')->toArray(),
            'data' => $stokEntries->map(fn ($s) => $s->total())->toArray(),
        ];

        // Penjualan per produk 30 hari (best-effort dari item_pesanan)
        $totalsByProduct = [];
        foreach (Produk::pluck('nama_produk') as $name) {
            $totalsByProduct[$name] = 0;
        }
        $recentSales = Penjualan::where('tanggal', '>=', now()->subDays(30)->toDateString())->where('metode_pembayaran', '!=', 'Donasi')->get();
        foreach ($recentSales as $sale) {
            foreach (explode(',', (string) $sale->item_pesanan) as $part) {
                if (preg_match('/^(.+?)\s*x(\d+)\s*$/i', trim($part), $m)) {
                    $name = trim($m[1]);
                    $qty = (int) $m[2];
                    if (isset($totalsByProduct[$name])) {
                        $totalsByProduct[$name] += $qty;
                    }
                }
            }
        }
        $penjualanProduk = [
            'labels' => array_keys($totalsByProduct),
            'data' => array_values($totalsByProduct),
        ];

        return view('qmas.dashboard', compact('stats', 'kas', 'penjualanAir', 'arusKas', 'stokProduk', 'penjualanProduk'));
    }

    public function index()
    {
        $entries = StokQmas::with('user')->latest()->paginate(15);

        return view('qmas.index', compact('entries'));
    }

    public function create()
    {
        return view('qmas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'stok_masuk_gl_19l' => 'nullable|integer|min:0',
            'stok_masuk_gelas_220ml' => 'nullable|integer|min:0',
            'stok_masuk_botol_330ml' => 'nullable|integer|min:0',
            'stok_masuk_botol_600ml' => 'nullable|integer|min:0',
            'stok_masuk_botol_15l' => 'nullable|integer|min:0',
            'stok_keluar_gl_19l' => 'nullable|integer|min:0',
            'stok_keluar_gelas_220ml' => 'nullable|integer|min:0',
            'stok_keluar_botol_330ml' => 'nullable|integer|min:0',
            'stok_keluar_botol_600ml' => 'nullable|integer|min:0',
            'stok_keluar_botol_15l' => 'nullable|integer|min:0',
            'sisa_stok_gl_19l' => 'nullable|integer|min:0',
            'sisa_stok_gelas_220ml' => 'nullable|integer|min:0',
            'sisa_stok_botol_330ml' => 'nullable|integer|min:0',
            'sisa_stok_botol_600ml' => 'nullable|integer|min:0',
            'sisa_stok_botol_15l' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        foreach (array_keys($data) as $k) {
            if ($k !== 'tanggal' && $k !== 'keterangan' && ($data[$k] === null)) {
                $data[$k] = 0;
            }
        }
        $data['user_id'] = auth()->id();

        StokQmas::create($data);

        return redirect()->route('qmas.index')->with('success', 'Data stok QMAS berhasil disimpan.');
    }

    public function produk()
    {
        return view('qmas.produk', ['title' => 'Produk']);
    }

    public function penjualan()
    {
        return view('qmas.penjualan', ['title' => 'Penjualan']);
    }

    public function pembelian()
    {
        return view('qmas.pembelian', ['title' => 'Pembelian']);
    }

    public function kasBank()
    {
        return view('qmas.kas-bank', ['title' => 'Kas & Bank']);
    }

    public function laporan()
    {
        return view('qmas.laporan', ['title' => 'Laporan']);
    }

    public function imporRiwayat()
    {
        return view('qmas.impor-riwayat', ['title' => 'Impor Riwayat']);
    }

    public function kontak()
    {
        return view('qmas.kontak', ['title' => 'Kontak']);
    }

    public function asetTetap()
    {
        return view('qmas.aset-tetap', ['title' => 'Aset Tetap']);
    }

    public function marketplace()
    {
        return view('qmas.marketplace', ['title' => 'Marketplace']);
    }

    public function importKartuKontrol(Request $request)
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
                return redirect()->route('qmas.index')
                    ->withErrors(['link' => 'Link Google Sheet tidak dapat diambil. Tempel data secara manual.']);
            }
        } else {
            $raw = (string) ($data['teks'] ?? '');
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);

        $masukKeys = ['stok_masuk_gl_19l', 'stok_masuk_gelas_220ml', 'stok_masuk_botol_330ml', 'stok_masuk_botol_600ml', 'stok_masuk_botol_15l'];
        $keluarKeys = ['stok_keluar_gl_19l', 'stok_keluar_gelas_220ml', 'stok_keluar_botol_330ml', 'stok_keluar_botol_600ml', 'stok_keluar_botol_15l'];
        $sisaKeys = ['sisa_stok_gl_19l', 'sisa_stok_gelas_220ml', 'sisa_stok_botol_330ml', 'sisa_stok_botol_600ml', 'sisa_stok_botol_15l'];

        $inserted = 0;
        $updated = 0;
        $errors = 0;

        foreach ($lines as $line) {
            if (strpos($line, "\t") !== false) {
                $cols = explode("\t", $line);
            } else {
                $cols = str_getcsv($line);
            }
            if (count($cols) < 16) {
                continue;
            }

            $dateRaw = trim($cols[0] ?? '');
            if ($dateRaw === '') {
                continue;
            }
            if (stripos($dateRaw, 'BULAN') !== false || stripos($dateRaw, 'JUMLAH') !== false
                || stripos($dateRaw, 'TOTAL') !== false || stripos($dateRaw, 'TANGGAL') !== false) {
                continue;
            }

            $tanggal = $this->parseKartuDate($dateRaw, $tahunDefault);
            if ($tanggal === null) {
                $errors++;
                continue;
            }

            $payload = [];
            foreach (range(0, 4) as $i) {
                $payload[$masukKeys[$i]] = (int) preg_replace('/[^0-9]/', '', trim($cols[1 + $i] ?? ''));
                $payload[$keluarKeys[$i]] = (int) preg_replace('/[^0-9]/', '', trim($cols[6 + $i] ?? ''));
                $payload[$sisaKeys[$i]] = (int) preg_replace('/[^0-9]/', '', trim($cols[11 + $i] ?? ''));
            }
            $payload['keterangan'] = 'Kartu Kontrol';
            $payload['user_id'] = auth()->id();

            $existing = \App\Models\StokQmas::whereDate('tanggal', $tanggal)->first();
            if ($existing) {
                $existing->update($payload);
                $updated++;
            } else {
                \App\Models\StokQmas::create(array_merge(['tanggal' => $tanggal], $payload));
                $inserted++;
            }
        }

        $message = "Impor kartu kontrol selesai: {$inserted} baru, {$updated} diperbarui.";
        if ($errors > 0) {
            $message .= " {$errors} baris bermasalah.";
        }

        return redirect()->route('qmas.index')->with('success', $message);
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
            $response = \Illuminate\Support\Facades\Http::timeout(20)->get($url);
            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    protected function parseKartuDate(string $raw, int $tahunDefault): ?string
    {
        $raw = trim($raw);
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'may' => 5,
            'jun' => 6, 'jul' => 7, 'agu' => 8, 'ags' => 8, 'aug' => 8, 'agus' => 8,
            'sep' => 9, 'okt' => 10, 'oct' => 10, 'nov' => 11, 'des' => 12, 'dec' => 12,
        ];

        if (preg_match('/^(\d{1,2})[\s\-]+([A-Za-z]+)[\s\-]*(\d{2,4})?$/', $raw, $m)) {
            $day = (int) $m[1];
            $monthKey = strtolower($m[2]);
            $short = substr($monthKey, 0, 3);
            if (! isset($months[$monthKey]) && isset($months[$short])) {
                $monthKey = $short;
            }
            if (! isset($months[$monthKey])) {
                return null;
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

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', $tahunDefault, (int) $m[2], (int) $m[1]);
        }

        return null;
    }
}
