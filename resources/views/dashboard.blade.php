<x-app-layout>
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
        
        <!-- Header Utama -->
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Selamat datang di BUMK UMGO
            </h1>
            <p class="mt-2 text-sm text-slate-500">Pilih unit usaha untuk mengelola operasional internal</p>
        </div>

        <!-- 3 Kartu Menu Horizontal -->
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Qmas Card -->
            <div onclick="openAdminModal('Qmas', '{{ route('qmas.dashboard') }}', '{{ route('qmas.laporan') }}')" 
                 class="group relative bg-white rounded-2xl p-6 border-2 border-purple-600 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Qmas</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Active
                        </span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Pengelolaan Air Minum Kemasan &amp; Pengiriman Galon
                    </p>
                </div>
                <div>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span>Kelola Qmas</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Foodcourt Card -->
            <div onclick="openAdminModal('Foodcourt', '{{ route('foodcourt.dashboard') }}', '{{ route('foodcourt.laporan') }}')" 
                 class="group relative bg-white rounded-2xl p-6 border-2 border-purple-600 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Foodcourt</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Active
                        </span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Pengelolaan Tenant &amp; Omset Harian
                    </p>
                </div>
                <div>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span>Kelola Foodcourt</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Suara Muhammadiyah Corner Card -->
            <div onclick="openAdminModal('Suara Muhammadiyah Corner', '{{ route('sm.dashboard') }}', '{{ route('sm.laporan') }}')" 
                 class="group relative bg-white rounded-2xl p-6 border-2 border-purple-600 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Suara Muhammadiyah Corner</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            Active
                        </span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        Pengelolaan Inventaris Buku, Majalah, &amp; Merchandise
                    </p>
                </div>
                <div>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span>Kelola SM Corner</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Pop-up Pintas Admin -->
    <div id="adminModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-900">Aksi Admin</h3>
                <button onclick="closeAdminModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="py-6 space-y-3">
                <a id="modalDashboardLink" href="#" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-purple-50 hover:text-purple-700 border border-slate-200 rounded-xl font-medium text-slate-700 transition-colors">
                    <span>Buka Dashboard Utama</span>
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a id="modalReportLink" href="#" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-purple-50 hover:text-purple-700 border border-slate-200 rounded-xl font-medium text-slate-700 transition-colors">
                    <span>Lihat Laporan Penjualan</span>
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
            </div>

            <div class="pt-2 flex justify-end">
                <button onclick="closeAdminModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function openAdminModal(unitName, dashboardUrl, reportUrl) {
            document.getElementById('modalTitle').innerText = 'Kelola ' + unitName;
            document.getElementById('modalDashboardLink').href = dashboardUrl;
            document.getElementById('modalReportLink').href = reportUrl;
            
            const modal = document.getElementById('adminModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAdminModal() {
            const modal = document.getElementById('adminModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>