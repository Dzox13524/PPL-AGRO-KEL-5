@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard')

@section('content')
@php
    $firstDetails = ($firstPlantId !== null && isset($plantDetails[$firstPlantId])) ? $plantDetails[$firstPlantId] : [
        'records' => [],
        'meta' => [],
        'chart' => [],
        'svg' => [
            'has_data' => false,
            'y_ticks' => [
                ['y' => 30, 'label' => '20'],
                ['y' => 70, 'label' => '15'],
                ['y' => 110, 'label' => '10'],
                ['y' => 150, 'label' => '5'],
                ['y' => 190, 'label' => '0'],
            ],
            'points' => [],
            'line_path' => '',
            'area_path' => '',
            'ideal_path' => '',
        ],
    ];
    $initialRecords = $firstDetails['records'] ?? [];
    $initialMeta = $firstDetails['meta'] ?? [];
    $initialSvg = $firstDetails['svg'] ?? [];
    $latestRecord = count($initialRecords) > 0 ? $initialRecords[count($initialRecords) - 1] : null;
    $reversedRecords = array_slice(array_reverse($initialRecords), 0, 5);
    $bedImages = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];
@endphp

<div class="max-w-[1400px] mx-auto space-y-6">
    @if(!empty($apiError))
        <div id="apiWarningBanner" class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-xs font-medium flex items-center justify-between gap-3">
            <span>{{ $apiError }}</span>
            <button type="button" onclick="window.location.reload()" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 rounded-md font-semibold cursor-pointer">Muat Ulang</button>
        </div>
    @endif

    <div class="flex items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Selamat Datang, <span id="welcomeUsername" class="text-[#137a46]">{{ $user['username'] ?? 'owner' }}</span>
            </h1>
            <p id="welcomeSubtitle" class="text-xs text-gray-500 mt-0.5">
                {{ $user['nama'] ?? 'Pemilik Mitra' }} • Ringkasan data aktual budidaya selada hidroponik
            </p>
        </div>
        <div class="shrink-0">
            {{-- Unduh Ringkasan --}}
            <button type="button"
                    id="btnDownloadSummary"
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
                <span id="cardHarvestValue" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $summaryCards['harvest']['value'] }}</span>
                <span id="cardHarvestUnit" class="text-xs text-gray-500">{{ $summaryCards['harvest']['unit'] }}</span>
            </div>
            <div class="flex items-center gap-3 mt-3 flex-wrap">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span id="cardHarvestSub1">{{ $summaryCards['harvest']['sub1'] }}</span>
                </span>
                <span id="cardHarvestSub2" class="text-[11px] text-gray-400">{{ $summaryCards['harvest']['sub2'] }}</span>
            </div>
        </div>

        {{-- Card 2: Tanaman Aktif --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Tanaman Aktif</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span id="cardActiveValue" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $summaryCards['active']['value'] }}</span>
                <span id="cardActiveUnit" class="text-xs text-gray-500">{{ $summaryCards['active']['unit'] }}</span>
            </div>
            <div class="flex items-center gap-3 mt-3 flex-wrap">
                <span id="cardActiveSub1" class="text-[11px] font-semibold text-emerald-600">{{ $summaryCards['active']['sub1'] }}</span>
                <span id="cardActiveSub2" class="text-[11px] text-gray-400">{{ $summaryCards['active']['sub2'] }}</span>
            </div>
        </div>

        {{-- Card 3: Ketersediaan Nutrisi --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Ketersediaan Nutrisi</p>
            <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                <span id="cardNutrientValue" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $summaryCards['nutrient']['value'] }}</span>
                <span id="cardNutrientUnit" class="text-xs text-gray-500">{{ $summaryCards['nutrient']['unit'] }}</span>
            </div>
            <div class="flex items-center gap-3 mt-3 flex-wrap">
                <span id="cardNutrientSub1Wrap" class="inline-flex items-center gap-1 text-[11px] font-semibold {{ !empty($summaryCards['nutrient']['sub1_warn']) ? 'text-amber-600' : 'text-emerald-600' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span id="cardNutrientSub1">{{ $summaryCards['nutrient']['sub1'] }}</span>
                </span>
                <span id="cardNutrientSub2" class="text-[11px] text-gray-400">{{ $summaryCards['nutrient']['sub2'] }}</span>
            </div>
        </div>

        {{-- Card 4: Kesehatan Tanaman --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Kesehatan Tanaman</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span id="cardHealthValue" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $summaryCards['health']['value'] }}</span>
                <span id="cardHealthUnit" class="text-xs text-gray-500">{{ $summaryCards['health']['unit'] }}</span>
            </div>
            <div class="flex items-center gap-3 mt-3 flex-wrap">
                <span id="cardHealthSub1" class="text-[11px] font-semibold text-emerald-600">{{ $summaryCards['health']['sub1'] }}</span>
                <span id="cardHealthSub2" class="text-[11px] text-gray-400">{{ $summaryCards['health']['sub2'] }}</span>
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
                <p id="chartPlantSubtitle" class="text-xs text-gray-500 mt-0.5">
                    @if($firstPlant)
                        {{ $firstPlant['kode_tanaman'] }} • {{ $firstPlant['nama'] }} ({{ $firstPlant['varietas'] ?? '-' }}) — {{ $firstPlant['keterangan'] ?? 'Monitoring pertumbuhan berkala' }}
                    @else
                        Monitoring biomassa, kenaikan tinggi tajuk, dan log observasi berkala
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Badge --}}
                <span id="chartGrowthBadge" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[11px] font-semibold px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    @if(isset($initialMeta['tinggi_gain_cm']))
                        +{{ number_format((float) $initialMeta['tinggi_gain_cm'], 2) }} cm Total Kenaikan
                    @else
                        0 Data Pertumbuhan
                    @endif
                </span>

                {{-- Pilih Tanaman / Batch Tabs --}}
                <div class="w-full sm:w-auto overflow-x-auto no-scrollbar">
                    <div id="plantTabsContainer" class="inline-flex items-center bg-gray-50 border border-gray-200 rounded-lg p-0.5 gap-0.5 min-w-max">
                        @forelse($plants as $index => $p)
                            <button type="button"
                                    class="bedeng-tab shrink-0 text-[11px] px-3 py-1.5 rounded-md transition cursor-pointer {{ $index === 0 ? 'bg-[#137a46] text-white shadow-sm font-semibold' : 'font-medium text-gray-600 hover:bg-white hover:shadow-xs' }}"
                                    data-plant-id="{{ $p['id'] }}"
                                    data-plant-code="{{ $p['kode_tanaman'] }}"
                                    data-plant-name="{{ $p['nama'] }}"
                                    data-plant-varietas="{{ $p['varietas'] ?? '-' }}"
                                    data-plant-ket="{{ $p['keterangan'] ?? '' }}">
                                {{ $p['kode_tanaman'] }}{{ !empty($p['varietas']) ? ' (' . $p['varietas'] . ')' : '' }}
                            </button>
                        @empty
                            <span class="text-[11px] text-gray-400 px-3 py-1.5">Belum ada batch tanaman</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart + Stats Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Chart Area (2/3) --}}
            <div class="lg:col-span-2 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span id="chartSeriesTitle" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        @if($firstPlant)
                            Tren Tinggi Tanaman {{ $firstPlant['nama'] }} — {{ $firstPlant['varietas'] ?? $firstPlant['kode_tanaman'] }} (cm)
                        @else
                            Tren Tinggi Tanaman Selada (cm)
                        @endif
                    </span>
                </div>

                {{-- SVG Chart Container --}}
                <div class="overflow-x-auto no-scrollbar -mx-2 px-2 sm:mx-0 sm:px-0">
                    <div class="relative w-full min-w-[460px] sm:min-w-0" style="height: 240px;">
                        <svg id="growthChart" viewBox="0 0 600 240" class="w-full h-full" preserveAspectRatio="xMidYMid meet">
                            <defs>
                                <linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#1a9e5f" stop-opacity="0.16"/>
                                    <stop offset="100%" stop-color="#1a9e5f" stop-opacity="0.01"/>
                                </linearGradient>
                            </defs>

                            {{-- Horizontal Grid --}}
                            <line x1="50" y1="30" x2="550" y2="30" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="70" x2="550" y2="70" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="110" x2="550" y2="110" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="150" x2="550" y2="150" stroke="#f1f5f9" stroke-width="1"/>
                            <line x1="50" y1="190" x2="550" y2="190" stroke="#e2e8f0" stroke-width="1"/>

                            {{-- Y-axis Labels --}}
                            <g id="chartYTicks">
                                @foreach(($initialSvg['y_ticks'] ?? []) as $tick)
                                    <text x="40" y="{{ $tick['y'] + 4 }}" text-anchor="end" class="text-[9px] fill-gray-400">{{ $tick['label'] }}</text>
                                @endforeach
                            </g>

                            {{-- Ideal Reference Curve --}}
                            <path id="chartIdealPath" d="{{ $initialSvg['ideal_path'] ?? '' }}" fill="none" stroke="#94a3b8" stroke-width="1.2" stroke-dasharray="6 4"/>

                            {{-- Area Fill --}}
                            <path id="chartAreaPath" d="{{ $initialSvg['area_path'] ?? '' }}" fill="url(#chartFill)"/>

                            {{-- Main Curve Line --}}
                            <path id="chartActualPath" d="{{ $initialSvg['line_path'] ?? '' }}" fill="none" stroke="#1a9e5f" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                            {{-- Data points --}}
                            <g id="chartPointsGroup">
                                @foreach(($initialSvg['points'] ?? []) as $pt)
                                    @if(!empty($pt['is_last']))
                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5.5" fill="#1a9e5f" stroke="white" stroke-width="2.5"/>
                                    @else
                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4.5" fill="white" stroke="#1a9e5f" stroke-width="2"/>
                                    @endif
                                @endforeach
                            </g>

                            {{-- X-axis Labels --}}
                            <g id="chartXLabelsGroup">
                                @php $totalPts = count($initialSvg['points'] ?? []); @endphp
                                @foreach(($initialSvg['points'] ?? []) as $idx => $pt)
                                    <text x="{{ $pt['x'] }}" y="213" text-anchor="middle" class="text-[9px] {{ !empty($pt['is_last']) ? 'fill-emerald-600 font-semibold' : 'fill-gray-400' }}">
                                        {{ $pt['label'] }}
                                    </text>
                                    @if(!empty($pt['is_last']) || $totalPts <= 5 || $idx % 2 === 0)
                                        <text x="{{ $pt['x'] }}" y="226" text-anchor="middle" class="text-[8.5px] {{ !empty($pt['is_last']) ? 'fill-emerald-600 font-semibold' : 'fill-gray-400' }}">
                                            ({{ number_format((float) $pt['actual'], 2) }} cm)
                                        </text>
                                    @endif
                                @endforeach
                            </g>

                            @if(empty($initialSvg['has_data']))
                                <text id="chartEmptyText" x="300" y="115" text-anchor="middle" class="text-xs fill-gray-400">Belum ada data catatan pertumbuhan</text>
                            @endif

                            {{-- Legend --}}
                            <circle cx="430" cy="12" r="3.5" fill="#1a9e5f"/>
                            <text x="438" y="15" class="text-[9px] fill-gray-500">Aktual (cm)</text>
                            <line x1="498" y1="12" x2="515" y2="12" stroke="#94a3b8" stroke-width="1.2" stroke-dasharray="4 3"/>
                            <text x="520" y="15" class="text-[9px] fill-gray-400">Ideal Referensi</text>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Right Side Stats + Observation Log --}}
            <div class="space-y-4">
                {{-- Stat Cards --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3.5">
                        <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Tinggi Terakhir</p>
                        <div class="mt-1.5 flex items-baseline gap-1">
                            <span id="statAvgHeight" class="text-xl font-extrabold text-gray-900">
                                {{ isset($initialMeta['latest_tinggi_cm']) && $initialMeta['latest_tinggi_cm'] !== null ? number_format((float) $initialMeta['latest_tinggi_cm'], 2) : '-' }}
                            </span>
                            <span class="text-[10px] text-gray-500">cm / tanaman</span>
                        </div>
                        <p id="statAvgHeightSub" class="text-[11px] font-medium text-emerald-600 mt-1.5">
                            @if(isset($initialMeta['tinggi_gain_cm']))
                                Gain +{{ number_format((float) $initialMeta['tinggi_gain_cm'], 2) }} cm (+{{ number_format((float) ($initialMeta['avg_daily_height_growth_cm'] ?? 0), 2) }} cm/hr)
                            @else
                                Belum ada catatan
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3.5">
                        <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase">Biomassa Terkini</p>
                        <div class="mt-1.5 flex items-baseline gap-1">
                            <span id="statAvgWeight" class="text-xl font-extrabold text-gray-900">
                                {{ isset($initialMeta['latest_berat_gram']) && $initialMeta['latest_berat_gram'] !== null ? number_format((float) $initialMeta['latest_berat_gram'], 2) : '-' }}
                            </span>
                            <span class="text-[10px] text-gray-500">gram / tanaman</span>
                        </div>
                        <p id="statAvgWeightSub" class="text-[11px] font-medium text-emerald-600 mt-1.5">
                            @if($latestRecord)
                                Daun: {{ $latestRecord['jumlah_daun'] ?? '-' }} helai • EC {{ isset($latestRecord['ec']) ? number_format((float) $latestRecord['ec'], 2) : '-' }}
                            @else
                                Belum ada pengukuran
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Observation Log --}}
                <div class="bg-gray-50 rounded-lg p-3.5">
                    <div class="flex items-center justify-between mb-3 gap-2">
                        <p class="text-xs font-bold text-gray-900">Log Observasi Lapangan</p>
                        <span id="obsLogLastTime" class="text-[10px] text-gray-400">
                            @if($latestRecord && !empty($latestRecord['tanggal_pengamatan']))
                                Terakhir: {{ \Carbon\Carbon::parse($latestRecord['tanggal_pengamatan'])->format('d M Y, H:i') }}
                            @else
                                Belum ada log
                            @endif
                        </span>
                    </div>
                    <div class="overflow-x-auto -mx-1 px-1">
                        <table class="w-full text-[11px] min-w-[280px]">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left text-gray-500 font-medium pb-2 pr-2">HST</th>
                                    <th class="text-left text-gray-500 font-medium pb-2 pr-2">Tinggi</th>
                                    <th class="text-left text-gray-500 font-medium pb-2 pr-2">Parameter / Catatan</th>
                                    <th class="text-right text-gray-500 font-medium pb-2">Petugas</th>
                                </tr>
                            </thead>
                            <tbody id="obsLogTableBody">
                                @forelse($reversedRecords as $idx => $rec)
                                    <tr class="{{ $idx < count($reversedRecords) - 1 ? 'border-b border-gray-100' : '' }}">
                                        <td class="py-2 pr-2 font-semibold text-gray-900">H-{{ $rec['umur_tanaman'] ?? '-' }}</td>
                                        <td class="py-2 pr-2 text-gray-600">{{ isset($rec['tinggi_cm']) ? number_format((float) $rec['tinggi_cm'], 2) . ' cm' : '-' }}</td>
                                        <td class="py-2 pr-2 font-medium text-emerald-600">
                                            {{ $rec['catatan'] ?: ('pH ' . ($rec['ph_air'] ?? '-') . ' • EC ' . ($rec['ec'] ?? '-')) }}
                                        </td>
                                        <td class="py-2 text-right text-gray-500">{{ $rec['recorded_by_nama'] ?? 'Petugas' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-400">Belum ada log observasi lapangan untuk batch ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         Status Bedeng / Batch Tanaman Aktif
    ═══════════════════════════════════════════ --}}
    <div class="space-y-3 pt-1">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-gray-900">Status Tanaman &amp; Batch Aktif</h2>
                <p class="text-xs text-gray-500">Ringkasan progres kultivasi dan jadwal panen langsung dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">plants</code></p>
            </div>
            <a href="{{ Route::has('admin.stok') ? route('admin.stok') : '/admin/stok' }}"
               class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition inline-flex items-center gap-1 shrink-0">
                Kelola Stok &amp; Inventaris
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Plant Cards --}}
        <div id="activePlantsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($plants as $index => $plant)
                @php
                    $img = $bedImages[$index % count($bedImages)];
                    $daysToHarvest = $plant['hari_menuju_panen'] ?? null;
                    $latest = is_array($plant['latest_growth'] ?? null) ? $plant['latest_growth'] : null;
                    if ($daysToHarvest !== null && $daysToHarvest < 0) {
                        $harvestLabel = 'Siap Panen (' . abs((int) $daysToHarvest) . ' hr lewat)';
                    } elseif ($daysToHarvest === 0) {
                        $harvestLabel = 'Panen Hari Ini';
                    } elseif ($daysToHarvest !== null) {
                        $harvestLabel = $daysToHarvest . ' Hari Lagi';
                    } else {
                        $harvestLabel = '-';
                    }
                    $statusText = ucfirst((string) ($plant['status'] ?? 'aktif'));
                    $isPrima = ($daysToHarvest !== null && $daysToHarvest <= 7);
                @endphp
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                    <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                        <img src="/images/beds/{{ $img }}" alt="{{ $plant['kode_tanaman'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 {{ $isPrima ? 'bg-[#137a46]' : 'bg-emerald-500' }} text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">
                            HST {{ $plant['umur_hari'] ?? 0 }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-bold text-gray-900 text-sm">{{ $plant['kode_tanaman'] }}</h3>
                            <span class="text-[10px] font-bold {{ $isPrima ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 bg-gray-100' }} px-2 py-0.5 rounded-full">
                                {{ $statusText }}
                            </span>
                        </div>
                        <p class="text-xs font-medium text-gray-700 mt-1">{{ $plant['nama'] }}{{ !empty($plant['varietas']) ? ' • ' . $plant['varietas'] : '' }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">{{ $plant['keterangan'] ?? '-' }}</p>

                        @if($latest)
                            <p class="text-[11px] text-emerald-700 font-medium mt-2">
                                Tinggi {{ number_format((float) ($latest['tinggi_cm'] ?? 0), 2) }} cm • {{ number_format((float) ($latest['berat_gram'] ?? 0), 2) }} g • {{ $latest['jumlah_daun'] ?? '-' }} daun
                            </p>
                        @else
                            <p class="text-[11px] text-gray-400 mt-2">Belum ada catatan pertumbuhan</p>
                        @endif

                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                            <span class="text-[11px] text-gray-400">Est. Panen:</span>
                            <span class="text-[11px] font-bold text-gray-900">{{ $plant['tanggal_panen'] ?? '-' }} ({{ $harvestLabel }})</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-gray-100 p-6 text-center text-xs text-gray-500">
                    Belum ada data tanaman pada database.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         Ringkasan Stok Bibit & Nutrisi (Database)
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-gray-900">Ringkasan Inventaris &amp; Stok Gudang</h2>
                <p class="text-xs text-gray-500">Status ketersediaan bibit, nutrisi, dan media tanam dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">inventory_items</code></p>
            </div>
            <span id="stocksSummaryMeta" class="text-[11px] font-semibold text-gray-500">
                Total {{ $stockSummary['total_items'] ?? count($stocks) }} item • {{ $stockSummary['low_stock_items'] ?? 0 }} di bawah minimum
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs min-w-[540px]">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="text-left font-medium pb-2.5 pr-3">Nama Barang</th>
                        <th class="text-left font-medium pb-2.5 pr-3">Jenis</th>
                        <th class="text-right font-medium pb-2.5 pr-3">Stok Tersedia</th>
                        <th class="text-right font-medium pb-2.5 pr-3">Stok Minimum</th>
                        <th class="text-right font-medium pb-2.5">Status</th>
                    </tr>
                </thead>
                <tbody id="stocksTableBody">
                    @forelse($stocks as $idx => $item)
                        @php
                            $qty = (float) ($item['jumlah_stok'] ?? 0);
                            $minQty = (float) ($item['stok_minimum'] ?? 0);
                            $isLow = !empty($item['is_low_stock']) || $qty <= $minQty;
                        @endphp
                        <tr class="{{ $idx < count($stocks) - 1 ? 'border-b border-gray-100' : '' }}">
                            <td class="py-2.5 pr-3 font-semibold text-gray-900">
                                {{ $item['nama_barang'] }}
                                @if(!empty($item['keterangan']))
                                    <span class="block text-[11px] font-normal text-gray-400">{{ $item['keterangan'] }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 pr-3 capitalize text-gray-600">{{ $item['jenis'] }}</td>
                            <td class="py-2.5 pr-3 text-right font-semibold text-gray-900">{{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }} {{ $item['satuan'] }}</td>
                            <td class="py-2.5 pr-3 text-right text-gray-500">{{ rtrim(rtrim(number_format($minQty, 2, ',', '.'), '0'), ',') }} {{ $item['satuan'] }}</td>
                            <td class="py-2.5 text-right">
                                @if($isLow)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">Stok Menipis</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400">Belum ada data inventaris di database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

    let cachedPlants = @json($plants);
    let cachedStocks = @json($stocks);
    let cachedPlantDetails = @json($plantDetails);
    let activePlantId = @json($firstPlantId);

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

    function escHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatNum(value, decimals = 2) {
        const num = Number(value);
        if (!Number.isFinite(num)) return '-';
        return num.toFixed(decimals);
    }

    function formatQtyId(value) {
        const num = Number(value);
        if (!Number.isFinite(num)) return '0';
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
    }

    function formatDateTimeId(isoStr) {
        if (!isoStr) return '-';
        const dt = new Date(isoStr);
        if (Number.isNaN(dt.getTime())) return String(isoStr);
        return dt.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    async function apiGet(path) {
        const token = getAuthCookie('hidara_token');
        if (!token) {
            window.location.href = '/login';
            return null;
        }

        const res = await fetch(`${BE_API_URL}${path}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        if (res.status === 401) {
            clearAuthCookies();
            window.location.href = '/login';
            return null;
        }

        const json = await res.json().catch(() => ({}));
        return { ok: res.ok, status: res.status, json };
    }

    function renderSvgChart(chartData) {
        const labels = Array.isArray(chartData?.labels) ? chartData.labels : [];
        const series = chartData?.series || {};
        const actualRaw = Array.isArray(series.tinggi_cm) ? series.tinggi_cm : [];
        const idealRaw = Array.isArray(series.ideal_height_cm) ? series.ideal_height_cm : [];

        const pts = [];
        const allVals = [1];
        const count = Math.min(labels.length, actualRaw.length);
        for (let i = 0; i < count; i++) {
            const val = Number(actualRaw[i]);
            if (!Number.isFinite(val)) continue;
            const idealVal = idealRaw[i] !== null && idealRaw[i] !== undefined ? Number(idealRaw[i]) : null;
            allVals.push(val);
            if (idealVal !== null && Number.isFinite(idealVal)) {
                allVals.push(idealVal);
            }
            pts.push({ label: String(labels[i]), actual: val, ideal: idealVal });
        }

        const maxVal = Math.max(...allVals);
        const step = maxVal <= 8 ? 2 : 5;
        const maxY = Math.max(step * 4, Math.ceil(maxVal / step) * step);

        const yTicksGroup = document.getElementById('chartYTicks');
        if (yTicksGroup) {
            const ticks = [
                { y: 30, label: maxY },
                { y: 70, label: Math.round(maxY * 0.75) },
                { y: 110, label: Math.round(maxY * 0.5) },
                { y: 150, label: Math.round(maxY * 0.25) },
                { y: 190, label: 0 },
            ];
            yTicksGroup.innerHTML = ticks
                .map(t => `<text x="40" y="${t.y + 4}" text-anchor="end" class="text-[9px] fill-gray-400">${t.label}</text>`)
                .join('');
        }

        const idealPathEl = document.getElementById('chartIdealPath');
        const areaPathEl = document.getElementById('chartAreaPath');
        const actualPathEl = document.getElementById('chartActualPath');
        const pointsGroup = document.getElementById('chartPointsGroup');
        const xLabelsGroup = document.getElementById('chartXLabelsGroup');
        const existingEmpty = document.getElementById('chartEmptyText');

        if (pts.length === 0) {
            if (idealPathEl) idealPathEl.setAttribute('d', '');
            if (areaPathEl) areaPathEl.setAttribute('d', '');
            if (actualPathEl) actualPathEl.setAttribute('d', '');
            if (pointsGroup) pointsGroup.innerHTML = '';
            if (xLabelsGroup) xLabelsGroup.innerHTML = '';
            if (!existingEmpty) {
                const svg = document.getElementById('growthChart');
                if (svg) {
                    const txt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    txt.setAttribute('id', 'chartEmptyText');
                    txt.setAttribute('x', '300');
                    txt.setAttribute('y', '115');
                    txt.setAttribute('text-anchor', 'middle');
                    txt.setAttribute('class', 'text-xs fill-gray-400');
                    txt.textContent = 'Belum ada data catatan pertumbuhan';
                    svg.appendChild(txt);
                }
            }
            return;
        }

        if (existingEmpty) existingEmpty.remove();

        const xStart = 75;
        const xEnd = 540;
        const yTop = 30;
        const yBottom = 190;
        const n = pts.length;

        const plotted = pts.map((p, idx) => {
            const x = n === 1 ? (xStart + xEnd) / 2 : xStart + (idx / (n - 1)) * (xEnd - xStart);
            const y = yBottom - ((p.actual / maxY) * (yBottom - yTop));
            const iy = p.ideal !== null && Number.isFinite(p.ideal)
                ? yBottom - ((p.ideal / maxY) * (yBottom - yTop))
                : null;
            return {
                x: Number(x.toFixed(1)),
                y: Number(y.toFixed(1)),
                iy: iy !== null ? Number(iy.toFixed(1)) : null,
                label: p.label,
                actual: p.actual,
                isLast: idx === n - 1,
            };
        });

        const linePath = plotted.map((p, idx) => `${idx === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
        const areaPath = `${linePath} L ${plotted[n - 1].x} 190 L ${plotted[0].x} 190 Z`;
        const idealCoords = plotted.filter(p => p.iy !== null);
        const idealPath = idealCoords.map((p, idx) => `${idx === 0 ? 'M' : 'L'} ${p.x} ${p.iy}`).join(' ');

        if (actualPathEl) actualPathEl.setAttribute('d', linePath);
        if (areaPathEl) areaPathEl.setAttribute('d', areaPath);
        if (idealPathEl) idealPathEl.setAttribute('d', idealPath);

        if (pointsGroup) {
            pointsGroup.innerHTML = plotted.map(p => p.isLast
                ? `<circle cx="${p.x}" cy="${p.y}" r="5.5" fill="#1a9e5f" stroke="white" stroke-width="2.5"/>`
                : `<circle cx="${p.x}" cy="${p.y}" r="4.5" fill="white" stroke="#1a9e5f" stroke-width="2"/>`
            ).join('');
        }

        if (xLabelsGroup) {
            xLabelsGroup.innerHTML = plotted.map((p, idx) => {
                const cls = p.isLast ? 'fill-emerald-600 font-semibold' : 'fill-gray-400';
                const showVal = p.isLast || n <= 5 || idx % 2 === 0;
                return `<text x="${p.x}" y="213" text-anchor="middle" class="text-[9px] ${cls}">${escHtml(p.label)}</text>` +
                    (showVal ? `<text x="${p.x}" y="226" text-anchor="middle" class="text-[8.5px] ${cls}">(${formatNum(p.actual, 2)} cm)</text>` : '');
            }).join('');
        }
    }

    function renderPlantSection(plant, records, meta, chart) {
        const subtitleEl = document.getElementById('chartPlantSubtitle');
        const seriesTitleEl = document.getElementById('chartSeriesTitle');
        const badgeEl = document.getElementById('chartGrowthBadge');
        const statHeightEl = document.getElementById('statAvgHeight');
        const statHeightSubEl = document.getElementById('statAvgHeightSub');
        const statWeightEl = document.getElementById('statAvgWeight');
        const statWeightSubEl = document.getElementById('statAvgWeightSub');
        const lastTimeEl = document.getElementById('obsLogLastTime');
        const tbodyEl = document.getElementById('obsLogTableBody');

        if (plant && subtitleEl) {
            subtitleEl.textContent = `${plant.kode_tanaman} • ${plant.nama} (${plant.varietas || '-'}) — ${plant.keterangan || 'Monitoring pertumbuhan berkala'}`;
        }
        if (plant && seriesTitleEl) {
            seriesTitleEl.innerHTML = `<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg> Tren Tinggi Tanaman ${escHtml(plant.nama)} — ${escHtml(plant.varietas || plant.kode_tanaman)} (cm)`;
        }

        if (badgeEl) {
            const gain = meta?.tinggi_gain_cm !== undefined && meta?.tinggi_gain_cm !== null
                ? `+${formatNum(meta.tinggi_gain_cm, 2)} cm Total Kenaikan`
                : '0 Data Pertumbuhan';
            badgeEl.innerHTML = `<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg> ${escHtml(gain)}`;
        }

        const safeRecords = Array.isArray(records) ? records : [];
        const latestRec = safeRecords.length > 0 ? safeRecords[safeRecords.length - 1] : null;

        if (statHeightEl) {
            statHeightEl.textContent = meta?.latest_tinggi_cm !== null && meta?.latest_tinggi_cm !== undefined
                ? formatNum(meta.latest_tinggi_cm, 2)
                : '-';
        }
        if (statHeightSubEl) {
            statHeightSubEl.textContent = meta?.tinggi_gain_cm !== undefined
                ? `Gain +${formatNum(meta.tinggi_gain_cm, 2)} cm (+${formatNum(meta.avg_daily_height_growth_cm || 0, 2)} cm/hr)`
                : 'Belum ada catatan';
        }
        if (statWeightEl) {
            statWeightEl.textContent = meta?.latest_berat_gram !== null && meta?.latest_berat_gram !== undefined
                ? formatNum(meta.latest_berat_gram, 2)
                : '-';
        }
        if (statWeightSubEl) {
            statWeightSubEl.textContent = latestRec
                ? `Daun: ${latestRec.jumlah_daun ?? '-'} helai • EC ${latestRec.ec !== null && latestRec.ec !== undefined ? formatNum(latestRec.ec, 2) : '-'}`
                : 'Belum ada pengukuran';
        }
        if (lastTimeEl) {
            lastTimeEl.textContent = latestRec?.tanggal_pengamatan
                ? `Terakhir: ${formatDateTimeId(latestRec.tanggal_pengamatan)}`
                : 'Belum ada log';
        }

        if (tbodyEl) {
            const latestFive = [...safeRecords].reverse().slice(0, 5);
            if (latestFive.length === 0) {
                tbodyEl.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada log observasi lapangan untuk batch ini.</td></tr>';
            } else {
                tbodyEl.innerHTML = latestFive.map((r, idx) => {
                    const borderCls = idx < latestFive.length - 1 ? 'border-b border-gray-100' : '';
                    const note = r.catatan || `pH ${r.ph_air ?? '-'} • EC ${r.ec ?? '-'}`;
                    return `<tr class="${borderCls}">
                        <td class="py-2 pr-2 font-semibold text-gray-900">H-${escHtml(r.umur_tanaman ?? '-')}</td>
                        <td class="py-2 pr-2 text-gray-600">${r.tinggi_cm !== null && r.tinggi_cm !== undefined ? escHtml(formatNum(r.tinggi_cm, 2)) + ' cm' : '-'}</td>
                        <td class="py-2 pr-2 font-medium text-emerald-600">${escHtml(note)}</td>
                        <td class="py-2 text-right text-gray-500">${escHtml(r.recorded_by_nama || 'Petugas')}</td>
                    </tr>`;
                }).join('');
            }
        }

        renderSvgChart(chart);
    }

    async function selectPlantTab(plantId) {
        activePlantId = Number(plantId);
        const tabs = document.querySelectorAll('#plantTabsContainer .bedeng-tab');
        tabs.forEach(t => {
            const isMatch = Number(t.dataset.plantId) === activePlantId;
            t.classList.toggle('bg-[#137a46]', isMatch);
            t.classList.toggle('text-white', isMatch);
            t.classList.toggle('shadow-sm', isMatch);
            t.classList.toggle('font-semibold', isMatch);
            t.classList.toggle('text-gray-600', !isMatch);
            t.classList.toggle('font-medium', !isMatch);
        });

        const plant = cachedPlants.find(p => Number(p.id) === activePlantId) || null;
        const cached = cachedPlantDetails[activePlantId];
        if (cached) {
            renderPlantSection(plant, cached.records, cached.meta, cached.chart);
        }

        try {
            const [histRes, chartRes] = await Promise.all([
                apiGet(`/plants/${activePlantId}/growth-history`),
                apiGet(`/plants/${activePlantId}/growth-chart`),
            ]);

            if (histRes?.ok && chartRes?.ok) {
                const records = Array.isArray(histRes.json?.data) ? histRes.json.data : [];
                const meta = histRes.json?.meta || {};
                const chart = chartRes.json?.data || {};
                cachedPlantDetails[activePlantId] = { records, meta, chart };
                renderPlantSection(plant, records, meta, chart);
            }
        } catch (_) {
            // Pertahankan data cache SSR bila backend sedang tidak dapat dijangkau.
        }
    }

    function bindTabEvents() {
        document.querySelectorAll('#plantTabsContainer .bedeng-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                selectPlantTab(tab.dataset.plantId);
            });
        });
    }

    function extractLubang(ket) {
        const m = String(ket || '').match(/(\d+)\s*lubang/i);
        return m ? Number(m[1]) : 0;
    }

    function updateSummaryCardsFromApi(overview, stocks, stockSummary, diagItems, diagStats) {
        const plants = Array.isArray(overview?.plants) ? overview.plants : cachedPlants;
        let totalLubang = 0;
        let actualGrams = 0;
        let estGrams = 0;
        const harvestDays = [];
        let readyCount = 0;
        const phVals = [];
        const ecVals = [];
        let optimalBatches = 0;
        let measuredBatches = 0;

        plants.forEach(p => {
            const lubang = extractLubang(p.keterangan);
            totalLubang += lubang;
            const latest = p.latest_growth || null;
            const berat = latest && latest.berat_gram !== null && latest.berat_gram !== undefined ? Number(latest.berat_gram) : 0;
            const mult = lubang > 0 ? lubang : 1;
            if (berat > 0) actualGrams += berat * mult;
            estGrams += Math.max(berat, 170) * mult;

            if (p.hari_menuju_panen !== null && p.hari_menuju_panen !== undefined) {
                const d = Number(p.hari_menuju_panen);
                harvestDays.push(d);
                if (d <= 0) readyCount++;
            }

            if (latest) {
                const ph = latest.ph_air !== null && latest.ph_air !== undefined ? Number(latest.ph_air) : null;
                const ec = latest.ec !== null && latest.ec !== undefined ? Number(latest.ec) : null;
                if (ph !== null) phVals.push(ph);
                if (ec !== null) ecVals.push(ec);
                if (ph !== null || ec !== null) {
                    measuredBatches++;
                    const phOk = ph === null || (ph >= 5.5 && ph <= 6.5);
                    const ecOk = ec === null || (ec >= 0.8 && ec <= 1.8);
                    if (phOk && ecOk) optimalBatches++;
                }
            }
        });

        const estKg = estGrams / 1000;
        const actKg = actualGrams / 1000;
        const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

        setEl('cardHarvestValue', estKg >= 1000 ? formatNum(estKg / 1000, 2) : formatNum(estKg, 1));
        setEl('cardHarvestUnit', estKg >= 1000 ? 'Ton' : 'kg');
        if (readyCount > 0) {
            setEl('cardHarvestSub1', `${readyCount} Batch Siap Panen`);
        } else if (harvestDays.length > 0) {
            setEl('cardHarvestSub1', `Panen ${Math.min(...harvestDays)} Hari Lagi`);
        } else {
            setEl('cardHarvestSub1', 'Belum Ada Jadwal');
        }
        setEl('cardHarvestSub2', `Terukur: ${formatNum(actKg, 2)} kg`);

        const varietasKeys = Object.keys(overview?.by_varietas || {});
        setEl('cardActiveValue', totalLubang > 0 ? formatQtyId(totalLubang) : String(plants.length));
        setEl('cardActiveUnit', totalLubang > 0 ? 'Lubang' : 'Batch');
        setEl('cardActiveSub1', `${plants.length} Batch Aktif`);
        setEl('cardActiveSub2', varietasKeys.length > 0 ? varietasKeys.slice(0, 2).join(', ') : 'Belum ada varietas');

        const nutrisiItems = (stocks || []).filter(s => s.jenis === 'nutrisi').sort((a, b) => {
            const aAb = String(a.nama_barang || '').toLowerCase().includes('ab mix') ? 0 : 1;
            const bAb = String(b.nama_barang || '').toLowerCase().includes('ab mix') ? 0 : 1;
            return aAb - bAb;
        });
        const bibitItems = (stocks || []).filter(s => s.jenis === 'bibit');
        const primaryNut = nutrisiItems[0] || null;
        const secondaryNut = nutrisiItems[1] || null;
        const totalBibit = bibitItems.reduce((acc, s) => acc + Number(s.jumlah_stok || 0), 0);
        const lowCount = Number(stockSummary?.low_stock_items || 0);

        setEl('cardNutrientValue', primaryNut ? formatQtyId(primaryNut.jumlah_stok) : '0');
        setEl('cardNutrientUnit', primaryNut ? `${primaryNut.satuan} ${primaryNut.nama_barang}` : 'Item Nutrisi');
        setEl('cardNutrientSub1', lowCount > 0 ? `${lowCount} Stok di Bawah Min` : 'Semua Stok Aman');
        const sub1Wrap = document.getElementById('cardNutrientSub1Wrap');
        if (sub1Wrap) {
            sub1Wrap.classList.toggle('text-amber-600', lowCount > 0);
            sub1Wrap.classList.toggle('text-emerald-600', lowCount === 0);
        }
        const nutParts = [];
        if (secondaryNut) {
            const shortName = String(secondaryNut.nama_barang || 'Nutrisi').split('(')[0].trim();
            nutParts.push(`${shortName}: ${formatQtyId(secondaryNut.jumlah_stok)} ${secondaryNut.satuan}`);
        }
        nutParts.push(`Bibit: ${formatQtyId(totalBibit)} biji`);
        setEl('cardNutrientSub2', nutParts.join(' • '));

        const totalDiag = Array.isArray(diagItems) ? diagItems.length : 0;
        if (totalDiag > 0) {
            const healthyCount = Number(diagStats?.Healthy || 0);
            const pct = (healthyCount / totalDiag) * 100;
            setEl('cardHealthValue', formatNum(pct, 1));
            setEl('cardHealthSub1', pct >= 80 ? 'Kondisi Prima' : 'Perlu Penanganan');
            setEl('cardHealthSub2', `${healthyCount}/${totalDiag} Diagnosis Sehat`);
        } else if (measuredBatches > 0) {
            const pct = (optimalBatches / measuredBatches) * 100;
            const avgPh = phVals.length ? (phVals.reduce((a, b) => a + b, 0) / phVals.length).toFixed(1) : '-';
            const avgEc = ecVals.length ? (ecVals.reduce((a, b) => a + b, 0) / ecVals.length).toFixed(2) : '-';
            setEl('cardHealthValue', formatNum(pct, 1));
            setEl('cardHealthSub1', optimalBatches === measuredBatches ? 'Parameter Air Optimal' : 'Cek pH / EC Nutrisi');
            setEl('cardHealthSub2', `pH ${avgPh} • EC ${avgEc} mS/cm`);
        }
    }

    function renderStocksTable(stocks, stockSummary) {
        const metaEl = document.getElementById('stocksSummaryMeta');
        const tbodyEl = document.getElementById('stocksTableBody');
        if (metaEl) {
            metaEl.textContent = `Total ${stockSummary?.total_items ?? stocks.length} item • ${stockSummary?.low_stock_items ?? 0} di bawah minimum`;
        }
        if (!tbodyEl) return;
        if (!Array.isArray(stocks) || stocks.length === 0) {
            tbodyEl.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada data inventaris di database.</td></tr>';
            return;
        }
        tbodyEl.innerHTML = stocks.map((item, idx) => {
            const qty = Number(item.jumlah_stok || 0);
            const minQty = Number(item.stok_minimum || 0);
            const isLow = Boolean(item.is_low_stock) || qty <= minQty;
            const borderCls = idx < stocks.length - 1 ? 'border-b border-gray-100' : '';
            const badge = isLow
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">Stok Menipis</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Aman</span>';
            return `<tr class="${borderCls}">
                <td class="py-2.5 pr-3 font-semibold text-gray-900">${escHtml(item.nama_barang)}${item.keterangan ? `<span class="block text-[11px] font-normal text-gray-400">${escHtml(item.keterangan)}</span>` : ''}</td>
                <td class="py-2.5 pr-3 capitalize text-gray-600">${escHtml(item.jenis)}</td>
                <td class="py-2.5 pr-3 text-right font-semibold text-gray-900">${escHtml(formatQtyId(qty))} ${escHtml(item.satuan)}</td>
                <td class="py-2.5 pr-3 text-right text-gray-500">${escHtml(formatQtyId(minQty))} ${escHtml(item.satuan)}</td>
                <td class="py-2.5 text-right">${badge}</td>
            </tr>`;
        }).join('');
    }

    async function syncDashboardWithCookie() {
        try {
            const [meRes, overviewRes, stocksRes, diagRes] = await Promise.all([
                apiGet('/auth/me'),
                apiGet('/stocks/planted-overview'),
                apiGet('/stocks?per_page=100'),
                apiGet('/diagnosis/history?per_page=50'),
            ]);

            if (meRes?.ok && meRes.json?.data) {
                const me = meRes.json.data;
                const userEl = document.getElementById('welcomeUsername');
                const subEl = document.getElementById('welcomeSubtitle');
                if (userEl && me.username) userEl.textContent = me.username;
                if (subEl && me.nama) subEl.textContent = `${me.nama} • Ringkasan data aktual budidaya selada hidroponik`;
            }

            const overview = overviewRes?.ok ? (overviewRes.json?.data || {}) : {};
            const stocks = stocksRes?.ok ? (stocksRes.json?.data || []) : cachedStocks;
            const stockSummary = overview.stock_summary || stocksRes?.json?.meta?.summary || {};
            const diagItems = diagRes?.ok ? (diagRes.json?.data || []) : [];
            const diagStats = diagRes?.json?.meta?.stats_by_label || {};

            if (Array.isArray(overview.plants) && overview.plants.length > 0) {
                cachedPlants = overview.plants;
            }
            if (Array.isArray(stocks)) {
                cachedStocks = stocks;
                renderStocksTable(stocks, stockSummary);
            }

            updateSummaryCardsFromApi(overview, cachedStocks, stockSummary, diagItems, diagStats);

            if (activePlantId) {
                await selectPlantTab(activePlantId);
            }
        } catch (_) {
            // Biarkan hasil render server tetap tampil apabila fetch klien gagal.
        }
    }

    const downloadBtn = document.getElementById('btnDownloadSummary');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', () => {
            const rows = [
                ['Kategori', 'Kode/Nama', 'Detail', 'Status/Nilai'],
                ...(cachedPlants || []).map(p => [
                    'Tanaman',
                    p.kode_tanaman || '-',
                    `${p.nama || ''} (${p.varietas || '-'}) - ${p.keterangan || ''}`,
                    `HST ${p.umur_hari ?? 0} | Panen ${p.tanggal_panen || '-'} | Tinggi ${p.latest_growth?.tinggi_cm ?? '-'} cm`,
                ]),
                ...(cachedStocks || []).map(s => [
                    `Stok (${s.jenis || '-'})`,
                    s.nama_barang || '-',
                    `Min: ${s.stok_minimum ?? 0} ${s.satuan || ''}`,
                    `${s.jumlah_stok ?? 0} ${s.satuan || ''}`,
                ]),
            ];
            const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ringkasan-hidara-${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    }

    bindTabEvents();
    syncDashboardWithCookie();
})();
</script>
@endpush
