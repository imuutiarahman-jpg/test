<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 bg-gradient-to-br from-indigo-900 via-blue-800 to-sky-600">
            <div class="mb-6 text-center">
                <a href="/" class="inline-flex items-center gap-3">
                    <span class="text-5xl">💧</span>
                    <span class="text-3xl font-bold text-white tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <p class="mt-2 text-blue-100 text-sm">Sistem manajemen inventaris & transaksi terpadu</p>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white/95 backdrop-blur shadow-2xl overflow-hidden sm:rounded-2xl border border-white/20">
                {{ $slot }}
            </div>

            <p class="mt-6 text-blue-100/80 text-xs">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </body>
</html>