<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Portal Foodcourt</h2>
            <a href="{{ route('menus.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Kelola Menu</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Tambah Transaksi</h3>
                <form method="POST" action="{{ route('foodcourt.store') }}" id="trxForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="datetime-local" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Metode</label>
                            <select name="metode" class="mt-1 w-full rounded-md border-gray-300">
                                <option>Tunai</option>
                                <option>Transfer</option>
                                <option>QRIS</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                        <div id="itemsContainer" class="space-y-2">
                            <div class="flex gap-2 item-row">
                                <select name="items[0][menu_id]" class="flex-1 rounded-md border-gray-300" required>
                                    <option value="">-- Pilih Menu --</option>
                                    @foreach ($menus as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama }} — Rp {{ number_format($m->harga,0,',','.') }}</option>
                                    @endforeach
                                </select>
                                <input type="number" min="1" name="items[0][qty]" value="1" class="w-24 rounded-md border-gray-300" required>
                            </div>
                        </div>
                        <button type="button" onclick="addItem()" class="mt-2 text-sm text-blue-600">+ Tambah Item</button>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Simpan Transaksi</button>
                </form>
            </div>

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
                                <td class="px-3 py-2 font-semibold">Rp {{ number_format($t->total,0,',','.') }}</td>
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
            </div>
            <div class="mt-4">{{ $transaksis->links() }}</div>
        </div>
    </div>

    <script>
        let idx = 1;
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('div');
            row.className = 'flex gap-2 item-row';
            row.innerHTML = `
                <select name="items[${idx}][menu_id]" class="flex-1 rounded-md border-gray-300" required>
                    <option value="">-- Pilih Menu --</option>
                    @foreach ($menus as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }} — Rp {{ number_format($m->harga,0,',','.') }}</option>
                    @endforeach
                </select>
                <input type="number" min="1" name="items[${idx}][qty]" value="1" class="w-24 rounded-md border-gray-300" required>
                <button type="button" onclick="this.parentElement.remove()" class="px-2 text-red-600">✕</button>
            `;
            container.appendChild(row);
            idx++;
        }
    </script>
</x-app-layout>