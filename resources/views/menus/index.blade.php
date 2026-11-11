<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Menu Foodcourt</h2>
            <a href="{{ route('menus.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm">+ Tambah Menu</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">Harga</th>
                            <th class="px-3 py-2 text-left">Kategori</th>
                            <th class="px-3 py-2 text-left">Aktif</th>
                            <th class="px-3 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $m)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $m->nama }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($m->harga,0,',','.') }}</td>
                                <td class="px-3 py-2">{{ $m->kategori }}</td>
                                <td class="px-3 py-2">{{ $m->aktif ? 'Ya' : 'Tidak' }}</td>
                                <td class="px-3 py-2 flex gap-2">
                                    <a href="{{ route('menus.edit', $m) }}" class="text-blue-600">Edit</a>
                                    <form method="POST" action="{{ route('menus.destroy', $m) }}" onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada menu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $menus->links() }}</div>
        </div>
    </div>
</x-app-layout>