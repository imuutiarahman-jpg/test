<?php

namespace App\Http\Controllers;

use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $defaultDari = Penjualan::min('tanggal') ?: Pembelian::min('tgl_beli');
        $defaultDari = $defaultDari ? \Carbon\Carbon::parse($defaultDari)->format('Y-m-d') : now()->startOfYear()->format('Y-m-d');
        $defaultSampai = Penjualan::max('tanggal') ?: Pembelian::max('tgl_beli');
        $defaultSampai = $defaultSampai ? \Carbon\Carbon::parse($defaultSampai)->format('Y-m-d') : now()->format('Y-m-d');
        if (\Carbon\Carbon::parse($defaultSampai)->lt(now())) {
            $defaultSampai = now()->format('Y-m-d');
        }

        $dari = $request->get('dari', $defaultDari);
        $sampai = $request->get('sampai', $defaultSampai);

        $pembelian = Pembelian::with('details.produk')
            ->whereDate('tgl_beli', '>=', $dari)
            ->whereDate('tgl_beli', '<=', $sampai)
            ->get();
        $penjualanAll = Penjualan::whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->get();
        $penjualan = $penjualanAll->where('metode_pembayaran', '!=', 'Donasi')->values();
        $sumbangan = $penjualanAll->where('kategori_lokasi', 'Donasi')->values();

        $totalPembelian = (float) $pembelian->sum('total_biaya');
        $countPembelian = $pembelian->count();
        $totalPenjualan = (float) $penjualan->sum('total_harga');
        $countPenjualan = $penjualan->count();
        $labaKotor = $totalPenjualan;
        $labaBersih = $labaKotor;

        $metode = $penjualan->groupBy('metode_pembayaran')->map(function ($items) {
            return ['nota' => $items->count(), 'total' => $items->sum('total_harga')];
        });

        $supplier = $pembelian->groupBy('nama_supplier')->map(function ($items) {
            return ['nota' => $items->count(), 'total' => $items->sum('total_biaya')];
        });

        // HPP rata-rata per produk dari pembelian
        $hppMap = DetailPembelian::selectRaw('produk_id, AVG(harga_beli_satuan) as hpp')
            ->groupBy('produk_id')
            ->pluck('hpp', 'produk_id');

        // qty terjual per produk (best-effort dari item_pesanan)
        $qtySold = [];
        foreach ($penjualan as $p) {
            foreach (explode(',', (string) $p->item_pesanan) as $part) {
                if (preg_match('/^(.+?)\s*x(\d+)\s*$/i', trim($part), $m)) {
                    $name = trim($m[1]);
                    $qty = (int) $m[2];
                    $qtySold[$name] = ($qtySold[$name] ?? 0) + $qty;
                }
            }
        }

        $penjualanPerProduk = [];
        foreach ($qtySold as $name => $qty) {
            $produk = Produk::where('nama_produk', $name)->first();
            $hpp = $produk ? (float) ($hppMap[$produk->id] ?? 0) : 0;
            $harga = $produk ? (float) $produk->harga : 0;
            $penjualanPerProduk[] = [
                'produk' => $name,
                'qty' => $qty,
                'hpp' => $hpp,
                'harga' => $harga,
                'laba' => ($harga - $hpp) * $qty,
                'ket' => $qty > 0 ? 'Laris' : '',
            ];
        }

        $lunas = $penjualan->where('status_pembayaran', 'Lunas')->values();
        $belumLunas = $penjualan->where('status_pembayaran', 'Belum Bayar')->values();

        $grafik = $penjualan->groupBy(fn ($p) => \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'))
            ->map(fn ($items, $date) => ['tanggal' => $date, 'total' => (float) $items->sum('total_harga')])
            ->values();

        return view('qmas.laporan', compact(
            'dari', 'sampai', 'totalPembelian', 'countPembelian',
            'totalPenjualan', 'countPenjualan', 'labaKotor', 'labaBersih',
            'metode', 'supplier', 'penjualanPerProduk', 'lunas', 'belumLunas',
            'sumbangan', 'grafik'
        ));
    }
}
