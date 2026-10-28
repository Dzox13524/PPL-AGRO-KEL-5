<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hidara Smart Farm - Sistem Manajemen Stok dan Tanaman Hidroponik">
    <title>@yield('title', 'Manajemen Stok & Tanaman — Hidara')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden font-sans antialiased text-gray-900 bg-[#f8fafc]">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar Component --}}
        @include('layouts.sidebar', ['active' => $active ?? 'stok'])

        {{-- Main Page Content --}}
        <main class="flex-1 h-screen overflow-y-auto px-10 py-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
