@extends('layouts.karyawan', ['active' => 'monitoring'])

@section('title', 'Monitoring & Log Pertumbuhan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Monitoring & Log Pertumbuhan</h1>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    onclick="exportSummary()"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#eef4f8] hover:bg-[#e2edf5] text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer active:scale-95 border border-slate-200/70 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export Ringkasan</span>
            </button>
            <button type="button"
                    onclick="openAddLogModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Data Pertumbuhan</span>
            </button>
        </div>
    </div>

    {{-- Metric Cards (4 Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Batch Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">TOTAL BATCH AKTIF</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">6</span>
                <span class="text-sm font-semibold text-gray-500">Batch</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Siklus 7 Hari Lagi</span>
                </div>
                <span class="text-gray-400">Target 100%</span>
            </div>
        </div>

        {{-- Card 2: Populasi Ditanam --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">POPULASI DITANAM</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">4.200</span>
                <span class="text-sm font-semibold text-gray-500">Lubang</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-emerald-700 font-medium">4 Bedeng Beroperasi</span>
                <span class="text-gray-400">Kapasitas 100%</span>
            </div>
        </div>

        {{-- Card 3: Siap Panen --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">SIAP PANEN</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">1.25</span>
                <span class="text-sm font-semibold text-gray-500">Ton</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-emerald-700 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Bedeng A-01</span>
                </div>
                <span class="text-gray-400">Target Panen 28 Okt</span>
            </div>
        </div>

        {{-- Card 4: Rata-Rata Kesehatan --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">RATA-RATA KESEHATAN</div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">98.4</span>
                <span class="text-lg font-bold text-gray-600">%</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-emerald-600 font-semibold">Kondisi Prima</span>
                <span class="text-gray-400">Evaluasi SVM Valid</span>
            </div>
        </div>
    </div>

    {{-- Status Bedeng Aktif Section --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Status Bedeng Aktif</h2>
            </div>
            <a href="#tableSection" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0e5f38] hover:text-[#0b4e2e] transition">
                <span>Lihat Semua Bedeng</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Bed A-01 --}}
            <div class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition">
                <div class="relative h-40 bg-gray-100 overflow-hidden">
                    <img src="/images/beds/bed_a01.jpg" alt="Bedeng A-01" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-[#0e5f38]/90 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                        Hari 28 / 35
                    </span>
                </div>
                <div class="p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-gray-900 text-sm">Bedeng A-01</span>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Prima</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Est. Panen:</span>
                        <span class="font-medium text-gray-800">1.25 Ton (7 Hari)</span>
                    </div>
                </div>
            </div>

            {{-- Bed A-02 --}}
            <div class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition">
                <div class="relative h-40 bg-gray-100 overflow-hidden">
                    <img src="/images/beds/bed_a02.jpg" alt="Bedeng A-02" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-[#0e5f38]/90 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                        Hari 24 / 35
                    </span>
                </div>
                <div class="p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-gray-900 text-sm">Bedeng A-02</span>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Prima</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Est. Panen:</span>
                        <span class="font-medium text-gray-800">1.20 Ton (11 Hari)</span>
                    </div>
                </div>
            </div>

            {{-- Bed B-01 --}}
            <div class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition">
                <div class="relative h-40 bg-gray-100 overflow-hidden">
                    <img src="/images/beds/bed_b01.jpg" alt="Bedeng B-01" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-slate-800/85 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                        Hari 21 / 35
                    </span>
                </div>
                <div class="p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-gray-900 text-sm">Bedeng B-01</span>
                        <span class="text-xs font-semibold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded-full">Normal</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Est. Panen:</span>
                        <span class="font-medium text-gray-800">1.20 Ton (14 Hari)</span>
                    </div>
                </div>
            </div>

            {{-- Bed B-02 --}}
            <div class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition">
                <div class="relative h-40 bg-gray-100 overflow-hidden">
                    <img src="/images/beds/bed_b02.jpg" alt="Bedeng B-02" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <span class="absolute top-2.5 right-2.5 bg-slate-800/85 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                        Hari 18 / 35
                    </span>
                </div>
                <div class="p-3.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-gray-900 text-sm">Bedeng B-02</span>
                        <span class="text-xs font-semibold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded-full">Normal</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Est. Panen:</span>
                        <span class="font-medium text-gray-800">1.20 Ton (17 Hari)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- UNIQUE SECTION: Growth Curve Chart (Kurva Pertumbuhan Tajuk & Bobot Biomassa HST) --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Kurva Pertumbuhan Tajuk & Bobot Biomassa (HST)</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Bed selector --}}
                <select id="chartBedengSelect"
                        onchange="updateChartData()"
                        class="px-3.5 py-1.5 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="A01">Bedeng A-01</option>
                    <option value="A02">Bedeng A-02</option>
                    <option value="B01">Bedeng B-01</option>
                    <option value="B02">Bedeng B-02</option>
                </select>

                {{-- Time Period Tabs --}}
                <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl text-xs">
                    <button type="button"
                            onclick="setPeriod(7)"
                            id="btnP7"
                            class="px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900 transition font-medium cursor-pointer">
                        7 Hari
                    </button>
                    <button type="button"
                            onclick="setPeriod(14)"
                            id="btnP14"
                            class="px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900 transition font-medium cursor-pointer">
                        14 Hari
                    </button>
                    <button type="button"
                            onclick="setPeriod('full')"
                            id="btnPFull"
                            class="px-3 py-1 rounded-lg bg-white text-gray-900 font-semibold shadow-2xs border border-gray-200/80 transition cursor-pointer">
                        Siklus Penuh
                    </button>
                </div>
            </div>
        </div>

        {{-- SVG Chart Viewport --}}
        <div class="w-full overflow-x-auto">
            <div class="min-w-[650px] sm:min-w-full relative py-2">
                <svg viewBox="0 0 900 320" class="w-full h-auto overflow-visible select-none" id="growthSvgChart">
                    <defs>
                        <linearGradient id="chartLineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#10b981" />
                            <stop offset="75%" stop-color="#0e5f38" />
                            <stop offset="100%" stop-color="#059669" />
                        </linearGradient>
                        <filter id="nodeGlow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#10b981" flood-opacity="0.35"/>
                        </filter>
                    </defs>

                    {{-- Horizontal Y-Gridlines --}}
                    {{-- 28 cm (Y=40) --}}
                    <g class="text-[11px] fill-gray-400">
                        <text x="35" y="44" text-anchor="end">28 cm</text>
                        <line x1="50" y1="40" x2="860" y2="40" stroke="#f1f5f9" stroke-dasharray="4 4" stroke-width="1.5"/>
                    </g>
                    {{-- 21 cm (Y=95) --}}
                    <g class="text-[11px] fill-gray-400">
                        <text x="35" y="99" text-anchor="end">21 cm</text>
                        <line x1="50" y1="95" x2="860" y2="95" stroke="#f1f5f9" stroke-dasharray="4 4" stroke-width="1.5"/>
                    </g>
                    {{-- 14 cm (Y=150) --}}
                    <g class="text-[11px] fill-gray-400">
                        <text x="35" y="154" text-anchor="end">14 cm</text>
                        <line x1="50" y1="150" x2="860" y2="150" stroke="#f1f5f9" stroke-dasharray="4 4" stroke-width="1.5"/>
                    </g>
                    {{-- 7 cm (Y=205) --}}
                    <g class="text-[11px] fill-gray-400">
                        <text x="35" y="209" text-anchor="end">7 cm</text>
                        <line x1="50" y1="205" x2="860" y2="205" stroke="#f1f5f9" stroke-dasharray="4 4" stroke-width="1.5"/>
                    </g>
                    {{-- 0 (Y=260) --}}
                    <g class="text-[11px] fill-gray-400">
                        <text x="35" y="264" text-anchor="end">0</text>
                        <line x1="50" y1="260" x2="860" y2="260" stroke="#e2e8f0" stroke-width="1.5"/>
                    </g>

                    {{-- Vertical Grid Markers for Days --}}
                    {{-- H-01 (X=90) --}}
                    <text x="90" y="285" text-anchor="middle" class="text-[11px] fill-gray-500 font-medium">H-01</text>
                    {{-- H-07 (X=230) --}}
                    <text x="230" y="285" text-anchor="middle" class="text-[11px] fill-gray-500 font-medium">H-07</text>
                    {{-- H-14 (X=390) --}}
                    <text x="390" y="285" text-anchor="middle" class="text-[11px] fill-gray-500 font-medium">H-14</text>
                    {{-- H-21 (X=550) --}}
                    <text x="550" y="285" text-anchor="middle" class="text-[11px] fill-gray-500 font-medium">H-21</text>
                    {{-- H-28 (Hari Ini) (X=690) --}}
                    <text x="690" y="285" text-anchor="middle" class="text-[11px] fill-[#0e5f38] font-bold">H-28 (Hari Ini)</text>
                    {{-- Vertical marker line for H-28 --}}
                    <line x1="690" y1="40" x2="690" y2="260" stroke="#10b981" stroke-dasharray="3 3" stroke-width="1" stroke-opacity="0.5"/>
                    {{-- H-35 (Panen) (X=820) --}}
                    <text x="820" y="285" text-anchor="middle" class="text-[11px] fill-gray-400 font-medium">H-35 (Panen)</text>

                    {{-- Dynamic Curve Path (Actual Measurements) --}}
                    <path id="actualCurvePath"
                          d="M 90 250 C 160 240, 180 230, 230 220 C 310 200, 340 170, 390 150 C 470 120, 500 80, 550 65 C 620 50, 650 48, 690 44"
                          fill="none"
                          stroke="url(#chartLineGrad)"
                          stroke-width="3.5"
                          stroke-linecap="round"/>

                    {{-- Projected Sigmoid Trajectory (Dashed line) --}}
                    <path id="projectedCurvePath"
                          d="M 690 44 C 740 40, 780 39, 820 38"
                          fill="none"
                          stroke="#10b981"
                          stroke-width="2.5"
                          stroke-dasharray="5 5"
                          stroke-linecap="round"/>

                    {{-- Data Nodes (Interactive Circles with tooltips) --}}
                    {{-- Node 1: H-01 (1.8 cm) --}}
                    <circle cx="90" cy="250" r="5" fill="#ffffff" stroke="#10b981" stroke-width="3" filter="url(#nodeGlow)" class="cursor-pointer hover:r-7 transition"/>
                    {{-- Node 2: H-07 (5.2 cm) --}}
                    <circle cx="230" cy="220" r="5" fill="#ffffff" stroke="#10b981" stroke-width="3" filter="url(#nodeGlow)" class="cursor-pointer hover:r-7 transition"/>
                    {{-- Node 3: H-14 (14.0 cm) --}}
                    <circle cx="390" cy="150" r="5" fill="#ffffff" stroke="#10b981" stroke-width="3" filter="url(#nodeGlow)" class="cursor-pointer hover:r-7 transition"/>
                    {{-- Node 4: H-21 (22.5 cm) --}}
                    <circle cx="550" cy="65" r="5" fill="#ffffff" stroke="#10b981" stroke-width="3" filter="url(#nodeGlow)" class="cursor-pointer hover:r-7 transition"/>
                    {{-- Node 5: H-28 Today (25.4 cm) --}}
                    <circle cx="690" cy="44" r="6" fill="#0e5f38" stroke="#ffffff" stroke-width="2.5" filter="url(#nodeGlow)" class="cursor-pointer hover:r-8 transition"/>
                    {{-- Node 6: H-35 Projected (28 cm) --}}
                    <circle cx="820" cy="38" r="5" fill="#ffffff" stroke="#10b981" stroke-width="2" stroke-dasharray="2 2" class="cursor-pointer hover:r-7 transition"/>
                </svg>
            </div>
        </div>

        {{-- Chart Key Statistics --}}
        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Tinggi Tajuk Terakhir</span>
                <span class="text-sm font-bold text-gray-900 mt-0.5 block">25.4 cm (H-28)</span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Laju Pertumbuhan Rerata</span>
                <span class="text-sm font-bold text-emerald-600 mt-0.5 block">+0.85 cm / hari</span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Target Tajuk Panen (H-35)</span>
                <span class="text-sm font-bold text-gray-900 mt-0.5 block">28.0 cm (Tercapai 91%)</span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Indeks Sigmoid R²</span>
                <span class="text-sm font-bold text-[#0e5f38] mt-0.5 block">0.974 (Sangat Presisi)</span>
            </div>
        </div>
    </div>

    {{-- Batch Table Section --}}
    <div id="tableSection" class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        {{-- Filter & Search Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-5 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                {{-- Search Input (NO PLACEHOLDER) --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           id="batchSearchInput"
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                <select id="bedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Bedeng</option>
                    <option value="Bedeng A-01">Bedeng A-01</option>
                    <option value="Bedeng A-02">Bedeng A-02</option>
                    <option value="Bedeng B-01">Bedeng B-01</option>
                    <option value="Bedeng B-02">Bedeng B-02</option>
                </select>

                <select id="faseFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Fase</option>
                    <option value="Persemaian / Pindah Tanam">Persemaian</option>
                    <option value="Vegetatif Awal">Vegetatif Awal</option>
                    <option value="Vegetatif Lanjut">Vegetatif Lanjut</option>
                    <option value="Menjelang Panen">Menjelang Panen</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button"
                        id="tabActive"
                        onclick="setTabStatus('aktif')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Aktif (5)
                </button>
                <button type="button"
                        id="tabFinished"
                        onclick="setTabStatus('selesai')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Panen Selesai (1)
                </button>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto -mx-5 sm:-mx-6 mt-2">
            <div class="inline-block min-w-full align-middle px-5 sm:px-6">
                <table class="min-w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th scope="col" class="py-3.5 pr-4">LOKASI BEDENG</th>
                            <th scope="col" class="py-3.5 px-4">TANGGAL TANAM & UMUR</th>
                            <th scope="col" class="py-3.5 px-4">FASE PERTUMBUHAN</th>
                            <th scope="col" class="py-3.5 px-4">KONDISI & STATUS</th>
                            <th scope="col" class="py-3.5 pl-4 text-right">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody id="batchTableBody" class="divide-y divide-gray-100 text-gray-700">
                        {{-- Rendered via JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Table Pagination & Info --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
            <div id="tableInfo">Menampilkan 4 dari 6 batch tanaman</div>
            <div class="flex items-center gap-1 self-center sm:self-auto">
                <button type="button" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    Sebelumnya
                </button>
                <button type="button" class="px-3 py-1.5 rounded-lg bg-[#0e5f38] text-white font-semibold transition">
                    1
                </button>
                <button type="button" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    2
                </button>
                <button type="button" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal / Drawer: Tambah Data Pertumbuhan --}}
<div id="addLogModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeAddLogModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 flex items-center justify-center">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 text-left">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0e5f38] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Catat Log Pertumbuhan Harian</h3>
                </div>
                <button type="button" onclick="closeAddLogModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="addLogForm" onsubmit="handleSaveLog(event)" class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Bedeng</label>
                        <select id="logBedeng" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                            <option value="Bedeng A-01">Bedeng A-01</option>
                            <option value="Bedeng A-02">Bedeng A-02</option>
                            <option value="Bedeng B-01">Bedeng B-01</option>
                            <option value="Bedeng B-02">Bedeng B-02</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hari Ke (HST)</label>
                        <input type="number" id="logHst" value="29" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tinggi Tajuk (cm)</label>
                        <input type="number" step="0.1" id="logTinggi" value="26.0" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Diameter Daun (cm)</label>
                        <input type="number" step="0.1" id="logDiameter" value="18.5" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Estimasi Biomassa (gram)</label>
                        <input type="number" id="logBiomassa" value="115" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Pengukuran</label>
                        <input type="date" id="logTanggal" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Kondisi</label>
                    <textarea id="logCatatan" rows="2" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAddLogModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold transition cursor-pointer shadow-xs">
                        Simpan Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentTab = 'aktif';

const batchesData = [
    {
        id: 1,
        lokasi: 'Bedeng A-01',
        lubang: '1.050 lubang',
        tanggal: '26 Sep 2024',
        umur: 'Hari ke-28 / 35 HST',
        fase: 'Menjelang Panen',
        faseClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        kondisi: 'Sehat (98.4%)',
        kondisiSub: 'Est. 1.25 Ton',
        kondisiClass: 'text-emerald-600',
        status: 'aktif'
    },
    {
        id: 2,
        lokasi: 'Bedeng A-02',
        lubang: '1.050 lubang',
        tanggal: '06 Okt 2024',
        umur: 'Hari ke-18 / 40 HST',
        fase: 'Vegetatif Lanjut',
        faseClass: 'bg-teal-50 text-teal-700 border-teal-200',
        kondisi: 'Optimal (96.2%)',
        kondisiSub: 'SVM Stabil',
        kondisiClass: 'text-teal-700',
        status: 'aktif'
    },
    {
        id: 3,
        lokasi: 'Bedeng B-01',
        lubang: '1.050 lubang',
        tanggal: '15 Okt 2024',
        umur: 'Hari ke-09 / 35 HST',
        fase: 'Vegetatif Awal',
        faseClass: 'bg-cyan-50 text-cyan-700 border-cyan-200',
        kondisi: 'Perhatian EC (+1.8)',
        kondisiSub: 'Kalibrasi',
        kondisiClass: 'text-amber-600',
        status: 'aktif'
    },
    {
        id: 4,
        lokasi: 'Bedeng B-02',
        lubang: '1.050 lubang',
        tanggal: '20 Okt 2024',
        umur: 'Hari ke-04 / 35 HST',
        fase: 'Persemaian / Pindah Tanam',
        faseClass: 'bg-blue-50 text-blue-700 border-blue-200',
        kondisi: 'Adaptasi Baik (99%)',
        kondisiSub: 'Kondisi Normal',
        kondisiClass: 'text-emerald-600',
        status: 'aktif'
    },
    {
        id: 5,
        lokasi: 'Bedeng A-03',
        lubang: '1.050 lubang',
        tanggal: '22 Okt 2024',
        umur: 'Hari ke-02 / 35 HST',
        fase: 'Persemaian / Pindah Tanam',
        faseClass: 'bg-blue-50 text-blue-700 border-blue-200',
        kondisi: 'Adaptasi Baik (98%)',
        kondisiSub: 'Kondisi Normal',
        kondisiClass: 'text-emerald-600',
        status: 'aktif'
    },
    {
        id: 6,
        lokasi: 'Bedeng Batch C-01',
        lubang: '1.050 lubang',
        tanggal: '15 Agu 2024',
        umur: '35 Hari Penuh (Panen)',
        fase: 'Menjelang Panen',
        faseClass: 'bg-gray-100 text-gray-700 border-gray-200',
        kondisi: 'Panen Selesai (1.3 Ton)',
        kondisiSub: 'Hasil Terverifikasi',
        kondisiClass: 'text-gray-600',
        status: 'selesai'
    }
];

function renderTable() {
    const tbody = document.getElementById('batchTableBody');
    const searchVal = document.getElementById('batchSearchInput').value.toLowerCase().trim();
    const bedengVal = document.getElementById('bedengFilter').value;
    const faseVal = document.getElementById('faseFilter').value;

    const filtered = batchesData.filter(item => {
        if (item.status !== currentTab) return false;
        if (searchVal && !item.lokasi.toLowerCase().includes(searchVal) && !item.fase.toLowerCase().includes(searchVal) && !item.kondisi.toLowerCase().includes(searchVal)) {
            return false;
        }
        if (bedengVal && item.lokasi !== bedengVal) return false;
        if (faseVal && item.fase !== faseVal) return false;
        return true;
    });

    tbody.innerHTML = '';

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                    Tidak ada data batch tanaman yang sesuai filter.
                </td>
            </tr>
        `;
        document.getElementById('tableInfo').innerText = `Menampilkan 0 batch tanaman`;
        return;
    }

    filtered.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/70 transition group';
        tr.innerHTML = `
            <td class="py-4 pr-4">
                <div class="font-bold text-gray-900">${item.lokasi}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">${item.lubang}</div>
            </td>
            <td class="py-4 px-4">
                <div class="font-medium text-gray-900">${item.tanggal}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">${item.umur}</div>
            </td>
            <td class="py-4 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${item.faseClass}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    <span>${item.fase}</span>
                </span>
            </td>
            <td class="py-4 px-4">
                <div class="font-bold ${item.kondisiClass}">${item.kondisi}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">${item.kondisiSub}</div>
            </td>
            <td class="py-4 pl-4 text-right">
                <div class="inline-flex items-center gap-1.5 justify-end">
                    <button type="button" onclick="alert('Detail log pertumbuhan untuk ' + '${item.lokasi}')" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Lihat Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="alert('Form kalibrasi kurva untuk ' + '${item.lokasi}')" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Data">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="deleteBatch(${item.id})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('tableInfo').innerText = `Menampilkan ${filtered.length} dari ${batchesData.length} batch tanaman`;
}

function setTabStatus(status) {
    currentTab = status;
    const tabActive = document.getElementById('tabActive');
    const tabFinished = document.getElementById('tabFinished');

    if (status === 'aktif') {
        tabActive.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
        tabFinished.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer";
    } else {
        tabFinished.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
        tabActive.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer";
    }
    renderTable();
}

function setPeriod(p) {
    const p7 = document.getElementById('btnP7');
    const p14 = document.getElementById('btnP14');
    const pFull = document.getElementById('btnPFull');

    [p7, p14, pFull].forEach(b => {
        b.className = "px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900 transition font-medium cursor-pointer";
    });

    if (p === 7) {
        p7.className = "px-3 py-1 rounded-lg bg-white text-gray-900 font-semibold shadow-2xs border border-gray-200/80 transition cursor-pointer";
    } else if (p === 14) {
        p14.className = "px-3 py-1 rounded-lg bg-white text-gray-900 font-semibold shadow-2xs border border-gray-200/80 transition cursor-pointer";
    } else {
        pFull.className = "px-3 py-1 rounded-lg bg-white text-gray-900 font-semibold shadow-2xs border border-gray-200/80 transition cursor-pointer";
    }
}

function updateChartData() {
    const selectedBed = document.getElementById('chartBedengSelect').value;
    const path = document.getElementById('actualCurvePath');
    if (selectedBed === 'A02') {
        path.setAttribute('d', 'M 90 255 C 160 245, 180 238, 230 225 C 310 205, 340 180, 390 160 C 470 135, 500 95, 550 78 C 620 62, 650 56, 690 52');
    } else if (selectedBed === 'B01') {
        path.setAttribute('d', 'M 90 258 C 160 250, 180 242, 230 230 C 310 215, 340 190, 390 175 C 470 150, 500 115, 550 95 C 620 80, 650 72, 690 68');
    } else {
        path.setAttribute('d', 'M 90 250 C 160 240, 180 230, 230 220 C 310 200, 340 170, 390 150 C 470 120, 500 80, 550 65 C 620 50, 650 48, 690 44');
    }
}

function openAddLogModal() {
    document.getElementById('logTanggal').valueAsDate = new Date();
    document.getElementById('addLogModal').classList.remove('hidden');
}

function closeAddLogModal() {
    document.getElementById('addLogModal').classList.add('hidden');
}

function handleSaveLog(e) {
    e.preventDefault();
    const bed = document.getElementById('logBedeng').value;
    const hst = document.getElementById('logHst').value;
    const tinggi = document.getElementById('logTinggi').value;

    alert(`Log pertumbuhan untuk ${bed} (Hari ke-${hst}, Tinggi: ${tinggi} cm) berhasil disimpan ke kurva telemetri!`);
    closeAddLogModal();
}

function deleteBatch(id) {
    if (confirm('Hapus log batch ini?')) {
        const idx = batchesData.findIndex(b => b.id === id);
        if (idx !== -1) {
            batchesData.splice(idx, 1);
            renderTable();
        }
    }
}

function exportSummary() {
    alert('Log monitoring pertumbuhan & kurva biomassa berhasil diekspor ke format Excel/PDF!');
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('batchSearchInput').addEventListener('input', renderTable);
    document.getElementById('bedengFilter').addEventListener('change', renderTable);
    document.getElementById('faseFilter').addEventListener('change', renderTable);
    renderTable();
});
</script>
@endpush
