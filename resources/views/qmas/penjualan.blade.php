@extends('layouts.qmas')

@section('title', 'Data Penjualan — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Data Penjualan</h2>
            <p class="text-sm text-slate-500">Kelola transaksi penjualan produk</p>
        </div>
        <button @click="addOpen=true" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">+ Transaksi</button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div x-data="{ addOpen: false }" class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-slate-600">TANGGAL</th>
                            <th class="px-3 py-2 text-left text-slate-600">PELANGGAN / ITEM</th>
                            <th class="px-3 py-2 text-left text-slate-600">TOTAL</th>
                            <th class="px-3 py-2 text-left text-slate-600">STATUS</th>
                            <th class="px-3 py-2 text-left text-slate-600">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualan as $p)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-2 align-top">
                                    <div class="text-xs text-slate-400">#{{ $p->id }}</div>
                                    <div class="text-slate-700">{{ $p->tanggal->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <div class="font-semibold text-slate-800">{{ $p->pelanggan }}</div>
                                    <div class="text-slate-600">{{ $p->item_pesanan }}</div>
                                    <div class="text-xs text-slate-400 mt-1">
                                        {{ $p->metode_pembayaran }}
                                        @if ($p->jatuh_tempo) • tempo {{ $p->jatuh_tempo->format('Y-m-d') }} @endif
                                        @if ($p->no_hp) • HP {{ $p->no_hp }} <a href="https://wa.me/{{ $p->no_hp }}" target="_blank" class="text-green-600">[WA ↗]</a> @endif
                                        • 📍 {{ $p->kategori_lokasi }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 align-top font-semibold text-slate-800">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 align-top">
                                    @if ($p->status_pembayaran === 'Lunas')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Lunas</span>
                                    @elseif ($p->status_pembayaran === 'Menunggu Pembayaran')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Menunggu Pembayaran</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <div class="flex items-center gap-2">
                                        @if ($p->status_pembayaran === 'Menunggu Pembayaran' && $p->payment_url)
                                            <a href="{{ $p->payment_url }}" target="_blank" class="px-3 py-1 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lanjutkan Bayar</a>
                                        @endif
                                        @if ($p->status_pembayaran !== 'Lunas')
                                            <form method="POST" action="{{ route('qmas.penjualan.update', $p) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status_pembayaran" value="Lunas">
                                                <button type="submit" class="px-3 py-1 text-xs font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Lunaskan</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('qmas.penjualan.destroy', $p) }}" onsubmit="return confirm('Hapus transaksi ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-6 text-center text-slate-500">Belum ada data penjualan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Tambah Transaksi --}}
        <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.away="addOpen=false" class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="font-semibold text-slate-800 mb-4">Tambah Transaksi</h3>
                <form method="POST" action="{{ route('qmas.penjualan.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Pelanggan</label>
                            <input type="text" name="pelanggan" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Item pesanan</label>
                        <input type="text" name="item_pesanan" placeholder="QMAS 600 ML x2" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Metode pembayaran</label>
                            <select name="metode_pembayaran" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                <option value="Tunai">Tunai</option>
                                <option value="Tagihan">Tagihan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Jatuh tempo</label>
                            <input type="date" name="jatuh_tempo" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">No HP</label>
                            <input type="text" name="no_hp" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Kategori lokasi</label>
                            <input type="text" name="kategori_lokasi" value="Food Court" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Total harga</label>
                            <input type="number" name="total_harga" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Status</label>
                            <select name="status_pembayaran" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                <option value="Lunas">Lunas</option>
                                <option value="Belum Bayar">Belum Bayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-5">
                        <button type="button" @click="addOpen=false" class="px-4 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
