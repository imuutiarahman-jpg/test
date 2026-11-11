@extends('layouts.qmas')

@section('title', $title.' — Qmas BUMK UMGO')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">{ $title }</h2>
        <p class="text-sm text-slate-500">Halaman { $title } (dalam pengembangan).</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100">
        <p class="text-slate-500">Konten { $title } akan ditambahkan.</p>
    </div>
@endsection
