<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::latest('tanggal')->latest('id')->get();

        return view('qmas.penjualan', compact('penjualan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'pelanggan' => 'required|string|max:255',
            'item_pesanan' => 'required|string',
            'metode_pembayaran' => 'required|string|max:255',
            'jatuh_tempo' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'kategori_lokasi' => 'required|string|max:255',
            'total_harga' => 'required|numeric|min:0',
            'status_pembayaran' => 'required|string|in:Lunas,Belum Bayar',
        ]);

        Penjualan::create($data);

        return redirect()->route('qmas.penjualan.index')->with('success', 'Transaksi penjualan berhasil ditambahkan.');
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $data = $request->validate([
            'status_pembayaran' => 'sometimes|required|string|in:Lunas,Belum Bayar',
            'tanggal' => 'sometimes|required|date',
            'pelanggan' => 'sometimes|required|string|max:255',
            'item_pesanan' => 'sometimes|required|string',
            'metode_pembayaran' => 'sometimes|required|string|max:255',
            'jatuh_tempo' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'kategori_lokasi' => 'sometimes|required|string|max:255',
            'total_harga' => 'sometimes|required|numeric|min:0',
        ]);

        $penjualan->update($data);

        return redirect()->route('qmas.penjualan.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan)
    {
        $penjualan->delete();

        return redirect()->route('qmas.penjualan.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
