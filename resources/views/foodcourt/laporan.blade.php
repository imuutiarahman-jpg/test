<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Penjualan Foodcourt</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dari</label>
                    <input type="date" name="awal" value="{{ $awal }}" class="mt-1 rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sampai</label>
                    <input type="date" name="akhir" value="{{ $akhir }}" class="mt-1 rounded-md border-gray-300">
                </div>
                <button class="px-4 py-2 bg-orange-600 text-white rounded-lg">Filter</button>
                <div class="ml-auto text-sm font-semibold">Total: Rp {{ number_format($total, 0, ',', '.') }}</div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">Total</th>
                            <th class="px-3 py-2 text-left">Metode</th>
                            <th class="px-3 py-2 text-left">Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $t)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $t->tanggal->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2 font-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">{{ $t->metode }}</td>
                                <td class="px-3 py-2">
                                    @foreach ($t->items as $it)
                                        {{ $it->menu->nama ?? '-' }} x{{ $it->qty }}@if(!$loop->last), @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $transaksis->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
