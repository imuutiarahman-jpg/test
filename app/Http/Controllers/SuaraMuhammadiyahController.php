<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SuaraMuhammadiyahController extends Controller
{
    /**
     * Ringkasan inventori sesuai database katalog BUMK Store
     * (sumber: bumb9491_bumk.sql, ekspor 11 November 2026).
     */
    private const STATS = [
        'total_produk' => 243,
        'total_varian' => 337,
        'total_unit' => 869,
        'total_nilai' => 98026160,
    ];

    /**
     * Rekapitulasi produk & stok per kategori.
     *
     * @var array<int, array{kategori: string, produk: int, varian: int, unit: int, nilai: int, rata_harga: int}>
     */
    private const REKAP = [
        ['kategori' => 'ATRIBUT HW', 'produk' => 26, 'varian' => 26, 'unit' => 107, 'nilai' => 3671000, 'rata_harga' => 60615],
        ['kategori' => 'BAJU', 'produk' => 39, 'varian' => 85, 'unit' => 126, 'nilai' => 21826760, 'rata_harga' => 193550],
        ['kategori' => 'BENDERA', 'produk' => 0, 'varian' => 0, 'unit' => 0, 'nilai' => 0, 'rata_harga' => 0],
        ['kategori' => 'BUKU', 'produk' => 133, 'varian' => 133, 'unit' => 210, 'nilai' => 13595400, 'rata_harga' => 62041],
        ['kategori' => 'CELANA', 'produk' => 1, 'varian' => 3, 'unit' => 15, 'nilai' => 1500000, 'rata_harga' => 100000],
        ['kategori' => 'KAIN', 'produk' => 30, 'varian' => 30, 'unit' => 228, 'nilai' => 38056000, 'rata_harga' => 142533],
        ['kategori' => 'KEMEJA', 'produk' => 8, 'varian' => 40, 'unit' => 168, 'nilai' => 18401000, 'rata_harga' => 139538],
        ['kategori' => 'PAYUNG', 'produk' => 0, 'varian' => 0, 'unit' => 0, 'nilai' => 0, 'rata_harga' => 0],
        ['kategori' => 'PECI', 'produk' => 0, 'varian' => 0, 'unit' => 0, 'nilai' => 0, 'rata_harga' => 0],
        ['kategori' => 'SARUNG', 'produk' => 2, 'varian' => 4, 'unit' => 5, 'nilai' => 496000, 'rata_harga' => 94500],
        ['kategori' => 'TAPLAK', 'produk' => 0, 'varian' => 0, 'unit' => 0, 'nilai' => 0, 'rata_harga' => 0],
        ['kategori' => 'TAS', 'produk' => 0, 'varian' => 0, 'unit' => 0, 'nilai' => 0, 'rata_harga' => 0],
        ['kategori' => 'TOPI', 'produk' => 4, 'varian' => 16, 'unit' => 10, 'nilai' => 480000, 'rata_harga' => 41875],
    ];

    private ?Collection $produkCache = null;

    public function index()
    {
        return redirect()->route('sm.dashboard');
    }

    public function dashboard()
    {
        return view('suara-muhammadiyah.dashboard', [
            'stats' => self::STATS,
            'rekap' => collect(self::REKAP),
            'kategoriAktif' => collect(self::REKAP)->where('produk', '>', 0)->sortByDesc('nilai')->values(),
        ]);
    }

    public function katalog(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $kategori = trim((string) $request->query('kategori', ''));

        $products = $this->produk()
            ->when($q !== '', fn (Collection $items) => $items->filter(
                fn (array $p) => $this->matches($p, $q)
            ))
            ->when($kategori !== '', fn (Collection $items) => $items->where('kategori', $kategori))
            ->values();

        return view('suara-muhammadiyah.katalog', [
            'products' => $products,
            'categories' => $this->kategoriList(),
            'q' => $q,
            'kategori' => $kategori,
            'stats' => self::STATS,
        ]);
    }

    public function stok(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $kategori = trim((string) $request->query('kategori', ''));

        $products = $this->produk()
            ->when($q !== '', fn (Collection $items) => $items->filter(
                fn (array $p) => $this->matches($p, $q)
            ))
            ->when($kategori !== '', fn (Collection $items) => $items->where('kategori', $kategori))
            ->values();

        return view('suara-muhammadiyah.stok', [
            'products' => $products,
            'categories' => $this->kategoriList(),
            'q' => $q,
            'kategori' => $kategori,
            'ringkasan' => [
                'total_unit' => $products->sum('stok'),
                'menipis' => $products->where('status', 'Menipis')->count(),
                'habis' => $products->where('status', 'Habis')->count(),
            ],
        ]);
    }

    public function kasir(Request $request)
    {
        $kategori = trim((string) $request->query('kategori', ''));

        $products = $this->produk()
            ->when($kategori !== '', fn (Collection $items) => $items->where('kategori', $kategori))
            ->where('stok', '>', 0)
            ->values();

        return view('suara-muhammadiyah.kasir', [
            'products' => $products,
            'categories' => $this->kategoriList(),
            'kategori' => $kategori,
        ]);
    }

    public function laporan()
    {
        $rekap = collect(self::REKAP);
        $totalNilai = max(1, $rekap->sum('nilai'));

        return view('suara-muhammadiyah.laporan', [
            'stats' => self::STATS,
            'rekap' => $rekap,
            'totalNilai' => $totalNilai,
        ]);
    }

    public function scan(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $hasil = $q === ''
            ? null
            : $this->produk()->first(fn (array $p) => mb_strtolower($p['kode']) === mb_strtolower($q)
                || $this->matches($p, $q));

        return view('suara-muhammadiyah.scan', [
            'products' => $this->produk(),
            'q' => $q,
            'hasil' => $hasil,
        ]);
    }

    /**
     * @param  array<string, mixed>  $product
     */
    private function matches(array $product, string $needle): bool
    {
        $needle = mb_strtolower($needle);

        return str_contains(mb_strtolower($product['nama']), $needle)
            || str_contains(mb_strtolower($product['kode']), $needle)
            || str_contains(mb_strtolower($product['kategori']), $needle);
    }

    /**
     * Memuat katalog hasil ekspor database BUMK Store.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function produk(): Collection
    {
        if ($this->produkCache !== null) {
            return $this->produkCache;
        }

        $path = resource_path('data/suara-muhammadiyah/katalog.json');
        $rows = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return $this->produkCache = collect($rows)->map(function (array $row) {
            $rincian = $row['rincian'] ?? [];
            $stok = array_sum($rincian);
            $status = $stok <= 0 ? 'Habis' : ($stok <= 5 ? 'Menipis' : 'Tersedia');

            return [
                'kode' => $row['kode'],
                'kategori' => $row['kategori'],
                'nama' => $row['nama'],
                'warna' => $row['warna'],
                'rincian' => $rincian,
                'ukuran' => array_keys($rincian),
                'stok' => $stok,
                'harga_dasar' => (int) $row['harga_dasar'],
                'harga_min' => (int) $row['harga_min'],
                'harga_max' => (int) $row['harga_max'],
                'status' => $status,
                'tanggal' => $row['tanggal'],
            ];
        });
    }

    /**
     * @return array<int, string>
     */
    private function kategoriList(): array
    {
        return array_column(self::REKAP, 'kategori');
    }
}
