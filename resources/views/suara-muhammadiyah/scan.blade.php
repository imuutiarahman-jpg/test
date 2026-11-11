@extends('layouts.suara-muhammadiyah')

@section('title', 'Scan + Stok — Suara Muhammadiyah')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Scan + Stok</h2>
        <p class="text-sm text-slate-500">Cari produk berdasarkan kode barang atau nama untuk melihat detail stok.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <form method="GET" action="{{ route('sm.scan') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[240px]">
                    <label class="block text-xs text-slate-500 mb-1">Kode barang / nama produk</label>
                    <div class="relative">
                        <i class="fa-solid fa-qrcode absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="q" value="{{ $q }}" autofocus
                            placeholder="Contoh: BRG-0002"
                            class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari
                </button>
            </form>
            <p class="text-xs text-slate-400 mt-3">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Pemindai barcode dapat dihubungkan pada tahap integrasi perangkat.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-2">Ringkasan</h3>
            <div class="text-sm text-slate-500 space-y-1">
                <div class="flex justify-between"><span>Produk terdaftar</span><strong class="text-slate-700">{{ $products->count() }}</strong></div>
                <div class="flex justify-between"><span>Total varian</span><strong class="text-slate-700">{{ $products->sum(fn ($p) => count($p['rincian'])) }}</strong></div>
                <div class="flex justify-between"><span>Total unit</span><strong class="text-slate-700">{{ number_format($products->sum('stok'), 0, ',', '.') }}</strong></div>
            </div>
        </div>
    </div>

    @if ($q !== '')
        @if ($hasil)
            @php
                $statusClass = match ($hasil['status']) {
                    'Habis' => 'bg-rose-100 text-rose-700',
                    'Menipis' => 'bg-amber-100 text-amber-700',
                    default => 'bg-emerald-100 text-emerald-700',
                };
            @endphp
            <div class="bg-white rounded-xl shadow-sm p-5 border border-indigo-100 mb-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-indigo-50 text-indigo-600">{{ $hasil['kategori'] }}</span>
                        <h3 class="text-lg font-bold text-slate-800 mt-2">{{ $hasil['nama'] }}</h3>
                        <p class="text-sm text-slate-500">{{ $hasil['kode'] }} · Warna {{ $hasil['warna'] }} · Input {{ $hasil['tanggal'] }}</p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $statusClass }}">{{ $hasil['status'] }} · {{ $hasil['stok'] }} unit</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Harga Dasar</p>
                        <p class="font-semibold text-slate-800">Rp {{ number_format($hasil['harga_dasar'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Harga Min</p>
                        <p class="font-semibold text-slate-800">Rp {{ number_format($hasil['harga_min'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Harga Max</p>
                        <p class="font-semibold text-slate-800">Rp {{ number_format($hasil['harga_max'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase">Total Stok</p>
                        <p class="font-semibold text-slate-800">{{ number_format($hasil['stok'], 0, ',', '.') }} unit</p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs text-slate-500 uppercase mb-2">Rincian Stok per Ukuran</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($hasil['rincian'] as $ukuran => $stok)
                            <span class="text-sm px-3 py-1.5 rounded-lg {{ $stok > 0 ? 'bg-slate-100 text-slate-700' : 'bg-rose-50 text-rose-600' }}">
                                {{ $ukuran }}: <strong>{{ $stok }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 border border-amber-100 mb-4 text-sm text-amber-700">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                Produk dengan kode/nama <strong>{{ $q }}</strong> tidak ditemukan.
            </div>
        @endif
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Daftar Kode Barang</h3>
            <span class="text-xs text-slate-400">Klik untuk memindai</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-slate-600">Kode</th>
                        <th class="px-4 py-3 text-left text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-left text-slate-600">Kategori</th>
                        <th class="px-4 py-3 text-right text-slate-600">Stok</th>
                        <th class="px-4 py-3 text-right text-slate-600">Harga</th>
                        <th class="px-4 py-3 text-center text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $p)
                        <tr class="border-t border-slate-100 hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $p['kode'] }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $p['nama'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p['kategori'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $p['stok'] === 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ $p['stok'] }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">Rp {{ number_format($p['harga_min'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('sm.scan', ['q' => $p['kode']]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Scan</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
