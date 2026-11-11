<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Menu</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('menus.update', $menu) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" value="{{ old('nama', $menu->nama) }}" class="mt-1 w-full rounded-md border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" step="0.01" min="0" name="harga" value="{{ old('harga', $menu->harga) }}" class="mt-1 w-full rounded-md border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori', $menu->kategori) }}" class="mt-1 w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" class="mt-1 w-full rounded-md border-gray-300" rows="3">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="aktif" value="1" {{ old('aktif', $menu->aktif) ? 'checked' : '' }} class="rounded">
                        <label class="text-sm text-gray-700">Aktif</label>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Update</button>
                        <a href="{{ route('menus.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>