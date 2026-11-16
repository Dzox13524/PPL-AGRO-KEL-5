@extends('layouts.karyawan', ['active' => 'diagnosis'])

@section('title', 'Diagnosis Citra Selada & Kesehatan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Diagnosis Citra Selada & Kesehatan</h1>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    onclick="alert('Laporan diagnosis citra & kesehatan tanaman berhasil diexport (PDF)!')"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#eef4f8] hover:bg-[#e2edf5] text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer active:scale-95 border border-slate-200/70 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export Laporan Diagnosis</span>
            </button>
            <button type="button"
                    onclick="document.getElementById('uploadCitraSection').scrollIntoView({ behavior: 'smooth' })"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Diagnosis Citra Baru</span>
            </button>
        </div>
    </div>

    {{-- Main Two-Column Layout (Upload & Diagnosis Result) --}}
    <div id="uploadCitraSection" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Upload & Parameters (5 cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Unggah Citra Kanopi & Parameter</h2>
                <form id="svmForm" onsubmit="handleRunSvm(event)" class="mt-5 space-y-4">
                    {{-- Upload Drag & Drop Area --}}
                    <div class="border-2 border-dashed border-emerald-200/90 rounded-2xl p-6 bg-emerald-50/20 text-center hover:bg-emerald-50/40 transition cursor-pointer"
                         onclick="document.getElementById('svmFileInput').click()">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-[#0e5f38] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 mt-3">Pilih atau Seret Foto Kanopi Selada</h3>
                        <p class="text-xs text-gray-500 mt-1">Format didukung: JPG, PNG, WEBP &bull; Ukuran Maks: 10 MB</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <span>Resolusi minimal 800 x 800 px disarankan</span>
                        </div>
                        <input type="file" id="svmFileInput" class="hidden" accept="image/*" onchange="handleSvmImageUploaded(event)">
                    </div>

                    {{-- Form Fields --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Batch / Lokasi Bedeng Hidroponik</label>
                        <select id="svmBedeng" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                            <option value="Bedeng A-01">Bedeng A-01</option>
                            <option value="Bedeng A-02">Bedeng A-02</option>
                            <option value="Bedeng B-01">Bedeng B-01</option>
                            <option value="Bedeng B-02">Bedeng B-02</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Tanggal Pengambilan</label>
                            <input type="text" id="svmTanggal" value="24 Okt 2024 - 09:15" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Petugas Lapangan</label>
                            <div class="relative">
                                <input type="text" id="svmPetugas" value="username" class="w-full pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pipeline Feature Extraction Checklist --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-2">
                        <div class="text-[10.5px] font-bold text-emerald-800 uppercase tracking-wider">PIPELINE EKSTRAKSI FITUR (ACTIVE)</div>
                        <div class="space-y-1.5 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Validasi Resolusi Citra (Min 800x800 px)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Normalisasi Warna (RGB to HSV/LAB) & Masking Kanopi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Ekstraksi Fitur Tekstur (GLCM) & Histogram Warna Kanopi</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-2 flex items-center gap-2.5">
                        <button type="button"
                                onclick="document.getElementById('svmForm').reset()"
                                class="w-1/3 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition cursor-pointer">
                            Reset Citra
                        </button>
                        <button type="submit"
                                class="w-2/3 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold shadow-xs flex items-center justify-center gap-2 transition cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Jalankan Klasifikasi SVM</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Diagnosis Results Panel (7 cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs space-y-5">
            {{-- Header Result --}}
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div>
                    <span class="text-[10px] font-bold tracking-widest text-emerald-700 uppercase">HASIL DIAGNOSIS TERVERIFIKASI</span>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Bedeng A-01</h2>
                </div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-[#e8f8f1] text-[#0e5f38] border border-[#d2f3e4] shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>PRIMA & SEHAT (98.4%)</span>
                </div>
            </div>

            {{-- Segmented Canopy Visual Preview with Overlays --}}
            <div class="relative rounded-2xl overflow-hidden border border-emerald-950/20 bg-emerald-950 shadow-inner group">
                <img src="/images/beds/bed_a01.jpg" alt="SVM Canopy Segmentation" class="w-full h-64 sm:h-72 object-cover opacity-90 group-hover:scale-102 transition duration-500">

                {{-- Contours & Bounding Box SVGs Overlay --}}
                <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 600 350">
                    <rect x="80" y="50" width="440" height="240" fill="none" stroke="#10b981" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.8"/>
                    <circle cx="300" cy="170" r="110" fill="#10b981" fill-opacity="0.12" stroke="#34d399" stroke-width="2"/>
                    <circle cx="210" cy="140" r="45" fill="#34d399" fill-opacity="0.15" stroke="#10b981" stroke-width="1.5"/>
                    <circle cx="380" cy="150" r="50" fill="#34d399" fill-opacity="0.15" stroke="#10b981" stroke-width="1.5"/>
                    <text x="90" y="70" class="text-[10px] font-mono fill-emerald-300 font-bold">CANOPY DENSITY: 98.4%</text>
                    <text x="310" y="70" class="text-[10px] font-mono fill-cyan-300 font-bold">CHLOROPHYLL INDEX: OPTIMAL</text>
                </svg>

                {{-- Floating Top Badges --}}
                <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10.5px] font-bold bg-black/60 text-white backdrop-blur-md border border-white/20">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>SVM CANOPY SEGMENTATION ACTIVE</span>
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-emerald-900/80 text-emerald-200 backdrop-blur-md border border-emerald-500/40">
                        RBF Kernel Score: 0.984
                    </span>
                </div>

                {{-- Bottom Bar Over Image --}}
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4 flex items-center justify-between text-white">
                    <div>
                        <span class="text-[9.5px] font-bold tracking-wider uppercase text-emerald-400 block">MASK AREA RATIO</span>
                        <span class="text-sm sm:text-base font-extrabold tracking-tight">84.2% Kanopi Hijau Sehat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="alert('Memperbesar citra kanopi segmentasi SVM')" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 text-white transition cursor-pointer backdrop-blur-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                            </svg>
                        </button>
                        <button type="button" onclick="alert('Mengunduh hasil segmentasi citra kanopi')" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 text-white transition cursor-pointer backdrop-blur-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4 Analysis Insight Cards (2x2 Grid) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                {{-- 1. Kondisi Morfologi Kanopi --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <div class="flex items-center gap-2 text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Kondisi Morfologi Kanopi</span>
                    </div>
                    <p class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">
                        Helai daun mekar simetris, klorofil merata tanpa bercak nekrotik. Tingkat kehijauan 98.4% memenuhi standar kualitas ekspor.
                    </p>
                </div>

                {{-- 2. Analisis Status Nutrisi --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-cyan-200 transition">
                    <div class="flex items-center gap-2 text-cyan-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Analisis Status Nutrisi</span>
                    </div>
                    <p class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">
                        EC: 1.4 mS/cm &bull; pH: 6.1 &mdash; Penyerapan Nitrogen (N) & Kalsium (Ca) seimbang. Tidak terdeteksi klorosis interveinal atau margin burn.
                    </p>
                </div>

                {{-- 3. Indikasi Penyakit / Hama --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-teal-200 transition">
                    <div class="flex items-center gap-2 text-teal-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Indikasi Penyakit / Hama</span>
                    </div>
                    <p class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">
                        Nihil / Risiko Rendah (&lt;1.2%). Bebas dari spora jamur embun tepung (Downy Mildew) maupun busuk lunak basah (Erwinia).
                    </p>
                </div>

                {{-- 4. Rekomendasi Tindakan --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-amber-200 transition">
                    <div class="flex items-center gap-2 text-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Rekomendasi Tindakan</span>
                    </div>
                    <p class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">
                        Pertahankan laju debit NFT (1.8 L/menit) dan sirkulasi kipas kanopi greenhouse 24&deg;C untuk menjaga transpirasi daun optimal.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table Section: Riwayat Hasil Analisis Diagnosis Citra --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="pb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Riwayat Hasil Analisis Diagnosis Citra</h2>
        </div>
        {{-- Filters Bar --}}
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
                           id="svmSearchInput"
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                <select id="svmBedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Bedeng</option>
                    <option value="Bedeng A-01">Bedeng A-01</option>
                    <option value="Bedeng A-02">Bedeng A-02</option>
                    <option value="Bedeng B-01">Bedeng B-01</option>
                    <option value="Bedeng B-02">Bedeng B-02</option>
                </select>

                <select id="svmStatusFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Status Diagnosis</option>
                    <option value="Sehat / Prima">Sehat / Prima</option>
                    <option value="Optimal">Optimal</option>
                    <option value="Klorosis Ringan">Klorosis Ringan</option>
                    <option value="Adaptif Baik">Adaptif Baik</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button"
                        id="tabSvmSemua"
                        onclick="setSvmTab('semua')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Semua (142)
                </button>
                <button type="button"
                        id="tabSvmPrima"
                        onclick="setSvmTab('prima')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Prima / Sehat (127)
                </button>
                <button type="button"
                        id="tabSvmNutrisi"
                        onclick="setSvmTab('nutrisi')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Perhatian Nutrisi (15)
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto -mx-5 sm:-mx-6 mt-2">
            <div class="inline-block min-w-full align-middle px-5 sm:px-6">
                <table class="min-w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th scope="col" class="py-3.5 pr-3">ID & TANGGAL DIAGNOSIS</th>
                            <th scope="col" class="py-3.5 px-3">LOKASI BEDENG</th>
                            <th scope="col" class="py-3.5 px-3">SAMPEL CITRA</th>
                            <th scope="col" class="py-3.5 px-3">KLASIFIKASI SVM</th>
                            <th scope="col" class="py-3.5 px-3">STATUS NUTRISI</th>
                            <th scope="col" class="py-3.5 px-3">INDIKASI PENYAKIT</th>
                            <th scope="col" class="py-3.5 pl-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="svmTableBody" class="divide-y divide-gray-100 text-gray-700">
                        {{-- Rendered via JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
            <div id="svmTableInfo">Menampilkan 4 dari 142 riwayat diagnosis</div>
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
                    3
                </button>
                <button type="button" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let svmActiveTab = 'semua';

const svmDiagnoses = [
    {
        id: '#SVM-2024-0089',
        tanggal: '24 Okt 2024 &bull; 09:15 WIB',
        bedeng: 'Bedeng A-01',
        img: '/images/beds/bed_a01.jpg',
        klasifikasi: 'Sehat / Prima (98.4%)',
        klasifikasiPill: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        nutrisi: 'Nutrisi Seimbang',
        nutrisiDetail: 'EC 1.4 mS/cm &bull; pH 6.1',
        penyakit: 'Nihil / Bebas Gejala',
        category: 'prima'
    },
    {
        id: '#SVM-2024-0088',
        tanggal: '23 Okt 2024 &bull; 14:30 WIB',
        bedeng: 'Bedeng A-02',
        img: '/images/beds/bed_a02.jpg',
        klasifikasi: 'Optimal (96.2%)',
        klasifikasiPill: 'bg-cyan-50 text-cyan-700 border-cyan-200',
        nutrisi: 'Kalsium Optimal',
        nutrisiDetail: 'EC 1.6 mS/cm &bull; pH 6.0',
        penyakit: 'Nihil / Bebas Gejala',
        category: 'prima'
    },
    {
        id: '#SVM-2024-0087',
        tanggal: '22 Okt 2024 &bull; 11:10 WIB',
        bedeng: 'Bedeng B-01',
        img: '/images/beds/bed_b01.jpg',
        klasifikasi: 'Klorosis Ringan (88.1%)',
        klasifikasiPill: 'bg-amber-50 text-amber-700 border-amber-200',
        nutrisi: 'Defisiensi N Terdeteksi',
        nutrisiDetail: 'EC 1.1 mS/cm (Rendah)',
        penyakit: 'Klorosis Daun Luar',
        category: 'nutrisi'
    },
    {
        id: '#SVM-2024-0086',
        tanggal: '21 Okt 2024 &bull; 16:45 WIB',
        bedeng: 'Bedeng B-02',
        img: '/images/beds/bed_b02.jpg',
        klasifikasi: 'Adaptif Baik (92.4%)',
        klasifikasiPill: 'bg-blue-50 text-blue-700 border-blue-200',
        nutrisi: 'EC Kalibrasi +1.8',
        nutrisiDetail: 'pH 6.3 (Perlu Koreksi)',
        penyakit: 'Indikasi Awal Tipburn (Ca)',
        category: 'nutrisi'
    }
];

function renderSvmTable() {
    const tbody = document.getElementById('svmTableBody');
    const searchVal = document.getElementById('svmSearchInput').value.toLowerCase().trim();
    const bedengVal = document.getElementById('svmBedengFilter').value;
    const statusVal = document.getElementById('svmStatusFilter').value;

    const filtered = svmDiagnoses.filter(item => {
        if (svmActiveTab === 'prima' && item.category !== 'prima') return false;
        if (svmActiveTab === 'nutrisi' && item.category !== 'nutrisi') return false;
        if (searchVal && !item.id.toLowerCase().includes(searchVal) && !item.bedeng.toLowerCase().includes(searchVal) && !item.klasifikasi.toLowerCase().includes(searchVal)) {
            return false;
        }
        if (bedengVal && item.bedeng !== bedengVal) return false;
        if (statusVal && !item.klasifikasi.toLowerCase().includes(statusVal.toLowerCase())) return false;
        return true;
    });

    tbody.innerHTML = '';

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400 text-xs">
                    Tidak ada riwayat analisis SVM yang sesuai filter.
                </td>
            </tr>
        `;
        return;
    }

    filtered.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/70 transition group';
        tr.innerHTML = `
            <td class="py-4 pr-3">
                <div class="font-bold text-gray-900">${item.id}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">${item.tanggal}</div>
            </td>
            <td class="py-4 px-3">
                <div class="font-bold text-gray-900">${item.bedeng}</div>
            </td>
            <td class="py-4 px-3">
                <img src="${item.img}" alt="Sample" class="w-11 h-11 rounded-lg object-cover border border-gray-200">
            </td>
            <td class="py-4 px-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ${item.klasifikasiPill}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    <span>${item.klasifikasi}</span>
                </span>
            </td>
            <td class="py-4 px-3">
                <div class="font-bold text-gray-900 text-xs">${item.nutrisi}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">${item.nutrisiDetail}</div>
            </td>
            <td class="py-4 px-3">
                <div class="text-xs ${item.penyakit.includes('Nihil') ? 'text-gray-500' : 'text-amber-600 font-semibold'}">${item.penyakit}</div>
            </td>
            <td class="py-4 pl-3 text-right">
                <div class="inline-flex items-center gap-1.5 justify-end">
                    <button type="button" onclick="alert('Membuka visualisasi GLCM & Histogram SVM untuk ' + '${item.id}')" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Lihat Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="alert('Mengunduh sertifikat diagnosis ' + '${item.id}')" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Unduh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                    <button type="button" onclick="deleteSvmRow('${item.id}')" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function setSvmTab(tab) {
    svmActiveTab = tab;
    const tSemua = document.getElementById('tabSvmSemua');
    const tPrima = document.getElementById('tabSvmPrima');
    const tNutrisi = document.getElementById('tabSvmNutrisi');

    [tSemua, tPrima, tNutrisi].forEach(b => {
        b.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer";
    });

    if (tab === 'semua') {
        tSemua.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
    } else if (tab === 'prima') {
        tPrima.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
    } else {
        tNutrisi.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
    }

    renderSvmTable();
}

function handleRunSvm(e) {
    e.preventDefault();
    const bed = document.getElementById('svmBedeng').value;
    alert(`Menjalankan ekstraksi GLCM & klasifikasi RBF-SVM untuk ${bed}... Hasil: PRIMA & SEHAT (98.4%) - RBF Kernel Score: 0.984`);
}

function handleSvmImageUploaded(e) {
    if (e.target.files && e.target.files[0]) {
        alert('Citra ' + e.target.files[0].name + ' berhasil dimuat ke buffer pipeline SVM.');
    }
}

function deleteSvmRow(id) {
    if (confirm('Hapus log diagnosis ' + id + '?')) {
        const idx = svmDiagnoses.findIndex(s => s.id === id);
        if (idx !== -1) {
            svmDiagnoses.splice(idx, 1);
            renderSvmTable();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('svmSearchInput').addEventListener('input', renderSvmTable);
    document.getElementById('svmBedengFilter').addEventListener('change', renderSvmTable);
    document.getElementById('svmStatusFilter').addEventListener('change', renderSvmTable);
    renderSvmTable();
});
</script>
@endpush
