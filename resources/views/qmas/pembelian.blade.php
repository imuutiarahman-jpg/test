@extends('layouts.qmas')

@section('title', 'Data Pembelian — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Data Pembelian</h2>
        <p class="text-sm text-slate-500">Catat pembelian stok dari supplier</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div x-data="{
        items: [{ produk_id: '', harga: 0, qty: 1 }],
        ongkir: 0,
        addItem() { this.items.push({ produk_id: '', harga: 0, qty: 1 }); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        subtotal(i) { return (Number(this.items[i].harga) || 0) * (Number(this.items[i].qty) || 0); },
        totalItems() { return this.items.reduce((s, it) => s + (Number(it.harga)||0)*(Number(it.qty)||0), 0); },
        grandTotal() { return this.totalItems() + (Number(this.ongkir)||0); }
    }" class="space-y-4">
        {{-- Form Pembelian Baru --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Pembelian Baru — banyak item + ongkir, stok otomatis +</h3>
            <form method="POST" action="{{ route('qmas.pembelian.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Nama supplier</label>
                        <input type="text" name="nama_supplier" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Keterangan (mis. no. DO)</label>
                        <input type="text" name="keterangan" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Tgl beli</label>
                        <input type="date" name="tgl_beli" required value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Jatuh tempo</label>
                        <input type="date" name="jatuh_tempo" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Masuk ke lokasi</label>
                        <select name="lokasi_penyimpanan" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            <option value="Daqam">Daqam</option>
                            <option value="Kantor">Kantor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Status pembayaran</label>
                        <select name="status_pembayaran" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            <option value="Belum Bayar">Belum Bayar</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Akun (jika lunas)</label>
                        <select name="akun_pembayaran" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            <option value="">— pilih akun —</option>
                            <option value="Kas Tunai">Kas Tunai</option>
                            <option value="Bank BRI">Bank BRI</option>
                        </select>
                    </div>
                </div>

                {{-- Items --}}
                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-wrap items-end gap-2 p-3 bg-slate-50 rounded-lg">
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs text-slate-500 mb-1">Produk</label>
                                <select :name="'produk_id['+index+']'" x-model="item.produk_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                    <option value="">Pilih produk</option>
                                    @foreach ($produk as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nama_produk }} (jual Rp {{ number_format($pr->harga,0,',','.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="min-w-[120px]">
                                <label class="block text-xs text-slate-500 mb-1">Harga beli</label>
                                <input type="number" :name="'harga_beli_satuan['+index+']'" x-model="item.harga" required min="0" placeholder="cth. 60000" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            </div>
                            <div class="min-w-[80px]">
                                <label class="block text-xs text-slate-500 mb-1">Qty</label>
                                <input type="number" :name="'qty['+index+']'" x-model="item.qty" required min="1" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                            </div>
                            <div class="min-w-[120px]">
                                <label class="block text-xs text-slate-500 mb-1">Subtotal</label>
                                <input type="text" readonly :value="'Rp ' + subtotal(index).toLocaleString('id-ID')" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-100">
                            </div>
                            <button type="button" @click="removeItem(index)" class="px-3 py-2 text-sm border border-red-200 text-red-600 rounded-lg hover:bg-red-50">×</button>
                        </div>
                    </template>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <button type="button" @click="addItem()" class="px-4 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-50">+ Tambah Item</button>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-slate-600">Ongkir Rp</label>
                            <input type="number" name="ongkir" x-model="ongkir" min="0" value="0" class="w-28 px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        </div>
                        <div class="text-sm font-semibold text-slate-800">Total: Rp <span x-text="grandTotal().toLocaleString('id-ID')"></span></div>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Simpan Pembelian</button>
                    </div>
                </div>
            </form>
        </div>


        {{-- Impor Stok Masuk (hanya tanggal + jumlah masuk) --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-3">Impor Stok Masuk (hanya tanggal + jumlah masuk)</h3>
            <p class="text-xs text-slate-500 mb-4">Format: file CSV/TSV/TXT, tempel teks, atau link Google Sheet. Satu baris data = tanggal + masuk per produk. Harga default 0, supplier/lokasi default, stok tidak berubah.</p>
            <form method="POST" action="{{ route('qmas.pembelian.importStokMasuk') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Tahun Default</label>
                        <input type="number" name="tahun_default" value="2026" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Berkas CSV/TSV/TXT</label>
                        <input type="file" name="file" accept=".csv,.tsv,.txt" class="block text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Link Google Sheet (opsional)</label>
                        <input type="text" name="link" placeholder="https://docs.google.com/spreadsheets/d/.../edit#gid=0" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">..atau tempel isi sheet di sini</label>
                    <textarea name="teks" rows="6" placeholder="Tempel baris dari Google Sheets / Excel" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Baca & Simpan</button>
            </form>
        </div>


        {{-- Filter Periode --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <form method="GET" action="{{ route('qmas.pembelian.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Dari</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Sampai</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Tampilkan</button>
            </form>
        </div>

        {{-- Riwayat --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-slate-800">Data Pembelian</h3>
                <form method="GET" class="flex items-center gap-2">
                    <input type="text" placeholder="Cari invoice / supplier..." class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <button type="button" class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg">Cari</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-slate-600">INVOICE</th>
                            <th class="px-3 py-2 text-left text-slate-600">SUPPLIER / ITEM</th>
                            <th class="px-3 py-2 text-left text-slate-600">ONGKIR</th>
                            <th class="px-3 py-2 text-left text-slate-600">TOTAL</th>
                            <th class="px-3 py-2 text-left text-slate-600">STATUS</th>
                            <th class="px-3 py-2 text-left text-slate-600">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembelian as $pb)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-2 align-top">
                                    <div class="font-medium text-slate-800">{{ $pb->no_invoice }}</div>
                                    <div class="text-xs text-slate-400">Beli: {{ $pb->tgl_beli->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <div class="font-semibold text-slate-800">{{ $pb->nama_supplier }} <span class="text-xs text-slate-400">📍 {{ $pb->lokasi_penyimpanan }}</span></div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        @foreach ($pb->details as $d)
                                            {{ $d->produk->nama_produk ?? 'Produk' }} x{{ $d->qty }} @Rp {{ number_format($d->harga_beli_satuan,0,',','.') }}@if(!$loop->last), @endif
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1">Terbayar Rp {{ number_format($pb->status_pembayaran === 'Lunas' ? $pb->total_biaya : 0, 0, ',', '.') }} • Sisa Rp {{ number_format($pb->status_pembayaran === 'Lunas' ? 0 : $pb->total_biaya, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-3 py-2 align-top text-slate-600">Rp {{ number_format($pb->ongkir, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 align-top font-semibold text-slate-800">Rp {{ number_format($pb->total_biaya, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 align-top">
                                    @if ($pb->status_pembayaran === 'Lunas')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Lunas</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <form method="POST" action="{{ route('qmas.pembelian.destroy', $pb) }}" onsubmit="return confirm('Hapus pembelian ini? Stok akan dikurangi.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-xs border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Belum ada data pembelian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
