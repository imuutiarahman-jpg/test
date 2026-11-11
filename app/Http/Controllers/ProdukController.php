<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $kategori = $request->get('kategori');

        $produk = Produk::query()
            ->when($q, fn ($query) => $query->where('nama_produk', 'like', "%{$q}%"))
            ->when($kategori, fn ($query) => $query->where('kategori', $kategori))
            ->latest()
            ->get();

        $kategoris = ['Galon 19L', 'Cup 240ML', 'Botol 330ML', 'Botol 600ML', 'Botol 1.5L'];

        return view('qmas.produk', compact('produk', 'kategoris', 'q', 'kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'batas_menipis' => 'required|integer|min:0',
        ]);

        Produk::create($data);

        return redirect()->route('qmas.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'nama_produk' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|numeric|min:0',
            'stok' => 'sometimes|required|integer|min:0',
            'batas_menipis' => 'sometimes|required|integer|min:0',
        ]);

        $produk->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'harga' => $produk->harga]);
        }

        return redirect()->route('qmas.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('qmas.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
