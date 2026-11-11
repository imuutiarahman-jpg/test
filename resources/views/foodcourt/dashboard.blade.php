<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Foodcourt</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                    <p class="text-xs text-slate-500 uppercase">Omset Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['omset_hari_ini'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                    <p class="text-xs text-slate-500 uppercase">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['trx_hari_ini'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                    <p class="text-xs text-slate-500 uppercase">Menu Aktif</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['menu_aktif'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                    <p class="text-xs text-slate-500 uppercase">Omset 30 Hari</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['omset_30_hari'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                    <h3 class="font-semibold text-slate-800 mb-3">Penjualan 7 Hari</h3>
                    <canvas id="chartPenjualan" height="200"></canvas>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
                    <h3 class="font-semibold text-slate-800 mb-3">Menu Terlaris 30 Hari</h3>
                    <canvas id="chartMenu" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ph = @json($penjualanHarian);
        new Chart(document.getElementById('chartPenjualan'), {
            type: 'bar',
            data: { labels: ph.labels, datasets: [{ label: 'Omset', data: ph.data, backgroundColor: '#f97316' }] },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        const mt = @json($menuTerlaris);
        new Chart(document.getElementById('chartMenu'), {
            type: 'doughnut',
            data: { labels: mt.labels, datasets: [{ data: mt.data, backgroundColor: ['#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5'] }] },
            options: { responsive: true }
        });
    </script>
</x-app-layout>
