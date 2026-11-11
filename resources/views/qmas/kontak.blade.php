@extends('layouts.qmas')

@section('title', 'Data Kontak')

@section('content')
<div class="space-y-6" x-data="{ editOpen: false, editId: null, editNama: '', editTipe: 'Pelanggan', editNoHp: '', editAlamat: '' }">
    <h1 class="text-2xl font-bold text-slate-800">Data Kontak</h1>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="font-semibold text-slate-800 mb-3">Tambah Kontak</h2>
        <form method="POST" action="{{ route('qmas.kontak.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <label class="text-sm text-slate-600">Nama</label>
                <input type="text" name="nama" class="w-full mt-1 rounded-lg border-slate-200" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Tipe</label>
                <select name="tipe" class="w-full mt-1 rounded-lg border-slate-200">
                    <option value="Pelanggan">Pelanggan</option>
                    <option value="Supplier">Supplier</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-slate-600">No HP</label>
                <input type="text" name="no_hp" class="w-full mt-1 rounded-lg border-slate-200">
            </div>
            <div>
                <label class="text-sm text-slate-600">Alamat</label>
                <input type="text" name="alamat" class="w-full mt-1 rounded-lg border-slate-200">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">Simpan</button>
            </div>
        </form>
    </div>

    <div class="flex items-center gap-2">
        <form method="GET" action="{{ route('qmas.kontak') }}" class="flex items-center gap-2 flex-1">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari kontak..." class="flex-1 rounded-lg border-slate-200">
            <select name="tipe" class="rounded-lg border-slate-200">
                <option value="Semua" {{ $tipe === '' || $tipe === 'Semua' ? 'selected' : '' }}>Semua</option>
                <option value="Pelanggan" {{ $tipe === 'Pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                <option value="Supplier" {{ $tipe === 'Supplier' ? 'selected' : '' }}>Supplier</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Cari</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($kontaks as $k)
            <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800">{{ $k->nama }}</span>
                    <span class="text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded">{{ $k->tipe }}</span>
                </div>
                <div class="text-sm text-slate-600">{{ $k->no_hp }}</div>
                @if ($k->alamat)
                    <div class="text-xs text-slate-400">{{ $k->alamat }}</div>
                @endif
                <div class="flex items-center gap-2 mt-2">
                    <button
                        type="button"
                        class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50"
                        @click="editOpen=true; editId={{ $k->id }}; editNama='{{ addslashes($k->nama) }}'; editTipe='{{ $k->tipe }}'; editNoHp='{{ $k->no_hp }}'; editAlamat='{{ addslashes($k->alamat ?? '') }}'"
                    >Edit</button>
                    <form method="POST" action="{{ route('qmas.kontak.destroy', $k) }}" onsubmit="return confirm('Hapus kontak ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 text-sm border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
                    </form>
                    <a href="https://wa.me/{{ preg_replace('/\D/','',$k->no_hp ?? '') }}" target="_blank" class="px-3 py-1.5 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600">WhatsApp →</a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-slate-500 py-6">Belum ada data.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $kontaks->links() }}</div>

    <div x-show="editOpen" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center" @click.self="editOpen=false">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
            <h2 class="font-semibold text-slate-800 mb-3">Edit Kontak</h2>
            <form method="POST" :action="'{{ url('/qmas/kontak') }}/' + editId">
                @csrf
                @method('PUT')
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-slate-600">Nama</label>
                        <input type="text" name="nama" x-model="editNama" class="w-full mt-1 rounded-lg border-slate-200" required>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Tipe</label>
                        <select name="tipe" x-model="editTipe" class="w-full mt-1 rounded-lg border-slate-200">
                            <option value="Pelanggan">Pelanggan</option>
                            <option value="Supplier">Supplier</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">No HP</label>
                        <input type="text" name="no_hp" x-model="editNoHp" class="w-full mt-1 rounded-lg border-slate-200">
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Alamat</label>
                        <input type="text" name="alamat" x-model="editAlamat" class="w-full mt-1 rounded-lg border-slate-200">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="editOpen=false" class="px-4 py-2 border border-slate-200 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
