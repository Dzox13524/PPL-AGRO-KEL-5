@extends('layouts.karyawan', ['active' => 'tanaman'])

@section('title', 'Manajemen Tanaman')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Manajemen Tanaman</h1>
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
                    onclick="openAddBatchModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Data Tanaman</span>
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

        {{-- 4 Bed Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Bed A-01 --}}
            <div class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition">
                <div class="relative h-40 bg-gray-100 overflow-hidden">
                    <img src="/images/beds/bed_a01.jpg"
                         alt="Bedeng A-01"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                    <img src="/images/beds/bed_a02.jpg"
                         alt="Bedeng A-02"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                    <img src="/images/beds/bed_b01.jpg"
                         alt="Bedeng B-01"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                    <img src="/images/beds/bed_b02.jpg"
                         alt="Bedeng B-02"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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

                {{-- Bedeng Filter --}}
                <select id="bedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Bedeng</option>
                    <option value="Bedeng A-01">Bedeng A-01</option>
                    <option value="Bedeng A-02">Bedeng A-02</option>
                    <option value="Bedeng B-01">Bedeng B-01</option>
                    <option value="Bedeng B-02">Bedeng B-02</option>
                </select>

                {{-- Fase Filter --}}
                <select id="faseFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Fase</option>
                    <option value="Persemaian / Pindah Tanam">Persemaian</option>
                    <option value="Vegetatif Awal">Vegetatif Awal</option>
                    <option value="Vegetatif Lanjut">Vegetatif Lanjut</option>
                    <option value="Menjelang Panen">Menjelang Panen</option>
                </select>
            </div>

            {{-- Status Tabs --}}
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
                <button type="button"
                        class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    Sebelumnya
                </button>
                <button type="button"
                        class="px-3 py-1.5 rounded-lg bg-[#0e5f38] text-white font-semibold transition">
                    1
                </button>
                <button type="button"
                        class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    2
                </button>
                <button type="button"
                        class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal / Drawer: Tambah Data Tanaman --}}
<div id="addBatchModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeAddBatchModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 flex items-center justify-center">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 text-left">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0e5f38] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900" id="modal-title">Tambah Batch Tanaman Baru</h3>
                </div>
                <button type="button" onclick="closeAddBatchModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="addBatchForm" onsubmit="handleSaveBatch(event)" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi Bedeng</label>
                    <select id="modalBedeng" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        <option value="Bedeng A-01">Bedeng A-01</option>
                        <option value="Bedeng A-02">Bedeng A-02</option>
                        <option value="Bedeng B-01">Bedeng B-01</option>
                        <option value="Bedeng B-02">Bedeng B-02</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kapasitas Lubang</label>
                        <input type="number" id="modalLubang" value="1050" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Tanam</label>
                        <input type="date" id="modalTanggal" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Fase Pertumbuhan</label>
                        <select id="modalFase" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                            <option value="Persemaian / Pindah Tanam">Persemaian</option>
                            <option value="Vegetatif Awal">Vegetatif Awal</option>
                            <option value="Vegetatif Lanjut">Vegetatif Lanjut</option>
                            <option value="Menjelang Panen">Menjelang Panen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kesehatan</label>
                        <select id="modalStatus" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                            <option value="Prima (99%)">Prima (99%)</option>
                            <option value="Sehat (98.4%)">Sehat (98.4%)</option>
                            <option value="Optimal (96.2%)">Optimal (96.2%)</option>
                            <option value="Perhatian EC (+1.8)">Perhatian EC (+1.8)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Agronomis</label>
                    <textarea id="modalCatatan" rows="2" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAddBatchModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold transition cursor-pointer shadow-xs">
                        Simpan Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Batch --}}
<div id="detailBatchModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeDetailBatchModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 flex items-center justify-center">
        <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 text-left">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900" id="detailTitle">Detail Bedeng Tanaman</h3>
                <button type="button" onclick="closeDetailBatchModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="detailContent" class="py-4 space-y-3 text-xs sm:text-sm text-gray-700">
                {{-- Injected via JS --}}
            </div>
            <div class="pt-3 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closeDetailBatchModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold transition">
                    Tutup
                </button>
            </div>
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
                    <button type="button" onclick="showDetail(${item.id})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Lihat Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="editBatch(${item.id})" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Data">
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

function openAddBatchModal() {
    document.getElementById('modalTanggal').valueAsDate = new Date();
    document.getElementById('addBatchModal').classList.remove('hidden');
}

function closeAddBatchModal() {
    document.getElementById('addBatchModal').classList.add('hidden');
}

function handleSaveBatch(e) {
    e.preventDefault();
    const lokasi = document.getElementById('modalBedeng').value;
    const lubang = document.getElementById('modalLubang').value + ' lubang';
    const tanggal = document.getElementById('modalTanggal').value;
    const fase = document.getElementById('modalFase').value;
    const statusVal = document.getElementById('modalStatus').value;

    batchesData.unshift({
        id: Date.now(),
        lokasi: lokasi,
        lubang: lubang,
        tanggal: tanggal,
        umur: 'Hari ke-01 / 35 HST',
        fase: fase,
        faseClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        kondisi: statusVal,
        kondisiSub: 'Baru Dicatat',
        kondisiClass: 'text-emerald-600',
        status: 'aktif'
    });

    closeAddBatchModal();
    setTabStatus('aktif');
    alert('Data batch tanaman berhasil ditambahkan!');
}

function showDetail(id) {
    const item = batchesData.find(b => b.id === id);
    if (!item) return;

    const detailContent = document.getElementById('detailContent');
    detailContent.innerHTML = `
        <div class="p-3 bg-gray-50 rounded-xl space-y-2">
            <div class="flex justify-between"><span class="text-gray-500">Lokasi:</span> <span class="font-bold text-gray-900">${item.lokasi}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Populasi:</span> <span class="font-medium text-gray-800">${item.lubang}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tanggal Tanam:</span> <span class="font-medium text-gray-800">${item.tanggal}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Umur Tanaman:</span> <span class="font-bold text-emerald-600">${item.umur}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Fase Saat Ini:</span> <span class="font-semibold text-gray-800">${item.fase}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status Kesehatan:</span> <span class="font-bold ${item.kondisiClass}">${item.kondisi}</span></div>
        </div>
        <p class="text-gray-500 text-[11.5px] leading-relaxed">
            Data telemetri batch ini disinkronkan secara realtime dengan sensor EC/pH dan citra kanopi bedeng hidroponik.
        </p>
    `;
    document.getElementById('detailBatchModal').classList.remove('hidden');
}

function closeDetailBatchModal() {
    document.getElementById('detailBatchModal').classList.add('hidden');
}

function editBatch(id) {
    alert('Fitur edit untuk batch #' + id + ' siap digunakan.');
}

function deleteBatch(id) {
    if (confirm('Apakah Anda yakin ingin menghapus catatan batch ini?')) {
        const idx = batchesData.findIndex(b => b.id === id);
        if (idx !== -1) {
            batchesData.splice(idx, 1);
            renderTable();
        }
    }
}

function exportSummary() {
    alert('Ringkasan kultivasi tanaman berhasil diexport ke format PDF/Excel!');
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('batchSearchInput').addEventListener('input', renderTable);
    document.getElementById('bedengFilter').addEventListener('change', renderTable);
    document.getElementById('faseFilter').addEventListener('change', renderTable);
    renderTable();
});
</script>
@endpush
