@extends('layouts.qmas')

@section('title', 'Laporan — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Laporan</h2>
        <p class="text-sm text-slate-500">Laporan keuangan & operasional</p>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <form method="GET" action="{{ route('qmas.laporan') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Tampilkan</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 text-sm border border-slate-200 rounded-lg hover:bg-slate-50">Cetak / PDF</button>
        </form>
    </div>

    {{-- Header Info --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Qmas BUMK UMGO</h3>
                <p class="text-sm text-slate-500">Laporan Keuangan & Operasional</p>
            </div>
            <div class="text-right text-sm text-slate-600">
                <div>PERIODE LAPORAN: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</div>
                <div>Dicetak: {{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Total Pembelian</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $countPembelian }} Nota Pembelian</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
            <p class="text-xs text-slate-500 uppercase">Total Penjualan</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $countPenjualan }} Nota Transaksi</p>
        </div>
        <div class="bg-green-50 rounded-xl shadow-sm p-5 border border-green-100">
            <p class="text-xs text-green-700 uppercase">Laba Kotor</p>
            <p class="text-xl font-bold text-green-800 mt-1">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
            <p class="text-xs text-green-600 mt-1">Pendapatan – HPP barang terjual</p>
        </div>
        <div class="bg-green-50 rounded-xl shadow-sm p-5 border border-green-100">
            <p class="text-xs text-green-700 uppercase">Laba Bersih</p>
            <p class="text-xl font-bold text-green-800 mt-1">Rp {{ number_format($labaBersih, 0, ',', '.') }}</p>
            <p class="text-xs text-green-600 mt-1">Setelah HPP donasi Rp 0</p>
        </div>
    </div>

    {{-- Laba Rugi Pendapatan --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <h3 class="font-semibold text-slate-800 mb-3">Laba Rugi — Pendapatan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">Metode Pembayaran</th>
                        <th class="px-3 py-2 text-left text-slate-600">Nota</th>
                        <th class="px-3 py-2 text-left text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($metode as $m => $v)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-700">{{ $m }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $v['nota'] }}</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($v['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-3 py-6 text-center text-slate-500">Tidak ada penjualan</td></tr>
                    @endforelse
                    <tr class="border-t border-slate-200 bg-slate-50 font-semibold">
                        <td class="px-3 py-2">Total Pendapatan</td>
                        <td class="px-3 py-2">{{ $countPenjualan }}</td>
                        <td class="px-3 py-2">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Belanja per Supplier --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100">
        <h3 class="font-semibold text-slate-800 mb-3">Laba Rugi — Belanja per Supplier</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">Nama Supplier</th>
                        <th class="px-3 py-2 text-left text-slate-600">Nota</th>
                        <th class="px-3 py-2 text-left text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($supplier as $s => $v)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-700">{{ $s }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $v['nota'] }}</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($v['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-3 py-6 text-center text-slate-500">Tidak ada pembelian</td></tr>
                    @endforelse
                    <tr class="border-t border-slate-200 bg-slate-50 font-semibold">
                        <td class="px-3 py-2">Total Belanja</td>
                        <td class="px-3 py-2">{{ $countPembelian }}</td>
                        <td class="px-3 py-2">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Penjualan per Produk --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <h3 class="font-semibold text-slate-800 mb-3">Penjualan per Produk</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">Produk</th>
                        <th class="px-3 py-2 text-left text-slate-600">Qty</th>
                        <th class="px-3 py-2 text-left text-slate-600">Modal (HPP)</th>
                        <th class="px-3 py-2 text-left text-slate-600">Harga Jual</th>
                        <th class="px-3 py-2 text-left text-slate-600">Laba BUMK</th>
                        <th class="px-3 py-2 text-left text-slate-600">Ket</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penjualanPerProduk as $row)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-700">{{ $row['produk'] }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $row['qty'] }}</td>
                            <td class="px-3 py-2 text-slate-600">Rp {{ number_format($row['hpp'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-slate-600">Rp {{ number_format($row['harga'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($row['laba'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $row['ket'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Tidak ada penjualan pada periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rincian Nota Lunas --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <h3 class="font-semibold text-slate-800 mb-3">Rincian Nota Transaksi Lunas</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">No. Transaksi</th>
                        <th class="px-3 py-2 text-left text-slate-600">Tanggal</th>
                        <th class="px-3 py-2 text-left text-slate-600">Pelanggan</th>
                        <th class="px-3 py-2 text-left text-slate-600">Detail Barang & Keterangan</th>
                        <th class="px-3 py-2 text-left text-slate-600">Metode</th>
                        <th class="px-3 py-2 text-left text-slate-600">Status</th>
                        <th class="px-3 py-2 text-left text-slate-600">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lunas as $p)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-600">#{{ $p->id }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-slate-700">{{ $p->pelanggan }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $p->item_pesanan }} @if($p->no_hp) • HP {{ $p->no_hp }} @endif • 📍 {{ $p->kategori_lokasi }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $p->metode_pembayaran }}</td>
                            <td class="px-3 py-2 text-green-700">Lunas</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-6 text-center text-slate-500">Tidak ada transaksi lunas pada periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Belum Lunas --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <h3 class="font-semibold text-slate-800 mb-3">Daftar Transaksi Belum Lunas (Piutang Penjualan)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">No. Transaksi</th>
                        <th class="px-3 py-2 text-left text-slate-600">Tgl Transaksi</th>
                        <th class="px-3 py-2 text-left text-slate-600">Pelanggan</th>
                        <th class="px-3 py-2 text-left text-slate-600">Detail Barang</th>
                        <th class="px-3 py-2 text-left text-slate-600">Jatuh Tempo</th>
                        <th class="px-3 py-2 text-left text-slate-600">Status</th>
                        <th class="px-3 py-2 text-left text-slate-600">Tagihan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($belumLunas as $p)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-600">#{{ $p->id }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-slate-700">{{ $p->pelanggan }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $p->item_pesanan }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $p->jatuh_tempo ? \Carbon\Carbon::parse($p->jatuh_tempo)->format('Y-m-d') : '-' }}</td>
                            <td class="px-3 py-2 text-amber-700">Belum Bayar</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-6 text-center text-slate-500">Tidak ada piutang — semua lunas ✅</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sumbangan --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <h3 class="font-semibold text-slate-800 mb-3">Sumbangan (Donasi)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">No. Transaksi</th>
                        <th class="px-3 py-2 text-left text-slate-600">Tanggal</th>
                        <th class="px-3 py-2 text-left text-slate-600">Penerima/Keperluan</th>
                        <th class="px-3 py-2 text-left text-slate-600">Detail Barang</th>
                        <th class="px-3 py-2 text-left text-slate-600">Qty</th>
                        <th class="px-3 py-2 text-left text-slate-600">Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sumbangan as $p)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2 text-slate-600">#{{ $p->id }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-slate-700">{{ $p->pelanggan }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $p->item_pesanan }}</td>
                            <td class="px-3 py-2 text-slate-600">-</td>
                            <td class="px-3 py-2 font-semibold text-slate-800">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Tidak ada sumbangan pada periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Grafik Harian --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 mb-6">
        <h3 class="font-semibold text-slate-800 mb-3">Grafik Harian (layar saja)</h3>
        <canvas id="chartHarian" height="120"></canvas>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const gh = @json($grafik);
        new Chart(document.getElementById('chartHarian'), {
            type: 'bar',
            data: {
                labels: gh.map(x => x.tanggal),
                datasets: [{ label: 'Penjualan', data: gh.map(x => x.total), backgroundColor: '#2563eb' }]
            },
            options: { responsive: true }
        });
    </script>
    @endpush

@endsection
