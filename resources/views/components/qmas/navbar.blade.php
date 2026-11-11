<div class="flex items-center justify-between px-4 py-3 gap-3">
    <div class="flex items-center gap-2">
        <button @click="sidebarOpen=!sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-slate-100">
            <i class="fa-solid fa-bars"></i>
        </button>
        <nav class="hidden sm:flex items-center gap-1 text-sm">
            <a href="{{ route('qmas.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('qmas.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100' }}">Dasbor</a>
            <a href="{{ route('qmas.penjualan.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Transaksi</a>
            <a href="{{ route('qmas.kontak') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Kontak</a>
        </nav>
    </div>

    <div class="flex-1 max-w-md">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Pencarian cepat..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('qmas.create') }}" class="px-3 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Transaksi</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-3 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-100">Keluar</button>
        </form>
    </div>
</div>
