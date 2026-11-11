<?php

namespace App\Http\Controllers;

use App\Models\AsetTetap;
use Illuminate\Http\Request;

class AsetTetapController extends Controller
{
    public function index()
    {
        $asets = AsetTetap::latest()->paginate(15);
        $totalNilai = (float) AsetTetap::sum('nilai_perolehan');
        $totalAset = AsetTetap::count();

        return view('qmas.aset-tetap', compact('asets', 'totalNilai', 'totalAset'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'tgl_beli' => 'required|date',
            'nilai_perolehan' => 'required|numeric|min:0',
            'umur' => 'required|integer|min:1',
        ]);

        $bulan = $data['umur'] * 12;
        $data['susut_per_bulan'] = $bulan > 0 ? $data['nilai_perolehan'] / $bulan : 0;

        AsetTetap::create($data);

        return redirect()->route('qmas.asetTetap')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function destroy(AsetTetap $aset_tetap)
    {
        $aset_tetap->delete();

        return redirect()->route('qmas.asetTetap')->with('success', 'Aset berhasil dihapus.');
    }
}
