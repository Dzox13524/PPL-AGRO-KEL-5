<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="Hidara Smart Farm - Operasional Karyawan & Monitoring Hidroponik">
    <title>@yield('title', 'Operasional Hidara') - Hidara Smart Farm</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden font-sans antialiased text-gray-900 bg-[#f8fafc]">
    <div class="flex h-screen overflow-hidden relative">
        {{-- Mobile Backdrop --}}
        <div id="sidebarBackdrop"
             class="fixed inset-0 bg-black/40 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity duration-300 opacity-0 cursor-pointer"
             aria-hidden="true"></div>

        {{-- Sidebar Component Karyawan --}}
        @include('layouts.sidebar-karyawan', ['active' => $active ?? 'tanaman'])

        {{-- Main Area with Top Header on Mobile --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">
            {{-- Mobile Top Bar --}}
            <header class="lg:hidden bg-white/95 backdrop-blur border-b border-gray-100 px-4 py-3 flex items-center justify-between shrink-0 z-20">
                <div class="flex items-center gap-3">
                    <button type="button"
                            id="sidebarToggleBtn"
                            class="p-2 -ml-1 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition cursor-pointer active:scale-95"
                            aria-label="Buka navigasi menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-[17px] font-bold text-gray-900 tracking-tight leading-none">Hidara</span>
                        <span class="text-[9px] font-bold tracking-widest text-[#147a46] uppercase px-1.5 py-0.5 bg-emerald-50 rounded">SMART FARM</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-[#147a46] bg-[#e8f8f1] px-2.5 py-1 rounded-full capitalize">
                        {{ $active ?? 'karyawan' }}
                    </span>
                </div>
            </header>

            {{-- Main Page Content --}}
            <main class="flex-1 h-full overflow-y-auto px-4 py-5 sm:px-6 md:px-8 lg:px-10 md:py-8 overscroll-contain">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('mainSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            if (backdrop) {
                backdrop.classList.remove('hidden');
                requestAnimationFrame(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                });
            }
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            if (backdrop) {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('translate-x-0')) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024 && sidebar && sidebar.classList.contains('translate-x-0')) {
                closeSidebar();
            }
        });

        if (sidebar) {
            sidebar.querySelectorAll('nav a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
