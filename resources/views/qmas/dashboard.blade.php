@extends('layouts.qmas')

@section('title', 'Dashboard — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Beranda</h2>
        <p class="text-sm text-slate-500">Ringkasan performa Qmas BUMK UMGO</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Omset Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['omset_hari_ini'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stats['omset_label'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Stok</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_stok'] }} unit</p>
                    <p class="text-xs text-slate-400 mt-1">semua varian</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-boxes-stacked"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Total Kas & Bank</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['total_kas'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stats['kas_akun'] }} akun aktif</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-building-columns"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Tagihan & Piutang</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['tagihan'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stats['tagihan_jumlah'] }} tagihan</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-3">Penjualan Air Harian</h3>
            <canvas id="chartPenjualanAir" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-3">Arus Kas 7 Hari</h3>
            <canvas id="chartArusKas" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-3">Stok Produk</h3>
            <canvas id="chartStok" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-3">Ringkasan Kas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-slate-600">Nama Kas</th>
                            <th class="px-3 py-2 text-left text-slate-600">Uraian</th>
                            <th class="px-3 py-2 text-right text-slate-600">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kas as $k)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-2 font-medium">{{ $k['nama'] }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $k['uraian'] }}</td>
                                <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($k['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-3">Penjualan per Produk 30 hari</h3>
            <canvas id="chartPenjualanProduk" height="200"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const pa = @json($penjualanAir);
    new Chart(document.getElementById('chartPenjualanAir'), {
        type: 'line',
        data: { labels: pa.labels, datasets: [{ label: 'Omset', data: pa.data, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.1)', tension: .3, fill: true }] },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    const ak = @json($arusKas);
    new Chart(document.getElementById('chartArusKas'), {
        type: 'bar',
        data: { labels: ak.labels, datasets: [
            { label: 'Masuk', data: ak.masuk, backgroundColor: '#10b981' },
            { label: 'Keluar', data: ak.keluar, backgroundColor: '#f43f5e' }
        ] },
        options: { responsive: true }
    });

    const sp = @json($stokProduk);
    new Chart(document.getElementById('chartStok'), {
        type: 'bar',
        data: { labels: sp.labels, datasets: [{ label: 'Stok', data: sp.data, backgroundColor: '#6366f1' }] },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    const pp = @json($penjualanProduk);
    new Chart(document.getElementById('chartPenjualanProduk'), {
        type: 'doughnut',
        data: { labels: pp.labels, datasets: [{ data: pp.data, backgroundColor: ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6'] }] },
        options: { responsive: true }
    });
</script>
@endpush
