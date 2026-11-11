<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Portal QMAS — Stok Harian</h2>
            <a href="{{ route('qmas.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah Data</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-slate-800 mb-2">Impor Kartu Kontrol (Stok Harian)</h3>
                <form method="POST" action="{{ route('qmas.importKartuKontrol') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-sm text-slate-600">Tahun Default</label>
                            <input type="number" name="tahun_default" value="2026" required class="w-full mt-1 rounded-lg border-slate-200">
                        </div>
                        <div>
                            <label class="text-sm text-slate-600">Berkas TSV/CSV/TXT</label>
                            <input type="file" name="file" accept=".csv,.tsv,.txt" class="block mt-1 text-sm">
                        </div>
                        <div>
                            <label class="text-sm text-slate-600">Link Google Sheet (opsional)</label>
                            <input type="text" name="link" placeholder="https://docs.google.com/spreadsheets/d/.../edit#gid=0" class="w-full mt-1 rounded-lg border-slate-200">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">..atau tempel isi sheet di sini</label>
                        <textarea name="teks" rows="6" placeholder="Tempel baris dari Google Sheets / Excel (tab-separated)" class="w-full mt-1 rounded-lg border-slate-200"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">Baca &amp; Simpan</button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">Masuk GL19L</th>
                            <th class="px-3 py-2 text-left">Keluar GL19L</th>
                            <th class="px-3 py-2 text-left">Sisa GL19L</th>
                            <th class="px-3 py-2 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $e)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $e->tanggal->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">{{ $e->stok_masuk_gl_19l }}</td>
                                <td class="px-3 py-2">{{ $e->stok_keluar_gl_19l }}</td>
                                <td class="px-3 py-2 font-semibold">{{ $e->sisa_stok_gl_19l }}</td>
                                <td class="px-3 py-2">{{ $e->keterangan }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $entries->links() }}</div>
        </div>
    </div>
</x-app-layout>