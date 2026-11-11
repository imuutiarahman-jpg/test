@extends('layouts.suara-muhammadiyah')

@section('title', 'Suara Muhammadiyah')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100">
        <h3 class="text-lg font-semibold text-slate-800 mb-2">Suara Muhammadiyah Corner</h3>
        <p class="text-sm text-slate-500">Gunakan menu di samping untuk mengakses katalog, kasir, stok, laporan, dan scan produk.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('sm.dashboard') }}" class="px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Buka Dashboard</a>
            <a href="{{ route('sm.katalog') }}" class="px-4 py-2 text-sm font-semibold border border-slate-200 rounded-lg hover:bg-slate-50">Katalog Produk</a>
        </div>
    </div>
@endsection
