<div class="flex items-center justify-between px-4 py-3 gap-3">
    <div class="flex items-center gap-2">
        <button @click="sidebarOpen=!sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-slate-100">
            <i class="fa-solid fa-bars"></i>
        </button>
        <nav class="hidden sm:flex items-center gap-1 text-sm">
            <a href="{{ route('sm.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('sm.dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100' }}">Dasbor</a>
            <a href="{{ route('sm.katalog') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('sm.katalog') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100' }}">Katalog</a>
            <a href="{{ route('sm.kasir') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('sm.kasir') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100' }}">Kasir</a>
        </nav>
    </div>

    <div class="flex-1 max-w-md">
        <form method="GET" action="{{ route('sm.katalog') }}" class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk atau kode barang..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </form>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('sm.kasir') }}" class="px-3 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <i class="fa-solid fa-cash-register mr-1"></i> Kasir
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-3 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-100">Keluar</button>
        </form>
    </div>
</div>
