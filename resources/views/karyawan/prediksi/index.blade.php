@extends('layouts.karyawan', ['active' => 'prediksi'])

@section('title', 'Prediksi Yield & Biomassa')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Prediksi Pertumbuhan & Hasil Panen Tanaman</h1>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    onclick="alert('Laporan analisis prediksi yield berhasil diunduh (PDF)!')"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#eef4f8] hover:bg-[#e2edf5] text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer active:scale-95 border border-slate-200/70 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh Laporan Analisis</span>
            </button>
            <button type="button"
                    onclick="document.getElementById('inputCitraSection').scrollIntoView({ behavior: 'smooth' })"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Uji Sampel Citra Baru</span>
            </button>
        </div>
    </div>

    {{-- Main Two-Column Layout (Form & Results) --}}
    <div id="inputCitraSection" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Input Parameter & Upload Form (5 cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Input Citra Top-View & Parameter Phenotyping</h2>

                <form id="predictionForm" onsubmit="handleRunPrediction(event)" class="mt-5 space-y-4">
                    {{-- Row: Bedeng & Umur --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">PILIH BEDENG / BATCH</label>
                            <select id="predBedeng" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                                <option value="Bedeng A-01">Bedeng A-01</option>
                                <option value="Bedeng A-02">Bedeng A-02</option>
                                <option value="Bedeng B-01">Bedeng B-01</option>
                                <option value="Bedeng B-02">Bedeng B-02</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">UMUR TANAMAN SAAT INI</label>
                            <div class="relative">
                                <input type="number" id="predHst" value="28" min="1" max="60" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-[10px] text-gray-400 font-semibold pointer-events-none">HST (19-44)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Image Upload Area --}}
                    <div class="border-2 border-dashed border-emerald-200/90 rounded-2xl p-4 sm:p-5 bg-emerald-50/20 text-center hover:bg-emerald-50/40 transition">
                        <div class="flex items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-emerald-100 overflow-hidden relative shrink-0 border border-emerald-200">
                                <img src="/images/beds/bed_a01.jpg" alt="Top-View Sample" class="w-full h-full object-cover">
                                <span class="absolute bottom-0 inset-x-0 bg-black/60 text-[8px] text-white py-0.5 font-bold">Top-View</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-xs sm:text-sm font-bold text-gray-900">Unggah / Seret Citra Top-View Kanopi</h3>
                                <p class="text-[10px] sm:text-[11px] text-gray-500">Format: JPG, PNG (Maks 15 MB/Foto)</p>
                            </div>
                        </div>

                        <div class="mt-3.5 flex items-center justify-center gap-2">
                            <button type="button"
                                    onclick="alert('Fitur kamera langsung siap dihubungkan ke webcam greenhouse.')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-xs font-medium text-gray-700 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Ambil Kamera</span>
                            </button>
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-medium text-gray-700 transition cursor-pointer">
                                <input type="file" id="predFileInput" class="hidden" accept="image/*" onchange="handleImagePicked(event)">
                                <span>Pilih File</span>
                            </label>
                        </div>

                        <div class="mt-3 p-2.5 bg-white/80 rounded-xl border border-emerald-100/70 text-[10.5px] text-gray-600 text-left leading-relaxed">
                            <span class="font-bold text-emerald-800">Panduan Pengambilan Citra:</span> Pastikan hanya 1 tanaman berada di tengah frame dengan pencahayaan merata dan orientasi tegak lurus (top-view).
                        </div>
                    </div>

                    {{-- Kalibrasi Skala Piksel --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider">KALIBRASI SKALA PIKSEL</label>
                            <span class="text-[9.5px] font-bold bg-emerald-100 text-[#0e5f38] px-2 py-0.5 rounded-full">SKALA TERKALIBRASI</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <input type="text" value="Piksel Standar (10 cm = 320 px)" readonly class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-[11px] text-gray-700 font-medium">
                            <input type="text" value="Jarak Kamera: 40 cm" readonly class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-[11px] text-gray-700 font-medium">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Menjaga akurasi dimensi fisik tajuk tanpa pre-cropping manual sebelum segmentasi.</p>
                    </div>

                    {{-- Varietas Selada --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider">VARIETAS SELADA (AUTO CONVNEXT)</label>
                            <span class="text-[9.5px] font-bold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">6 Kelas Jurnal</span>
                        </div>
                        <select id="predVarietas" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                            <option value="Crisphead">Crisphead (Terdeteksi ConvNeXt: 99.6%)</option>
                            <option value="Butterhead">Butterhead</option>
                            <option value="Romaine">Romaine</option>
                            <option value="Looseleaf">Looseleaf</option>
                            <option value="Batavia">Batavia</option>
                            <option value="Oakleaf">Oakleaf</option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">Auto-detected by ConvNeXt, dapat di-override manual jika hasil prediksi meleset.</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-2 flex items-center gap-2.5">
                        <button type="button"
                                onclick="document.getElementById('predictionForm').reset()"
                                class="w-1/3 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition cursor-pointer">
                            Reset
                        </button>
                        <button type="submit"
                                class="w-2/3 py-2.5 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold shadow-xs flex items-center justify-center gap-2 transition cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Proses Segmentasi & Prediksi Yield</span>
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
                    <span class="text-[10px] font-bold tracking-widest text-[#0e5f38] uppercase">HASIL INFERENSI PIPELINE &bull; BEDENG A-01</span>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Proyeksi Akumulasi Biomassa Segar (Target D60)</h2>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0e5f38] border border-emerald-200 self-start sm:self-auto">
                    PREDIKSI YIELD: 215g (D60)
                </span>
            </div>

            {{-- Masking visualization banner --}}
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-950 overflow-hidden relative shrink-0 border border-emerald-700">
                        <img src="/images/beds/bed_a01.jpg" alt="SegFormer Mask" class="w-full h-full object-cover">
                        <span class="absolute inset-0 bg-emerald-500/30"></span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-900">Masking Kanopi Bebas Latar (Background-Free)</div>
                        <div class="text-[10px] text-gray-500">Ekstraksi kontur hijau SegFormer tanpa netpot & instalasi pipa</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold bg-emerald-100 text-[#0e5f38] px-2 py-0.5 rounded-full">IoU: 94.8%</span>
                    <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full hidden sm:inline">ConvNeXt: Crisphead (99.6%)</span>
                </div>
            </div>

            {{-- Light Biomass Area Chart --}}
            <div class="bg-gradient-to-b from-[#fbfdfc] to-[#f4f9f6] rounded-2xl p-4 sm:p-5 border border-emerald-100/90 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-gray-100">
                    <div>
                        <span class="text-[10.5px] font-bold text-[#0e5f38] uppercase tracking-wider block">KURVA BIOMASSA SEGAR (FRESH WEIGHT TRAJECTORY)</span>
                        <div class="flex items-center gap-3 text-[11px] text-gray-600 mt-1">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="font-medium text-gray-700">Observasi Nyata (19-28 HST)</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-0.5 bg-[#10b981] inline-block"></span>
                                <span class="font-medium text-gray-700">Proyeksi ResNet-34 (D28 s/d D60)</span>
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-emerald-50 text-[#0e5f38] border border-emerald-200">
                            Fase: D28 (104g) &bull; Target: D60 (215g)
                        </span>
                    </div>
                </div>

                {{-- Chart SVG Canvas --}}
                <div class="w-full overflow-x-auto py-3">
                    <div class="min-w-[500px]">
                        <svg viewBox="0 0 650 200" class="w-full h-auto overflow-visible select-none">
                            <defs>
                                <linearGradient id="areaGradientLight" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.28" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.02" />
                                </linearGradient>
                                <filter id="nodeGlowLight" x="-20%" y="-20%" width="140%" height="140%">
                                    <feDropShadow dx="0" dy="1.5" stdDeviation="2" flood-color="#0e5f38" flood-opacity="0.2"/>
                                </filter>
                            </defs>

                            {{-- Horizontal reference grid lines --}}
                            <g class="text-[9px] fill-gray-400 font-medium">
                                <line x1="45" y1="30" x2="620" y2="30" stroke="#e2e8f0" stroke-dasharray="3 3"/>
                                <text x="35" y="33" text-anchor="end">200g</text>

                                <line x1="45" y1="80" x2="620" y2="80" stroke="#e2e8f0" stroke-dasharray="3 3"/>
                                <text x="35" y="83" text-anchor="end">150g</text>

                                <line x1="45" y1="130" x2="620" y2="130" stroke="#e2e8f0" stroke-dasharray="3 3"/>
                                <text x="35" y="133" text-anchor="end">50g</text>

                                <line x1="45" y1="170" x2="620" y2="170" stroke="#cbd5e1" stroke-width="1.5"/>
                            </g>

                            {{-- Area under curve --}}
                            <path d="M 65 170 L 65 158 Q 140 148, 175 142 T 260 102 T 370 78 T 490 54 T 595 24 L 595 170 Z"
                                  fill="url(#areaGradientLight)" />

                            {{-- Solid line (D19 to D28) --}}
                            <path d="M 65 158 Q 140 148, 175 142 T 260 102"
                                  fill="none"
                                  stroke="#f59e0b"
                                  stroke-width="3"
                                  stroke-linecap="round"/>

                            {{-- Dashed line (D28 to D60) --}}
                            <path d="M 260 102 T 370 78 T 490 54 T 595 24"
                                  fill="none"
                                  stroke="#10b981"
                                  stroke-width="2.5"
                                  stroke-dasharray="4 4"
                                  stroke-linecap="round"/>

                            {{-- Data Nodes --}}
                            {{-- D19: 38g --}}
                            <circle cx="65" cy="158" r="4.5" fill="#f59e0b" stroke="#ffffff" stroke-width="2" filter="url(#nodeGlowLight)"/>
                            <text x="65" y="186" text-anchor="middle" class="text-[9.5px] fill-gray-500 font-medium">D19 (38g)</text>

                            {{-- D24: 65g --}}
                            <circle cx="175" cy="142" r="4.5" fill="#f59e0b" stroke="#ffffff" stroke-width="2" filter="url(#nodeGlowLight)"/>
                            <text x="175" y="186" text-anchor="middle" class="text-[9.5px] fill-gray-500 font-medium">D24 (65g)</text>

                            {{-- D28 (Today): 104g --}}
                            <circle cx="260" cy="102" r="6.5" fill="#d97706" stroke="#ffffff" stroke-width="2.5" filter="url(#nodeGlowLight)"/>
                            <text x="260" y="88" text-anchor="middle" class="text-[10.5px] font-bold fill-amber-700">104g</text>
                            <text x="260" y="186" text-anchor="middle" class="text-[9.5px] font-bold fill-amber-700">D28 (Hari Ini)</text>

                            {{-- D35: 142g --}}
                            <circle cx="370" cy="78" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" filter="url(#nodeGlowLight)"/>
                            <text x="370" y="66" text-anchor="middle" class="text-[9.5px] fill-emerald-700 font-semibold">142g</text>
                            <text x="370" y="186" text-anchor="middle" class="text-[9.5px] fill-gray-500 font-medium">D35</text>

                            {{-- D44: 182g --}}
                            <circle cx="490" cy="54" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" filter="url(#nodeGlowLight)"/>
                            <text x="490" y="42" text-anchor="middle" class="text-[9.5px] fill-emerald-700 font-semibold">182g</text>
                            <text x="490" y="186" text-anchor="middle" class="text-[9.5px] fill-gray-500 font-medium">D44</text>

                            {{-- D60 Harvest: 215g --}}
                            <circle cx="595" cy="24" r="6.5" fill="#0e5f38" stroke="#ffffff" stroke-width="2.5" filter="url(#nodeGlowLight)"/>
                            <text x="595" y="14" text-anchor="middle" class="text-[11px] font-extrabold fill-[#0e5f38]">215g (Panen)</text>
                            <text x="595" y="186" text-anchor="middle" class="text-[9.5px] font-bold fill-[#0e5f38]">D60 (Panen)</text>
                        </svg>
                    </div>
                </div>

                {{-- Chart Footer Stats --}}
                <div class="mt-2 pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-[11px] text-gray-600">
                    <div>
                        <span class="font-bold text-[#0e5f38]">ESTIMASI PANEN D60:</span> 215 gram / tanaman (Akumulasi kenaikan: +111 gram dari D28)
                    </div>
                    <div class="text-gray-500 text-xs font-medium">
                        Model ResNet-34 Margin: &plusmn;12 gram (Confidence 95%)
                    </div>
                </div>
            </div>

            {{-- 4 Metric Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                {{-- Card 1: Varietas ConvNeXt --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Hasil Klasifikasi Varietas & Kepercayaan</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm font-bold text-gray-900">Crisphead</span>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">99.6% Confidence</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                        ConvNeXt Multi-Class Classifier memprediksi morfologi daun Crisphead dengan tingkat probabilitas tertinggi di antara 6 varietas selada.
                    </p>
                </div>

                {{-- Card 2: Estimasi Berat Segar --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Estimasi Berat Segar D60</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-base font-extrabold text-gray-900">215 gram</span>
                        <span class="text-xs font-semibold text-gray-500">(Toleransi &plusmn;12g)</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                        Standar panen komersil selada hidroponik dicapai pada siklus 60 hari dengan proyeksi biomassa segar optimal.
                    </p>
                </div>

                {{-- Card 3: Luas Daun Kanopi --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Luas Daun Kanopi Tersegmentasi</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-base font-extrabold text-amber-600">184.2 cm&sup2;</span>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">(SegFormer Mask)</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                        Ekstraksi luas kanopi dari citra bebas latar belakang netpot dan pipa memberikan korelasi kuat terhadap bobot segar tanaman.
                    </p>
                </div>

                {{-- Card 4: Sub-Model ResNet-34 --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 transition">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sub-Model ResNet-34 Aktif</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm font-bold text-purple-900">Klaster HST 25-35</span>
                        <span class="text-xs font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full">R&sup2; = 0.742</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                        RMSE = 8.4g pada fase kanopi menengah. Sub-model otomatis terpicu berdasarkan input umur tanaman 28 HST.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table Section: Riwayat Prediksi Yield & Log Analisis Citra Jurnal --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="pb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Riwayat Prediksi Yield & Log Analisis Citra Jurnal</h2>
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
                           id="predSearchInput"
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                <select id="predBedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Bedeng</option>
                    <option value="Bedeng A-01">Bedeng A-01</option>
                    <option value="Bedeng A-02">Bedeng A-02</option>
                    <option value="Bedeng B-01">Bedeng B-01</option>
                    <option value="Bedeng B-02">Bedeng B-02</option>
                </select>

                <select id="predVarietasFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua 6 Varietas Jurnal</option>
                    <option value="Crisphead">Crisphead</option>
                    <option value="Butterhead">Butterhead</option>
                    <option value="Romaine">Romaine</option>
                    <option value="Looseleaf">Looseleaf</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button"
                        id="tabPredSemua"
                        onclick="setPredTab('semua')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Semua (142)
                </button>
                <button type="button"
                        id="tabPredOptimal"
                        onclick="setPredTab('optimal')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Optimal / Valid (131)
                </button>
                <button type="button"
                        id="tabPredEvaluasi"
                        onclick="setPredTab('evaluasi')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Perlu Evaluasi (11)
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
                            <th scope="col" class="py-3.5 px-3">BATCH / BEDENG</th>
                            <th scope="col" class="py-3.5 px-3">CITRA & INPUT (SEGFORMER)</th>
                            <th scope="col" class="py-3.5 px-3">KLASIFIKASI VARIETAS</th>
                            <th scope="col" class="py-3.5 px-3">HASIL PREDIKSI YIELD</th>
                            <th scope="col" class="py-3.5 px-3">METRIK MODEL</th>
                            <th scope="col" class="py-3.5 px-3">STATUS</th>
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
            <div id="predTableInfo">Menampilkan 4 dari 142 sesi estimasi Deep Learning</div>
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
let predFilterStatus = 'semua';

const historyPredictions = [
    {
        id: '#DL-2024-0091',
        tanggal: '24 Okt 2024 &bull; 09:15 WIB',
        bedeng: 'Bedeng A-01',
        kodeBatch: 'SLD-2024-C01',
        img: '/images/beds/bed_a01.jpg',
        hst: '28 Hari',
        skala: '10cm = 320px',
        kanopi: '184.2 cm²',
        varietas: 'Crisphead',
        confidence: '99.6%',
        yieldD60: '215 gram',
        yieldSaatIni: 'Saat ini (D28): 104g (+111g)',
        r2: '0.742',
        iou: '94.8%',
        rmse: '8.4g',
        status: 'Terverifikasi',
        type: 'optimal'
    },
    {
        id: '#DL-2024-0090',
        tanggal: '23 Okt 2024 &bull; 14:30 WIB',
        bedeng: 'Bedeng A-02',
        kodeBatch: 'SLD-2024-B02',
        img: '/images/beds/bed_a02.jpg',
        hst: '32 Hari',
        skala: '10cm = 320px',
        kanopi: '172.5 cm²',
        varietas: 'Butterhead',
        confidence: '98.2%',
        yieldD60: '198 gram',
        yieldSaatIni: 'Saat ini (D32): 126g (+72g)',
        r2: '0.710',
        iou: '95.1%',
        rmse: '7.1g',
        status: 'Terverifikasi',
        type: 'optimal'
    },
    {
        id: '#DL-2024-0089',
        tanggal: '22 Okt 2024 &bull; 11:10 WIB',
        bedeng: 'Bedeng B-01',
        kodeBatch: 'SLD-2024-R03',
        img: '/images/beds/bed_b01.jpg',
        hst: '22 Hari',
        skala: '10cm = 320px',
        kanopi: '114.8 cm²',
        varietas: 'Romaine',
        confidence: '97.5%',
        yieldD60: '206 gram',
        yieldSaatIni: 'Saat ini (D22): 54g (+152g)',
        r2: '0.685',
        iou: '93.9%',
        rmse: '10.2g',
        status: 'Terverifikasi',
        type: 'optimal'
    },
    {
        id: '#DL-2024-0088',
        tanggal: '21 Okt 2024 &bull; 16:45 WIB',
        bedeng: 'Bedeng B-02',
        kodeBatch: 'SLD-2024-L04',
        img: '/images/beds/bed_b02.jpg',
        hst: '35 Hari',
        skala: '10cm = 320px',
        kanopi: '196.4 cm²',
        varietas: 'Looseleaf',
        confidence: '96.1%',
        yieldD60: '195 gram',
        yieldSaatIni: 'Saat ini (D35): 140g (+55g)',
        r2: '0.725',
        iou: '94.2%',
        rmse: '8.8g',
        status: 'Terverifikasi',
        type: 'optimal'
    }
];

function renderPredTable() {
    const tbody = document.getElementById('predTableBody');
    const searchVal = document.getElementById('predSearchInput').value.toLowerCase().trim();
    const bedengVal = document.getElementById('predBedengFilter').value;
    const varietasVal = document.getElementById('predVarietasFilter').value;

    const filtered = historyPredictions.filter(item => {
        if (predFilterStatus === 'optimal' && item.type !== 'optimal') return false;
        if (predFilterStatus === 'evaluasi' && item.type !== 'evaluasi') return false;
        if (searchVal && !item.id.toLowerCase().includes(searchVal) && !item.bedeng.toLowerCase().includes(searchVal) && !item.varietas.toLowerCase().includes(searchVal)) {
            return false;
        }
        if (bedengVal && item.bedeng !== bedengVal) return false;
        if (varietasVal && item.varietas !== varietasVal) return false;
        return true;
    });

    tbody.innerHTML = '';

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="py-8 text-center text-gray-400 text-xs">
                    Tidak ada riwayat prediksi yield yang sesuai filter.
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
                <div class="text-[11px] text-gray-400 mt-0.5">${item.kodeBatch}</div>
            </td>
            <td class="py-4 px-3">
                <div class="flex items-center gap-2.5">
                    <img src="${item.img}" alt="Thumb" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shrink-0">
                    <div>
                        <div class="text-xs font-bold text-gray-900">HST: ${item.hst}</div>
                        <div class="text-[10.5px] text-gray-400">Skala: ${item.skala}</div>
                        <div class="text-[10.5px] font-semibold text-emerald-600">Kanopi: ${item.kanopi}</div>
                    </div>
                </div>
            </td>
            <td class="py-4 px-3">
                <div class="font-bold text-gray-900">${item.varietas}</div>
                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    ${item.confidence} Confidence
                </span>
            </td>
            <td class="py-4 px-3">
                <div class="font-bold text-emerald-600 text-sm">D60: ${item.yieldD60}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">${item.yieldSaatIni}</div>
            </td>
            <td class="py-4 px-3">
                <div class="text-xs font-semibold text-gray-800">R&sup2;: ${item.r2}</div>
                <div class="text-[10.5px] text-gray-500">IoU: ${item.iou}</div>
                <div class="text-[10.5px] text-gray-400">RMSE: ${item.rmse}</div>
            </td>
            <td class="py-4 px-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>${item.status}</span>
                </span>
            </td>
            <td class="py-4 pl-3 text-right">
                <div class="inline-flex items-center gap-1.5 justify-end">
                    <button type="button" onclick="alert('Membuka detail segmentasi SegFormer ' + '${item.id}')" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Lihat Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="alert('Mengunduh hasil segmentasi citra ' + '${item.id}')" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Unduh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                    <button type="button" onclick="deletePredRow('${item.id}')" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
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

function setPredTab(tab) {
    predFilterStatus = tab;
    const tSemua = document.getElementById('tabPredSemua');
    const tOpt = document.getElementById('tabPredOptimal');
    const tEval = document.getElementById('tabPredEvaluasi');

    [tSemua, tOpt, tEval].forEach(b => {
        b.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer";
    });

    if (tab === 'semua') {
        tSemua.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
    } else if (tab === 'optimal') {
        tOpt.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
    } else {
        tEval.className = "px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer";
    }

    renderPredTable();
}

function handleRunPrediction(e) {
    e.preventDefault();
    const bed = document.getElementById('predBedeng').value;
    const hst = document.getElementById('predHst').value;
    const varietas = document.getElementById('predVarietas').value;

    alert(`Memproses segmentasi citra SegFormer & inferensi ResNet-34 untuk ${bed} (${varietas}, ${hst} HST)... Hasil: Prediksi Yield D60: 215g (Valid, IoU 94.8%)`);
}

function handleImagePicked(e) {
    if (e.target.files && e.target.files[0]) {
        alert('Citra kanopi ' + e.target.files[0].name + ' berhasil diunggah. Siap diproses oleh SegFormer!');
    }
}

function deletePredRow(id) {
    if (confirm('Hapus riwayat prediksi ' + id + '?')) {
        const idx = historyPredictions.findIndex(p => p.id === id);
        if (idx !== -1) {
            historyPredictions.splice(idx, 1);
            renderPredTable();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('predSearchInput').addEventListener('input', renderPredTable);
    document.getElementById('predBedengFilter').addEventListener('change', renderPredTable);
    document.getElementById('predVarietasFilter').addEventListener('change', renderPredTable);
    renderPredTable();
});
</script>
@endpush
