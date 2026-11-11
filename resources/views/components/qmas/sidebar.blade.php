<div class="flex flex-col h-full">
    <div class="px-5 py-5 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center font-bold">Q</div>
            <div>
                <h1 class="text-lg font-bold leading-tight">Qmas BUMK UMGO</h1>
                <p class="text-xs text-slate-400">Portal Manajemen</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
        @php
            $items = [
                ['Beranda', 'fa-house', route('qmas.dashboard')],
                ['Kasir Penjualan', 'fa-cash-register', route('qmas.kasir')],
                ['Produk', 'fa-box', route('qmas.produk.index')],
                ['Penjualan', 'fa-receipt', route('qmas.penjualan.index')],
                ['Pembelian', 'fa-cart-shopping', route('qmas.pembelian.index')],
                ['Kas & Bank', 'fa-building-columns', route('qmas.kasBank.index')],
                ['Laporan', 'fa-chart-line', route('qmas.laporan')],
                ['Impor Riwayat', 'fa-file-import', route('qmas.imporRiwayat')],
                ['Stok', 'fa-warehouse', route('qmas.stok.index')],
                ['Kontak', 'fa-address-book', route('qmas.kontak')],
                ['Aset Tetap', 'fa-building', route('qmas.asetTetap')],
                ['Marketplace', 'fa-store', route('qmas.marketplace')],
            ];
        @endphp
        @foreach ($items as [$label, $icon, $href])
            <a href="{{ $href }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 transition {{ request()->url() === $href ? 'bg-slate-800' : '' }}">
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
