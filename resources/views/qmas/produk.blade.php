@extends('layouts.qmas')

@section('title', 'Data Produk — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Data Produk</h2>
        <p class="text-sm text-slate-500">Kelola daftar produk Qmas BUMK UMGO</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div x-data="{ editOpen: false, editId: null, editNama: '', editKategori: '', editHarga: 0, editStok: 0, editBatas: 0 }" class="space-y-4">
        {{-- Tambah Produk --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Tambah Produk</h3>
            <form method="POST" action="{{ route('qmas.produk.store') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs text-slate-500 mb-1">Nama produk</label>
                    <input type="text" name="nama_produk" placeholder="Nama produk (maks..." required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs text-slate-500 mb-1">Kategori</label>
                    <select name="kategori" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach ($kategoris as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[110px]">
                    <label class="block text-xs text-slate-500 mb-1">Harga Rp</label>
                    <input type="number" name="harga" placeholder="Harga Rp" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="min-w-[80px]">
                    <label class="block text-xs text-slate-500 mb-1">Stok</label>
                    <input type="number" name="stok" value="0" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="min-w-[90px]">
                    <label class="block text-xs text-slate-500 mb-1">Batas menipis</label>
                    <input type="number" name="batas_menipis" value="10" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Simpan</button>
            </form>
        </div>

        {{-- Daftar Produk --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <h3 class="font-semibold text-slate-800">Daftar Produk</h3>
                <form method="GET" action="{{ route('qmas.produk.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari produk..." class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <select name="kategori" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k }}" {{ $kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
                </form>
            </div>

            <div class="mb-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                Klik angka harga untuk mengubah langsung (Enter = simpan, Esc = batal).
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-slate-600">PRODUK</th>
                            <th class="px-3 py-2 text-left text-slate-600">KATEGORI</th>
                            <th class="px-3 py-2 text-left text-slate-600">HARGA</th>
                            <th class="px-3 py-2 text-left text-slate-600">STOK</th>
                            <th class="px-3 py-2 text-left text-slate-600">STATUS</th>
                            <th class="px-3 py-2 text-left text-slate-600">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produk as $p)
                            @php
                                $status = $p->stok > $p->batas_menipis ? 'Aman' : 'Menipis';
                                $statusClass = $status === 'Aman' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                            @endphp
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-2">
                                    <div class="font-medium text-slate-800">{{ $p->nama_produk }}</div>
                                    <div class="text-xs text-slate-400">#{{ $p->id }}</div>
                                </td>
                                <td class="px-3 py-2 text-slate-600">{{ $p->kategori }}</td>
                                <td class="px-3 py-2">
                                    <span class="price-cell cursor-pointer underline decoration-dashed decoration-slate-400" data-id="{{ $p->id }}" data-price="{{ $p->harga }}">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-3 py-2 text-slate-600">{{ $p->stok }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $status }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="px-3 py-1 text-xs border border-slate-200 rounded-lg hover:bg-slate-50">Riwayat</button>
                                        <button type="button"
                                            @click="editOpen=true; editId={{ $p->id }}; editNama='{{ addslashes($p->nama_produk) }}'; editKategori='{{ addslashes($p->kategori) }}'; editHarga={{ $p->harga }}; editStok={{ $p->stok }}; editBatas={{ $p->batas_menipis }}"
                                            class="px-3 py-1 text-xs border border-slate-200 rounded-lg hover:bg-slate-50">Edit</button>
                                        <form method="POST" action="{{ route('qmas.produk.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Belum ada data produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.away="editOpen=false" class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Edit Produk</h3>
                <form method="POST" :action="'/qmas/produk/' + editId">
                    @csrf
                    @method('PUT')
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Nama produk</label>
                            <input type="text" name="nama_produk" x-model="editNama" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Kategori</label>
                            <select name="kategori" x-model="editKategori" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                @foreach ($kategoris as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Harga</label>
                                <input type="number" name="harga" x-model="editHarga" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Stok</label>
                                <input type="number" name="stok" x-model="editStok" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Batas menipis</label>
                                <input type="number" name="batas_menipis" x-model="editBatas" required min="0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-5">
                        <button type="button" @click="editOpen=false" class="px-4 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.price-cell').forEach(cell => {
        cell.addEventListener('click', () => {
            if (cell.querySelector('input')) return;
            const id = cell.dataset.id;
            const current = cell.dataset.price;
            const input = document.createElement('input');
            input.type = 'number';
            input.value = current;
            input.className = 'w-24 px-2 py-1 text-sm border border-blue-400 rounded';
            cell.textContent = '';
            cell.appendChild(input);
            input.focus();

            const cancel = () => { cell.textContent = 'Rp ' + Number(current).toLocaleString('id-ID'); };
            const save = async () => {
                const val = input.value;
                try {
                    const res = await fetch('/qmas/produk/' + id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ harga: val })
                    });
                    if (res.ok) {
                        cell.dataset.price = val;
                        cell.textContent = 'Rp ' + Number(val).toLocaleString('id-ID');
                    } else {
                        cancel();
                    }
                } catch (e) { cancel(); }
            };

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); save(); }
                if (e.key === 'Escape') { e.preventDefault(); cancel(); }
            });
            input.addEventListener('blur', save);
        });
    });
</script>
@endpush
