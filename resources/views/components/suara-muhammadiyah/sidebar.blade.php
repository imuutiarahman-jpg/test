<div class="flex flex-col h-full">
    <div class="px-5 py-5 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-500 flex items-center justify-center font-bold">S</div>
            <div>
                <h1 class="text-lg font-bold leading-tight">Suara Muhammadiyah</h1>
                <p class="text-xs text-slate-400">Portal Manajemen</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
        @php
            $items = [
                ['Dashboard', 'fa-house', 'sm.dashboard'],
                ['Kasir', 'fa-cash-register', 'sm.kasir'],
                ['Katalog Produk', 'fa-boxes-stacked', 'sm.katalog'],
                ['Kelola Stok', 'fa-warehouse', 'sm.stok'],
                ['Laporan & Inventori', 'fa-chart-line', 'sm.laporan'],
                ['Scan + Stok', 'fa-qrcode', 'sm.scan'],
            ];
        @endphp
        @foreach ($items as [$label, $icon, $route])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs($route) ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <i class="fa-solid {{ $icon }} w-5 text-center text-slate-300"></i>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-4 py-4 border-t border-slate-800">
        <div class="bg-slate-800 rounded-lg p-3 text-xs">
            <div class="font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-red-300 hover:text-red-200">Keluar</button>
            </form>
        </div>
    </div>
</div>
