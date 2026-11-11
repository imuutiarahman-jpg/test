@extends('layouts.qmas')

@section('title', 'Kas & Bank — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Kas & Bank</h2>
        <p class="text-sm text-slate-500">Pantau saldo dan transaksi kas</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Ringkasan Saldo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($akun as $a)
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase">{{ $a->nama_kas }}</p>
                <p class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($a->saldo_saat_ini, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $a->uraian }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Tambah Akun --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Tambah Akun Kas</h3>
            <form method="POST" action="{{ route('qmas.kasBank.storeAkun') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Nama kas</label>
                    <input type="text" name="nama_kas" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Uraian</label>
                    <input type="text" name="uraian" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Saldo awal</label>
                    <input type="number" name="saldo_awal" value="0" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Simpan</button>
            </form>
        </div>

        {{-- Transaksi Kas --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Catat Transaksi Kas</h3>
            <form method="POST" action="{{ route('qmas.kasBank.storeTransaksi') }}" class="space-y-3" x-data="{ jenis: 'Masuk' }">
                @csrf
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Jenis</label>
                    <select name="jenis_transaksi" x-model="jenis" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="Masuk">Masuk</option>
                        <option value="Keluar">Keluar</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Dari akun</label>
                    <select name="akun_sumber_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach ($akun as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_kas }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="jenis === 'Transfer'">
                    <label class="block text-xs text-slate-500 mb-1">Ke akun</label>
                    <select name="akun_tujuan_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="">— pilih —</option>
                        @foreach ($akun as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_kas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Nominal</label>
                    <input type="number" name="nominal" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Simpan</button>
            </form>
        </div>
    </div>

    {{-- Riwayat Transaksi --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
        <h3 class="font-semibold text-slate-800 mb-4">Riwayat Transaksi Kas</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">TANGGAL</th>
                        <th class="px-3 py-2 text-left text-slate-600">JENIS</th>
                        <th class="px-3 py-2 text-left text-slate-600">DARI</th>
                        <th class="px-3 py-2 text-left text-slate-600">KE</th>
                        <th class="px-3 py-2 text-left text-slate-600">NOMINAL</th>
                        <th class="px-3 py-2 text-left text-slate-600">KETERANGAN</th>
                        <th class="px-3 py-2 text-left text-slate-600">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $t)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-600">{{ $t->tanggal->format('Y-m-d') }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $t->jenis_transaksi === 'Masuk' ? 'bg-green-100 text-green-700' : ($t->jenis_transaksi === 'Keluar' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $t->jenis_transaksi }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-slate-600">{{ $t->sumber->nama_kas ?? '-' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $t->tujuan->nama_kas ?? '-' }}</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $t->keterangan }}</td>
                            <td class="px-3 py-2">
                                <form method="POST" action="{{ route('qmas.kasBank.destroyTransaksi', $t) }}" onsubmit="return confirm('Hapus transaksi ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-xs border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-6 text-center text-slate-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
