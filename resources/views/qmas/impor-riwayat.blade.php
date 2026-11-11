@extends('layouts.qmas')

@section('title', 'Impor Riwayat Penjualan')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Impor Riwayat Penjualan</h1>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="font-semibold text-slate-800 mb-1">Impor Riwayat Penjualan</h2>
        <p class="text-sm text-slate-500 mb-4">untuk laporan saja, tanpa ubah stok &amp; kas</p>
        <p class="text-xs text-slate-500 mb-4">Format yang didukung: berkas CSV/TSV/TXT ekspor sheet "list orderan QMAS", tempel teks, atau link Google Sheet. Satu baris = satu nota. Aman dijalankan berulang: nota yang sudah pernah masuk akan dilewati.</p>

        <form method="POST" action="{{ route('qmas.imporRiwayat.proses') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm text-slate-600">Tahun Default (bila tanggal tanpa tahun)</label>
                <input type="number" name="tahun_default" value="2025" class="w-32 mt-1 rounded-lg border-slate-200" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Berkas CSV/TSV/TXT</label>
                <input type="file" name="file" accept=".csv,.tsv,.txt" class="block mt-1 text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-600">..atau tempel isi sheet di sini</label>
                <textarea name="teks" rows="6" placeholder="Tempel baris dari Google Sheets / Excel (boleh termasuk baris judul)" class="w-full mt-1 rounded-lg border-slate-200"></textarea>
            </div>
            <div>
                <label class="text-sm text-slate-600">..atau link Google Sheet - opsional</label>
                <input type="text" name="link" placeholder="https://docs.google.com/spreadsheets/d/.../edit#gid=0" class="w-full mt-1 rounded-lg border-slate-200">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">Baca &amp; Pratinjau</button>
                <span class="text-xs text-slate-500">Belum ada data yang disimpan pada langkah ini.</span>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="font-semibold text-slate-800 mb-3">Data Impor Tersimpan</h2>
        <div class="space-y-2">
            @forelse ($imports as $imp)
                <div class="flex items-center justify-between border border-slate-100 rounded-lg px-3 py-2">
                    <div>
                        <div class="font-medium text-slate-800">{{ $imp->sumber_data }} — {{ $imp->tahun_default }}</div>
                        <div class="text-xs text-slate-400">{{ $imp->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <form method="POST" action="{{ route('qmas.imporRiwayat.destroy', $imp) }}" onsubmit="return confirm('Batalkan impor ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 text-sm border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Batalkan</button>
                    </form>
                </div>
            @empty
                <div class="text-slate-500 text-sm">Belum ada data impor.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $imports->links() }}</div>
    </div>
</div>
@endsection
