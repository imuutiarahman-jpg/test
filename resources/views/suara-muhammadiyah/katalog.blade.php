@extends('layouts.suara-muhammadiyah')

@section('title', 'Katalog Produk — Suara Muhammadiyah')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Katalog Produk</h2>
        <p class="text-sm text-slate-500">
            Menampilkan seluruh master produk beserta ringkasan stok per ukuran dan rentang harga jual.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-4">
        <form method="GET" action="{{ route('sm.katalog') }}" class="flex flex-wrap items-end gap-3">
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
                <a href="{{ route('sm.katalog') }}" class="px-4 py-2 text-sm font-semibold border border-slate-200 rounded-lg hover:bg-slate-50">Reset</a>
            @endif
        </form>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-2 mb-3 text-sm text-slate-500">
        <span>Menampilkan <strong class="text-slate-700">{{ $products->count() }}</strong> dari {{ number_format($stats['total_produk'], 0, ',', '.') }} produk</span>
        <span>{{ count($categories) }} kategori · {{ number_format($stats['total_varian'], 0, ',', '.') }} varian</span>
    </div>

    @if ($products->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-10 border border-slate-100 text-center">
            <i class="fa-solid fa-box-open text-3xl text-slate-300"></i>
            <p class="mt-3 text-sm text-slate-500">Tidak ada produk yang cocok dengan filter.</p>
            <a href="{{ route('sm.katalog') }}" class="inline-block mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-700">Tampilkan semua produk</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($products as $p)
                @php
                    $statusClass = match ($p['status']) {
                        'Habis' => 'bg-rose-100 text-rose-700',
                        'Menipis' => 'bg-amber-100 text-amber-700',
                        default => 'bg-emerald-100 text-emerald-700',
                    };
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                    <div class="relative h-28 bg-gradient-to-br from-indigo-50 to-slate-100 flex items-center justify-center text-indigo-300">
                        <i class="fa-solid fa-shirt text-3xl"></i>
                        <span class="absolute top-2 left-2 bg-white/90 text-slate-700 text-xs font-semibold px-2 py-1 rounded-full">
                            {{ $p['kategori'] }}
                        </span>
                        <span class="absolute top-2 right-2 text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $p['status'] }} · {{ $p['stok'] }}
                        </span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="font-semibold text-slate-800 leading-snug">{{ $p['nama'] }}</div>
                        <div class="text-xs text-slate-400 mt-1">Kode: {{ $p['kode'] }} · Warna: {{ $p['warna'] }}</div>

                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach ($p['rincian'] as $ukuran => $stok)
                                <span class="text-[11px] px-2 py-0.5 rounded-full {{ $stok > 0 ? 'bg-slate-100 text-slate-600' : 'bg-rose-50 text-rose-500' }}">
                                    {{ $ukuran }}: {{ $stok }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-auto pt-3 flex items-end justify-between">
                            <div>
                                <div class="text-[11px] text-slate-400">Harga jual</div>
                                <div class="font-bold text-slate-800">
                                    Rp {{ number_format($p['harga_min'], 0, ',', '.') }}
                                    @if ($p['harga_max'] > $p['harga_min'])
                                        <span class="font-normal text-slate-400">– Rp {{ number_format($p['harga_max'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('sm.scan', ['q' => $p['kode']]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                                Detail <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
