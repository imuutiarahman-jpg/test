<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Qmas BUMK UMGO')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white transform transition-transform duration-200 -translate-x-full md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            @include('components.qmas.sidebar')
        </aside>

        <div class="md:ml-64 min-h-screen flex flex-col">
            <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
                @include('components.qmas.navbar')
            </header>

            <main class="flex-1 p-4 md:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        <div x-show="sidebarOpen" @click="sidebarOpen=false" class="fixed inset-0 bg-black/40 z-30 md:hidden" x-cloak></div>
    </div>
    @stack('scripts')
</body>
</html>
