<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Data Stok QMAS</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('qmas.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="mt-1 w-full rounded-md border-gray-300" required>
                    </div>

                    <h3 class="font-semibold text-gray-800 mt-4">Stok Masuk</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach (['gl_19l'=>'GL 19L','gelas_220ml'=>'Gelas 220ml','botol_330ml'=>'Botol 330ml','botol_600ml'=>'Botol 600ml','botol_15l'=>'Botol 1.5L'] as $k=>$l)
                            <div>
                                <label class="block text-xs text-gray-600">{{ $l }}</label>
                                <input type="number" min="0" name="stok_masuk_{{ $k }}" value="{{ old('stok_masuk_'.$k,0) }}" class="mt-1 w-full rounded-md border-gray-300">
                            </div>
                        @endforeach
                    </div>

                    <h3 class="font-semibold text-gray-800 mt-4">Stok Keluar</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach (['gl_19l'=>'GL 19L','gelas_220ml'=>'Gelas 220ml','botol_330ml'=>'Botol 330ml','botol_600ml'=>'Botol 600ml','botol_15l'=>'Botol 1.5L'] as $k=>$l)
                            <div>
                                <label class="block text-xs text-gray-600">{{ $l }}</label>
                                <input type="number" min="0" name="stok_keluar_{{ $k }}" value="{{ old('stok_keluar_'.$k,0) }}" class="mt-1 w-full rounded-md border-gray-300">
                            </div>
                        @endforeach
                    </div>

                    <h3 class="font-semibold text-gray-800 mt-4">Sisa Stok</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach (['gl_19l'=>'GL 19L','gelas_220ml'=>'Gelas 220ml','botol_330ml'=>'Botol 330ml','botol_600ml'=>'Botol 600ml','botol_15l'=>'Botol 1.5L'] as $k=>$l)
                            <div>
                                <label class="block text-xs text-gray-600">{{ $l }}</label>
                                <input type="number" min="0" name="sisa_stok_{{ $k }}" value="{{ old('sisa_stok_'.$k,0) }}" class="mt-1 w-full rounded-md border-gray-300">
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea name="keterangan" class="mt-1 w-full rounded-md border-gray-300" rows="3">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                        <a href="{{ route('qmas.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>