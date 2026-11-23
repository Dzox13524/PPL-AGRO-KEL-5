@extends('layouts.karyawan', ['active' => 'prediksi'])

@section('title', 'Prediksi Yield & Biomassa')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Prediksi Pertumbuhan & Hasil Panen Tanaman</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Estimasi bobot panen, umur panen optimal, dan rekomendasi nutrisi berbasis kurva pertumbuhan MLP</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    onclick="exportPredictionsCsv()"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#eef4f8] hover:bg-[#e2edf5] text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer active:scale-95 border border-slate-200/70 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh Laporan Analisis</span>
            </button>
            <button type="button"
                    onclick="focusPredictionForm()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Hitung Prediksi Baru</span>
            </button>
        </div>
    </div>

    @if(!empty($apiError))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs sm:text-sm text-amber-800 flex items-center justify-between gap-3">
            <span>{{ $apiError }}</span>
            <span class="font-semibold text-[11px] uppercase tracking-wider">Periksa Backend API</span>
        </div>
    @endif

    {{-- Main Two-Column Layout (Form & Results) --}}
    <div id="inputCitraSection" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Input Parameter Phenotyping & Nutrisi (5 cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Input Parameter Pertumbuhan & Prediksi</h2>
                    <span class="text-[10px] font-bold bg-emerald-100 text-[#0e5f38] px-2.5 py-0.5 rounded-full">AI-1 MLP</span>
                </div>

                <form id="predictionForm" onsubmit="handleRunPrediction(event)" class="mt-5 space-y-4">
                    <div id="predFormError" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700"></div>

                    {{-- Row: Batch Tanaman & Umur --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">PILIH BATCH TANAMAN</label>
                            <select id="predBedeng" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                                @forelse($plants as $p)
                                    <option value="{{ $p['id'] }}">
                                        {{ $p['kode_tanaman'] }} — {{ $p['nama'] }}{{ !empty($p['varietas']) ? ' (' . $p['varietas'] . ')' : '' }}
                                    </option>
                                @empty
                                    <option value="">Belum ada tanaman</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">UMUR (HST)</label>
                            <div class="relative">
                                <input type="number" id="predHst" value="28" min="0" max="365" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-[10px] text-gray-400 font-semibold pointer-events-none">HST</span>
                            </div>
                        </div>
                    </div>

                    {{-- Morfologi: Tinggi, Jumlah Daun, Berat --}}
                    <div class="grid grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">Tinggi (cm)</label>
                            <input type="number" id="predTinggi" step="0.01" min="0" max="100" value="18.0" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        </div>
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">Daun (helai)</label>
                            <input type="number" id="predDaun" min="0" max="200" value="14" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        </div>
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">Berat (gram)</label>
                            <input type="number" id="predBerat" step="0.01" min="0" max="5000" value="" placeholder="Auto" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        </div>
                    </div>

                    {{-- Parameter Air & Nutrisi --}}
                    <div class="grid grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">pH Air</label>
                            <input type="number" id="predPh" step="0.01" min="0" max="14" value="6.0" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        </div>
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">EC (mS/cm)</label>
                            <input type="number" id="predEc" step="0.01" min="0" max="20" value="1.20" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        </div>
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">Suhu Air (°C)</label>
                            <input type="number" id="predSuhuAir" step="0.1" min="0" max="50" value="22.0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                        </div>
                    </div>

                    {{-- Target Prediksi & Varietas --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">TARGET PREDIKSI MLP</label>
                            <select id="predTarget" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                                <option value="berat_gram">Berat Panen (gram)</option>
                                <option value="tinggi_cm">Tinggi Tajuk (cm)</option>
                                <option value="jumlah_daun">Jumlah Daun (helai)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">VARIETAS TERDAFTAR</label>
                            <input type="text" id="predVarietas" readonly value="-" class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 font-medium">
                        </div>
                    </div>

                    {{-- Optional Canopy Reference Image Preview --}}
                    <div class="border border-dashed border-emerald-200/90 rounded-2xl p-3.5 bg-emerald-50/20 hover:bg-emerald-50/40 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 overflow-hidden relative shrink-0 border border-emerald-200">
                                    <img id="predThumbImg" src="/images/beds/bed_a01.jpg" alt="Referensi Sampel" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-gray-900 truncate">Foto Sampel Visual (Opsional)</div>
                                    <div id="predFileLabel" class="text-[10.5px] text-gray-500 truncate">Gunakan foto referensi untuk dokumentasi lapangan</div>
                                </div>
                            </div>
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-xs font-medium text-gray-700 transition cursor-pointer shrink-0">
                                <input type="file" id="predFileInput" class="hidden" accept=".jpg,.jpeg,.png" onchange="handleImagePicked(event)">
                                <span>Pilih Foto</span>
                            </label>
                        </div>
                    </div>

                    {{-- Catatan & Apply to Plant --}}
                    <div>
                        <label class="block text-[10.5px] font-bold text-gray-700 uppercase tracking-wider mb-1">CATATAN PENGAMATAN</label>
                        <input type="text" id="predCatatan" placeholder="Contoh: Cek rutin pagi, daun tampak subur..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>

                    <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer select-none">
                        <input type="checkbox" id="predApplyToPlant" checked class="rounded border-gray-300 text-[#0e5f38] focus:ring-[#0e5f38]">
                        <span>Perbarui estimasi tanggal panen & status siap panen di data tanaman</span>
                    </label>

                    {{-- Action Buttons --}}
                    <div class="pt-1 flex items-center gap-2.5">
                        <button type="button"
                                onclick="resetPredictionForm()"
                                class="w-1/3 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition cursor-pointer">
                            Reset
                        </button>
                        <button type="submit"
                                id="btnRunPrediction"
                                class="w-2/3 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold shadow-xs flex items-center justify-center gap-2 transition cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Hitung & Simpan Prediksi MLP</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Inference Pipeline Results (7 cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs space-y-5">
            {{-- Pipeline Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-gray-100">
                <div>
                    <span id="resHeaderBatch" class="text-[10px] font-bold tracking-widest text-[#0e5f38] uppercase">HASIL INFERENSI PIPELINE &bull; -</span>
                    <h2 id="resHeaderTitle" class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Proyeksi Akumulasi Biomassa & Hasil Panen</h2>
                </div>
                <span id="resYieldBadge" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0e5f38] border border-emerald-200 self-start sm:self-auto">
                    PREDIKSI: -
                </span>
            </div>

            {{-- Model Info Banner --}}
            <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-emerald-950 overflow-hidden relative shrink-0 border border-emerald-700">
                        <img id="resBannerImg" src="/images/beds/bed_a01.jpg" alt="Plant Preview" class="w-full h-full object-cover">
                        <span class="absolute inset-0 bg-emerald-500/20"></span>
                    </div>
                    <div class="min-w-0">
                        <div id="resModelTitle" class="text-xs font-bold text-gray-900 truncate">Model MLP Growth Predictor</div>
                        <div id="resModelSub" class="text-[10.5px] text-gray-500 truncate">Menunggu data prediksi dari database...</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span id="resBadgeTarget" class="text-[10px] font-bold bg-emerald-100 text-[#0e5f38] px-2 py-0.5 rounded-full">Target: -</span>
                    <span id="resBadgeVersion" class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">-</span>
                </div>
            </div>

            {{-- Dynamic Biomass Area Chart --}}
            <div class="bg-gradient-to-b from-[#fbfdfc] to-[#f4f9f6] rounded-2xl p-4 sm:p-5 border border-emerald-100/90 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-gray-100">
                    <div>
                        <span class="text-[10.5px] font-bold text-[#0e5f38] uppercase tracking-wider block">KURVA BIOMASSA SEGAR (FRESH WEIGHT TRAJECTORY)</span>
                        <div class="flex items-center gap-3 text-[11px] text-gray-600 mt-1">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="font-medium text-gray-700">Observasi Nyata (growth_records)</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-0.5 bg-[#10b981] inline-block"></span>
                                <span class="font-medium text-gray-700">Proyeksi Panen MLP</span>
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span id="resPhaseBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-emerald-50 text-[#0e5f38] border border-emerald-200">
                            -
                        </span>
                    </div>
                </div>

                {{-- Chart SVG Canvas --}}
                <div class="w-full overflow-x-auto py-3">
                    <div class="min-w-[500px]">
                        <svg id="predSvgChart" viewBox="0 0 650 205" class="w-full h-auto overflow-visible select-none">
                            <defs>
                                <linearGradient id="areaGradientLight" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.28" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.02" />
                                </linearGradient>
                                <filter id="nodeGlowLight" x="-20%" y="-20%" width="140%" height="140%">
                                    <feDropShadow dx="0" dy="1.5" stdDeviation="2" flood-color="#0e5f38" flood-opacity="0.2"/>
                                </filter>
                            </defs>
                            <g id="predChartGrid"></g>
                            <path id="predAreaPath" d="" fill="url(#areaGradientLight)" />
                            <path id="predActualPath" d="" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round"/>
                            <path id="predProjectedPath" d="" fill="none" stroke="#10b981" stroke-width="2.5" stroke-dasharray="5 4" stroke-linecap="round"/>
                            <g id="predChartNodes"></g>
                        </svg>
                    </div>
                </div>

                {{-- Chart Footer Stats --}}
                <div class="mt-2 pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-[11px] text-gray-600">
                    <div id="resChartFooterLeft">
                        <span class="font-bold text-[#0e5f38]">ESTIMASI PANEN:</span> Menunggu data prediksi
                    </div>
                    <div id="resChartFooterRight" class="text-gray-500 text-xs font-medium">
                        -
                    </div>
                </div>
            </div>

            {{-- 4 Metric Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                {{-- Card 1: Status Pertumbuhan --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Status Pertumbuhan & Skor MLP</span>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span id="resCardStatusTitle" class="text-sm font-bold text-gray-900">-</span>
                        <span id="resCardStatusBadge" class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">-</span>
                    </div>
                    <p id="resCardStatusDesc" class="text-[11px] text-gray-500 mt-1 leading-relaxed">-</p>
                </div>

                {{-- Card 2: Estimasi Hasil Panen --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Estimasi Hasil & Berat Segar</span>
                    <div class="flex items-baseline gap-1.5 mt-1 flex-wrap">
                        <span id="resCardWeightVal" class="text-base font-extrabold text-gray-900">-</span>
                        <span id="resCardWeightSub" class="text-xs font-semibold text-gray-500">-</span>
                    </div>
                    <p id="resCardWeightDesc" class="text-[11px] text-gray-500 mt-1 leading-relaxed">-</p>
                </div>

                {{-- Card 3: Jadwal Panen Optimal --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Proyeksi Jadwal Panen Optimal</span>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span id="resCardHarvestVal" class="text-base font-extrabold text-amber-600">-</span>
                        <span id="resCardHarvestBadge" class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">-</span>
                    </div>
                    <p id="resCardHarvestDesc" class="text-[11px] text-gray-500 mt-1 leading-relaxed">-</p>
                </div>

                {{-- Card 4: Evaluasi Parameter & Rekomendasi --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Rekomendasi Agronomi AI</span>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span id="resCardRecsTitle" class="text-sm font-bold text-purple-900">Evaluasi Parameter</span>
                        <span id="resCardRecsBadge" class="text-xs font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full">-</span>
                    </div>
                    <p id="resCardRecsDesc" class="text-[11px] text-gray-500 mt-1 leading-relaxed line-clamp-3">-</p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table Section: Riwayat Prediksi Pertumbuhan --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="pb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Riwayat Prediksi Pertumbuhan (`growth_predictions`)</h2>
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
                           id="predSearchInput"
                           placeholder="Cari kode batch, varietas, catatan..."
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                <select id="predBedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Batch Tanaman</option>
                    @foreach($plants as $p)
                        <option value="{{ $p['id'] }}">{{ $p['kode_tanaman'] }} — {{ $p['nama'] }}</option>
                    @endforeach
                </select>

                <select id="predVarietasFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Varietas</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button"
                        id="tabPredSemua"
                        onclick="setPredTab('semua')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Semua (0)
                </button>
                <button type="button"
                        id="tabPredOptimal"
                        onclick="setPredTab('optimal')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Optimal / Normal (0)
                </button>
                <button type="button"
                        id="tabPredEvaluasi"
                        onclick="setPredTab('evaluasi')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Perlu Evaluasi (0)
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto -mx-5 sm:-mx-6 mt-2">
            <div class="inline-block min-w-full align-middle px-5 sm:px-6">
                <table class="min-w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th scope="col" class="py-3.5 pr-3">ID & TANGGAL</th>
                            <th scope="col" class="py-3.5 px-3">BATCH TANAMAN</th>
                            <th scope="col" class="py-3.5 px-3">PARAMETER INPUT</th>
                            <th scope="col" class="py-3.5 px-3">MODEL & TARGET</th>
                            <th scope="col" class="py-3.5 px-3">HASIL PREDIKSI</th>
                            <th scope="col" class="py-3.5 px-3">STATUS & CATATAN</th>
                            <th scope="col" class="py-3.5 pl-3 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="predTableBody" class="divide-y divide-gray-100 text-gray-700">
                        {{-- Rendered via JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
            <div id="predTableInfo">Menampilkan 0 dari 0 riwayat prediksi</div>
            <div class="flex items-center gap-1 self-center sm:self-auto">
                <button type="button" id="btnPredPrev" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 text-gray-600 transition cursor-pointer">
                    Sebelumnya
                </button>
                <div id="predPageButtons" class="flex items-center gap-1"></div>
                <button type="button" id="btnPredNext" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 text-gray-600 transition cursor-pointer">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Detail Prediction Modal --}}
<div id="predDetailModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closePredDetailModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 id="predDetailTitle" class="text-base font-bold text-gray-900">Detail Prediksi Pertumbuhan</h3>
                <button type="button" onclick="closePredDetailModal()" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="predDetailBody" class="mt-4 space-y-3 text-xs"></div>
            <div class="mt-5 flex justify-end">
                <button type="button" onclick="closePredDetailModal()" class="px-4 py-2 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="predToast" class="fixed bottom-6 right-6 z-50 translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div class="bg-[#0e5f38] text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 text-xs sm:text-sm font-medium border border-emerald-400/30">
        <span id="predToastMessage">Berhasil.</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const apiHost = window.location.hostname === '127.0.0.1' ? '127.0.0.1' : 'localhost';
    const BE_API_URL = `http://${apiHost}:8000/api/v1`;
    const BED_IMAGES = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];

    let plantsData = @json($plants ?? []);
    let predictionsData = @json($predictions ?? []);
    let plantHistories = @json($plantHistories ?? []);
    const detailCache = {};
    let predFilterStatus = 'semua';
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
        if (options.body && !headers['Content-Type']) {
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

    function fmtNum(val, dec = 1) {
        const n = Number(val);
        return Number.isFinite(n) ? n.toFixed(dec) : '-';
    }

    function fmtDate(iso) {
        if (!iso) return '-';
        const dt = new Date(iso);
        if (Number.isNaN(dt.getTime())) return String(iso);
        return dt.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function showToast(msg) {
        const t = document.getElementById('predToast');
        const m = document.getElementById('predToastMessage');
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

    function parseNoteMeta(catatan) {
        const text = String(catatan || '');
        const statusMatch = text.match(/Status:\s*([A-Z_]+)\s*\(skor\s*([\d.]+)\/100\)/i);
        const harvestWeightMatch = text.match(/Est\.\s*berat panen:\s*([\d.]+)\s*g/i);
        const harvestDateMatch = text.match(/Panen optimal:\s*(\d{4}-\d{2}-\d{2})\s*\((\d+)\s*hari lagi\)/i);
        const recIdx = text.indexOf('Rekomendasi:');
        const recs = recIdx !== -1 ? text.slice(recIdx + 'Rekomendasi:'.length).trim() : '';
        const statusCode = statusMatch ? statusMatch[1].toUpperCase() : 'NORMAL';
        const score = statusMatch ? Number(statusMatch[2]) : null;
        return {
            statusCode,
            score,
            harvestWeight: harvestWeightMatch ? Number(harvestWeightMatch[1]) : null,
            harvestDate: harvestDateMatch ? harvestDateMatch[1] : null,
            daysLeft: harvestDateMatch ? Number(harvestDateMatch[2]) : null,
            recs: recs || text || 'Lanjutkan pemantauan berkala.',
        };
    }

    function classifyType(item) {
        const meta = parseNoteMeta(item.catatan);
        if (meta.statusCode === 'LAMBAT' || meta.statusCode === 'KRITIS') return 'evaluasi';
        return 'optimal';
    }

    function populateFormFromSelectedPlant() {
        const sel = document.getElementById('predBedeng');
        if (!sel || !sel.value) return;
        const plant = plantsData.find(p => String(p.id) === String(sel.value));
        if (!plant) return;

        const latest = plant.latest_growth_record || null;
        document.getElementById('predHst').value = plant.umur_hari ?? (latest?.umur_tanaman ?? 21);
        document.getElementById('predTinggi').value = latest?.tinggi_cm !== null && latest?.tinggi_cm !== undefined ? latest.tinggi_cm : '15.0';
        document.getElementById('predDaun').value = latest?.jumlah_daun !== null && latest?.jumlah_daun !== undefined ? latest.jumlah_daun : '10';
        document.getElementById('predBerat').value = latest?.berat_gram !== null && latest?.berat_gram !== undefined ? latest.berat_gram : '';
        document.getElementById('predPh').value = latest?.ph_air !== null && latest?.ph_air !== undefined ? latest.ph_air : '6.0';
        document.getElementById('predEc').value = latest?.ec !== null && latest?.ec !== undefined ? latest.ec : '1.20';
        document.getElementById('predSuhuAir').value = latest?.suhu_air !== null && latest?.suhu_air !== undefined ? latest.suhu_air : '22.0';
        document.getElementById('predVarietas').value = `${plant.nama || 'Selada'}${plant.varietas ? ' (' + plant.varietas + ')' : ''}`;

        const pIdx = plantsData.findIndex(p => Number(p.id) === Number(plant.id));
        const imgUrl = `/images/beds/${BED_IMAGES[Math.max(0, pIdx) % BED_IMAGES.length]}`;
        if (!document.getElementById('predFileInput')?.files?.length) {
            document.getElementById('predThumbImg').src = imgUrl;
        }
        document.getElementById('resBannerImg').src = imgUrl;

        const matchingPred = predictionsData.find(pr => Number(pr.plant_id) === Number(plant.id)) || null;
        renderResultPanel(matchingPred, plant);
    }

    function populateFilters() {
        const plantSel = document.getElementById('predBedeng');
        const filterPlant = document.getElementById('predBedengFilter');
        const filterVar = document.getElementById('predVarietasFilter');

        if (plantSel) {
            const cur = plantSel.value;
            plantSel.innerHTML = plantsData.length
                ? plantsData.map(p => `<option value="${p.id}" ${String(p.id) === String(cur) ? 'selected' : ''}>${esc(p.kode_tanaman)} — ${esc(p.nama)}${p.varietas ? ' (' + esc(p.varietas) + ')' : ''}</option>`).join('')
                : '<option value="">Belum ada data tanaman</option>';
        }
        if (filterPlant) {
            const cur = filterPlant.value;
            filterPlant.innerHTML = '<option value="">Semua Batch Tanaman</option>' +
                plantsData.map(p => `<option value="${p.id}" ${String(p.id) === String(cur) ? 'selected' : ''}>${esc(p.kode_tanaman)} — ${esc(p.nama)}</option>`).join('');
        }
        if (filterVar) {
            const cur = filterVar.value;
            const vars = Array.from(new Set(
                plantsData.map(p => p.varietas).concat(predictionsData.map(pr => pr.varietas)).filter(Boolean)
            ));
            filterVar.innerHTML = '<option value="">Semua Varietas</option>' +
                vars.map(v => `<option value="${esc(v)}" ${v === cur ? 'selected' : ''}>${esc(v)}</option>`).join('');
        }
    }

    function renderResultSvg(plantId, detail) {
        const records = Array.isArray(plantHistories[plantId]) ? plantHistories[plantId] : [];
        const points = records
            .filter(r => r.berat_gram !== null && r.berat_gram !== undefined)
            .map(r => ({
                hst: Number(r.umur_tanaman ?? 0),
                val: Number(r.berat_gram),
                projected: false,
            }));

        const parsed = detail?.catatan ? parseNoteMeta(detail.catatan) : {};
        const projVal = detail?.predicted_harvest_weight_g ?? parsed.harvestWeight ?? (detail?.target_prediksi === 'berat_gram' ? Number(detail.nilai_prediksi) : null);
        const plant = plantsData.find(p => Number(p.id) === Number(plantId));
        const lastHst = points.length ? points[points.length - 1].hst : (plant?.umur_hari ?? 21);
        const projHst = detail?.optimal_harvest_age_days ?? (lastHst + (parsed.daysLeft ?? 10));

        const allPts = [...points];
        if (projVal && Number.isFinite(Number(projVal))) {
            allPts.push({ hst: Math.max(projHst, lastHst + 1), val: Number(projVal), projected: true });
        }

        const gridEl = document.getElementById('predChartGrid');
        const areaEl = document.getElementById('predAreaPath');
        const actEl = document.getElementById('predActualPath');
        const projEl = document.getElementById('predProjectedPath');
        const nodesEl = document.getElementById('predChartNodes');

        if (allPts.length === 0) {
            gridEl.innerHTML = '<text x="325" y="105" text-anchor="middle" class="text-xs fill-gray-400">Belum ada titik data biomassa untuk batch ini</text>';
            areaEl.setAttribute('d', '');
            actEl.setAttribute('d', '');
            projEl.setAttribute('d', '');
            nodesEl.innerHTML = '';
            return;
        }

        const maxVal = Math.max(200, ...allPts.map(p => p.val));
        const maxY = Math.ceil(maxVal / 50) * 50;
        const yTicks = [
            { y: 30, val: maxY },
            { y: 77, val: Math.round(maxY * 0.67) },
            { y: 123, val: Math.round(maxY * 0.33) },
            { y: 170, val: 0 },
        ];
        gridEl.innerHTML = yTicks.map((t, i) => `
            <g class="text-[9px] fill-gray-400 font-medium">
                <line x1="45" y1="${t.y}" x2="620" y2="${t.y}" stroke="${i === 3 ? '#cbd5e1' : '#e2e8f0'}" ${i === 3 ? 'stroke-width="1.5"' : 'stroke-dasharray="3 3"'}/>
                <text x="38" y="${t.y + 3}" text-anchor="end">${t.val}g</text>
            </g>`).join('');

        const xStart = 65;
        const xEnd = 595;
        const yTop = 30;
        const yBottom = 170;
        const n = allPts.length;

        const coords = allPts.map((pt, idx) => {
            const x = n === 1 ? (xStart + xEnd) / 2 : xStart + (idx / (n - 1)) * (xEnd - xStart);
            const y = yBottom - ((pt.val / maxY) * (yBottom - yTop));
            return { ...pt, x: Number(x.toFixed(1)), y: Number(y.toFixed(1)) };
        });

        const obsCoords = coords.filter(c => !c.projected);
        const projCoords = coords.filter(c => c.projected);

        const areaD = `M ${coords[0].x} ${yBottom} ` +
            coords.map(c => `L ${c.x} ${c.y}`).join(' ') +
            ` L ${coords[coords.length - 1].x} ${yBottom} Z`;
        areaEl.setAttribute('d', areaD);

        actEl.setAttribute('d', obsCoords.length > 1
            ? obsCoords.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.y}`).join(' ')
            : '');

        if (obsCoords.length >= 1 && projCoords.length >= 1) {
            const bridge = [obsCoords[obsCoords.length - 1], ...projCoords];
            projEl.setAttribute('d', bridge.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.y}`).join(' '));
        } else {
            projEl.setAttribute('d', '');
        }

        nodesEl.innerHTML = coords.map((c, idx) => {
            const color = c.projected ? '#0e5f38' : '#f59e0b';
            const r = c.projected || idx === obsCoords.length - 1 ? 6 : 4.5;
            const showLabel = coords.length <= 7 || idx === 0 || idx === obsCoords.length - 1 || c.projected;
            const lbl = c.projected ? `D${c.hst} (${fmtNum(c.val, 0)}g)` : `H${c.hst}`;
            return `
                <circle cx="${c.x}" cy="${c.y}" r="${r}" fill="${color}" stroke="#ffffff" stroke-width="2" filter="url(#nodeGlowLight)">
                    <title>${esc(c.projected ? 'Proyeksi Panen' : 'Observasi')} HST ${c.hst}: ${fmtNum(c.val, 1)} g</title>
                </circle>
                ${showLabel ? `<text x="${c.x}" y="188" text-anchor="middle" class="text-[9.5px] ${c.projected ? 'fill-[#0e5f38] font-bold' : 'fill-gray-500 font-medium'}">${esc(lbl)}</text>` : ''}
                ${c.projected ? `<text x="${c.x}" y="${Math.max(16, c.y - 11)}" text-anchor="middle" class="text-[10.5px] font-extrabold fill-[#0e5f38]">${fmtNum(c.val, 1)}g</text>` : ''}
            `;
        }).join('');
    }

    function renderResultPanel(predOrDetail, plantOverride = null) {
        const plantId = predOrDetail?.plant_id ?? plantOverride?.id ?? document.getElementById('predBedeng')?.value;
        const plant = plantOverride || plantsData.find(p => Number(p.id) === Number(plantId)) || null;
        const detail = predOrDetail && detailCache[predOrDetail.id] ? detailCache[predOrDetail.id] : predOrDetail;

        const setText = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };

        if (!detail) {
            setText('resHeaderBatch', `HASIL INFERENSI PIPELINE • ${plant ? plant.kode_tanaman : '-'}`);
            setText('resHeaderTitle', plant ? `${plant.nama}${plant.varietas ? ' (' + plant.varietas + ')' : ''}` : 'Belum Ada Prediksi');
            setText('resYieldBadge', 'BELUM ADA PREDIKSI');
            setText('resModelTitle', 'Model MLP Growth Predictor');
            setText('resModelSub', 'Isi parameter di sebelah kiri lalu klik "Hitung & Simpan Prediksi MLP".');
            setText('resBadgeTarget', 'Target: -');
            setText('resBadgeVersion', 'MLP');
            setText('resPhaseBadge', plant ? `Umur ${plant.umur_hari ?? 0} HST` : '-');
            setText('resChartFooterLeft', 'Belum ada prediksi tersimpan untuk batch tanaman ini.');
            setText('resChartFooterRight', '-');
            setText('resCardStatusTitle', '-');
            setText('resCardStatusBadge', '-');
            setText('resCardStatusDesc', 'Jalankan prediksi MLP untuk melihat skor kesehatan pertumbuhan.');
            setText('resCardWeightVal', '-');
            setText('resCardWeightSub', '');
            setText('resCardWeightDesc', '-');
            setText('resCardHarvestVal', '-');
            setText('resCardHarvestBadge', '-');
            setText('resCardHarvestDesc', '-');
            setText('resCardRecsBadge', '-');
            setText('resCardRecsDesc', '-');
            if (plantId) renderResultSvg(plantId, null);
            return;
        }

        const parsed = parseNoteMeta(detail.catatan);
        const statusText = (detail.growth_status || parsed.statusCode || 'NORMAL').toUpperCase();
        const scoreVal = detail.growth_score !== undefined ? detail.growth_score : parsed.score;
        const predWeight = detail.predicted_harvest_weight_g ?? parsed.harvestWeight ?? (detail.target_prediksi === 'berat_gram' ? detail.nilai_prediksi : null);
        const curWeight = detail.current_estimated_weight_g ?? detail.growth_record?.berat_gram ?? null;
        const harvestDate = detail.predicted_harvest_date ?? parsed.harvestDate ?? plant?.tanggal_panen ?? '-';
        const daysLeft = detail.days_to_harvest !== undefined ? detail.days_to_harvest : parsed.daysLeft;
        const optAge = detail.optimal_harvest_age_days ?? ((detail.growth_record?.umur_tanaman ?? plant?.umur_hari ?? 0) + (daysLeft ?? 0));
        const recsList = Array.isArray(detail.recommendations) ? detail.recommendations.join(' ') : parsed.recs;

        const kode = detail.kode_tanaman || plant?.kode_tanaman || `Plant #${plantId}`;
        const nama = detail.nama_tanaman || plant?.nama || 'Selada';
        const varietas = detail.varietas || plant?.varietas || '-';

        setText('resHeaderBatch', `HASIL INFERENSI PIPELINE • ${kode}`);
        setText('resHeaderTitle', `${nama} (${varietas})`);
        setText('resYieldBadge', `PREDIKSI: ${fmtNum(detail.nilai_prediksi, 1)} ${detail.satuan || ''}`);
        setText('resModelTitle', `Model ${detail.model || 'MLP'} — Prediksi #${detail.id}`);
        setText('resModelSub', `Dihitung pada ${fmtDate(detail.tanggal_prediksi)} oleh ${detail.predicted_by_nama || 'Staf'}`);
        setText('resBadgeTarget', `Target: ${detail.target_prediksi}`);
        setText('resBadgeVersion', detail.versi_model || 'MLP v1');

        const curAge = detail.growth_record?.umur_tanaman ?? plant?.umur_hari ?? '-';
        setText('resPhaseBadge', `Saat Ini: D${curAge} (${curWeight ? fmtNum(curWeight, 1) + 'g' : '-'}) • Panen: D${optAge}`);

        const footerLeft = document.getElementById('resChartFooterLeft');
        if (footerLeft) {
            footerLeft.innerHTML = `<span class="font-bold text-[#0e5f38]">ESTIMASI PANEN:</span> ${predWeight ? fmtNum(predWeight, 1) + ' gram / tanaman' : fmtNum(detail.nilai_prediksi, 1) + ' ' + (detail.satuan || '')}`;
        }
        setText('resChartFooterRight', `${detail.model || 'MLP'} • ${detail.versi_model || 'lettuce-logistic-curve-v1'}`);

        setText('resCardStatusTitle', statusText);
        const statusBadge = document.getElementById('resCardStatusBadge');
        if (statusBadge) {
            statusBadge.textContent = scoreVal !== null ? `Skor ${scoreVal}/100` : statusText;
            statusBadge.className = (statusText === 'LAMBAT' || statusText === 'KRITIS')
                ? 'text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full'
                : 'text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full';
        }
        const pa = detail.parameter_analysis;
        setText('resCardStatusDesc', pa
            ? `Growth index: ${pa.growth_index} • Faktor lingkungan: ${pa.environment_factor} (pH ${pa.water_ph?.status || '-'}, Nutrisi ${pa.nutrient_ppm?.status || '-'})`
            : `Tinggi: ${detail.growth_record?.tinggi_cm ?? '-'} cm • Daun: ${detail.growth_record?.jumlah_daun ?? '-'} helai • pH: ${detail.growth_record?.ph_air ?? '-'}`);

        setText('resCardWeightVal', `${fmtNum(detail.nilai_prediksi, 1)} ${detail.satuan || ''}`);
        setText('resCardWeightSub', curWeight ? `(Saat ini: ${fmtNum(curWeight, 1)}g)` : '');
        setText('resCardWeightDesc', `Target disimpan (${detail.target_prediksi}): ${fmtNum(detail.nilai_prediksi, 1)} ${detail.satuan || ''}.${predWeight ? ' Est. bobot segar panen: ' + fmtNum(predWeight, 1) + ' g.' : ''}`);

        setText('resCardHarvestVal', harvestDate);
        setText('resCardHarvestBadge', daysLeft !== null && daysLeft !== undefined ? (daysLeft <= 0 ? 'Siap Panen' : `${daysLeft} Hari Lagi`) : `D${optAge}`);
        setText('resCardHarvestDesc', `Umur panen optimal: ${optAge} HST.${detail.harvest_ready ? ' Tanaman sudah mencapai standar siap panen.' : ' Lanjutkan pemeliharaan sesuai jadwal.'}`);

        setText('resCardRecsBadge', statusText);
        setText('resCardRecsDesc', recsList);

        renderResultSvg(plantId, detail);
    }

    function getFilteredPredictions() {
        const q = (document.getElementById('predSearchInput')?.value || '').toLowerCase().trim();
        const plantFilter = document.getElementById('predBedengFilter')?.value || '';
        const varFilter = document.getElementById('predVarietasFilter')?.value || '';

        return predictionsData.filter(item => {
            const typ = classifyType(item);
            if (predFilterStatus === 'optimal' && typ !== 'optimal') return false;
            if (predFilterStatus === 'evaluasi' && typ !== 'evaluasi') return false;
            if (plantFilter && String(item.plant_id) !== String(plantFilter)) return false;
            if (varFilter && String(item.varietas || '') !== String(varFilter)) return false;
            if (q) {
                const hay = `#pred-${item.id} ${item.kode_tanaman || ''} ${item.nama_tanaman || ''} ${item.varietas || ''} ${item.target_prediksi || ''} ${item.catatan || ''} ${item.predicted_by_nama || ''}`.toLowerCase();
                if (!hay.includes(q)) return false;
            }
            return true;
        });
    }

    function updateTabCounts() {
        const optCount = predictionsData.filter(p => classifyType(p) === 'optimal').length;
        const evalCount = predictionsData.length - optCount;
        const tSemua = document.getElementById('tabPredSemua');
        const tOpt = document.getElementById('tabPredOptimal');
        const tEval = document.getElementById('tabPredEvaluasi');
        if (tSemua) tSemua.textContent = `Semua (${predictionsData.length})`;
        if (tOpt) tOpt.textContent = `Optimal / Normal (${optCount})`;
        if (tEval) tEval.textContent = `Perlu Evaluasi (${evalCount})`;
    }

    function renderPredTable() {
        updateTabCounts();
        const tbody = document.getElementById('predTableBody');
        const infoEl = document.getElementById('predTableInfo');
        const prevBtn = document.getElementById('btnPredPrev');
        const nextBtn = document.getElementById('btnPredNext');
        const pageBtns = document.getElementById('predPageButtons');
        if (!tbody) return;

        const filtered = getFilteredPredictions();
        const total = filtered.length;
        const totalPages = Math.ceil(total / perPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * perPage;
        const items = filtered.slice(start, start + perPage);

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-gray-400 text-xs">Tidak ada riwayat prediksi yang sesuai filter.</td></tr>';
        } else {
            tbody.innerHTML = items.map(item => {
                const parsed = parseNoteMeta(item.catatan);
                const rec = item.growth_record || null;
                const isEval = classifyType(item) === 'evaluasi';
                const statusCls = isEval
                    ? 'bg-amber-50 text-amber-700 border-amber-200'
                    : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                const dotCls = isEval ? 'bg-amber-500' : 'bg-emerald-500';
                return `
                    <tr class="hover:bg-gray-50/70 transition group">
                        <td class="py-4 pr-3">
                            <div class="font-bold text-gray-900">#PRED-${item.id}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">${esc(fmtDate(item.tanggal_prediksi))}</div>
                        </td>
                        <td class="py-4 px-3">
                            <div class="font-bold text-gray-900">${esc(item.kode_tanaman || 'Plant #' + item.plant_id)}</div>
                            <div class="text-[11px] text-gray-500 mt-0.5">${esc(item.nama_tanaman || 'Selada')}${item.varietas ? ' · ' + esc(item.varietas) : ''}</div>
                        </td>
                        <td class="py-4 px-3">
                            ${rec
                                ? `<div class="text-xs font-bold text-gray-900">HST ${esc(rec.umur_tanaman ?? '-')} • ${fmtNum(rec.tinggi_cm, 1)} cm • ${esc(rec.jumlah_daun ?? '-')} daun</div>
                                   <div class="text-[10.5px] text-gray-500 mt-0.5">pH ${esc(rec.ph_air ?? '-')} · EC ${esc(rec.ec ?? '-')} · Berat ${fmtNum(rec.berat_gram, 1)}g</div>`
                                : '<span class="text-xs text-gray-400">-</span>'}
                        </td>
                        <td class="py-4 px-3">
                            <div class="font-bold text-gray-900 text-xs">${esc(item.target_prediksi)}</div>
                            <div class="text-[10.5px] text-gray-500 mt-0.5">${esc(item.model)} · ${esc(item.versi_model || '-')}</div>
                            <div class="text-[10px] text-gray-400">${esc(item.predicted_by_nama || 'Staf')}</div>
                        </td>
                        <td class="py-4 px-3">
                            <div class="font-bold text-emerald-700 text-sm">${fmtNum(item.nilai_prediksi, 1)} ${esc(item.satuan || '')}</div>
                            ${parsed.harvestDate ? `<div class="text-[10.5px] text-gray-500 mt-0.5">Panen: ${esc(parsed.harvestDate)}</div>` : ''}
                        </td>
                        <td class="py-4 px-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${statusCls}">
                                <span class="w-1.5 h-1.5 rounded-full ${dotCls}"></span>
                                <span>${esc(parsed.statusCode)}${parsed.score !== null ? ' (' + parsed.score + ')' : ''}</span>
                            </span>
                        </td>
                        <td class="py-4 pl-3 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <button type="button" onclick="viewPredictionDetail(${item.id})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition cursor-pointer" title="Lihat Detail & Tampilkan Grafik">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" onclick="deletePredRow(${item.id})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        if (infoEl) infoEl.textContent = `Menampilkan ${items.length} dari ${total} sesi prediksi MLP`;
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
                btn.addEventListener('click', () => { currentPage = i; renderPredTable(); });
                pageBtns.appendChild(btn);
            }
        }
    }

    async function syncFromApi() {
        try {
            const [plRes, prRes] = await Promise.all([
                apiRequest('/plants?per_page=100', { method: 'GET' }),
                apiRequest('/growth-prediction?limit=100', { method: 'GET' }),
            ]);
            if (plRes?.ok && Array.isArray(plRes.json?.data)) {
                plantsData = plRes.json.data;
            }
            if (prRes?.ok && Array.isArray(prRes.json?.data)) {
                predictionsData = prRes.json.data;
            }
            await Promise.all(plantsData.map(async p => {
                const hRes = await apiRequest(`/plants/${p.id}/growth-history`, { method: 'GET' });
                if (hRes?.ok && Array.isArray(hRes.json?.data)) {
                    plantHistories[p.id] = hRes.json.data;
                }
            }));
            populateFilters();
            populateFormFromSelectedPlant();
            renderPredTable();
        } catch (_) {}
    }

    window.focusPredictionForm = function () {
        document.getElementById('inputCitraSection')?.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('predBedeng')?.focus();
    };

    window.resetPredictionForm = function () {
        document.getElementById('predFormError').classList.add('hidden');
        document.getElementById('predCatatan').value = '';
        populateFormFromSelectedPlant();
    };

    window.setPredTab = function (tab) {
        predFilterStatus = tab;
        currentPage = 1;
        const map = { semua: 'tabPredSemua', optimal: 'tabPredOptimal', evaluasi: 'tabPredEvaluasi' };
        Object.entries(map).forEach(([k, id]) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.className = k === tab
                ? 'px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer'
                : 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer';
        });
        renderPredTable();
    };

    window.handleImagePicked = function (e) {
        const file = e.target.files?.[0];
        if (!file) return;
        document.getElementById('predFileLabel').textContent = `Foto dipilih: ${file.name}`;
        const reader = new FileReader();
        reader.onload = ev => {
            if (ev.target?.result) {
                document.getElementById('predThumbImg').src = ev.target.result;
                document.getElementById('resBannerImg').src = ev.target.result;
            }
        };
        reader.readAsDataURL(file);
    };

    window.handleRunPrediction = async function (e) {
        e.preventDefault();
        const errBox = document.getElementById('predFormError');
        errBox.classList.add('hidden');

        const plantId = parseInt(document.getElementById('predBedeng').value, 10);
        if (!Number.isFinite(plantId)) {
            errBox.textContent = 'Pilih batch tanaman terlebih dahulu.';
            errBox.classList.remove('hidden');
            return;
        }

        const beratVal = document.getElementById('predBerat').value.trim();
        const ecVal = document.getElementById('predEc').value.trim();
        const suhuAirVal = document.getElementById('predSuhuAir').value.trim();
        const hstVal = document.getElementById('predHst').value.trim();
        const catVal = document.getElementById('predCatatan').value.trim();

        const payload = {
            plant_id: plantId,
            umur_tanaman: hstVal !== '' ? parseInt(hstVal, 10) : null,
            tinggi_cm: parseFloat(document.getElementById('predTinggi').value),
            jumlah_daun: parseInt(document.getElementById('predDaun').value, 10),
            berat_gram: beratVal !== '' ? parseFloat(beratVal) : null,
            ph_air: parseFloat(document.getElementById('predPh').value),
            ec: ecVal !== '' ? parseFloat(ecVal) : 1.2,
            suhu_air: suhuAirVal !== '' ? parseFloat(suhuAirVal) : null,
            target_prediksi: document.getElementById('predTarget').value || 'berat_gram',
            catatan: catVal || null,
            apply_to_plant: document.getElementById('predApplyToPlant').checked,
        };

        const btn = document.getElementById('btnRunPrediction');
        btn.disabled = true;
        btn.querySelector('span').textContent = 'Memproses MLP...';

        try {
            const res = await apiRequest('/growth-prediction', {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            if (!res) return;

            if (res.ok && res.json?.data) {
                const detail = res.json.data;
                detailCache[detail.id] = detail;
                predictionsData = [detail, ...predictionsData.filter(p => Number(p.id) !== Number(detail.id))];

                const hRes = await apiRequest(`/plants/${plantId}/growth-history`, { method: 'GET' });
                if (hRes?.ok && Array.isArray(hRes.json?.data)) {
                    plantHistories[plantId] = hRes.json.data;
                }
                const plRes = await apiRequest('/plants?per_page=100', { method: 'GET' });
                if (plRes?.ok && Array.isArray(plRes.json?.data)) {
                    plantsData = plRes.json.data;
                }

                populateFilters();
                renderResultPanel(detail);
                renderPredTable();
                showToast(`Prediksi #PRED-${detail.id} berhasil disimpan ke database!`);
            } else {
                errBox.textContent = extractApiError(res.json, 'Gagal menghitung prediksi pertumbuhan.');
                errBox.classList.remove('hidden');
            }
        } catch (_) {
            errBox.textContent = 'Tidak dapat terhubung ke Backend API (port 8000).';
            errBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'Hitung & Simpan Prediksi MLP';
        }
    };

    window.viewPredictionDetail = function (predId) {
        const pred = predictionsData.find(p => Number(p.id) === Number(predId));
        if (!pred) return;
        const plantSelect = document.getElementById('predBedeng');
        if (plantSelect) plantSelect.value = String(pred.plant_id);
        renderResultPanel(pred);

        const parsed = parseNoteMeta(pred.catatan);
        const rec = pred.growth_record || {};
        document.getElementById('predDetailTitle').textContent = `Detail Prediksi #PRED-${pred.id} — ${pred.kode_tanaman || 'Tanaman'}`;
        document.getElementById('predDetailBody').innerHTML = `
            <div class="p-3.5 bg-gray-50 rounded-xl space-y-2">
                <div class="flex justify-between"><span class="text-gray-500">Tanaman:</span> <span class="font-bold text-gray-900">${esc(pred.nama_tanaman || '-')} (${esc(pred.varietas || '-')})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal Prediksi:</span> <span class="font-medium text-gray-800">${esc(fmtDate(pred.tanggal_prediksi))}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Target & Hasil:</span> <span class="font-bold text-emerald-700">${esc(pred.target_prediksi)}: ${fmtNum(pred.nilai_prediksi, 1)} ${esc(pred.satuan || '')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Input Observasi:</span> <span class="font-medium text-gray-800">HST ${esc(rec.umur_tanaman ?? '-')} • ${fmtNum(rec.tinggi_cm, 1)} cm • ${esc(rec.jumlah_daun ?? '-')} daun</span></div>
                <div class="flex justify-between"><span class="text-gray-500">pH / EC Nutrisi:</span> <span class="font-medium text-gray-800">pH ${esc(rec.ph_air ?? '-')} • EC ${esc(rec.ec ?? '-')} mS/cm</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Model & Petugas:</span> <span class="font-medium text-gray-800">${esc(pred.model)} (${esc(pred.versi_model || '-')}) · ${esc(pred.predicted_by_nama || '-')}</span></div>
                <div class="pt-2 border-t border-gray-200"><span class="text-gray-500 block mb-1">Ringkasan & Rekomendasi:</span><span class="text-gray-800 leading-relaxed block">${esc(pred.catatan || parsed.recs || '-')}</span></div>
            </div>`;
        document.getElementById('predDetailModal').classList.remove('hidden');
    };

    window.closePredDetailModal = function () {
        document.getElementById('predDetailModal').classList.add('hidden');
    };

    window.deletePredRow = async function (id) {
        if (!confirm(`Hapus riwayat prediksi #PRED-${id} dari database?`)) return;
        try {
            const res = await apiRequest(`/growth-prediction/${id}`, { method: 'DELETE' });
            if (!res) return;
            if (res.ok) {
                predictionsData = predictionsData.filter(p => Number(p.id) !== Number(id));
                renderPredTable();
                const selPid = document.getElementById('predBedeng')?.value;
                const match = predictionsData.find(p => Number(p.plant_id) === Number(selPid)) || predictionsData[0] || null;
                renderResultPanel(match);
                showToast(`Riwayat prediksi #PRED-${id} berhasil dihapus.`);
            } else {
                showToast(extractApiError(res.json, 'Gagal menghapus riwayat prediksi.'));
            }
        } catch (_) {
            showToast('Tidak dapat terhubung ke Backend API (port 8000).');
        }
    };

    window.exportPredictionsCsv = function () {
        const rows = [
            ['ID', 'Tanggal', 'Kode Tanaman', 'Nama', 'Varietas', 'Target', 'Nilai Prediksi', 'Satuan', 'Model', 'Petugas', 'Catatan'],
            ...predictionsData.map(p => [
                p.id,
                p.tanggal_prediksi || '',
                p.kode_tanaman || '',
                p.nama_tanaman || '',
                p.varietas || '',
                p.target_prediksi || '',
                p.nilai_prediksi ?? '',
                p.satuan || '',
                p.model || '',
                p.predicted_by_nama || '',
                p.catatan || '',
            ]),
        ];
        const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `prediksi-pertumbuhan-hidara-${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('predBedeng')?.addEventListener('change', populateFormFromSelectedPlant);
        document.getElementById('predSearchInput')?.addEventListener('input', () => { currentPage = 1; renderPredTable(); });
        document.getElementById('predBedengFilter')?.addEventListener('change', () => { currentPage = 1; renderPredTable(); });
        document.getElementById('predVarietasFilter')?.addEventListener('change', () => { currentPage = 1; renderPredTable(); });
        document.getElementById('btnPredPrev')?.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderPredTable(); } });
        document.getElementById('btnPredNext')?.addEventListener('click', () => {
            const totalPages = Math.ceil(getFilteredPredictions().length / perPage) || 1;
            if (currentPage < totalPages) { currentPage++; renderPredTable(); }
        });

        populateFilters();
        populateFormFromSelectedPlant();
        renderPredTable();
        syncFromApi();
    });
})();
</script>
@endpush
