@extends('layouts.qmas')

@section('title', 'Stok Produk')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Stok Produk</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="font-semibold text-slate-800 mb-3">Stok Opname / Adjust</h2>
            <form method="POST" action="{{ route('qmas.stok.adjust') }}" class="grid grid-cols-2 gap-3">
                @csrf
                <input type="hidden" name="q" value="{{ $search }}">
                <div class="col-span-2">
                    <label class="text-sm text-slate-600">Produk</label>
                    <select name="produk_id" class="w-full mt-1 rounded-lg border-slate-200">
                        @foreach ($entries as $e)
                            <option value="{{ $e->produk_id }}">{{ $e->produk->nama_produk ?? '-' }} (total {{ $e->total() }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-600">Lokasi</label>
                    <select name="lokasi" class="w-full mt-1 rounded-lg border-slate-200">
                        <option value="daqam">Daqam</option>
                        <option value="food_court">Food Court</option>
                        <option value="kantor">Kantor</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-600">Aksi</label>
                    <select name="aksi" class="w-full mt-1 rounded-lg border-slate-200">
                        <option value="tambah">Tambah</option>
                        <option value="kurang">Kurang</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="text-sm text-slate-600">Qty</label>
                    <input type="number" name="qty" min="1" class="w-full mt-1 rounded-lg border-slate-200" required>
                </div>
                <div class="col-span-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">Simpan</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="font-semibold text-slate-800 mb-1">Pindah Stok</h2>
            <p class="text-xs text-slate-500 mb-3">mis. Daqam → Food Court</p>
            <form method="POST" action="{{ route('qmas.stok.transfer') }}" class="grid grid-cols-2 gap-3">
                @csrf
                <input type="hidden" name="q" value="{{ $search }}">
                <div class="col-span-2">
                    <label class="text-sm text-slate-600">Produk Asal</label>
                    <select name="produk_id" class="w-full mt-1 rounded-lg border-slate-200">
                        @foreach ($entries as $e)
                            <option value="{{ $e->produk_id }}">{{ $e->produk->nama_produk ?? '-' }} (total {{ $e->total() }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-600">Lokasi Asal</label>
                    <select name="asal" class="w-full mt-1 rounded-lg border-slate-200">
                        <option value="daqam">Daqam</option>
                        <option value="food_court">Food Court</option>
                        <option value="kantor">Kantor</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-600">Lokasi Tujuan</label>
                    <select name="tujuan" class="w-full mt-1 rounded-lg border-slate-200">
                        <option value="food_court">Food Court</option>
                        <option value="daqam">Daqam</option>
                        <option value="kantor">Kantor</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="text-sm text-slate-600">Qty</label>
                    <input type="number" name="qty" min="1" class="w-full mt-1 rounded-lg border-slate-200" required>
                </div>
                <div class="col-span-2">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Pindah</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('qmas.stok.index') }}" class="flex items-center gap-2 mb-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari stok..." class="flex-1 rounded-lg border-slate-200">
            <button type="submit" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300">Cari</button>
        </form>

        <div class="mb-3 rounded-lg bg-yellow-50 text-yellow-800 px-4 py-2 text-sm">
            💡 Klik angka gudang (Daqam / Food Court / Kantor) untuk mengubah langsung (Enter = simpan, Esc = batal).
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left">PRODUK</th>
                        <th class="px-3 py-2 text-left">TOTAL</th>
                        <th class="px-3 py-2 text-left">TOTAL HARGA</th>
                        <th class="px-3 py-2 text-left">DAQAM</th>
                        <th class="px-3 py-2 text-left">FOOD COURT</th>
                        <th class="px-3 py-2 text-left">KANTOR</th>
                        <th class="px-3 py-2 text-left">JML GDG</th>
                        <th class="px-3 py-2 text-left">KURANG</th>
                        <th class="px-3 py-2 text-left">MIN</th>
                        <th class="px-3 py-2 text-left">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $e)
                        @php
                            $total = $e->total();
                            $kurang = $total < $e->min_stok ? ($e->min_stok - $total) : null;
                        @endphp
                        <tr class="border-t">
                            <td class="px-3 py-2">
                                <div class="font-medium text-slate-800">{{ $e->produk->nama_produk ?? '-' }}</div>
                                <div class="text-xs text-slate-500">@Rp {{ number_format($e->produk->harga ?? 0, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-3 py-2 font-semibold">{{ $total }}</td>
                            <td class="px-3 py-2">Rp {{ number_format($total * ($e->produk->harga ?? 0), 0, ',', '.') }}</td>
                            @foreach (['daqam','food_court','kantor'] as $loc)
                                <td class="px-3 py-2">
                                    <span
                                        x-data="{ editing: false, val: {{ $e->{$loc} }}, original: {{ $e->{$loc} }} }"
                                        @click="editing = true; $nextTick(() => $refs.input.focus())"
                                        class="cursor-pointer"
                                    >
                                        <template x-if="!editing">
                                            <span x-text="val"></span>
                                        </template>
                                        <template x-if="editing">
                                            <input
                                                x-ref="input"
                                                type="number"
                                                min="0"
                                                x-model="val"
                                                @keydown.enter.prevent="
                                                    fetch('{{ route('qmas.stok.inline') }}', {
                                                        method: 'POST',
                                                        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                                        body: JSON.stringify({produk_id: {{ $e->produk_id }}, lokasi: '{{ $loc }}', value: Number(val)})
                                                    }).then(() => { editing=false; original=val; }).catch(() => { val=original; editing=false; });
                                                "
                                                @keydown.escape.prevent="val=original; editing=false"
                                                @blur="val=original; editing=false"
                                                class="w-16 rounded border-slate-300"
                                            >
                                        </template>
                                    </span>
                                </td>
                            @endforeach
                            <td class="px-3 py-2">{{ $total }}</td>
                            <td class="px-3 py-2">{{ $kurang === null ? '—' : $kurang }}</td>
                            <td class="px-3 py-2">{{ $e->min_stok }}</td>
                            <td class="px-3 py-2">
                                @if ($total < $e->min_stok)
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">Restock!</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-3 py-6 text-center text-slate-500">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $entries->links() }}</div>
    </div>
</div>
@endsection
