@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard')

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">
    <div class="flex items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Selamat Datang</h1>
        </div>
        <div class="shrink-0">
            {{-- Unduh Ringkasan --}}
            <button type="button"
                    class="inline-flex items-center gap-1.5 sm:gap-2 bg-white border border-gray-200 text-gray-700 text-xs font-semibold px-3 py-2 sm:px-4 sm:py-2.5 rounded-lg shadow-xs hover:bg-gray-50 transition active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Unduh Ringkasan</span>
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Estimasi Panen --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Estimasi Panen</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">4.85</span>
                <span class="text-xs text-gray-500">Ton</span>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Siklus 7 Hari Lagi
                </span>
                <span class="text-[11px] text-gray-400">Target 100%</span>
            </div>
        </div>

        {{-- Card 2: Tanaman Aktif --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Tanaman Aktif</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">4.200</span>
                <span class="text-xs text-gray-500">Lubang</span>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-[11px] font-semibold text-emerald-600">4 Bedeng Beroperasi</span>
                <span class="text-[11px] text-gray-400">Kapasitas 100%</span>
            </div>
        </div>

        {{-- Card 3: Ketersediaan Nutrisi --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Ketersediaan Nutrisi</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">185</span>
                <span class="text-xs text-gray-500">Liter AB Mix</span>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Stok Aman
                </span>
                <span class="text-[11px] text-gray-400">Cukup ~32 Hari</span>
            </div>
        </div>

        {{-- Card 4: Kesehatan Tanaman --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Kesehatan Tanaman</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">98.4</span>
                <span class="text-xs text-gray-500">%</span>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-[11px] font-semibold text-emerald-600">Kondisi Prima</span>
                <span class="text-[11px] text-gray-400">Bebas Hama Mayor</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         Grafik & Riwayat Pertumbuhan Tanaman
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-4 sm:p-6">
        {{-- Section Header --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-gray-900">Grafik & Riwayat Pertumbuhan Tanaman</h2>
                <p class="text-xs text-gray-500 mt-0.5">Monitoring biomassa, kenaikan tinggi tajuk, dan log observasi berkala</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Badge --}}
                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[11px] font-semibold px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    +12% Rata-rata Mingguan
                </span>
                {{-- Pilih Bedeng Tabs (Scrollable on small screens) --}}
                <div class="w-full sm:w-auto overflow-x-auto no-scrollbar">
                    <div class="inline-flex items-center bg-gray-50 border border-gray-200 rounded-lg p-0.5 gap-0.5 min-w-max">
                        <button type="button" class="bedeng-tab shrink-0 text-[11px] font-semibold px-3 py-1.5 rounded-md bg-[#137a46] text-white shadow-sm transition cursor-pointer" data-bedeng="a01">Bedeng A-01</button>
                        <button type="button" class="bedeng-tab shrink-0 text-[11px] font-medium px-3 py-1.5 rounded-md text-gray-600 hover:bg-white hover:shadow-xs transition cursor-pointer" data-bedeng="a02">Bedeng A-02</button>
                        <button type="button" class="bedeng-tab shrink-0 text-[11px] font-medium px-3 py-1.5 rounded-md text-gray-600 hover:bg-white hover:shadow-xs transition cursor-pointer" data-bedeng="b01">Bedeng B-01</button>
                        <button type="button" class="bedeng-tab shrink-0 text-[11px] font-medium px-3 py-1.5 rounded-md text-gray-600 hover:bg-white hover:shadow-xs transition cursor-pointer" data-bedeng="b02">Bedeng B-02</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart + Stats Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Chart Area (2/3) --}}
            <div class="lg:col-span-2 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Tren Ketinggian Tajuk Selada Butterhead (cm)
                    </span>
                </div>

                {{-- SVG Chart Container with scroll support on narrow mobile --}}
                <div class="overflow-x-auto no-scrollbar -mx-2 px-2 sm:mx-0 sm:px-0">
                    <div class="relative w-full min-w-[460px] sm:min-w-0" style="height: 240px;">
                        <svg id="growthChart" viewBox="0 0 600 240" class="w-full h-full" preserveAspectRatio="xMidYMid meet">
                            {{-- Grid Lines --}}
                            <defs>
                                <linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#1a9e5f" stop-opacity="0.15"/>
                                    <stop offset="100%" stop-color="#1a9e5f" stop-opacity="0.01"/>
                                </linearGradient>
                            </defs>

                            {{-- Horizontal Grid --}}
                            <line x1="50" y1="30" x2="550" y2="30" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="70" x2="550" y2="70" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="110" x2="550" y2="110" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="150" x2="550" y2="150" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="190" x2="550" y2="190" stroke="#f1f5f9" stroke-width="1"/>

                            {{-- Y-axis Labels --}}
                            <text x="40" y="34" text-anchor="end" class="text-[9px] fill-gray-400">25</text>
                            <text x="40" y="74" text-anchor="end" class="text-[9px] fill-gray-400">20</text>
                            <text x="40" y="114" text-anchor="end" class="text-[9px] fill-gray-400">15</text>
                            <text x="40" y="154" text-anchor="end" class="text-[9px] fill-gray-400">10</text>
                            <text x="40" y="194" text-anchor="end" class="text-[9px] fill-gray-400">5</text>

                            {{-- Standar Siklus (dashed reference line) --}}
                            <line x1="50" y1="55" x2="550" y2="55" stroke="#94a3b8" stroke-width="1" stroke-dasharray="6 4"/>

                        {{-- Area Fill --}}
                        <path d="M 80 185 C 130 178, 160 160, 200 150 C 240 138, 290 110, 330 90 C 370 75, 400 60, 440 50 C 470 42, 510 36, 540 32 L 540 210 L 80 210 Z"
                              fill="url(#chartFill)"/>

                        {{-- Main curve line --}}
                        <path d="M 80 185 C 130 178, 160 160, 200 150 C 240 138, 290 110, 330 90 C 370 75, 400 60, 440 50 C 470 42, 510 36, 540 32"
                              fill="none" stroke="#1a9e5f" stroke-width="2.5" stroke-linecap="round"/>

                        {{-- Data points --}}
                        <circle cx="80" cy="185" r="5" fill="white" stroke="#1a9e5f" stroke-width="2"/>
                        <circle cx="200" cy="150" r="5" fill="white" stroke="#1a9e5f" stroke-width="2"/>
                        <circle cx="330" cy="90" r="5" fill="white" stroke="#1a9e5f" stroke-width="2"/>
                        <circle cx="440" cy="50" r="5" fill="white" stroke="#1a9e5f" stroke-width="2"/>
                        <circle cx="540" cy="32" r="6" fill="#1a9e5f" stroke="white" stroke-width="2.5"/>

                        {{-- X-axis Labels --}}
                        <text x="80" y="225" text-anchor="middle" class="text-[9px] fill-gray-400">Hari 1 (3.2 cm)</text>
                        <text x="200" y="225" text-anchor="middle" class="text-[9px] fill-gray-400">Hari 7 (6.5 cm)</text>
                        <text x="330" y="225" text-anchor="middle" class="text-[9px] fill-gray-400">Hari 14 (12.1 cm)</text>
                        <text x="440" y="225" text-anchor="middle" class="text-[9px] fill-gray-400">Hari 21 (18.4 cm)</text>
                        <text x="540" y="225" text-anchor="middle" class="text-[9px] fill-emerald-600 font-semibold">Hari 28 (23.8 cm)</text>

                        {{-- Legend --}}
                        <circle cx="470" cy="12" r="3.5" fill="#1a9e5f"/>
                        <text x="478" y="15" class="text-[9px] fill-gray-500">Aktual (cm)</text>
                        <line x1="520" y1="12" x2="535" y2="12" stroke="#94a3b8" stroke-width="1" stroke-dasharray="4 3"/>
                        <text x="540" y="15" class="text-[9px] fill-gray-400">Standar Siklus</text>
                    </svg>
                </div>
            </div>

            {{-- Right Side Stats + Observation Log --}}
            <div class="space-y-4">
                {{-- Stat Cards --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3.5">
                        <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Tinggi Rata-rata</p>
                        <div class="mt-1.5 flex items-baseline gap-1">
                            <span class="text-xl font-extrabold text-gray-900">23.8</span>
                            <span class="text-[10px] text-gray-500">cm / tanaman</span>
                        </div>
                        <p class="text-[11px] font-medium text-emerald-600 mt-1.5">Optimal (+2.1 cm/3 hari)</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3.5">
                        <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Biomassa Rerata</p>
                        <div class="mt-1.5 flex items-baseline gap-1">
                            <span class="text-xl font-extrabold text-gray-900">142</span>
                            <span class="text-[10px] text-gray-500">gram / ikat</span>
                        </div>
                        <p class="text-[11px] font-medium text-emerald-600 mt-1.5">Target Panen: 170g</p>
                    </div>
                </div>

                {{-- Observation Log --}}
                <div class="bg-gray-50 rounded-lg p-3.5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold text-gray-900">Log Observasi Lapangan</p>
                        <span class="text-[10px] text-gray-400">Terakhir: Hari ini, 08:30</span>
                    </div>
                    <div class="overflow-x-auto -mx-1 px-1">
                        <table class="w-full text-[11px] min-w-[280px]">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left text-gray-500 font-medium pb-2 pr-2">Hari</th>
                                    <th class="text-left text-gray-500 font-medium pb-2 pr-2">Tinggi</th>
                                    <th class="text-left text-gray-500 font-medium pb-2 pr-2">Status Daun</th>
                                    <th class="text-right text-gray-500 font-medium pb-2">Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pr-2 font-semibold text-gray-900">H-28</td>
                                    <td class="py-2 pr-2 text-gray-600">23.8 cm</td>
                                    <td class="py-2 pr-2 font-medium text-emerald-600">Daun Lebar &amp; Tegak</td>
                                    <td class="py-2 text-right text-gray-500">Rian T.</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pr-2 font-semibold text-gray-900">H-21</td>
                                    <td class="py-2 pr-2 text-gray-600">18.4 cm</td>
                                    <td class="py-2 pr-2 font-medium text-emerald-600">Warna Hijau Cerah</td>
                                    <td class="py-2 text-right text-gray-500">Budi S.</td>
                                </tr>
                                <tr>
                                    <td class="py-2 pr-2 font-semibold text-gray-900">H-14</td>
                                    <td class="py-2 pr-2 text-gray-600">12.1 cm</td>
                                    <td class="py-2 pr-2 font-medium text-emerald-600">Fase Vegetatif Aktif</td>
                                    <td class="py-2 text-right text-gray-500">Rian T.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         Status Bedeng Aktif
    ═══════════════════════════════════════════ --}}
    <div class="space-y-3 pt-1">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-gray-900">Status Bedeng Aktif</h2>
                <p class="text-xs text-gray-500">Ringkasan progres kultivasi dan jadwal panen per unit bedeng hidroponik</p>
            </div>
            <a href="{{ Route::has('admin.stok') ? route('admin.stok') : '/admin/stok' }}"
               class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition inline-flex items-center gap-1 shrink-0">
                Lihat Semua Bedeng
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Bedeng Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Bedeng A-01 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_a01.jpg" alt="Bedeng A-01" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-[#137a46] text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">Hari 28 / 35</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng A-01</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Prima</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Butterhead • 1.050 Lubang</p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <span class="text-[11px] text-gray-400">Est. Panen:</span>
                        <span class="text-[11px] font-bold text-gray-900">1.25 Ton (7 Hari)</span>
                    </div>
                </div>
            </div>

            {{-- Bedeng A-02 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_a02.jpg" alt="Bedeng A-02" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-[#137a46] text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">Hari 24 / 35</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng A-02</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Prima</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Butterhead • 1.050 Lubang</p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <span class="text-[11px] text-gray-400">Est. Panen:</span>
                        <span class="text-[11px] font-bold text-gray-900">1.20 Ton (11 Hari)</span>
                    </div>
                </div>
            </div>

            {{-- Bedeng B-01 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_b01.jpg" alt="Bedeng B-01" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">Hari 21 / 35</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng B-01</h3>
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Normal</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Romaine • 1.050 Lubang</p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <span class="text-[11px] text-gray-400">Est. Panen:</span>
                        <span class="text-[11px] font-bold text-gray-900">1.20 Ton (14 Hari)</span>
                    </div>
                </div>
            </div>

            {{-- Bedeng B-02 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_b02.jpg" alt="Bedeng B-02" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">Hari 18 / 35</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng B-02</h3>
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Normal</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Romaine • 1.050 Lubang</p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <span class="text-[11px] text-gray-400">Est. Panen:</span>
                        <span class="text-[11px] font-bold text-gray-900">1.20 Ton (17 Hari)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // Bedeng tab switching (UI only – toggles active class)
    const tabs = document.querySelectorAll('.bedeng-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => {
                t.classList.remove('bg-[#137a46]', 'text-white', 'shadow-sm', 'font-semibold');
                t.classList.add('text-gray-600', 'font-medium');
            });
            tab.classList.add('bg-[#137a46]', 'text-white', 'shadow-sm', 'font-semibold');
            tab.classList.remove('text-gray-600', 'font-medium');
        });
    });
})();
</script>
@endpush
