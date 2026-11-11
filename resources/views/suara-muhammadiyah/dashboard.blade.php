@extends('layouts.suara-muhammadiyah')

@section('title', 'Dashboard — Suara Muhammadiyah')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Beranda</h2>
            <p class="text-sm text-slate-500">Ringkasan katalog & inventori Suara Muhammadiyah Corner</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('sm.katalog') }}" class="px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fa-solid fa-boxes-stacked mr-1"></i> Katalog
            </a>
            <a href="{{ route('sm.stok') }}" class="px-4 py-2 text-sm font-semibold border border-slate-200 bg-white rounded-lg hover:bg-slate-50">
                <i class="fa-solid fa-warehouse mr-1"></i> Kelola Stok
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Jenis Produk</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_produk'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">produk terdaftar</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Varian Ukuran</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_varian'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">varian stok</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-ruler-combined"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Unit Stok</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_unit'], 0, ',', '.') }} unit</p>
                    <p class="text-xs text-slate-400 mt-1">seluruh kategori</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Nilai Inventori</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">nilai jual varian</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-slate-800">Rekap Produk & Stok per Kategori</h3>
                    <p class="text-xs text-slate-500">Sumber: database katalog BUMK Store</p>
                </div>
                <a href="{{ route('sm.laporan') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Lihat laporan</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-slate-600">Kategori</th>
                            <th class="px-3 py-2 text-right text-slate-600">Produk</th>
                            <th class="px-3 py-2 text-right text-slate-600">Varian</th>
                            <th class="px-3 py-2 text-right text-slate-600">Unit</th>
                            <th class="px-3 py-2 text-right text-slate-600">Nilai Inventori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $row)
                            <tr class="border-t border-slate-100 {{ $row['produk'] === 0 ? 'text-slate-400' : '' }}">
                                <td class="px-3 py-2 font-medium">{{ $row['kategori'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['produk'], 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['varian'], 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['unit'], 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($row['nilai'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                            <td class="px-3 py-2">Total Keseluruhan</td>
                            <td class="px-3 py-2 text-right">{{ number_format($stats['total_produk'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($stats['total_varian'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($stats['total_unit'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                <h3 class="font-semibold text-slate-800 mb-3">Kategori Nilai Terbesar</h3>
                <div class="space-y-3">
                    @php $maks = max(1, $kategoriAktif->max('nilai')); @endphp
                    @foreach ($kategoriAktif->take(5) as $row)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-700">{{ $row['kategori'] }}</span>
                                <span class="text-slate-500">Rp {{ number_format($row['nilai'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ round($row['nilai'] / $maks * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                <h3 class="font-semibold text-slate-800 mb-3">Akses Cepat</h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <a href="{{ route('sm.katalog') }}" class="flex items-center gap-2 p-3 rounded-lg border border-slate-100 hover:bg-indigo-50 hover:border-indigo-200">
                        <i class="fa-solid fa-boxes-stacked text-indigo-600"></i> Katalog
                    </a>
                    <a href="{{ route('sm.kasir') }}" class="flex items-center gap-2 p-3 rounded-lg border border-slate-100 hover:bg-indigo-50 hover:border-indigo-200">
                        <i class="fa-solid fa-cash-register text-indigo-600"></i> Kasir
                    </a>
                    <a href="{{ route('sm.stok') }}" class="flex items-center gap-2 p-3 rounded-lg border border-slate-100 hover:bg-indigo-50 hover:border-indigo-200">
                        <i class="fa-solid fa-warehouse text-indigo-600"></i> Stok
                    </a>
                    <a href="{{ route('sm.scan') }}" class="flex items-center gap-2 p-3 rounded-lg border border-slate-100 hover:bg-indigo-50 hover:border-indigo-200">
                        <i class="fa-solid fa-qrcode text-indigo-600"></i> Scan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
