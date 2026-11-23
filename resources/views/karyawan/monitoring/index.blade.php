@extends('layouts.karyawan', ['active' => 'monitoring'])

@section('title', 'Monitoring & Log Pertumbuhan')

@section('content')
@php
    $bedImages = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];
    $activePlants = $metrics['active_plants'] ?? [];
    $firstChartData = ($firstPlantId !== null && isset($plantCharts[$firstPlantId])) ? $plantCharts[$firstPlantId] : [
        'records' => [],
        'meta' => [],
        'chart' => [],
        'svg' => [
            'has_data' => false,
            'y_ticks' => [
                ['y' => 40, 'label' => '20 cm'],
                ['y' => 95, 'label' => '15 cm'],
                ['y' => 150, 'label' => '10 cm'],
                ['y' => 205, 'label' => '5 cm'],
                ['y' => 260, 'label' => '0'],
            ],
            'points' => [],
            'actual_path' => '',
            'ideal_path' => '',
        ],
    ];
    $initialSvg = $firstChartData['svg'] ?? [];
    $initialRecords = $firstChartData['records'] ?? [];
    $initialMeta = $firstChartData['meta'] ?? [];
    $latestInitialRec = count($initialRecords) > 0 ? $initialRecords[count($initialRecords) - 1] : null;
@endphp

<div class="space-y-6 max-w-7xl mx-auto pb-12">
    @if(!empty($apiError))
        <div id="apiWarningBanner" class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-xs font-medium flex items-center justify-between gap-3">
            <span>{{ $apiError }}</span>
            <button type="button" onclick="window.location.reload()" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 rounded-md font-semibold cursor-pointer">Muat Ulang</button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Monitoring &amp; Log Pertumbuhan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kurva pertumbuhan &amp; log observasi lapangan aktual dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">growth_records</code></p>
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
                    onclick="openLogModal('tambah')"
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
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">TOTAL BATCH AKTIF</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statBatchAktif" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['active_count'] ?? 0 }}</span>
                <span class="text-sm font-semibold text-gray-500">Batch</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs gap-2">
                <span id="statBatchSub1" class="text-emerald-600 font-medium">{{ $metrics['batch_sub1'] ?? '-' }}</span>
                <span id="statBatchSub2" class="text-gray-400">{{ $metrics['batch_sub2'] ?? '0 Log Tercatat' }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">POPULASI DITANAM</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statPopulasi" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['populasi_value'] ?? '0' }}</span>
                <span id="statPopulasiUnit" class="text-sm font-semibold text-gray-500">{{ $metrics['populasi_unit'] ?? 'Lubang' }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs gap-2">
                <span id="statPopulasiSub1" class="text-emerald-700 font-medium">{{ $metrics['populasi_sub1'] ?? '-' }}</span>
                <span id="statPopulasiSub2" class="text-gray-400 truncate">{{ $metrics['populasi_sub2'] ?? '-' }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">ESTIMASI PANEN</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statSiapPanen" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['panen_value'] ?? '0.0' }}</span>
                <span id="statSiapPanenUnit" class="text-sm font-semibold text-gray-500">{{ $metrics['panen_unit'] ?? 'kg' }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs gap-2">
                <span id="statSiapPanenSub1" class="text-emerald-700 font-medium">{{ $metrics['panen_sub1'] ?? '-' }}</span>
                <span id="statSiapPanenSub2" class="text-gray-400">{{ $metrics['panen_sub2'] ?? '-' }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">RATA-RATA KESEHATAN</div>
            <div class="mt-2 flex items-baseline gap-1">
                <span id="statKesehatan" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['health_value'] ?? '0.0' }}</span>
                <span class="text-lg font-bold text-gray-600">%</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs gap-2">
                <span id="statKesehatanSub1" class="text-emerald-600 font-semibold">{{ $metrics['health_sub1'] ?? '-' }}</span>
                <span id="statKesehatanSub2" class="text-gray-400">{{ $metrics['health_sub2'] ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Status Bedeng Aktif Section --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Status Bedeng &amp; Batch Aktif</h2>
                <p class="text-xs text-gray-500">Klik kartu batch untuk menampilkan kurva pertumbuhannya pada grafik</p>
            </div>
            <a href="#tableSection" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0e5f38] hover:text-[#0b4e2e] transition">
                <span>Lihat Log Pertumbuhan</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <div id="activeBedsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($activePlants as $idx => $p)
                @php
                    $img = $bedImages[$idx % count($bedImages)];
                    $days = $p['hari_menuju_panen'] ?? null;
                    $latest = is_array($p['latest_growth_record'] ?? null) ? $p['latest_growth_record'] : null;
                    $panenBadge = $days === null ? '-' : ($days < 0 ? 'Siap Panen' : ($days === 0 ? 'Hari Ini' : $days . ' Hari Lagi'));
                @endphp
                <div onclick="selectChartPlant({{ $p['id'] }})" class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-300 hover:shadow-md transition cursor-pointer">
                    <div class="relative h-36 bg-gray-100 overflow-hidden">
                        <img src="/images/beds/{{ $img }}" alt="{{ $p['kode_tanaman'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-[#0e5f38]/90 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                            HST {{ $p['umur_hari'] ?? 0 }}
                        </span>
                    </div>
                    <div class="p-3.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-gray-900 text-sm">{{ $p['kode_tanaman'] }}</span>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full capitalize">{{ str_replace('_', ' ', (string) ($p['status'] ?? 'aktif')) }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $p['nama'] }}{{ !empty($p['varietas']) ? ' · ' . $p['varietas'] : '' }}</p>
                        @if($latest)
                            <p class="text-[11px] font-medium text-emerald-700 mt-1">
                                {{ number_format((float) ($latest['tinggi_cm'] ?? 0), 2) }} cm · {{ number_format((float) ($latest['berat_gram'] ?? 0), 2) }} g
                            </p>
                        @endif
                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                            <span>Est. Panen:</span>
                            <span class="font-medium text-gray-800">{{ $p['tanggal_panen'] ?? '-' }} ({{ $panenBadge }})</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-xs text-gray-400">Belum ada batch tanaman aktif.</div>
            @endforelse
        </div>
    </div>

    {{-- Growth Curve Chart --}}
    <div id="chartSection" class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Kurva Pertumbuhan Tajuk &amp; Bobot Biomassa (HST)</h2>
                <p id="chartPlantDesc" class="text-xs text-gray-500 mt-0.5">
                    @if($firstPlant)
                        {{ $firstPlant['kode_tanaman'] }} • {{ $firstPlant['nama'] }} ({{ $firstPlant['varietas'] ?? '-' }}) — {{ $firstPlant['keterangan'] ?? '' }}
                    @else
                        Pilih batch tanaman untuk melihat kurva pertumbuhan
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <select id="chartBedengSelect"
                        onchange="updateChartData()"
                        class="px-3.5 py-1.5 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    @foreach($plants ?? [] as $p)
                        <option value="{{ $p['id'] }}" {{ (int) ($p['id'] ?? 0) === (int) ($firstPlantId ?? 0) ? 'selected' : '' }}>
                            {{ $p['kode_tanaman'] }}{{ !empty($p['varietas']) ? ' (' . $p['varietas'] . ')' : '' }}
                        </option>
                    @endforeach
                </select>

                <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl text-xs">
                    <button type="button" onclick="setPeriod(7)" id="btnP7" class="px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900 transition font-medium cursor-pointer">7 Hari</button>
                    <button type="button" onclick="setPeriod(14)" id="btnP14" class="px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900 transition font-medium cursor-pointer">14 Hari</button>
                    <button type="button" onclick="setPeriod('full')" id="btnPFull" class="px-3 py-1 rounded-lg bg-white text-gray-900 font-semibold shadow-2xs border border-gray-200/80 transition cursor-pointer">Siklus Penuh</button>
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

                    <g id="chartYAxisGroup">
                        @foreach(($initialSvg['y_ticks'] ?? []) as $idx => $tick)
                            <g class="text-[11px] fill-gray-400">
                                <text x="42" y="{{ $tick['y'] + 4 }}" text-anchor="end">{{ $tick['label'] }}</text>
                                <line x1="55" y1="{{ $tick['y'] }}" x2="860" y2="{{ $tick['y'] }}"
                                      stroke="{{ $idx === count($initialSvg['y_ticks'] ?? []) - 1 ? '#e2e8f0' : '#f1f5f9' }}"
                                      stroke-dasharray="{{ $idx === count($initialSvg['y_ticks'] ?? []) - 1 ? '' : '4 4' }}"
                                      stroke-width="1.5"/>
                            </g>
                        @endforeach
                    </g>

                    {{-- Ideal Reference Trajectory (Dashed line) --}}
                    <path id="projectedCurvePath"
                          d="{{ $initialSvg['ideal_path'] ?? '' }}"
                          fill="none"
                          stroke="#94a3b8"
                          stroke-width="2"
                          stroke-dasharray="5 5"
                          stroke-linecap="round"/>

                    {{-- Actual Measurements Path --}}
                    <path id="actualCurvePath"
                          d="{{ $initialSvg['actual_path'] ?? '' }}"
                          fill="none"
                          stroke="url(#chartLineGrad)"
                          stroke-width="3.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>

                    <g id="chartNodesGroup">
                        @foreach(($initialSvg['points'] ?? []) as $pt)
                            @if(!empty($pt['is_last']))
                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="6" fill="#0e5f38" stroke="#ffffff" stroke-width="2.5" filter="url(#nodeGlow)">
                                    <title>{{ $pt['label'] }}: {{ number_format((float) $pt['tinggi_cm'], 2) }} cm</title>
                                </circle>
                            @else
                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#ffffff" stroke="#10b981" stroke-width="3" filter="url(#nodeGlow)">
                                    <title>{{ $pt['label'] }}: {{ number_format((float) $pt['tinggi_cm'], 2) }} cm</title>
                                </circle>
                            @endif
                        @endforeach
                    </g>

                    <g id="chartXAxisGroup">
                        @foreach(($initialSvg['points'] ?? []) as $pt)
                            <text x="{{ $pt['x'] }}" y="285" text-anchor="middle" class="text-[11px] {{ !empty($pt['is_last']) ? 'fill-[#0e5f38] font-bold' : 'fill-gray-500 font-medium' }}">
                                {{ $pt['label'] }} ({{ number_format((float) $pt['tinggi_cm'], 1) }} cm)
                            </text>
                        @endforeach
                    </g>

                    {{-- Legend --}}
                    <circle cx="670" cy="14" r="4" fill="#0e5f38"/>
                    <text x="680" y="18" class="text-[11px] fill-gray-600 font-medium">Tinggi Aktual</text>
                    <line x1="765" y1="14" x2="788" y2="14" stroke="#94a3b8" stroke-width="2" stroke-dasharray="5 5"/>
                    <text x="795" y="18" class="text-[11px] fill-gray-500">Kurva Ideal</text>
                </svg>
            </div>
        </div>

        {{-- Chart Key Statistics --}}
        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Tinggi Tajuk Terakhir</span>
                <span id="statChartHeight" class="text-sm font-bold text-gray-900 mt-0.5 block">
                    {{ $latestInitialRec && isset($latestInitialRec['tinggi_cm']) ? number_format((float) $latestInitialRec['tinggi_cm'], 2) . ' cm (H-' . ($latestInitialRec['umur_tanaman'] ?? '-') . ')' : '-' }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Kenaikan &amp; Laju Rerata</span>
                <span id="statChartRate" class="text-sm font-bold text-emerald-600 mt-0.5 block">
                    @if(isset($initialMeta['tinggi_gain_cm']))
                        +{{ number_format((float) $initialMeta['tinggi_gain_cm'], 2) }} cm (+{{ number_format((float) ($initialMeta['avg_daily_height_growth_cm'] ?? 0), 2) }} cm/hr)
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Biomassa &amp; Daun Terakhir</span>
                <span id="statChartBiomass" class="text-sm font-bold text-gray-900 mt-0.5 block">
                    {{ $latestInitialRec ? (number_format((float) ($latestInitialRec['berat_gram'] ?? 0), 2) . ' g (' . ($latestInitialRec['jumlah_daun'] ?? '-') . ' helai)') : '-' }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <span class="text-gray-400 block text-[11px]">Parameter Nutrisi Terkini</span>
                <span id="statChartWater" class="text-sm font-bold text-[#0e5f38] mt-0.5 block">
                    {{ $latestInitialRec ? ('pH ' . ($latestInitialRec['ph_air'] ?? '-') . ' • EC ' . ($latestInitialRec['ec'] ?? '-') . ' mS/cm') : '-' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Log Pertumbuhan Table Section --}}
    <div id="tableSection" class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-5 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           id="batchSearchInput"
                           placeholder="Cari kode tanaman, catatan, atau petugas..."
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                <select id="bedengFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Batch Tanaman</option>
                    @foreach($plants ?? [] as $p)
                        <option value="{{ $p['id'] }}">{{ $p['kode_tanaman'] }} ({{ $p['varietas'] ?? $p['nama'] }})</option>
                    @endforeach
                </select>

                <select id="faseFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Umur (HST)</option>
                    <option value="awal">Awal (0 - 14 HST)</option>
                    <option value="tengah">Vegetatif (15 - 25 HST)</option>
                    <option value="panen">Menjelang Panen (26+ HST)</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button" id="tabActive" onclick="setTabStatus('aktif')" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Batch Aktif
                </button>
                <button type="button" id="tabFinished" onclick="setTabStatus('semua')" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Semua Log ({{ count($growthLogs ?? []) }})
                </button>
            </div>
        </div>

        <div class="overflow-x-auto -mx-5 sm:-mx-6 mt-2">
            <div class="inline-block min-w-full align-middle px-5 sm:px-6">
                <table class="min-w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th scope="col" class="py-3.5 pr-4">BATCH TANAMAN</th>
                            <th scope="col" class="py-3.5 px-4">TANGGAL &amp; UMUR (HST)</th>
                            <th scope="col" class="py-3.5 px-4">MORFOLOGI</th>
                            <th scope="col" class="py-3.5 px-4">AIR &amp; CATATAN</th>
                            <th scope="col" class="py-3.5 pl-4 text-right">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody id="batchTableBody" class="divide-y divide-gray-100 text-gray-700">
                        @forelse(array_slice($growthLogs ?? [], 0, 8) as $log)
                            <tr class="hover:bg-gray-50/70 transition group">
                                <td class="py-4 pr-4">
                                    <div class="font-bold text-gray-900">{{ $log['kode_tanaman'] }} — {{ $log['nama_tanaman'] }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $log['varietas'] ?? '-' }} • {{ $log['recorded_by_nama'] ?? 'Staf' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-medium text-gray-900">{{ !empty($log['tanggal_pengamatan']) ? \Carbon\Carbon::parse($log['tanggal_pengamatan'])->format('d M Y, H:i') : '-' }}</div>
                                    <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">HST {{ $log['umur_tanaman'] ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-emerald-700">{{ isset($log['tinggi_cm']) ? number_format((float) $log['tinggi_cm'], 2) . ' cm' : '-' }} • {{ isset($log['berat_gram']) ? number_format((float) $log['berat_gram'], 2) . ' g' : '-' }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $log['jumlah_daun'] ?? '-' }} helai daun</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-gray-800">pH {{ $log['ph_air'] ?? '-' }} • EC {{ $log['ec'] ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $log['catatan'] ?? '-' }}</div>
                                </td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <button type="button" onclick="showLogDetail({{ $log['id'] }})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition cursor-pointer">Detail</button>
                                        <button type="button" onclick="editLog({{ $log['id'] }})" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition cursor-pointer">Edit</button>
                                        <button type="button" onclick="deleteLog({{ $log['plant_id'] }}, {{ $log['id'] }})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada log observasi pertumbuhan di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
            <div id="tableInfo">Menampilkan {{ min(8, count($growthLogs ?? [])) }} dari {{ count($growthLogs ?? []) }} log pertumbuhan</div>
            <div class="flex items-center gap-1 self-center sm:self-auto">
                <button type="button" id="btnPrevLog" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">Sebelumnya</button>
                <div id="logPageButtons" class="flex items-center gap-1"></div>
                <button type="button" id="btnNextLog" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">Berikutnya</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Tambah / Edit Data Pertumbuhan --}}
<div id="addLogModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeLogModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 flex items-center justify-center">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 text-left">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#0e5f38] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 id="logModalTitle" class="text-base font-bold text-gray-900">Catat Log Pertumbuhan Harian</h3>
                </div>
                <button type="button" onclick="closeLogModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="addLogForm" onsubmit="handleSaveLog(event)" class="mt-4 space-y-4">
                <input type="hidden" id="logFormMode" value="tambah">
                <input type="hidden" id="editLogId" value="">

                <div id="logModalError" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium rounded-xl px-3.5 py-2.5"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="logPlantId" class="block text-xs font-semibold text-gray-700 mb-1">Batch Tanaman <span class="text-rose-500">*</span></label>
                        <select id="logPlantId" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                            @foreach($plants ?? [] as $p)
                                <option value="{{ $p['id'] }}">{{ $p['kode_tanaman'] }} — {{ $p['nama'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="logTanggal" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Pengamatan <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" id="logTanggal" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label for="logHst" class="block text-xs font-semibold text-gray-700 mb-1">HST (opsional)</label>
                        <input type="number" id="logHst" min="0" max="365" placeholder="Otomatis" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="logTinggi" class="block text-xs font-semibold text-gray-700 mb-1">Tinggi (cm) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0" id="logTinggi" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="logDaun" class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Daun</label>
                        <input type="number" min="0" id="logDaun" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="logBiomassa" class="block text-xs font-semibold text-gray-700 mb-1">Berat (gram)</label>
                        <input type="number" step="0.01" min="0" id="logBiomassa" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label for="logPh" class="block text-xs font-semibold text-gray-700 mb-1">pH Air</label>
                        <input type="number" step="0.1" min="0" max="14" id="logPh" value="6.0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="logEc" class="block text-xs font-semibold text-gray-700 mb-1">EC (mS/cm)</label>
                        <input type="number" step="0.01" min="0" max="20" id="logEc" value="1.2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="logSuhuAir" class="block text-xs font-semibold text-gray-700 mb-1">Suhu Air (°C)</label>
                        <input type="number" step="0.1" id="logSuhuAir" value="22.0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="logKelembapan" class="block text-xs font-semibold text-gray-700 mb-1">Kelembapan (%)</label>
                        <input type="number" step="0.1" min="0" max="100" id="logKelembapan" value="70" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div>
                    <label for="logCatatan" class="block text-xs font-semibold text-gray-700 mb-1">Catatan Kondisi</label>
                    <textarea id="logCatatan" rows="2" placeholder="Contoh: Pengamatan rutin pagi hari" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeLogModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer">Batal</button>
                    <button type="submit" id="btnSaveLog" class="px-4 py-2 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold transition cursor-pointer shadow-xs disabled:opacity-60">Simpan Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Log --}}
<div id="detailLogModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeDetailLogModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 flex items-center justify-center">
        <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 text-left">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900" id="detailLogTitle">Detail Log Pertumbuhan</h3>
                <button type="button" onclick="closeDetailLogModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="detailLogContent" class="py-4 space-y-2.5 text-xs sm:text-sm text-gray-700"></div>
            <div class="pt-3 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closeDetailLogModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="monitoringToastNotification"
     class="fixed bottom-4 sm:bottom-6 right-4 sm:right-6 max-w-sm z-50 transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-2.5 bg-gray-900 text-white text-xs font-medium px-4 py-3 rounded-xl shadow-lg pointer-events-none">
    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
    <span id="monitoringToastMessage">Berhasil</span>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const apiHost = window.location.hostname === '127.0.0.1' ? '127.0.0.1' : 'localhost';
    const BE_API_URL = `http://${apiHost}:8000/api/v1`;
    const BED_IMAGES = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];
    const ACTIVE_STATUSES = ['pembibitan', 'pertumbuhan', 'siap_panen'];

    let plantsData = @json($plants ?? []);
    let plantCharts = @json($plantCharts ?? []);
    let growthLogs = @json($growthLogs ?? []);
    let diagnosisData = @json($diagnosisItems ?? []);
    let diagnosisStats = @json($diagnosisStats ?? []);
    let currentTab = 'aktif';
    let currentPeriod = 'full';
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

    function formatNum(val, dec = 2) {
        const num = Number(val);
        if (!Number.isFinite(num)) return '-';
        return num.toFixed(dec);
    }

    function formatDateTimeId(iso) {
        if (!iso) return '-';
        const dt = new Date(iso);
        if (Number.isNaN(dt.getTime())) return String(iso);
        return dt.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function toLocalInputValue(iso) {
        const dt = iso ? new Date(iso) : new Date();
        const valid = Number.isNaN(dt.getTime()) ? new Date() : dt;
        const local = new Date(valid.getTime() - valid.getTimezoneOffset() * 60000);
        return local.toISOString().slice(0, 16);
    }

    function showToast(msg) {
        const toast = document.getElementById('monitoringToastNotification');
        const msgEl = document.getElementById('monitoringToastMessage');
        if (!toast || !msgEl) return;
        clearTimeout(toastTimer);
        msgEl.textContent = msg;
        toast.classList.remove('translate-y-20', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
        toastTimer = setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }

    function updateCardsAndSelectors() {
        const active = plantsData.filter(p => ACTIVE_STATUSES.includes(p.status));
        let totalLubang = 0;
        let estGrams = 0;
        let actGrams = 0;
        let minDays = null;
        let nearest = null;
        let readyCount = 0;
        const varietasSet = new Set();
        const phVals = [];
        const ecVals = [];
        let optimal = 0;
        let measured = 0;

        active.forEach(p => {
            const m = String(p.keterangan || '').match(/(\d+)\s*lubang/i);
            const lubang = m ? Number(m[1]) : 0;
            totalLubang += lubang;
            if (p.varietas) varietasSet.add(p.varietas);
            const latest = p.latest_growth_record || null;
            const berat = latest && latest.berat_gram !== null && latest.berat_gram !== undefined ? Number(latest.berat_gram) : 0;
            const mult = lubang > 0 ? lubang : 1;
            if (berat > 0) actGrams += berat * mult;
            estGrams += Math.max(berat, 170) * mult;

            if (p.hari_menuju_panen !== null && p.hari_menuju_panen !== undefined) {
                const d = Number(p.hari_menuju_panen);
                if (d <= 0) readyCount++;
                if (minDays === null || d < minDays) { minDays = d; nearest = p; }
            }
            if (latest) {
                const ph = latest.ph_air !== null && latest.ph_air !== undefined ? Number(latest.ph_air) : null;
                const ec = latest.ec !== null && latest.ec !== undefined ? Number(latest.ec) : null;
                if (ph !== null) phVals.push(ph);
                if (ec !== null) ecVals.push(ec);
                if (ph !== null || ec !== null) {
                    measured++;
                    if ((ph === null || (ph >= 5.5 && ph <= 6.5)) && (ec === null || (ec >= 0.8 && ec <= 1.8))) optimal++;
                }
            }
        });

        const setEl = (id, t) => { const el = document.getElementById(id); if (el) el.textContent = t; };
        setEl('statBatchAktif', String(active.length));
        setEl('statBatchSub1', readyCount > 0 ? `${readyCount} Batch Siap Panen` : (minDays !== null ? `Panen ${minDays} Hari Lagi` : 'Belum Ada Jadwal'));
        setEl('statBatchSub2', `${growthLogs.length} Log Tercatat`);
        setEl('statPopulasi', totalLubang > 0 ? new Intl.NumberFormat('id-ID').format(totalLubang) : String(active.length));
        setEl('statPopulasiUnit', totalLubang > 0 ? 'Lubang' : 'Batch');
        setEl('statPopulasiSub1', `${active.length} Batch Beroperasi`);
        setEl('statPopulasiSub2', Array.from(varietasSet).slice(0, 2).join(', ') || '-');

        const estKg = estGrams / 1000;
        const actKg = actGrams / 1000;
        setEl('statSiapPanen', estKg >= 1000 ? formatNum(estKg / 1000, 2) : formatNum(estKg, 1));
        setEl('statSiapPanenUnit', estKg >= 1000 ? 'Ton' : 'kg');
        setEl('statSiapPanenSub1', nearest ? nearest.kode_tanaman : '-');
        setEl('statSiapPanenSub2', `Terukur: ${formatNum(actKg, 2)} kg`);

        const totalDiag = diagnosisData.length;
        if (totalDiag > 0) {
            const healthy = Number(diagnosisStats?.Healthy || 0);
            const pct = (healthy / totalDiag) * 100;
            setEl('statKesehatan', formatNum(pct, 1));
            setEl('statKesehatanSub1', pct >= 80 ? 'Kondisi Prima' : 'Perlu Penanganan');
            setEl('statKesehatanSub2', `${healthy}/${totalDiag} Diagnosis Sehat`);
        } else if (measured > 0) {
            const pct = (optimal / measured) * 100;
            const avgPh = phVals.length ? (phVals.reduce((a, b) => a + b, 0) / phVals.length).toFixed(1) : '-';
            const avgEc = ecVals.length ? (ecVals.reduce((a, b) => a + b, 0) / ecVals.length).toFixed(2) : '-';
            setEl('statKesehatan', formatNum(pct, 1));
            setEl('statKesehatanSub1', optimal === measured ? 'Parameter Air Optimal' : 'Cek pH / EC Nutrisi');
            setEl('statKesehatanSub2', `pH ${avgPh} • EC ${avgEc} mS/cm`);
        }

        const tabFinished = document.getElementById('tabFinished');
        if (tabFinished) tabFinished.textContent = `Semua Log (${growthLogs.length})`;

        const populatePlantSelect = (selectEl, includeAllLabel) => {
            if (!selectEl) return;
            const cur = selectEl.value;
            let html = includeAllLabel ? `<option value="">${esc(includeAllLabel)}</option>` : '';
            html += plantsData.map(p => `<option value="${p.id}" ${String(p.id) === String(cur) ? 'selected' : ''}>${esc(p.kode_tanaman)}${p.varietas ? ' (' + esc(p.varietas) + ')' : ''}</option>`).join('');
            selectEl.innerHTML = html;
            if (!includeAllLabel && !cur && plantsData.length > 0) {
                selectEl.value = String(plantsData[0].id);
            }
        };

        populatePlantSelect(document.getElementById('chartBedengSelect'), null);
        populatePlantSelect(document.getElementById('bedengFilter'), 'Semua Batch Tanaman');
        populatePlantSelect(document.getElementById('logPlantId'), null);
    }

    function renderActiveBedsGrid() {
        const grid = document.getElementById('activeBedsGrid');
        if (!grid) return;
        const active = plantsData.filter(p => ACTIVE_STATUSES.includes(p.status));
        if (active.length === 0) {
            grid.innerHTML = '<div class="col-span-full py-6 text-center text-xs text-gray-400">Belum ada batch tanaman aktif.</div>';
            return;
        }
        grid.innerHTML = active.map((p, idx) => {
            const img = BED_IMAGES[idx % BED_IMAGES.length];
            const d = p.hari_menuju_panen;
            const panenBadge = d === null || d === undefined ? '-' : (d < 0 ? 'Siap Panen' : (d === 0 ? 'Hari Ini' : `${d} Hari Lagi`));
            const latest = p.latest_growth_record || null;
            const metricLine = latest
                ? `<p class="text-[11px] font-medium text-emerald-700 mt-1">${formatNum(latest.tinggi_cm, 2)} cm · ${formatNum(latest.berat_gram, 2)} g</p>`
                : '';
            return `
                <div onclick="selectChartPlant(${p.id})" class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-300 hover:shadow-md transition cursor-pointer">
                    <div class="relative h-36 bg-gray-100 overflow-hidden">
                        <img src="/images/beds/${img}" alt="${esc(p.kode_tanaman)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-[#0e5f38]/90 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">HST ${esc(p.umur_hari ?? 0)}</span>
                    </div>
                    <div class="p-3.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-gray-900 text-sm">${esc(p.kode_tanaman)}</span>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full capitalize">${esc(String(p.status || 'aktif').replace(/_/g, ' '))}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">${esc(p.nama)}${p.varietas ? ' · ' + esc(p.varietas) : ''}</p>
                        ${metricLine}
                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                            <span>Est. Panen:</span>
                            <span class="font-medium text-gray-800">${esc(p.tanggal_panen || '-')} (${esc(panenBadge)})</span>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    function renderSvgChartForPlant(plantId) {
        const plant = plantsData.find(p => Number(p.id) === Number(plantId)) || null;
        const data = plantCharts[plantId] || { records: [], meta: {}, chart: {} };
        const allRecords = Array.isArray(data.records) ? data.records : [];
        const chartSeries = data.chart?.series || {};
        const idealAll = Array.isArray(chartSeries.ideal_height_cm) ? chartSeries.ideal_height_cm : [];

        const descEl = document.getElementById('chartPlantDesc');
        if (descEl && plant) {
            descEl.textContent = `${plant.kode_tanaman} • ${plant.nama} (${plant.varietas || '-'}) — ${plant.keterangan || ''}`;
        }

        let paired = allRecords.map((r, idx) => ({
            rec: r,
            tinggi: r.tinggi_cm !== null && r.tinggi_cm !== undefined ? Number(r.tinggi_cm) : null,
            ideal: idealAll[idx] !== null && idealAll[idx] !== undefined ? Number(idealAll[idx]) : null,
        })).filter(item => item.tinggi !== null && Number.isFinite(item.tinggi));

        if (currentPeriod !== 'full' && paired.length > 0) {
            const maxHst = Math.max(...paired.map(p => Number(p.rec.umur_tanaman || 0)));
            const cutoff = maxHst - Number(currentPeriod);
            const filtered = paired.filter(p => Number(p.rec.umur_tanaman || 0) >= cutoff);
            if (filtered.length > 0) paired = filtered;
        }

        const allVals = [4];
        paired.forEach(p => {
            allVals.push(p.tinggi);
            if (p.ideal !== null && Number.isFinite(p.ideal)) allVals.push(p.ideal);
        });
        const maxVal = Math.max(...allVals);
        const step = maxVal <= 8 ? 2 : 5;
        const maxY = Math.max(step * 4, Math.ceil(maxVal / step) * step);

        const yTicks = [
            { y: 40, label: `${maxY} cm` },
            { y: 95, label: `${Math.round(maxY * 0.75)} cm` },
            { y: 150, label: `${Math.round(maxY * 0.5)} cm` },
            { y: 205, label: `${Math.round(maxY * 0.25)} cm` },
            { y: 260, label: '0' },
        ];
        const yGroup = document.getElementById('chartYAxisGroup');
        if (yGroup) {
            yGroup.innerHTML = yTicks.map((t, idx) => {
                const isBase = idx === yTicks.length - 1;
                return `<g class="text-[11px] fill-gray-400">
                    <text x="42" y="${t.y + 4}" text-anchor="end">${t.label}</text>
                    <line x1="55" y1="${t.y}" x2="860" y2="${t.y}" stroke="${isBase ? '#e2e8f0' : '#f1f5f9'}" ${isBase ? '' : 'stroke-dasharray="4 4"'} stroke-width="1.5"/>
                </g>`;
            }).join('');
        }

        const actualEl = document.getElementById('actualCurvePath');
        const idealEl = document.getElementById('projectedCurvePath');
        const nodesEl = document.getElementById('chartNodesGroup');
        const xAxisEl = document.getElementById('chartXAxisGroup');

        if (paired.length === 0) {
            if (actualEl) actualEl.setAttribute('d', '');
            if (idealEl) idealEl.setAttribute('d', '');
            if (nodesEl) nodesEl.innerHTML = '';
            if (xAxisEl) xAxisEl.innerHTML = '<text x="450" y="155" text-anchor="middle" class="text-xs fill-gray-400">Belum ada catatan tinggi tanaman pada rentang ini</text>';
        } else {
            const xStart = 90;
            const xEnd = 820;
            const yTop = 40;
            const yBottom = 260;
            const n = paired.length;

            const coords = paired.map((p, idx) => {
                const x = n === 1 ? (xStart + xEnd) / 2 : xStart + (idx / (n - 1)) * (xEnd - xStart);
                const y = yBottom - ((p.tinggi / maxY) * (yBottom - yTop));
                const iy = p.ideal !== null && Number.isFinite(p.ideal) ? yBottom - ((p.ideal / maxY) * (yBottom - yTop)) : null;
                return {
                    x: Number(x.toFixed(1)),
                    y: Number(y.toFixed(1)),
                    iy: iy !== null ? Number(iy.toFixed(1)) : null,
                    label: `H-${p.rec.umur_tanaman ?? '-'}`,
                    tinggi: p.tinggi,
                    berat: p.rec.berat_gram,
                    daun: p.rec.jumlah_daun,
                    isLast: idx === n - 1,
                };
            });

            if (actualEl) actualEl.setAttribute('d', coords.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.y}`).join(' '));
            const idealPts = coords.filter(c => c.iy !== null);
            if (idealEl) idealEl.setAttribute('d', idealPts.map((c, i) => `${i === 0 ? 'M' : 'L'} ${c.x} ${c.iy}`).join(' '));
            if (nodesEl) {
                nodesEl.innerHTML = coords.map(c => {
                    const tip = `${c.label}: ${formatNum(c.tinggi, 2)} cm${c.berat ? ' | ' + formatNum(c.berat, 1) + ' g' : ''}${c.daun ? ' | ' + c.daun + ' daun' : ''}`;
                    return c.isLast
                        ? `<circle cx="${c.x}" cy="${c.y}" r="6" fill="#0e5f38" stroke="#ffffff" stroke-width="2.5" filter="url(#nodeGlow)"><title>${esc(tip)}</title></circle>`
                        : `<circle cx="${c.x}" cy="${c.y}" r="5" fill="#ffffff" stroke="#10b981" stroke-width="3" filter="url(#nodeGlow)"><title>${esc(tip)}</title></circle>`;
                }).join('');
            }
            if (xAxisEl) {
                xAxisEl.innerHTML = coords.map(c =>
                    `<text x="${c.x}" y="285" text-anchor="middle" class="text-[11px] ${c.isLast ? 'fill-[#0e5f38] font-bold' : 'fill-gray-500 font-medium'}">${esc(c.label)} (${formatNum(c.tinggi, 1)} cm)</text>`
                ).join('');
            }
        }

        const lastRec = allRecords.length > 0 ? allRecords[allRecords.length - 1] : null;
        const meta = data.meta || {};
        const setEl = (id, t) => { const el = document.getElementById(id); if (el) el.textContent = t; };
        setEl('statChartHeight', lastRec && lastRec.tinggi_cm !== null ? `${formatNum(lastRec.tinggi_cm, 2)} cm (H-${lastRec.umur_tanaman ?? '-'})` : '-');
        setEl('statChartRate', meta.tinggi_gain_cm !== undefined ? `+${formatNum(meta.tinggi_gain_cm, 2)} cm (+${formatNum(meta.avg_daily_height_growth_cm || 0, 2)} cm/hr)` : '-');
        setEl('statChartBiomass', lastRec ? `${formatNum(lastRec.berat_gram, 2)} g (${lastRec.jumlah_daun ?? '-'} helai)` : '-');
        setEl('statChartWater', lastRec ? `pH ${lastRec.ph_air ?? '-'} • EC ${lastRec.ec ?? '-'} mS/cm` : '-');
    }

    function getFilteredLogs() {
        const q = (document.getElementById('batchSearchInput')?.value || '').toLowerCase().trim();
        const plantFilter = document.getElementById('bedengFilter')?.value || '';
        const faseFilter = document.getElementById('faseFilter')?.value || '';

        return growthLogs.filter(log => {
            if (currentTab === 'aktif' && !ACTIVE_STATUSES.includes(log.plant_status)) return false;
            if (plantFilter && String(log.plant_id) !== String(plantFilter)) return false;
            const hst = Number(log.umur_tanaman ?? -1);
            if (faseFilter === 'awal' && (hst < 0 || hst > 14)) return false;
            if (faseFilter === 'tengah' && (hst < 15 || hst > 25)) return false;
            if (faseFilter === 'panen' && hst < 26) return false;
            if (q) {
                const hay = `${log.kode_tanaman || ''} ${log.nama_tanaman || ''} ${log.varietas || ''} ${log.catatan || ''} ${log.recorded_by_nama || ''}`.toLowerCase();
                if (!hay.includes(q)) return false;
            }
            return true;
        });
    }

    function renderTable() {
        const tbody = document.getElementById('batchTableBody');
        const infoEl = document.getElementById('tableInfo');
        const prevBtn = document.getElementById('btnPrevLog');
        const nextBtn = document.getElementById('btnNextLog');
        const pageBtns = document.getElementById('logPageButtons');
        if (!tbody) return;

        const filtered = getFilteredLogs();
        const total = filtered.length;
        const totalPages = Math.ceil(total / perPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * perPage;
        const items = filtered.slice(start, start + perPage);

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-gray-400 text-xs">Tidak ada log pertumbuhan yang sesuai filter.</td></tr>';
        } else {
            tbody.innerHTML = items.map(log => `
                <tr class="hover:bg-gray-50/70 transition group">
                    <td class="py-4 pr-4">
                        <div class="font-bold text-gray-900">${esc(log.kode_tanaman)} — ${esc(log.nama_tanaman)}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">${esc(log.varietas || '-')} • ${esc(log.recorded_by_nama || 'Staf')}</div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="font-medium text-gray-900">${esc(formatDateTimeId(log.tanggal_pengamatan))}</div>
                        <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">HST ${esc(log.umur_tanaman ?? '-')}</div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="font-bold text-emerald-700">${log.tinggi_cm !== null && log.tinggi_cm !== undefined ? formatNum(log.tinggi_cm, 2) + ' cm' : '-'} • ${log.berat_gram !== null && log.berat_gram !== undefined ? formatNum(log.berat_gram, 2) + ' g' : '-'}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">${esc(log.jumlah_daun ?? '-')} helai daun</div>
                    </td>
                    <td class="py-4 px-4">
                        <div class="font-semibold text-gray-800">pH ${esc(log.ph_air ?? '-')} • EC ${esc(log.ec ?? '-')}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">${esc(log.catatan || '-')}</div>
                    </td>
                    <td class="py-4 pl-4 text-right">
                        <div class="inline-flex items-center gap-1.5 justify-end">
                            <button type="button" onclick="showLogDetail(${log.id})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition cursor-pointer" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button type="button" onclick="editLog(${log.id})" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" onclick="deleteLog(${log.plant_id}, ${log.id})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>`
            ).join('');
        }

        if (infoEl) infoEl.textContent = `Menampilkan ${items.length} dari ${total} log pertumbuhan`;
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
                btn.addEventListener('click', () => { currentPage = i; renderTable(); });
                pageBtns.appendChild(btn);
            }
        }
    }

    async function syncMonitoringFromApi() {
        try {
            const [plRes, diagRes] = await Promise.all([
                apiRequest('/plants?per_page=100', { method: 'GET' }),
                apiRequest('/diagnosis/history?per_page=50', { method: 'GET' }),
            ]);
            if (plRes?.ok && Array.isArray(plRes.json?.data)) {
                plantsData = plRes.json.data;
            }
            if (diagRes?.ok) {
                diagnosisData = Array.isArray(diagRes.json?.data) ? diagRes.json.data : [];
                diagnosisStats = diagRes.json?.meta?.stats_by_label || {};
            }

            const newLogs = [];
            await Promise.all(plantsData.map(async plant => {
                const [hRes, cRes] = await Promise.all([
                    apiRequest(`/plants/${plant.id}/growth-history`, { method: 'GET' }),
                    apiRequest(`/plants/${plant.id}/growth-chart`, { method: 'GET' }),
                ]);
                const records = hRes?.ok && Array.isArray(hRes.json?.data) ? hRes.json.data : [];
                const meta = hRes?.ok ? (hRes.json?.meta || {}) : {};
                const chart = cRes?.ok ? (cRes.json?.data || {}) : {};
                plantCharts[plant.id] = { records, meta, chart };
                records.forEach(r => {
                    newLogs.push({
                        ...r,
                        kode_tanaman: plant.kode_tanaman || '-',
                        nama_tanaman: plant.nama || 'Selada',
                        varietas: plant.varietas || '-',
                        plant_status: plant.status || 'pertumbuhan',
                        keterangan_tanaman: plant.keterangan || '-',
                    });
                });
            }));

            newLogs.sort((a, b) => String(b.tanggal_pengamatan || '').localeCompare(String(a.tanggal_pengamatan || '')) || (Number(b.id) - Number(a.id)));
            growthLogs = newLogs;

            updateCardsAndSelectors();
            renderActiveBedsGrid();
            renderTable();
            const selId = document.getElementById('chartBedengSelect')?.value || plantsData[0]?.id;
            if (selId) renderSvgChartForPlant(selId);
        } catch (_) {
            // Gunakan hasil render SSR bila backend gagal dijangkau.
        }
    }

    window.selectChartPlant = function (plantId) {
        const sel = document.getElementById('chartBedengSelect');
        if (sel) {
            sel.value = String(plantId);
            window.updateChartData();
            document.getElementById('chartSection')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    window.updateChartData = async function () {
        const sel = document.getElementById('chartBedengSelect');
        if (!sel || !sel.value) return;
        const pid = sel.value;
        renderSvgChartForPlant(pid);
        try {
            const [hRes, cRes] = await Promise.all([
                apiRequest(`/plants/${pid}/growth-history`, { method: 'GET' }),
                apiRequest(`/plants/${pid}/growth-chart`, { method: 'GET' }),
            ]);
            if (hRes?.ok && cRes?.ok) {
                plantCharts[pid] = {
                    records: hRes.json?.data || [],
                    meta: hRes.json?.meta || {},
                    chart: cRes.json?.data || {},
                };
                renderSvgChartForPlant(pid);
            }
        } catch (_) {}
    };

    window.setPeriod = function (p) {
        currentPeriod = p;
        ['btnP7', 'btnP14', 'btnPFull'].forEach(id => {
            const b = document.getElementById(id);
            if (b) b.className = 'px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900 transition font-medium cursor-pointer';
        });
        const activeId = p === 7 ? 'btnP7' : (p === 14 ? 'btnP14' : 'btnPFull');
        const activeBtn = document.getElementById(activeId);
        if (activeBtn) activeBtn.className = 'px-3 py-1 rounded-lg bg-white text-gray-900 font-semibold shadow-2xs border border-gray-200/80 transition cursor-pointer';

        const sel = document.getElementById('chartBedengSelect');
        if (sel && sel.value) renderSvgChartForPlant(sel.value);
    };

    window.setTabStatus = function (tab) {
        currentTab = tab;
        currentPage = 1;
        const tAct = document.getElementById('tabActive');
        const tFin = document.getElementById('tabFinished');
        if (tab === 'aktif') {
            tAct.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer';
            tFin.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer';
        } else {
            tFin.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer';
            tAct.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer';
        }
        renderTable();
    };

    function showLogModalError(msg) {
        const box = document.getElementById('logModalError');
        if (!box) return;
        if (!msg) { box.classList.add('hidden'); box.textContent = ''; return; }
        box.textContent = msg;
        box.classList.remove('hidden');
    }

    window.openLogModal = function (mode = 'tambah', log = null) {
        document.getElementById('logFormMode').value = mode;
        showLogModalError('');
        const plantSel = document.getElementById('logPlantId');
        const btn = document.getElementById('btnSaveLog');
        btn.disabled = false;

        if (mode === 'tambah') {
            document.getElementById('logModalTitle').textContent = 'Catat Log Pertumbuhan Harian';
            btn.textContent = 'Simpan Log';
            document.getElementById('editLogId').value = '';
            plantSel.disabled = false;
            const chartSel = document.getElementById('chartBedengSelect');
            if (chartSel?.value) plantSel.value = chartSel.value;
            document.getElementById('logTanggal').value = toLocalInputValue(null);
            document.getElementById('logHst').value = '';
            document.getElementById('logTinggi').value = '';
            document.getElementById('logDaun').value = '';
            document.getElementById('logBiomassa').value = '';
            document.getElementById('logPh').value = '6.0';
            document.getElementById('logEc').value = '1.2';
            document.getElementById('logSuhuAir').value = '22.0';
            document.getElementById('logKelembapan').value = '70';
            document.getElementById('logCatatan').value = '';
        } else {
            document.getElementById('logModalTitle').textContent = `Edit Log HST ${log.umur_tanaman ?? ''} (${log.kode_tanaman})`;
            btn.textContent = 'Simpan Perubahan';
            document.getElementById('editLogId').value = log.id;
            plantSel.value = String(log.plant_id);
            plantSel.disabled = true;
            document.getElementById('logTanggal').value = toLocalInputValue(log.tanggal_pengamatan);
            document.getElementById('logHst').value = log.umur_tanaman ?? '';
            document.getElementById('logTinggi').value = log.tinggi_cm ?? '';
            document.getElementById('logDaun').value = log.jumlah_daun ?? '';
            document.getElementById('logBiomassa').value = log.berat_gram ?? '';
            document.getElementById('logPh').value = log.ph_air ?? '';
            document.getElementById('logEc').value = log.ec ?? '';
            document.getElementById('logSuhuAir').value = log.suhu_air ?? '';
            document.getElementById('logKelembapan').value = log.kelembapan_persen ?? '';
            document.getElementById('logCatatan').value = log.catatan || '';
        }

        document.getElementById('addLogModal').classList.remove('hidden');
    };

    window.openAddLogModal = function () { window.openLogModal('tambah'); };

    window.closeLogModal = function () {
        document.getElementById('addLogModal').classList.add('hidden');
    };

    window.handleSaveLog = async function (e) {
        e.preventDefault();
        const mode = document.getElementById('logFormMode').value;
        const plantId = document.getElementById('logPlantId').value;
        const dtRaw = document.getElementById('logTanggal').value;
        const hstRaw = document.getElementById('logHst').value;
        const tinggiRaw = document.getElementById('logTinggi').value;
        const daunRaw = document.getElementById('logDaun').value;
        const beratRaw = document.getElementById('logBiomassa').value;
        const phRaw = document.getElementById('logPh').value;
        const ecRaw = document.getElementById('logEc').value;
        const suhuAirRaw = document.getElementById('logSuhuAir').value;
        const lembapRaw = document.getElementById('logKelembapan').value;
        const catatan = document.getElementById('logCatatan').value.trim();
        const btn = document.getElementById('btnSaveLog');

        showLogModalError('');
        if (!plantId) { showLogModalError('Pilih batch tanaman terlebih dahulu.'); return; }

        const payload = {
            tanggal_pengamatan: dtRaw.length === 16 ? `${dtRaw}:00` : dtRaw,
            umur_tanaman: hstRaw !== '' ? parseInt(hstRaw, 10) : null,
            tinggi_cm: tinggiRaw !== '' ? parseFloat(tinggiRaw) : null,
            jumlah_daun: daunRaw !== '' ? parseInt(daunRaw, 10) : null,
            berat_gram: beratRaw !== '' ? parseFloat(beratRaw) : null,
            ph_air: phRaw !== '' ? parseFloat(phRaw) : null,
            ec: ecRaw !== '' ? parseFloat(ecRaw) : null,
            suhu_air: suhuAirRaw !== '' ? parseFloat(suhuAirRaw) : null,
            kelembapan_persen: lembapRaw !== '' ? parseFloat(lembapRaw) : null,
            catatan: catatan || null,
        };

        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const url = mode === 'tambah'
                ? `/plants/${plantId}/growth-records`
                : `/plants/${plantId}/growth-records/${document.getElementById('editLogId').value}`;
            const method = mode === 'tambah' ? 'POST' : 'PUT';
            const res = await apiRequest(url, { method, body: JSON.stringify(payload) });
            if (!res) return;

            if (res.ok) {
                await syncMonitoringFromApi();
                window.closeLogModal();
                showToast(mode === 'tambah' ? 'Log pertumbuhan berhasil disimpan ke database!' : 'Perubahan log pertumbuhan berhasil disimpan!');
            } else {
                showLogModalError(extractApiError(res.json, 'Gagal menyimpan log pertumbuhan.'));
            }
        } catch (_) {
            showLogModalError('Tidak dapat terhubung ke Backend API (port 8000).');
        } finally {
            btn.disabled = false;
            btn.textContent = mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Log';
        }
    };

    window.showLogDetail = function (logId) {
        const log = growthLogs.find(l => Number(l.id) === Number(logId));
        if (!log) return;
        document.getElementById('detailLogTitle').textContent = `Log ${log.kode_tanaman} (HST ${log.umur_tanaman ?? '-'})`;
        document.getElementById('detailLogContent').innerHTML = `
            <div class="p-3 bg-gray-50 rounded-xl space-y-2">
                <div class="flex justify-between"><span class="text-gray-500">Tanaman:</span> <span class="font-bold text-gray-900">${esc(log.nama_tanaman)} (${esc(log.varietas || '-')})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Waktu:</span> <span class="font-medium text-gray-800">${esc(formatDateTimeId(log.tanggal_pengamatan))}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tinggi / Daun / Berat:</span> <span class="font-bold text-emerald-700">${formatNum(log.tinggi_cm, 2)} cm · ${esc(log.jumlah_daun ?? '-')} daun · ${formatNum(log.berat_gram, 2)} g</span></div>
                <div class="flex justify-between"><span class="text-gray-500">pH / EC / PPM:</span> <span class="font-semibold text-gray-800">pH ${esc(log.ph_air ?? '-')} • EC ${esc(log.ec ?? '-')} (${esc(log.ppm_estimasi ?? '-')} ppm)</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Suhu Air / Kelembapan:</span> <span class="font-medium text-gray-800">${esc(log.suhu_air ?? '-')} °C · ${esc(log.kelembapan_persen ?? '-')}%</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Petugas:</span> <span class="font-medium text-gray-800">${esc(log.recorded_by_nama || 'Staf Kebun')}</span></div>
                <div class="pt-1 border-t border-gray-200"><span class="text-gray-500 block">Catatan:</span><span class="text-gray-800">${esc(log.catatan || '-')}</span></div>
            </div>`;
        document.getElementById('detailLogModal').classList.remove('hidden');
    };

    window.closeDetailLogModal = function () {
        document.getElementById('detailLogModal').classList.add('hidden');
    };

    window.editLog = function (logId) {
        const log = growthLogs.find(l => Number(l.id) === Number(logId));
        if (log) window.openLogModal('edit', log);
    };

    window.deleteLog = async function (plantId, logId) {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan pertumbuhan ini dari database?')) return;
        try {
            const res = await apiRequest(`/plants/${plantId}/growth-records/${logId}`, { method: 'DELETE' });
            if (!res) return;
            if (res.ok) {
                await syncMonitoringFromApi();
                showToast('Log pertumbuhan berhasil dihapus.');
            } else {
                showToast(extractApiError(res.json, 'Gagal menghapus log pertumbuhan.'));
            }
        } catch (_) {
            showToast('Gagal terhubung ke Backend API (port 8000).');
        }
    };

    window.exportSummary = function () {
        const rows = [
            ['Kode Tanaman', 'Nama', 'Varietas', 'Tanggal', 'HST', 'Tinggi (cm)', 'Daun', 'Berat (g)', 'pH', 'EC', 'Petugas', 'Catatan'],
            ...growthLogs.map(l => [
                l.kode_tanaman || '',
                l.nama_tanaman || '',
                l.varietas || '',
                l.tanggal_pengamatan || '',
                l.umur_tanaman ?? '',
                l.tinggi_cm ?? '',
                l.jumlah_daun ?? '',
                l.berat_gram ?? '',
                l.ph_air ?? '',
                l.ec ?? '',
                l.recorded_by_nama || '',
                l.catatan || '',
            ]),
        ];
        const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `monitoring-pertumbuhan-hidara-${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('batchSearchInput')?.addEventListener('input', () => { currentPage = 1; renderTable(); });
        document.getElementById('bedengFilter')?.addEventListener('change', () => { currentPage = 1; renderTable(); });
        document.getElementById('faseFilter')?.addEventListener('change', () => { currentPage = 1; renderTable(); });
        document.getElementById('btnPrevLog')?.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderTable(); } });
        document.getElementById('btnNextLog')?.addEventListener('click', () => {
            const totalPages = Math.ceil(getFilteredLogs().length / perPage) || 1;
            if (currentPage < totalPages) { currentPage++; renderTable(); }
        });

        updateCardsAndSelectors();
        renderActiveBedsGrid();
        renderTable();
        const selId = document.getElementById('chartBedengSelect')?.value || plantsData[0]?.id;
        if (selId) renderSvgChartForPlant(selId);
        syncMonitoringFromApi();
    });
})();
</script>
@endpush
