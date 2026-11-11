@extends('layouts.suara-muhammadiyah')

@section('title', 'Kelola Stok — Suara Muhammadiyah')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Kelola Stok</h2>
        <p class="text-sm text-slate-500">Rincian stok per produk dan ukuran berdasarkan katalog BUMK Store.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase">Total Unit (filter)</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($ringkasan['total_unit'], 0, ',', '.') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-warehouse"></i></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase">Stok Menipis</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($ringkasan['menipis'], 0, ',', '.') }} produk</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase">Stok Habis</p>
                <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($ringkasan['habis'], 0, ',', '.') }} produk</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i class="fa-solid fa-circle-xmark"></i></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-4">
        <form method="GET" action="{{ route('sm.stok') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs text-slate-500 mb-1">Pencarian</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Nama produk atau kode barang..."
                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="min-w-[170px]">
                <label class="block text-xs text-slate-500 mb-1">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" {{ $kategori === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fa-solid fa-filter mr-1"></i> Terapkan
            </button>
            @if ($q !== '' || $kategori !== '')
                <a href="{{ route('sm.stok') }}" class="px-4 py-2 text-sm font-semibold border border-slate-200 rounded-lg hover:bg-slate-50">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-slate-600">Kode</th>
                        <th class="px-4 py-3 text-left text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-left text-slate-600">Kategori</th>
                        <th class="px-4 py-3 text-left text-slate-600">Warna</th>
                        <th class="px-4 py-3 text-left text-slate-600">Rincian Stok</th>
                        <th class="px-4 py-3 text-right text-slate-600">Total</th>
                        <th class="px-4 py-3 text-right text-slate-600">Harga</th>
                        <th class="px-4 py-3 text-center text-slate-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $p)
                        @php
                            $statusClass = match ($p['status']) {
                                'Habis' => 'bg-rose-100 text-rose-700',
                                'Menipis' => 'bg-amber-100 text-amber-700',
                                default => 'bg-emerald-100 text-emerald-700',
                            };
                        @endphp
                        <tr class="border-t border-slate-100 hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $p['kode'] }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $p['nama'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p['kategori'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p['warna'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($p['rincian'] as $ukuran => $stok)
                                        <span class="text-[11px] px-2 py-0.5 rounded-full {{ $stok > 0 ? 'bg-slate-100 text-slate-600' : 'bg-rose-50 text-rose-500' }}">
                                            {{ $ukuran }}: {{ $stok }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ number_format($p['stok'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">Rp {{ number_format($p['harga_min'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $statusClass }}">{{ $p['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                                Tidak ada data stok yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
