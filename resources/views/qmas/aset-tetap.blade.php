@extends('layouts.qmas')

@section('title', 'Aset Tetap')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Aset Tetap</h1>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="text-sm text-slate-500">Total Nilai Aset</div>
        <div class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400">{{ $totalAset }} aset tercatat</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="font-semibold text-slate-800 mb-3">Tambah Aset</h2>
        <form method="POST" action="{{ route('qmas.asetTetap.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <label class="text-sm text-slate-600">Nama aset</label>
                <input type="text" name="nama_aset" class="w-full mt-1 rounded-lg border-slate-200" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Tgl beli</label>
                <input type="date" name="tgl_beli" class="w-full mt-1 rounded-lg border-slate-200" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Nilai perolehan Rp</label>
                <input type="number" name="nilai_perolehan" min="0" class="w-full mt-1 rounded-lg border-slate-200" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Umur (tahun)</label>
                <input type="number" name="umur" min="1" value="5" class="w-full mt-1 rounded-lg border-slate-200" required>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">Simpan</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left">NAMA ASET</th>
                    <th class="px-3 py-2 text-left">TGL BELI</th>
                    <th class="px-3 py-2 text-left">NILAI PEROLEHAN</th>
                    <th class="px-3 py-2 text-left">UMUR (THN)</th>
                    <th class="px-3 py-2 text-left">SUSUT/BULAN</th>
                    <th class="px-3 py-2 text-left">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($asets as $a)
                    <tr class="border-t">
                        <td class="px-3 py-2 font-medium text-slate-800">{{ $a->nama_aset }}</td>
                        <td class="px-3 py-2">{{ $a->tgl_beli->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">Rp {{ number_format($a->nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ $a->umur }}</td>
                        <td class="px-3 py-2">Rp {{ number_format($a->susut_per_bulan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2">
                            <form method="POST" action="{{ route('qmas.asetTetap.destroy', $a) }}" onsubmit="return confirm('Hapus aset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-sm border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $asets->links() }}</div>
</div>
@endsection
