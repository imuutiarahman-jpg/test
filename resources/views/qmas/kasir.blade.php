@extends('layouts.qmas')

@section('title', 'Kasir Penjualan — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Kasir Penjualan</h2>
        <p class="text-sm text-slate-500">Proses transaksi penjualan cepat</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div x-data="{
        cart: [],
        add(p) {
            const found = this.cart.find(i => i.id === p.id);
            if (found) { if (found.qty < p.stok) found.qty++; }
            else { this.cart.push({ id: p.id, nama: p.nama, harga: p.harga, stok: p.stok, qty: 1 }); }
        },
        remove(i) { this.cart.splice(i, 1); },
        total() { return this.cart.reduce((s, i) => s + i.harga * i.qty, 0); }
    }" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Produk --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Produk</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($produk as $p)
                    <button type="button"
                        @click="add({ id: {{ $p->id }}, nama: '{{ addslashes($p->nama_produk) }}', harga: {{ $p->harga }}, stok: {{ $p->stok }} })"
                        class="text-left p-3 border border-slate-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition">
                        <div class="font-medium text-slate-800 text-sm">{{ $p->nama_produk }}</div>
                        <div class="text-xs text-slate-500">{{ $p->kategori }}</div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-600">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-400">stok {{ $p->stok }}</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Cart --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Keranjang</h3>
            <div class="space-y-2 mb-4">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-center justify-between gap-2 p-2 bg-slate-50 rounded-lg">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-slate-800" x-text="item.nama"></div>
                            <div class="text-xs text-slate-500">Rp <span x-text="item.harga.toLocaleString('id-ID')"></span></div>
                        </div>
                        <input type="number" min="1" :max="item.stok" x-model.number="item.qty" class="w-14 px-2 py-1 text-sm border border-slate-200 rounded">
                        <button type="button" @click="remove(index)" class="text-red-500 text-sm">×</button>
                    </div>
                </template>
                <div x-show="cart.length === 0" class="text-sm text-slate-400 text-center py-4">Keranjang kosong</div>
            </div>

            <div class="border-t border-slate-100 pt-3 mb-4">
                <div class="flex items-center justify-between font-semibold text-slate-800">
                    <span>Total</span>
                    <span>Rp <span x-text="total().toLocaleString('id-ID')"></span></span>
                </div>
            </div>

            <form method="POST" action="{{ route('qmas.kasir.store') }}" @submit="if(cart.length===0){alert('Keranjang kosong'); $event.preventDefault();}">
                @csrf
                <div class="space-y-2 mb-3">
                    <input type="text" name="pelanggan" placeholder="Nama pelanggan (opsional)" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <select name="metode_pembayaran" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="Tunai">Tunai</option>
                        <option value="Tagihan">Tagihan</option>
                        <option value="Midtrans">Midtrans (Semua Metode)</option>
                    </select>
                </div>
                <template x-for="(item, index) in cart" :key="'h'+index">
                    <div>
                        <input type="hidden" :name="'items['+index+'][produk_id]'" :value="item.id">
                        <input type="hidden" :name="'items['+index+'][qty]'" :value="item.qty">
                    </div>
                </template>
                <button type="submit" class="w-full px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Bayar</button>
            </form>
        </div>
    </div>
@endsection
