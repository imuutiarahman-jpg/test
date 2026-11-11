@extends('layouts.suara-muhammadiyah')

@section('title', 'Laporan — Suara Muhammadiyah')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Inventori</h2>
        <p class="text-sm text-slate-500">Rekapitulasi nilai inventori Suara Muhammadiyah Corner per kategori.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Jenis Produk</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_produk'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Varian Ukuran</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_varian'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Unit Stok</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_unit'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Nilai Inventori</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Rekapitulasi Produk & Stok per Kategori</h3>
                <p class="text-xs text-slate-500">Ekspor database katalog BUMK Store, 11 November 2026</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-slate-600">No</th>
                            <th class="px-4 py-3 text-left text-slate-600">Kategori</th>
                            <th class="px-4 py-3 text-right text-slate-600">Produk</th>
                            <th class="px-4 py-3 text-right text-slate-600">Varian</th>
                            <th class="px-4 py-3 text-right text-slate-600">Unit</th>
                            <th class="px-4 py-3 text-right text-slate-600">Nilai Inventori</th>
                            <th class="px-4 py-3 text-right text-slate-600">Rata-rata Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $i => $row)
                            <tr class="border-t border-slate-100 {{ $row['produk'] === 0 ? 'text-slate-400' : '' }}">
                                <td class="px-4 py-3">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ $row['kategori'] }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['produk'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['varian'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['unit'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($row['nilai'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($row['rata_harga'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                            <td class="px-4 py-3" colspan="2">Total Keseluruhan</td>
                            <td class="px-4 py-3 text-right">{{ number_format($stats['total_produk'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($stats['total_varian'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($stats['total_unit'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">Rp 111.230</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 h-fit">
            <h3 class="font-semibold text-slate-800 mb-1">Komposisi Nilai Inventori</h3>
            <p class="text-xs text-slate-500 mb-4">Persentase terhadap total nilai</p>
            <div class="space-y-3">
                @foreach ($rekap->where('nilai', '>', 0)->sortByDesc('nilai') as $row)
                    @php $persen = $row['nilai'] / $totalNilai * 100; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700">{{ $row['kategori'] }}</span>
                            <span class="text-slate-500">{{ number_format($persen, 1, ',', '.') }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ round($persen, 1) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100">
                <h4 class="text-sm font-semibold text-slate-800 mb-2">Kategori tanpa stok</h4>
                <div class="flex flex-wrap gap-1">
                    @forelse ($rekap->where('produk', 0) as $row)
                        <span class="text-[11px] px-2 py-1 rounded-full bg-slate-100 text-slate-500">{{ $row['kategori'] }}</span>
                    @empty
                        <span class="text-xs text-slate-400">Tidak ada.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
