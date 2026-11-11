<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();

        return view('qmas.kasir', compact('produk'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pelanggan' => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $items = [];
        $total = 0;
        $detail = [];
        foreach ($data['items'] as $it) {
            $produk = Produk::findOrFail($it['produk_id']);
            if ($produk->stok < $it['qty']) {
                return back()->with('error', 'Stok ' . $produk->nama_produk . ' tidak cukup.');
            }
            $items[] = $produk->nama_produk . ' x' . $it['qty'];
            $total += (float) $produk->harga * (int) $it['qty'];
            $detail[] = ['produk' => $produk, 'qty' => (int) $it['qty']];
        }

        if ($data['metode_pembayaran'] === 'Midtrans') {
            if (! config('services.midtrans.server_key') || ! config('services.midtrans.client_key')) {
                return back()->with('error', 'Kunci Midtrans belum diisi di .env.');
            }

            $penjualan = Penjualan::create([
                'tanggal' => now()->toDateString(),
                'pelanggan' => $data['pelanggan'] ?: 'Umum',
                'item_pesanan' => implode(', ', $items),
                'metode_pembayaran' => 'Midtrans',
                'kategori_lokasi' => 'Kasir',
                'total_harga' => $total,
                'status_pembayaran' => 'Menunggu Pembayaran',
            ]);

            $result = app(MidtransService::class)->createPayment($penjualan);

            if (! empty($result['redirect_url'])) {
                return redirect()->away($result['redirect_url']);
            }

            return redirect()->route('qmas.kasir')->with('error', 'Gagal membuat pembayaran Midtrans.');
        }

        Penjualan::create([
            'tanggal' => now()->toDateString(),
            'pelanggan' => $data['pelanggan'] ?: 'Umum',
            'item_pesanan' => implode(', ', $items),
            'metode_pembayaran' => $data['metode_pembayaran'],
            'kategori_lokasi' => 'Kasir',
            'total_harga' => $total,
            'status_pembayaran' => 'Lunas',
        ]);

        foreach ($detail as $d) {
            $d['produk']->decrement('stok', $d['qty']);
        }

        return redirect()->route('qmas.kasir')->with('success', 'Transaksi kasir berhasil. Total Rp ' . number_format($total, 0, ',', '.'));
    }
}
