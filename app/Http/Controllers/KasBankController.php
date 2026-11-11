<?php

namespace App\Http\Controllers;

use App\Models\AkunKas;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class KasBankController extends Controller
{
    public function index()
    {
        $akun = AkunKas::orderBy('nama_kas')->get();
        $transaksi = TransaksiKas::with(['sumber', 'tujuan'])->latest('tanggal')->latest('id')->get();

        return view('qmas.kas-bank', compact('akun', 'transaksi'));
    }

    public function storeAkun(Request $request)
    {
        $data = $request->validate([
            'nama_kas' => 'required|string|max:255',
            'uraian' => 'nullable|string',
            'saldo_awal' => 'required|numeric|min:0',
        ]);
        $data['saldo_saat_ini'] = $data['saldo_awal'];
        AkunKas::create($data);

        return redirect()->route('qmas.kasBank.index')->with('success', 'Akun kas berhasil ditambahkan.');
    }

    public function storeTransaksi(Request $request)
    {
        $data = $request->validate([
            'jenis_transaksi' => 'required|string|in:Masuk,Keluar,Transfer',
            'akun_sumber_id' => 'required|exists:akun_kas,id',
            'akun_tujuan_id' => 'nullable|exists:akun_kas,id',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        $nominal = (float) $data['nominal'];
        $sumber = AkunKas::findOrFail($data['akun_sumber_id']);

        if ($data['jenis_transaksi'] === 'Masuk') {
            $sumber->increment('saldo_saat_ini', $nominal);
        } elseif ($data['jenis_transaksi'] === 'Keluar') {
            $sumber->decrement('saldo_saat_ini', $nominal);
        } elseif ($data['jenis_transaksi'] === 'Transfer') {
            $tujuan = AkunKas::findOrFail($data['akun_tujuan_id']);
            $sumber->decrement('saldo_saat_ini', $nominal);
            $tujuan->increment('saldo_saat_ini', $nominal);
        }

        TransaksiKas::create($data);

        return redirect()->route('qmas.kasBank.index')->with('success', 'Transaksi kas berhasil dicatat.');
    }

    public function destroyTransaksi(TransaksiKas $transaksi)
    {
        // revert
        $nominal = (float) $transaksi->nominal;
        $sumber = AkunKas::find($transaksi->akun_sumber_id);
        if ($transaksi->jenis_transaksi === 'Masuk') {
            $sumber?->decrement('saldo_saat_ini', $nominal);
        } elseif ($transaksi->jenis_transaksi === 'Keluar') {
            $sumber?->increment('saldo_saat_ini', $nominal);
        } elseif ($transaksi->jenis_transaksi === 'Transfer') {
            $sumber?->increment('saldo_saat_ini', $nominal);
            $tujuan = AkunKas::find($transaksi->akun_tujuan_id);
            $tujuan?->decrement('saldo_saat_ini', $nominal);
        }
        $transaksi->delete();

        return redirect()->route('qmas.kasBank.index')->with('success', 'Transaksi dihapus.');
    }
}
