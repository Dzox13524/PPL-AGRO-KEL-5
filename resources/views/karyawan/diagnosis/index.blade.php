@extends('layouts.karyawan', ['active' => 'diagnosis'])

@section('title', 'Diagnosis Citra Selada & Kesehatan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Diagnosis Citra Selada & Kesehatan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Deteksi penyakit daun & analisis kondisi nutrisi otomatis dari foto tanaman</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    onclick="exportDiagnosisCsv()"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#eef4f8] hover:bg-[#e2edf5] text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer active:scale-95 border border-slate-200/70 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export Laporan Diagnosis</span>
            </button>
            <button type="button"
                    onclick="focusDiagnosisUpload()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Diagnosis Citra Baru</span>
            </button>
        </div>
    </div>

    @if(!empty($apiError))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs sm:text-sm text-amber-800 flex items-center justify-between gap-3">
            <span>{{ $apiError }}</span>
            <span class="font-semibold text-[11px] uppercase tracking-wider">Periksa Backend API</span>
        </div>
    @endif

    {{-- Main Two-Column Layout (Upload & Diagnosis Result) --}}
    <div id="uploadCitraSection" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Upload & Parameters (5 cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Unggah Citra Kanopi & Parameter</h2>
                <form id="svmForm" onsubmit="handleRunSvm(event)" class="mt-5 space-y-4">
                    <div id="svmFormError" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700"></div>

                    {{-- Upload Drag & Drop Area --}}
                    <div id="svmDropzone"
                         class="border-2 border-dashed border-emerald-200/90 rounded-2xl p-5 bg-emerald-50/20 text-center hover:bg-emerald-50/40 transition cursor-pointer"
                         onclick="document.getElementById('svmFileInput').click()">
                        <div id="svmUploadPreviewWrap" class="hidden mb-3">
                            <img id="svmUploadPreviewImg" src="/images/beds/bed_a01.jpg" alt="Preview Upload" class="w-28 h-28 mx-auto rounded-xl object-cover border border-emerald-200 shadow-2xs">
                        </div>
                        <div id="svmUploadIcon" class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-[#0e5f38] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <h3 id="svmUploadTitle" class="text-sm font-bold text-gray-900 mt-3">Pilih atau Seret Foto Kanopi Selada</h3>
                        <p id="svmFileNameLabel" class="text-xs text-gray-500 mt-1">Format didukung: JPG, JPEG, PNG &bull; Ukuran Maks: 5 MB</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <span>Klik untuk memilih file foto dari perangkat Anda</span>
                        </div>
                        <input type="file" id="svmFileInput" class="hidden" accept=".jpg,.jpeg,.png" onchange="handleSvmImageUploaded(event)">
                    </div>

                    {{-- Form Fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-7">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Batch / Tanaman</label>
                            <select id="svmBedeng" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                                @forelse($plants as $p)
                                    <option value="{{ $p['id'] }}">
                                        {{ $p['kode_tanaman'] }} — {{ $p['nama'] }}{{ !empty($p['varietas']) ? ' (' . $p['varietas'] . ')' : '' }}
                                    </option>
                                @empty
                                    <option value="">Belum ada tanaman</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Bagian Sampel</label>
                            <select id="svmPlantPart" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                                <option value="daun">Daun</option>
                                <option value="akar">Akar</option>
                                <option value="seluruh_tanaman">Seluruh Tanaman</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Waktu Pengambilan</label>
                            <input type="text" id="svmTanggal" readonly value="" class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-700">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Petugas Lapangan</label>
                            <div class="relative">
                                <input type="text" id="svmPetugas" readonly value="{{ $currentUser['nama'] ?? ($currentUser['username'] ?? 'Staf') }}" class="w-full pl-3 pr-8 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-700">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Catatan Gejala / Lapangan (Opsional)</label>
                        <input type="text" id="svmCatatan" placeholder="Contoh: Tepi daun muda sedikit mengering..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>

                    {{-- Pipeline Feature Extraction Checklist --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-2">
                        <div class="text-[10.5px] font-bold text-emerald-800 uppercase tracking-wider">PIPELINE DIAGNOSIS CITRA (AI-2)</div>
                        <div class="space-y-1.5 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Validasi Citra & Ekstraksi Warna Daun (HSV)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Klasifikasi: Healthy, Tip Burn, Leaf Blight, Downy Mildew</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Pemetaan Basis Pengetahuan Penyakit & Nutrisi DB</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-2 flex items-center gap-2.5">
                        <button type="button"
                                onclick="resetSvmForm()"
                                class="w-1/3 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition cursor-pointer">
                            Reset Citra
                        </button>
                        <button type="submit"
                                id="btnRunSvm"
                                class="w-2/3 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold shadow-xs flex items-center justify-center gap-2 transition cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Jalankan Diagnosis & Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Diagnosis Results Panel (7 cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs space-y-5">
            {{-- Header Result --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-gray-100">
                <div>
                    <span id="diagResKicker" class="text-[10px] font-bold tracking-widest text-emerald-700 uppercase">HASIL DIAGNOSIS TERVERIFIKASI</span>
                    <h2 id="diagResPlant" class="text-lg font-bold text-gray-900 tracking-tight">-</h2>
                    <p id="diagResMeta" class="text-[11px] text-gray-400 mt-0.5">-</p>
                </div>
                <div id="diagResBadge" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-[#e8f8f1] text-[#0e5f38] border border-[#d2f3e4] shadow-xs self-start sm:self-auto">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>MENUNGGU DATA</span>
                </div>
            </div>

            {{-- Segmented Canopy Visual Preview with Overlays --}}
            <div class="relative rounded-2xl overflow-hidden border border-emerald-950/20 bg-emerald-950 shadow-inner group">
                <img id="diagResImage" src="/images/beds/bed_a01.jpg" onerror="this.onerror=null;this.src='/images/beds/bed_a01.jpg';" alt="Diagnosis Canopy Preview" class="w-full h-64 sm:h-72 object-cover opacity-90 group-hover:scale-102 transition duration-500">

                {{-- Contours & Bounding Box SVGs Overlay --}}
                <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 600 350">
                    <rect x="80" y="50" width="440" height="240" fill="none" stroke="#10b981" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.8"/>
                    <circle cx="300" cy="170" r="110" fill="#10b981" fill-opacity="0.10" stroke="#34d399" stroke-width="2"/>
                    <text id="diagOverlayClass" x="92" y="72" class="text-[11px] font-mono fill-emerald-300 font-bold">KELAS: -</text>
                    <text id="diagOverlayConf" x="340" y="72" class="text-[11px] font-mono fill-cyan-300 font-bold">CONFIDENCE: -</text>
                </svg>

                {{-- Floating Top Badges --}}
                <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10.5px] font-bold bg-black/60 text-white backdrop-blur-md border border-white/20">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="diagBadgeModel">AI-2 CANOPY DIAGNOSIS</span>
                    </span>
                    <span id="diagBadgeVersion" class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-emerald-900/80 text-emerald-200 backdrop-blur-md border border-emerald-500/40">
                        -
                    </span>
                </div>

                {{-- Bottom Bar Over Image --}}
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4 flex items-center justify-between text-white">
                    <div>
                        <span class="text-[9.5px] font-bold tracking-wider uppercase text-emerald-400 block">KLASIFIKASI & BAGIAN TANAMAN</span>
                        <span id="diagResSummaryTitle" class="text-sm sm:text-base font-extrabold tracking-tight">-</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openActiveDiagnosisModal()" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 text-white transition cursor-pointer backdrop-blur-xs" title="Lihat Detail Lengkap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4 Analysis Insight Cards (2x2 Grid) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                {{-- 1. Kondisi & Keterangan --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <div class="flex items-center gap-2 text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Kondisi Kanopi & Ringkasan</span>
                    </div>
                    <p id="diagCardMorphology" class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">-</p>
                </div>

                {{-- 2. Analisis Status Nutrisi --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-cyan-200 transition">
                    <div class="flex items-center gap-2 text-cyan-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Analisis Status Nutrisi</span>
                    </div>
                    <p id="diagCardNutrition" class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">-</p>
                </div>

                {{-- 3. Informasi Penyakit & Gejala --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-teal-200 transition">
                    <div class="flex items-center gap-2 text-teal-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Informasi Penyakit & Gejala</span>
                    </div>
                    <p id="diagCardDisease" class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">-</p>
                </div>

                {{-- 4. Rekomendasi Tindakan --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-amber-200 transition">
                    <div class="flex items-center gap-2 text-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-900">Rekomendasi Penanganan</span>
                    </div>
                    <p id="diagCardRecommendation" class="text-[11px] text-gray-600 mt-1.5 leading-relaxed">-</p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table Section: Riwayat Hasil Analisis Diagnosis Citra --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="pb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Riwayat Hasil Analisis Diagnosis Citra (`diagnosis_records`)</h2>
        </div>

        {{-- Filters Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-5 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           id="svmSearchInput"
                           placeholder="Cari batch, kelas diagnosis, nutrisi..."
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                <select id="svmBedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Batch Tanaman</option>
                    @foreach($plants as $p)
                        <option value="{{ $p['id'] }}">{{ $p['kode_tanaman'] }} — {{ $p['nama'] }}</option>
                    @endforeach
                </select>

                <select id="svmStatusFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Kelas Diagnosis</option>
                    <option value="Healthy">Healthy (Sehat)</option>
                    <option value="Tip Burn">Tip Burn</option>
                    <option value="Leaf Blight">Leaf Blight</option>
                    <option value="Downy Mildew">Downy Mildew</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button"
                        id="tabSvmSemua"
                        onclick="setSvmTab('semua')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Semua (0)
                </button>
                <button type="button"
                        id="tabSvmPrima"
                        onclick="setSvmTab('prima')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Healthy / Sehat (0)
                </button>
                <button type="button"
                        id="tabSvmNutrisi"
                        onclick="setSvmTab('nutrisi')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Terindikasi Masalah (0)
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
                            <th scope="col" class="py-3.5 px-3">BATCH TANAMAN</th>
                            <th scope="col" class="py-3.5 px-3">SAMPEL CITRA</th>
                            <th scope="col" class="py-3.5 px-3">KLASIFIKASI & CONFIDENCE</th>
                            <th scope="col" class="py-3.5 px-3">STATUS NUTRISI</th>
                            <th scope="col" class="py-3.5 px-3">INDIKASI & PETUGAS</th>
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
            <div id="svmTableInfo">Menampilkan 0 dari 0 riwayat diagnosis</div>
            <div class="flex items-center gap-1 self-center sm:self-auto">
                <button type="button" id="btnSvmPrev" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 text-gray-600 transition cursor-pointer">
                    Sebelumnya
                </button>
                <div id="svmPageButtons" class="flex items-center gap-1"></div>
                <button type="button" id="btnSvmNext" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 text-gray-600 transition cursor-pointer">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Detail Diagnosis Modal --}}
<div id="diagDetailModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeDiagDetailModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 id="diagDetailTitle" class="text-base font-bold text-gray-900">Detail Diagnosis Kesehatan</h3>
                <button type="button" onclick="closeDiagDetailModal()" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="diagDetailBody" class="mt-4 space-y-3 text-xs"></div>
            <div class="mt-5 flex justify-end">
                <button type="button" onclick="closeDiagDetailModal()" class="px-4 py-2 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="diagToast" class="fixed bottom-6 right-6 z-50 translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div class="bg-[#0e5f38] text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 text-xs sm:text-sm font-medium border border-emerald-400/30">
        <span id="diagToastMessage">Berhasil.</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const apiHost = window.location.hostname === '127.0.0.1' ? '127.0.0.1' : 'localhost';
    const BE_ORIGIN = `http://${apiHost}:8000`;
    const BE_API_URL = `${BE_ORIGIN}/api/v1`;

    let plantsData = @json($plants ?? []);
    let diagnosesData = @json($diagnoses ?? []);
    const diseasesKb = @json($diseases ?? []);
    const nutritionKb = @json($nutritionConditions ?? []);
    let activeDiagnosis = diagnosesData[0] || null;
    let svmActiveTab = 'semua';
    let currentPage = 1;
    const perPage = 8;
    let toastTimer;

    function getAuthCookie(name) {
        const prefix = `${name}=`;
        const row = document.cookie.split('; ').find(r => r.startsWith(prefix));
        return row ? decodeURIComponent(row.slice(prefix.length)) : '';
    }

    function clearAuthCookies() {
        ['hidara_token', 'hidara_role', 'hidara_user'].forEach(name => {
            document.cookie = `${name}=; path=/; max-age=0; SameSite=Lax`;
        });
    }

    async function apiRequest(path, options = {}) {
        const token = getAuthCookie('hidara_token');
        if (!token) {
            window.location.href = '/login';
            return null;
        }
        const headers = {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...(options.headers || {}),
        };
        if (options.body && !(options.body instanceof FormData) && !headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }
        const res = await fetch(`${BE_API_URL}${path}`, { ...options, headers });
        if (res.status === 401) {
            clearAuthCookies();
            window.location.href = '/login';
            return null;
        }
        const json = await res.json().catch(() => ({}));
        return { ok: res.ok, status: res.status, json };
    }

    function extractApiError(json, fallback) {
        if (!json) return fallback;
        if (typeof json.message === 'string' && json.message) return json.message;
        if (Array.isArray(json.errors) && json.errors.length > 0) {
            return json.errors.map(e => e.msg || e.message || JSON.stringify(e)).join(', ');
        }
        if (typeof json.detail === 'string') return json.detail;
        return fallback;
    }

    function esc(val) {
        return String(val ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function fmtPct(conf) {
        const n = Number(conf);
        if (!Number.isFinite(n)) return '-';
        return `${(n <= 1 ? n * 100 : n).toFixed(1)}%`;
    }

    function fmtDate(iso) {
        if (!iso) return '-';
        const dt = new Date(iso);
        if (Number.isNaN(dt.getTime())) return String(iso);
        return dt.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function resolveImageUrl(item) {
        if (item?.image_url) {
            try {
                const parsed = new URL(item.image_url);
                return `${BE_ORIGIN}${parsed.pathname}`;
            } catch (_) {
                return item.image_url;
            }
        }
        if (item?.image_path) {
            return `${BE_ORIGIN}/uploads/${String(item.image_path).replace(/^\/+/, '')}`;
        }
        return '/images/beds/bed_a01.jpg';
    }

    function showToast(msg) {
        const t = document.getElementById('diagToast');
        const m = document.getElementById('diagToastMessage');
        if (!t || !m) return;
        clearTimeout(toastTimer);
        m.textContent = msg;
        t.classList.remove('translate-y-20', 'opacity-0');
        t.classList.add('translate-y-0', 'opacity-100');
        toastTimer = setTimeout(() => {
            t.classList.remove('translate-y-0', 'opacity-100');
            t.classList.add('translate-y-20', 'opacity-0');
        }, 3200);
    }

    function classPill(label) {
        switch (label) {
            case 'Healthy':
                return 'bg-emerald-50 text-emerald-700 border-emerald-200';
            case 'Tip Burn':
                return 'bg-amber-50 text-amber-700 border-amber-200';
            case 'Leaf Blight':
                return 'bg-rose-50 text-rose-700 border-rose-200';
            case 'Downy Mildew':
                return 'bg-purple-50 text-purple-700 border-purple-200';
            default:
                return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        }
    }

    function renderResultPanel(item) {
        activeDiagnosis = item || null;
        const setEl = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };

        if (!item) {
            setEl('diagResPlant', 'Belum Ada Riwayat Diagnosis');
            setEl('diagResMeta', 'Unggah foto daun selada di sebelah kiri lalu klik "Jalankan Diagnosis & Simpan".');
            setEl('diagOverlayClass', 'KELAS: -');
            setEl('diagOverlayConf', 'CONFIDENCE: -');
            setEl('diagBadgeVersion', '-');
            setEl('diagResSummaryTitle', 'Belum ada analisis tersimpan');
            setEl('diagCardMorphology', '-');
            setEl('diagCardNutrition', '-');
            setEl('diagCardDisease', '-');
            setEl('diagCardRecommendation', '-');
            return;
        }

        const isHealthy = item.kelas_diagnosis === 'Healthy';
        const confStr = fmtPct(item.confidence);
        const kode = item.kode_tanaman || `Plant #${item.plant_id}`;
        const nama = item.nama_tanaman || 'Selada';

        setEl('diagResPlant', `${kode} — ${nama}${item.varietas ? ' (' + item.varietas + ')' : ''}`);
        setEl('diagResMeta', `#DIAG-${item.id} • ${fmtDate(item.tanggal_diagnosis)} • Oleh ${item.diagnosed_by_nama || 'Staf'}`);

        const badgeEl = document.getElementById('diagResBadge');
        if (badgeEl) {
            badgeEl.className = isHealthy
                ? 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-[#e8f8f1] text-[#0e5f38] border border-[#d2f3e4] shadow-xs self-start sm:self-auto'
                : 'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-200 shadow-xs self-start sm:self-auto';
            badgeEl.innerHTML = `<span class="w-2 h-2 rounded-full ${isHealthy ? 'bg-emerald-500' : 'bg-amber-500'}"></span><span>${esc(String(item.kelas_diagnosis || '-').toUpperCase())} (${esc(confStr)})</span>`;
        }

        const imgEl = document.getElementById('diagResImage');
        if (imgEl) imgEl.src = resolveImageUrl(item);

        setEl('diagOverlayClass', `KELAS: ${item.kelas_diagnosis || '-'}`);
        setEl('diagOverlayConf', `CONFIDENCE: ${confStr}`);
        setEl('diagBadgeModel', `${item.model || 'SVM'} CANOPY DIAGNOSIS`);
        setEl('diagBadgeVersion', item.versi_model || 'hsv-color-heuristic-v1');
        setEl('diagResSummaryTitle', `${item.diagnosis_result || item.kelas_diagnosis} • Bagian: ${String(item.plant_part || 'daun').replace(/_/g, ' ')}`);

        const nutRel = item.nutrition_condition_rel;
        const disRel = item.disease_info_rel;
        const nutText = nutRel
            ? `${nutRel.nama_kondisi}${nutRel.unsur_nutrisi ? ' (' + nutRel.unsur_nutrisi + ')' : ''}. ${nutRel.deskripsi || ''} ${nutRel.penanganan_awal || ''}`
            : (item.nutrient_condition || 'Normal - tidak terdeteksi gangguan nutrisi.');
        const disText = disRel
            ? `${disRel.deskripsi || ''} ${disRel.gejala ? 'Gejala: ' + disRel.gejala : ''}`
            : (item.disease_info || 'Tidak ada catatan penyakit.');
        const recText = item.rekomendasi || disRel?.penanganan_awal || 'Pertahankan pemeliharaan rutin.';

        setEl('diagCardMorphology', item.keterangan || `${item.diagnosis_result || item.kelas_diagnosis} terdeteksi dengan keyakinan ${confStr}.`);
        setEl('diagCardNutrition', nutText.trim());
        setEl('diagCardDisease', disText.trim());
        setEl('diagCardRecommendation', recText.trim());
    }

    function populatePlantSelects() {
        const formSel = document.getElementById('svmBedeng');
        const filterSel = document.getElementById('svmBedengFilter');
        if (formSel) {
            const cur = formSel.value;
            formSel.innerHTML = plantsData.length
                ? plantsData.map(p => `<option value="${p.id}" ${String(p.id) === String(cur) ? 'selected' : ''}>${esc(p.kode_tanaman)} — ${esc(p.nama)}${p.varietas ? ' (' + esc(p.varietas) + ')' : ''}</option>`).join('')
                : '<option value="">Belum ada tanaman</option>';
        }
        if (filterSel) {
            const cur = filterSel.value;
            filterSel.innerHTML = '<option value="">Semua Batch Tanaman</option>' +
                plantsData.map(p => `<option value="${p.id}" ${String(p.id) === String(cur) ? 'selected' : ''}>${esc(p.kode_tanaman)} — ${esc(p.nama)}</option>`).join('');
        }
    }

    function updateTabCounts() {
        const healthyCount = diagnosesData.filter(d => d.kelas_diagnosis === 'Healthy').length;
        const issueCount = diagnosesData.length - healthyCount;
        const tSemua = document.getElementById('tabSvmSemua');
        const tPrima = document.getElementById('tabSvmPrima');
        const tNutrisi = document.getElementById('tabSvmNutrisi');
        if (tSemua) tSemua.textContent = `Semua (${diagnosesData.length})`;
        if (tPrima) tPrima.textContent = `Healthy / Sehat (${healthyCount})`;
        if (tNutrisi) tNutrisi.textContent = `Terindikasi Masalah (${issueCount})`;
    }

    function getFilteredDiagnoses() {
        const q = (document.getElementById('svmSearchInput')?.value || '').toLowerCase().trim();
        const plantFilter = document.getElementById('svmBedengFilter')?.value || '';
        const classFilter = document.getElementById('svmStatusFilter')?.value || '';

        return diagnosesData.filter(item => {
            const isHealthy = item.kelas_diagnosis === 'Healthy';
            if (svmActiveTab === 'prima' && !isHealthy) return false;
            if (svmActiveTab === 'nutrisi' && isHealthy) return false;
            if (plantFilter && String(item.plant_id) !== String(plantFilter)) return false;
            if (classFilter && String(item.kelas_diagnosis) !== String(classFilter)) return false;
            if (q) {
                const hay = `#diag-${item.id} ${item.kode_tanaman || ''} ${item.nama_tanaman || ''} ${item.kelas_diagnosis || ''} ${item.diagnosis_result || ''} ${item.nutrient_condition || ''} ${item.diagnosed_by_nama || ''}`.toLowerCase();
                if (!hay.includes(q)) return false;
            }
            return true;
        });
    }

    function renderSvmTable() {
        updateTabCounts();
        const tbody = document.getElementById('svmTableBody');
        const infoEl = document.getElementById('svmTableInfo');
        const prevBtn = document.getElementById('btnSvmPrev');
        const nextBtn = document.getElementById('btnSvmNext');
        const pageBtns = document.getElementById('svmPageButtons');
        if (!tbody) return;

        const filtered = getFilteredDiagnoses();
        const total = filtered.length;
        const totalPages = Math.ceil(total / perPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * perPage;
        const items = filtered.slice(start, start + perPage);

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-gray-400 text-xs">Tidak ada riwayat diagnosis yang sesuai filter.</td></tr>';
        } else {
            tbody.innerHTML = items.map(item => {
                const imgSrc = resolveImageUrl(item);
                const nutName = item.nutrition_condition_rel?.nama_kondisi || item.nutrient_condition || 'Normal';
                const nutUnsur = item.nutrition_condition_rel?.unsur_nutrisi || '-';
                return `
                    <tr class="hover:bg-gray-50/70 transition group">
                        <td class="py-4 pr-3">
                            <div class="font-bold text-gray-900">#DIAG-${item.id}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">${esc(fmtDate(item.tanggal_diagnosis))}</div>
                        </td>
                        <td class="py-4 px-3">
                            <div class="font-bold text-gray-900">${esc(item.kode_tanaman || 'Plant #' + item.plant_id)}</div>
                            <div class="text-[11px] text-gray-500 mt-0.5">${esc(item.nama_tanaman || 'Selada')} • ${esc(String(item.plant_part || 'daun').replace(/_/g, ' '))}</div>
                        </td>
                        <td class="py-4 px-3">
                            <img src="${esc(imgSrc)}" onerror="this.onerror=null;this.src='/images/beds/bed_a01.jpg';" alt="Sampel" class="w-11 h-11 rounded-lg object-cover border border-gray-200 cursor-pointer" onclick="selectDiagnosisRow(${item.id})">
                        </td>
                        <td class="py-4 px-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ${classPill(item.kelas_diagnosis)}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <span>${esc(item.kelas_diagnosis)} (${esc(fmtPct(item.confidence))})</span>
                            </span>
                            <div class="text-[10.5px] text-gray-400 mt-1">${esc(item.model || 'SVM')}</div>
                        </td>
                        <td class="py-4 px-3">
                            <div class="font-bold text-gray-900 text-xs">${esc(nutName)}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">${esc(nutUnsur)}</div>
                        </td>
                        <td class="py-4 px-3">
                            <div class="text-xs font-semibold ${item.kelas_diagnosis === 'Healthy' ? 'text-emerald-700' : 'text-amber-700'}">${esc(item.diagnosis_result || item.kelas_diagnosis)}</div>
                            <div class="text-[10.5px] text-gray-400 mt-0.5">Oleh: ${esc(item.diagnosed_by_nama || 'Staf')}</div>
                        </td>
                        <td class="py-4 pl-3 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <button type="button" onclick="viewDiagnosisDetail(${item.id})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition cursor-pointer" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" onclick="deleteSvmRow(${item.id})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        if (infoEl) infoEl.textContent = `Menampilkan ${items.length} dari ${total} riwayat diagnosis`;
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages;

        if (pageBtns) {
            pageBtns.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = i;
                btn.className = i === currentPage
                    ? 'px-3 py-1.5 rounded-lg bg-[#0e5f38] text-white font-semibold transition cursor-pointer'
                    : 'px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer';
                btn.addEventListener('click', () => { currentPage = i; renderSvmTable(); });
                pageBtns.appendChild(btn);
            }
        }
    }

    async function syncFromApi() {
        try {
            const [plRes, histRes] = await Promise.all([
                apiRequest('/plants?per_page=100', { method: 'GET' }),
                apiRequest('/diagnosis/history?per_page=100', { method: 'GET' }),
            ]);
            if (plRes?.ok && Array.isArray(plRes.json?.data)) {
                plantsData = plRes.json.data;
                populatePlantSelects();
            }
            if (histRes?.ok && Array.isArray(histRes.json?.data)) {
                diagnosesData = histRes.json.data;
                renderSvmTable();
                renderResultPanel(diagnosesData[0] || null);
            }
        } catch (_) {}
    }

    window.focusDiagnosisUpload = function () {
        document.getElementById('uploadCitraSection')?.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('svmBedeng')?.focus();
    };

    window.setSvmTab = function (tab) {
        svmActiveTab = tab;
        currentPage = 1;
        const map = { semua: 'tabSvmSemua', prima: 'tabSvmPrima', nutrisi: 'tabSvmNutrisi' };
        Object.entries(map).forEach(([k, id]) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.className = k === tab
                ? 'px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer'
                : 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer';
        });
        renderSvmTable();
    };

    window.handleSvmImageUploaded = function (e) {
        const file = e.target.files?.[0];
        if (!file) return;
        document.getElementById('svmUploadTitle').textContent = file.name;
        document.getElementById('svmFileNameLabel').textContent = `Ukuran ${(file.size / 1024).toFixed(0)} KB • Siap dianalisis`;
        const reader = new FileReader();
        reader.onload = ev => {
            if (ev.target?.result) {
                document.getElementById('svmUploadPreviewImg').src = ev.target.result;
                document.getElementById('svmUploadPreviewWrap').classList.remove('hidden');
                document.getElementById('svmUploadIcon').classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    };

    window.resetSvmForm = function () {
        document.getElementById('svmFileInput').value = '';
        document.getElementById('svmCatatan').value = '';
        document.getElementById('svmFormError').classList.add('hidden');
        document.getElementById('svmUploadPreviewWrap').classList.add('hidden');
        document.getElementById('svmUploadIcon').classList.remove('hidden');
        document.getElementById('svmUploadTitle').textContent = 'Pilih atau Seret Foto Kanopi Selada';
        document.getElementById('svmFileNameLabel').textContent = 'Format didukung: JPG, JPEG, PNG • Ukuran Maks: 5 MB';
        document.getElementById('svmTanggal').value = fmtDate(new Date().toISOString());
    };

    window.handleRunSvm = async function (e) {
        e.preventDefault();
        const errBox = document.getElementById('svmFormError');
        errBox.classList.add('hidden');

        const plantId = document.getElementById('svmBedeng').value;
        if (!plantId) {
            errBox.textContent = 'Pilih batch tanaman terlebih dahulu.';
            errBox.classList.remove('hidden');
            return;
        }

        const btn = document.getElementById('btnRunSvm');
        btn.disabled = true;
        btn.querySelector('span').textContent = 'Menganalisis Citra...';

        try {
            let fileObj = document.getElementById('svmFileInput').files?.[0] || null;
            if (!fileObj) {
                const sampleRes = await fetch('/images/beds/bed_a01.jpg');
                const blob = await sampleRes.blob();
                fileObj = new File([blob], 'sample_canopy.jpg', { type: 'image/jpeg' });
            }

            const form = new FormData();
            form.append('file', fileObj);
            form.append('plant_id', plantId);
            form.append('plant_part', document.getElementById('svmPlantPart').value || 'daun');
            form.append('sumber_data', 'upload_aplikasi');
            const catatan = document.getElementById('svmCatatan').value.trim();
            if (catatan) form.append('keterangan', catatan);

            const res = await apiRequest('/diagnosis/predict', {
                method: 'POST',
                body: form,
            });
            if (!res) return;

            if (res.ok && res.json?.data) {
                const detail = res.json.data;
                diagnosesData = [detail, ...diagnosesData.filter(d => Number(d.id) !== Number(detail.id))];
                renderResultPanel(detail);
                renderSvmTable();
                showToast(`Diagnosis #DIAG-${detail.id} (${detail.kelas_diagnosis}) berhasil disimpan ke database!`);
            } else {
                errBox.textContent = extractApiError(res.json, 'Gagal memproses diagnosis citra.');
                errBox.classList.remove('hidden');
            }
        } catch (_) {
            errBox.textContent = 'Tidak dapat terhubung ke Backend API (port 8000).';
            errBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'Jalankan Diagnosis & Simpan';
        }
    };

    window.selectDiagnosisRow = function (id) {
        const item = diagnosesData.find(d => Number(d.id) === Number(id));
        if (item) renderResultPanel(item);
    };

    function showModalForItem(item) {
        if (!item) return;
        const nutRel = item.nutrition_condition_rel;
        const disRel = item.disease_info_rel;
        document.getElementById('diagDetailTitle').textContent = `Detail Diagnosis #DIAG-${item.id} (${item.kelas_diagnosis})`;
        document.getElementById('diagDetailBody').innerHTML = `
            <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-900">
                <img src="${esc(resolveImageUrl(item))}" onerror="this.onerror=null;this.src='/images/beds/bed_a01.jpg';" alt="Diagnosis" class="w-full h-48 object-cover">
            </div>
            <div class="p-3.5 bg-gray-50 rounded-xl space-y-2">
                <div class="flex justify-between"><span class="text-gray-500">Tanaman:</span> <span class="font-bold text-gray-900">${esc(item.kode_tanaman || '-')} — ${esc(item.nama_tanaman || 'Selada')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Waktu & Petugas:</span> <span class="font-medium text-gray-800">${esc(fmtDate(item.tanggal_diagnosis))} • ${esc(item.diagnosed_by_nama || '-')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kelas & Confidence:</span> <span class="font-bold text-emerald-700">${esc(item.diagnosis_result || item.kelas_diagnosis)} (${esc(fmtPct(item.confidence))})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status Nutrisi:</span> <span class="font-semibold text-gray-800">${esc(nutRel?.nama_kondisi || item.nutrient_condition || 'Normal')}</span></div>
                <div class="pt-2 border-t border-gray-200"><span class="text-gray-500 block mb-0.5">Info Penyakit / Gejala:</span><span class="text-gray-800 leading-relaxed block">${esc(disRel ? (disRel.deskripsi + ' Gejala: ' + (disRel.gejala || '-')) : (item.disease_info || item.keterangan || '-'))}</span></div>
                <div class="pt-2 border-t border-gray-200"><span class="text-gray-500 block mb-0.5">Rekomendasi Penanganan:</span><span class="text-gray-800 leading-relaxed block">${esc(item.rekomendasi || disRel?.penanganan_awal || '-')}</span></div>
            </div>`;
        document.getElementById('diagDetailModal').classList.remove('hidden');
    }

    window.openActiveDiagnosisModal = function () {
        if (activeDiagnosis) showModalForItem(activeDiagnosis);
    };

    window.viewDiagnosisDetail = async function (id) {
        const existing = diagnosesData.find(d => Number(d.id) === Number(id));
        if (existing) renderResultPanel(existing);
        try {
            const res = await apiRequest(`/diagnosis/${id}`, { method: 'GET' });
            if (res?.ok && res.json?.data) {
                renderResultPanel(res.json.data);
                showModalForItem(res.json.data);
                return;
            }
        } catch (_) {}
        if (existing) showModalForItem(existing);
    };

    window.closeDiagDetailModal = function () {
        document.getElementById('diagDetailModal').classList.add('hidden');
    };

    window.deleteSvmRow = async function (id) {
        if (!confirm(`Hapus riwayat diagnosis #DIAG-${id} dari database?`)) return;
        try {
            const res = await apiRequest(`/diagnosis/${id}`, { method: 'DELETE' });
            if (!res) return;
            if (res.ok) {
                diagnosesData = diagnosesData.filter(d => Number(d.id) !== Number(id));
                renderSvmTable();
                renderResultPanel(diagnosesData[0] || null);
                showToast(`Riwayat diagnosis #DIAG-${id} berhasil dihapus.`);
            } else {
                showToast(extractApiError(res.json, 'Gagal menghapus riwayat diagnosis.'));
            }
        } catch (_) {
            showToast('Tidak dapat terhubung ke Backend API (port 8000).');
        }
    };

    window.exportDiagnosisCsv = function () {
        const rows = [
            ['ID', 'Tanggal', 'Kode Tanaman', 'Nama', 'Bagian', 'Kelas Diagnosis', 'Confidence', 'Kondisi Nutrisi', 'Petugas', 'Rekomendasi'],
            ...diagnosesData.map(d => [
                d.id,
                d.tanggal_diagnosis || '',
                d.kode_tanaman || '',
                d.nama_tanaman || '',
                d.plant_part || '',
                d.kelas_diagnosis || '',
                fmtPct(d.confidence),
                d.nutrient_condition || '',
                d.diagnosed_by_nama || '',
                d.rekomendasi || '',
            ]),
        ];
        const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `diagnosis-kesehatan-hidara-${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('svmTanggal').value = fmtDate(new Date().toISOString());
        document.getElementById('svmSearchInput')?.addEventListener('input', () => { currentPage = 1; renderSvmTable(); });
        document.getElementById('svmBedengFilter')?.addEventListener('change', () => { currentPage = 1; renderSvmTable(); });
        document.getElementById('svmStatusFilter')?.addEventListener('change', () => { currentPage = 1; renderSvmTable(); });
        document.getElementById('btnSvmPrev')?.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderSvmTable(); } });
        document.getElementById('btnSvmNext')?.addEventListener('click', () => {
            const totalPages = Math.ceil(getFilteredDiagnoses().length / perPage) || 1;
            if (currentPage < totalPages) { currentPage++; renderSvmTable(); }
        });

        populatePlantSelects();
        renderSvmTable();
        renderResultPanel(activeDiagnosis);
        syncFromApi();
    });
})();
</script>
@endpush
