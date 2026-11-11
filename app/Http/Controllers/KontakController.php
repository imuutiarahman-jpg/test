<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $tipe = trim((string) $request->query('tipe', ''));

        $kontaks = Kontak::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('no_hp', 'like', '%'.$search.'%')
                        ->orWhere('alamat', 'like', '%'.$search.'%');
                });
            })
            ->when($tipe !== '' && $tipe !== 'Semua', function ($query) use ($tipe) {
                $query->where('tipe', $tipe);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('qmas.kontak', compact('kontaks', 'search', 'tipe'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:Pelanggan,Supplier',
            'no_hp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        Kontak::create($data);

        return redirect()->route('qmas.kontak')->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function update(Request $request, Kontak $kontak)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:Pelanggan,Supplier',
            'no_hp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        $kontak->update($data);

        return redirect()->route('qmas.kontak')->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Kontak $kontak)
    {
        $kontak->delete();

        return redirect()->route('qmas.kontak')->with('success', 'Kontak berhasil dihapus.');
    }
}
