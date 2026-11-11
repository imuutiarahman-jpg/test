@extends('layouts.suara-muhammadiyah')

@section('title', 'Kasir — Suara Muhammadiyah')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kasir Penjualan</h2>
            <p class="text-sm text-slate-500">Proses transaksi penjualan Suara Muhammadiyah Corner</p>
        </div>
        <form method="GET" action="{{ route('sm.kasir') }}" class="flex items-center gap-2">
            <select name="kategori" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" {{ $kategori === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div x-data="{
        cart: [],
        add(p) {
            const found = this.cart.find(i => i.id === p.id);
            if (found) {
                if (found.qty < p.stok) found.qty++;
            } else {
                this.cart.push({ id: p.id, nama: p.nama, harga: p.harga, stok: p.stok, qty: 1 });
            }
        },
        remove(i) { this.cart.splice(i, 1); },
        total() { return this.cart.reduce((s, i) => s + i.harga * i.qty, 0); },
        totalQty() { return this.cart.reduce((s, i) => s + i.qty, 0); }
    }" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Produk --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Produk Tersedia</h3>
                <span class="text-xs text-slate-400">{{ $products->count() }} produk</span>
            </div>

            @if ($products->isEmpty())
                <div class="py-12 text-center text-slate-500 text-sm">Tidak ada produk pada kategori ini.</div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($products as $p)
                        <button type="button"
                            @click="add({ id: '{{ $p['kode'] }}', nama: '{{ addslashes($p['nama']) }}', harga: {{ $p['harga_min'] }}, stok: {{ $p['stok'] }} })"
                            class="text-left p-3 border border-slate-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 transition">
                            <div class="flex items-start justify-between gap-1">
                                <div class="font-medium text-slate-800 text-sm leading-snug">{{ $p['nama'] }}</div>
                                <i class="fa-solid fa-plus text-indigo-500 text-xs mt-1"></i>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1">{{ $p['kategori'] }} · {{ $p['kode'] }}</div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-sm font-semibold text-indigo-600">Rp {{ number_format($p['harga_min'], 0, ',', '.') }}</span>
                                <span class="text-xs {{ $p['stok'] <= 5 ? 'text-amber-600' : 'text-slate-400' }}">stok {{ $p['stok'] }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Keranjang --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 h-fit">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Keranjang</h3>
                <span class="text-xs text-slate-400"><span x-text="totalQty()"></span> item</span>
            </div>

            <div class="space-y-2 mb-4">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex items-center justify-between gap-2 p-2 bg-slate-50 rounded-lg">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-slate-800" x-text="item.nama"></div>
                            <div class="text-xs text-slate-500">Rp <span x-text="item.harga.toLocaleString('id-ID')"></span></div>
                        </div>
                        <input type="number" min="1" :max="item.stok" x-model.number="item.qty"
                            class="w-14 px-2 py-1 text-sm border border-slate-200 rounded-lg">
                        <button type="button" @click="remove(index)" class="text-rose-500 text-sm" aria-label="Hapus">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </template>
                <div x-show="cart.length === 0" class="text-sm text-slate-400 text-center py-6">
                    <i class="fa-solid fa-cart-shopping text-2xl mb-2 block text-slate-300"></i>
                    Keranjang masih kosong
                </div>
            </div>

            <div class="border-t border-slate-100 pt-3 mb-4">
                <div class="flex items-center justify-between font-semibold text-slate-800">
                    <span>Total</span>
                    <span>Rp <span x-text="total().toLocaleString('id-ID')"></span></span>
                </div>
            </div>

            <form @submit.prevent="if (cart.length === 0) { alert('Keranjang masih kosong'); return; } alert('Pembayaran berhasil disimulasikan. Integrasi pembayaran akan dihubungkan pada tahap berikutnya.'); cart = [];">
                <div class="space-y-2 mb-3">
                    <input type="text" x-model="pelanggan" placeholder="Nama pelanggan (opsional)"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <select x-model="metode" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Tunai">Tunai</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Proses Pembayaran
                </button>
                <p class="text-[11px] text-slate-400 mt-2 text-center">Mode demo — belum terhubung ke database penjualan.</p>
            </form>
        </div>
    </div>
@endsection
