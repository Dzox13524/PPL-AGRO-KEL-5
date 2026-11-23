@extends('layouts.karyawan', ['active' => 'tanaman'])

@section('title', 'Manajemen Tanaman')

@section('content')
@php
    $bedImages = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];
    $activeStatuses = ['pembibitan', 'pertumbuhan', 'siap_panen'];
    $activePlants = $metrics['active_plants'] ?? [];
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
            <h1 class="text-2xl sm:text-[26px] font-bold text-gray-900 tracking-tight">Manajemen Tanaman</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola batch tanaman selada hidroponik dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">plants</code> &amp; <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">growth_records</code></p>
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
                    onclick="openBatchModal('tambah')"
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
                <span id="statBatchAktif" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['active_count'] ?? 0 }}</span>
                <span class="text-sm font-semibold text-gray-500">Batch</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="statBatchSub1">{{ $metrics['batch_sub1'] ?? '-' }}</span>
                </div>
                <span id="statBatchSub2" class="text-gray-400">{{ $metrics['batch_sub2'] ?? '0 Batch Selesai' }}</span>
            </div>
        </div>

        {{-- Card 2: Populasi Ditanam --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">POPULASI DITANAM</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statPopulasi" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['populasi_value'] ?? '0' }}</span>
                <span id="statPopulasiUnit" class="text-sm font-semibold text-gray-500">{{ $metrics['populasi_unit'] ?? 'Lubang' }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs gap-2">
                <span id="statPopulasiSub1" class="text-emerald-700 font-medium">{{ $metrics['populasi_sub1'] ?? '0 Batch Beroperasi' }}</span>
                <span id="statPopulasiSub2" class="text-gray-400 truncate">{{ $metrics['populasi_sub2'] ?? '-' }}</span>
            </div>
        </div>

        {{-- Card 3: Estimasi Panen --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs hover:shadow-md transition">
            <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">ESTIMASI PANEN</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statSiapPanen" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['panen_value'] ?? '0.0' }}</span>
                <span id="statSiapPanenUnit" class="text-sm font-semibold text-gray-500">{{ $metrics['panen_unit'] ?? 'kg' }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs gap-2">
                <div class="flex items-center gap-1.5 text-emerald-700 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span id="statSiapPanenSub1">{{ $metrics['panen_sub1'] ?? '-' }}</span>
                </div>
                <span id="statSiapPanenSub2" class="text-gray-400">{{ $metrics['panen_sub2'] ?? 'Terukur: 0.00 kg' }}</span>
            </div>
        </div>

        {{-- Card 4: Rata-Rata Kesehatan --}}
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
            </div>
            <a href="#tableSection" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0e5f38] hover:text-[#0b4e2e] transition">
                <span>Lihat Daftar Lengkap</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        {{-- Active Plant Cards Grid --}}
        <div id="activeBedsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($activePlants as $idx => $p)
                @php
                    $img = $bedImages[$idx % count($bedImages)];
                    $days = $p['hari_menuju_panen'] ?? null;
                    $latest = is_array($p['latest_growth_record'] ?? null) ? $p['latest_growth_record'] : null;
                    if ($days !== null && $days < 0) {
                        $panenBadge = 'Siap Panen (' . abs((int) $days) . ' hr lewat)';
                    } elseif ($days === 0) {
                        $panenBadge = 'Panen Hari Ini';
                    } elseif ($days !== null) {
                        $panenBadge = $days . ' Hari Lagi';
                    } else {
                        $panenBadge = '-';
                    }
                    $statusLabel = ucwords(str_replace('_', ' ', (string) ($p['status'] ?? 'aktif')));
                @endphp
                <div onclick="showDetail({{ $p['id'] }})" class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition cursor-pointer">
                    <div class="relative h-40 bg-gray-100 overflow-hidden">
                        <img src="/images/beds/{{ $img }}"
                             alt="{{ $p['kode_tanaman'] }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-[#0e5f38]/90 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                            HST {{ $p['umur_hari'] ?? 0 }}
                        </span>
                    </div>
                    <div class="p-3.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-gray-900 text-sm">{{ $p['kode_tanaman'] }}</span>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $statusLabel }}</span>
                        </div>
                        <p class="text-xs font-medium text-gray-700 mt-1">{{ $p['nama'] }}{{ !empty($p['varietas']) ? ' · ' . $p['varietas'] : '' }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">{{ $p['keterangan'] ?? '-' }}</p>
                        @if($latest)
                            <p class="text-[11px] font-medium text-emerald-700 mt-1.5">
                                {{ number_format((float) ($latest['tinggi_cm'] ?? 0), 2) }} cm · {{ number_format((float) ($latest['berat_gram'] ?? 0), 2) }} g · {{ $latest['jumlah_daun'] ?? '-' }} daun
                            </p>
                        @endif
                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                            <span>Est. Panen:</span>
                            <span class="font-medium text-gray-800">{{ $p['tanggal_panen'] ?? '-' }} ({{ $panenBadge }})</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-xs text-gray-400">
                    Belum ada batch tanaman aktif pada database.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Batch Table Section --}}
    <div id="tableSection" class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 shadow-xs">
        {{-- Filter & Search Bar --}}
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
                           placeholder="Cari kode tanaman, nama, varietas, atau lokasi..."
                           class="w-full pl-10 pr-4 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition">
                </div>

                {{-- Varietas Filter --}}
                <select id="varietasFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Varietas</option>
                    @foreach(($metrics['varietas_list'] ?? []) as $varName)
                        <option value="{{ $varName }}">{{ $varName }}</option>
                    @endforeach
                </select>

                {{-- Fase / Status Filter --}}
                <select id="faseFilter"
                        class="px-3 py-2 bg-[#f8fafc] border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38] transition cursor-pointer">
                    <option value="">Semua Fase</option>
                    <option value="pembibitan">Pembibitan</option>
                    <option value="pertumbuhan">Pertumbuhan</option>
                    <option value="siap_panen">Siap Panen</option>
                    <option value="dipanen">Dipanen</option>
                    <option value="mati">Mati</option>
                </select>
            </div>

            {{-- Status Tabs --}}
            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl self-start sm:self-auto shrink-0">
                <button type="button"
                        id="tabActive"
                        onclick="setTabStatus('aktif')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer">
                    Aktif ({{ $metrics['active_count'] ?? 0 }})
                </button>
                <button type="button"
                        id="tabFinished"
                        onclick="setTabStatus('selesai')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer">
                    Panen / Selesai ({{ $metrics['finished_count'] ?? 0 }})
                </button>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto -mx-5 sm:-mx-6 mt-2">
            <div class="inline-block min-w-full align-middle px-5 sm:px-6">
                <table class="min-w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th scope="col" class="py-3.5 pr-4">KODE &amp; LOKASI TANAMAN</th>
                            <th scope="col" class="py-3.5 px-4">TANGGAL TANAM &amp; UMUR</th>
                            <th scope="col" class="py-3.5 px-4">FASE / STATUS</th>
                            <th scope="col" class="py-3.5 px-4">PERTUMBUHAN TERKINI</th>
                            <th scope="col" class="py-3.5 pl-4 text-right">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody id="batchTableBody" class="divide-y divide-gray-100 text-gray-700">
                        @forelse($activePlants as $p)
                            @php
                                $latest = is_array($p['latest_growth_record'] ?? null) ? $p['latest_growth_record'] : null;
                                $stLabel = ucwords(str_replace('_', ' ', (string) ($p['status'] ?? 'aktif')));
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition group">
                                <td class="py-4 pr-4">
                                    <div class="font-bold text-gray-900">{{ $p['kode_tanaman'] }} — {{ $p['nama'] }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $p['varietas'] ?? '-' }} • {{ $p['keterangan'] ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-medium text-gray-900">{{ $p['tanggal_tanam'] ?? '-' }}</div>
                                    <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">HST {{ $p['umur_hari'] ?? 0 }} (Panen: {{ $p['tanggal_panen'] ?? '-' }})</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-emerald-50 text-emerald-700 border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        <span>{{ $stLabel }}</span>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if($latest)
                                        <div class="font-bold text-emerald-700">{{ number_format((float) ($latest['tinggi_cm'] ?? 0), 2) }} cm • {{ number_format((float) ($latest['berat_gram'] ?? 0), 2) }} g</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $latest['jumlah_daun'] ?? '-' }} daun • pH {{ $latest['ph_air'] ?? '-' }} • EC {{ $latest['ec'] ?? '-' }}</div>
                                    @else
                                        <div class="font-medium text-gray-400">Belum ada catatan</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">0 log pertumbuhan</div>
                                    @endif
                                </td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <button type="button" onclick="showDetail({{ $p['id'] }})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition cursor-pointer" title="Lihat Detail">Detail</button>
                                        <button type="button" onclick="editBatch({{ $p['id'] }})" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit Data">Edit</button>
                                        <button type="button" onclick="deleteBatch({{ $p['id'] }})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada batch tanaman di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Table Pagination & Info --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
            <div id="tableInfo">Menampilkan {{ count($activePlants) }} dari {{ count($plants ?? []) }} batch tanaman</div>
            <div class="flex items-center gap-1 self-center sm:self-auto">
                <button type="button" id="btnPrevBatch" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    Sebelumnya
                </button>
                <div id="batchPageButtons" class="flex items-center gap-1"></div>
                <button type="button" id="btnNextBatch" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Tambah / Edit Data Tanaman --}}
<div id="addBatchModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="closeBatchModal()"></div>
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
                <button type="button" onclick="closeBatchModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="addBatchForm" onsubmit="handleSaveBatch(event)" class="mt-4 space-y-4">
                <input type="hidden" id="batchFormMode" value="tambah">
                <input type="hidden" id="editBatchId" value="">

                <div id="modalErrorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium rounded-xl px-3.5 py-2.5"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="modalNama" class="block text-xs font-semibold text-gray-700 mb-1">Nama Tanaman <span class="text-rose-500">*</span></label>
                        <input type="text" id="modalNama" required placeholder="Contoh: Selada Keriting" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="modalVarietas" class="block text-xs font-semibold text-gray-700 mb-1">Varietas</label>
                        <input type="text" id="modalVarietas" placeholder="Contoh: Grand Rapids" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="modalKodeTanaman" class="block text-xs font-semibold text-gray-700 mb-1">Kode Tanaman</label>
                        <input type="text" id="modalKodeTanaman" placeholder="Otomatis jika kosong" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="modalStatus" class="block text-xs font-semibold text-gray-700 mb-1">Fase / Status <span class="text-rose-500">*</span></label>
                        <select id="modalStatus" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                            <option value="pembibitan">Pembibitan</option>
                            <option value="pertumbuhan">Pertumbuhan</option>
                            <option value="siap_panen">Siap Panen</option>
                            <option value="dipanen">Dipanen</option>
                            <option value="mati">Mati</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="modalTanggal" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Tanam <span class="text-rose-500">*</span></label>
                        <input type="date" id="modalTanggal" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                    <div>
                        <label for="modalTanggalPanen" class="block text-xs font-semibold text-gray-700 mb-1">Estimasi Panen</label>
                        <input type="date" id="modalTanggalPanen" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]">
                    </div>
                </div>

                <div>
                    <label for="modalKeterangan" class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Lokasi Bedeng &amp; Jumlah Lubang</label>
                    <textarea id="modalKeterangan" rows="2" placeholder="Contoh: Greenhouse A - Rak 1 (120 lubang NFT)" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0e5f38]/20 focus:border-[#0e5f38]"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeBatchModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSaveBatch" class="px-4 py-2 rounded-xl bg-[#0e5f38] hover:bg-[#0b4e2e] text-white text-xs font-semibold transition cursor-pointer shadow-xs disabled:opacity-60">
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
                <h3 class="text-base font-bold text-gray-900" id="detailTitle">Detail Batch Tanaman</h3>
                <button type="button" onclick="closeDetailBatchModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="detailContent" class="py-4 space-y-3 text-xs sm:text-sm text-gray-700"></div>
            <div class="pt-3 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closeDetailBatchModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="plantToastNotification"
     class="fixed bottom-4 sm:bottom-6 right-4 sm:right-6 max-w-sm z-50 transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-2.5 bg-gray-900 text-white text-xs font-medium px-4 py-3 rounded-xl shadow-lg pointer-events-none">
    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
    <span id="plantToastMessage">Berhasil</span>
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
    let diagnosisData = @json($diagnosisItems ?? []);
    let diagnosisStats = @json($diagnosisStats ?? []);
    let currentTab = 'aktif';
    let currentPage = 1;
    const perPage = 6;
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

    function formatIntId(val) {
        return new Intl.NumberFormat('id-ID').format(Number(val) || 0);
    }

    function getTodayDateString() {
        const d = new Date(Date.now() - new Date().getTimezoneOffset() * 60000);
        return d.toISOString().split('T')[0];
    }

    function showToast(msg) {
        const toast = document.getElementById('plantToastNotification');
        const msgEl = document.getElementById('plantToastMessage');
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

    function statusMeta(status) {
        const map = {
            pembibitan: { label: 'Pembibitan', cls: 'bg-blue-50 text-blue-700 border-blue-200' },
            pertumbuhan: { label: 'Pertumbuhan', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
            siap_panen: { label: 'Siap Panen', cls: 'bg-teal-50 text-teal-700 border-teal-200' },
            dipanen: { label: 'Dipanen', cls: 'bg-gray-100 text-gray-700 border-gray-200' },
            mati: { label: 'Mati', cls: 'bg-rose-50 text-rose-700 border-rose-200' },
        };
        return map[status] || { label: String(status || 'Aktif'), cls: 'bg-gray-100 text-gray-700 border-gray-200' };
    }

    function extractLubang(ket) {
        const m = String(ket || '').match(/(\d+)\s*lubang/i);
        return m ? Number(m[1]) : 0;
    }

    function updateMetricsAndFilters() {
        const active = plantsData.filter(p => ACTIVE_STATUSES.includes(p.status));
        const finished = plantsData.filter(p => !ACTIVE_STATUSES.includes(p.status));

        let totalLubang = 0;
        let estGrams = 0;
        let actGrams = 0;
        let minDays = null;
        let nearest = null;
        let readyCount = 0;
        const varietasSet = new Set();
        const phVals = [];
        const ecVals = [];
        let optimalBatches = 0;
        let measuredBatches = 0;

        active.forEach(p => {
            const lubang = extractLubang(p.keterangan);
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
                if (minDays === null || d < minDays) {
                    minDays = d;
                    nearest = p;
                }
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

        const setEl = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };

        setEl('statBatchAktif', String(active.length));
        setEl('statBatchSub1', readyCount > 0 ? `${readyCount} Batch Siap Panen` : (minDays !== null ? `Panen ${minDays} Hari Lagi` : 'Belum Ada Jadwal'));
        setEl('statBatchSub2', `${finished.length} Batch Selesai`);

        const varietasArr = Array.from(varietasSet);
        setEl('statPopulasi', totalLubang > 0 ? formatIntId(totalLubang) : String(active.length));
        setEl('statPopulasiUnit', totalLubang > 0 ? 'Lubang' : 'Batch');
        setEl('statPopulasiSub1', `${active.length} Batch Beroperasi`);
        setEl('statPopulasiSub2', varietasArr.length ? varietasArr.slice(0, 2).join(', ') : 'Belum ada varietas');

        const estKg = estGrams / 1000;
        const actKg = actGrams / 1000;
        setEl('statSiapPanen', estKg >= 1000 ? formatNum(estKg / 1000, 2) : formatNum(estKg, 1));
        setEl('statSiapPanenUnit', estKg >= 1000 ? 'Ton' : 'kg');
        setEl('statSiapPanenSub1', nearest ? nearest.kode_tanaman : 'Belum ada batch');
        setEl('statSiapPanenSub2', `Terukur: ${formatNum(actKg, 2)} kg`);

        const totalDiag = Array.isArray(diagnosisData) ? diagnosisData.length : 0;
        if (totalDiag > 0) {
            const healthyCount = Number(diagnosisStats?.Healthy || 0);
            const pct = (healthyCount / totalDiag) * 100;
            setEl('statKesehatan', formatNum(pct, 1));
            setEl('statKesehatanSub1', pct >= 80 ? 'Kondisi Prima' : 'Perlu Penanganan');
            setEl('statKesehatanSub2', `${healthyCount}/${totalDiag} Diagnosis Sehat`);
        } else if (measuredBatches > 0) {
            const pct = (optimalBatches / measuredBatches) * 100;
            const avgPh = phVals.length ? (phVals.reduce((a, b) => a + b, 0) / phVals.length).toFixed(1) : '-';
            const avgEc = ecVals.length ? (ecVals.reduce((a, b) => a + b, 0) / ecVals.length).toFixed(2) : '-';
            setEl('statKesehatan', formatNum(pct, 1));
            setEl('statKesehatanSub1', optimalBatches === measuredBatches ? 'Parameter Air Optimal' : 'Cek pH / EC Nutrisi');
            setEl('statKesehatanSub2', `pH ${avgPh} • EC ${avgEc} mS/cm`);
        } else {
            setEl('statKesehatan', '0.0');
            setEl('statKesehatanSub1', 'Belum Ada Pengamatan');
            setEl('statKesehatanSub2', 'Menunggu Catatan / SVM');
        }

        const tabActive = document.getElementById('tabActive');
        const tabFinished = document.getElementById('tabFinished');
        if (tabActive) tabActive.textContent = `Aktif (${active.length})`;
        if (tabFinished) tabFinished.textContent = `Panen / Selesai (${finished.length})`;

        const varSelect = document.getElementById('varietasFilter');
        if (varSelect) {
            const currentVar = varSelect.value;
            const allVarietas = Array.from(new Set(plantsData.map(p => p.varietas).filter(Boolean)));
            varSelect.innerHTML = '<option value="">Semua Varietas</option>' +
                allVarietas.map(v => `<option value="${esc(v)}" ${currentVar === v ? 'selected' : ''}>${esc(v)}</option>`).join('');
        }
    }

    function renderActiveBedsGrid() {
        const grid = document.getElementById('activeBedsGrid');
        if (!grid) return;
        const active = plantsData.filter(p => ACTIVE_STATUSES.includes(p.status));

        if (active.length === 0) {
            grid.innerHTML = '<div class="col-span-full py-6 text-center text-xs text-gray-400">Belum ada batch tanaman aktif pada database.</div>';
            return;
        }

        grid.innerHTML = active.map((p, idx) => {
            const img = BED_IMAGES[idx % BED_IMAGES.length];
            const st = statusMeta(p.status);
            const d = p.hari_menuju_panen;
            let panenBadge = '-';
            if (d !== null && d !== undefined) {
                const num = Number(d);
                if (num < 0) panenBadge = `Siap Panen (${Math.abs(num)} hr lewat)`;
                else if (num === 0) panenBadge = 'Panen Hari Ini';
                else panenBadge = `${num} Hari Lagi`;
            }
            const latest = p.latest_growth_record || null;
            const metricLine = latest
                ? `<p class="text-[11px] font-medium text-emerald-700 mt-1.5">${formatNum(latest.tinggi_cm, 2)} cm · ${formatNum(latest.berat_gram, 2)} g · ${esc(latest.jumlah_daun ?? '-')} daun</p>`
                : '';

            return `
                <div onclick="showDetail(${p.id})" class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:border-emerald-200 hover:shadow-md transition cursor-pointer">
                    <div class="relative h-40 bg-gray-100 overflow-hidden">
                        <img src="/images/beds/${img}" alt="${esc(p.kode_tanaman)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-[#0e5f38]/90 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                            HST ${esc(p.umur_hari ?? 0)}
                        </span>
                    </div>
                    <div class="p-3.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-gray-900 text-sm">${esc(p.kode_tanaman)}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border ${st.cls}">${esc(st.label)}</span>
                        </div>
                        <p class="text-xs font-medium text-gray-700 mt-1">${esc(p.nama)}${p.varietas ? ' · ' + esc(p.varietas) : ''}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">${esc(p.keterangan || '-')}</p>
                        ${metricLine}
                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                            <span>Est. Panen:</span>
                            <span class="font-medium text-gray-800">${esc(p.tanggal_panen || '-')} (${esc(panenBadge)})</span>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    function getFilteredPlants() {
        const q = (document.getElementById('batchSearchInput')?.value || '').toLowerCase().trim();
        const varVal = document.getElementById('varietasFilter')?.value || '';
        const faseVal = document.getElementById('faseFilter')?.value || '';

        return plantsData.filter(p => {
            const isActive = ACTIVE_STATUSES.includes(p.status);
            if (currentTab === 'aktif' && !isActive) return false;
            if (currentTab === 'selesai' && isActive) return false;
            if (varVal && p.varietas !== varVal) return false;
            if (faseVal && p.status !== faseVal) return false;
            if (q) {
                const hay = `${p.kode_tanaman || ''} ${p.nama || ''} ${p.varietas || ''} ${p.keterangan || ''}`.toLowerCase();
                if (!hay.includes(q)) return false;
            }
            return true;
        });
    }

    function renderTable() {
        const tbody = document.getElementById('batchTableBody');
        const infoEl = document.getElementById('tableInfo');
        const prevBtn = document.getElementById('btnPrevBatch');
        const nextBtn = document.getElementById('btnNextBatch');
        const pageBtns = document.getElementById('batchPageButtons');
        if (!tbody) return;

        const filtered = getFilteredPlants();
        const total = filtered.length;
        const totalPages = Math.ceil(total / perPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        if (pageItems.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-gray-400 text-xs">Tidak ada data batch tanaman yang sesuai filter.</td></tr>';
        } else {
            tbody.innerHTML = pageItems.map(p => {
                const st = statusMeta(p.status);
                const latest = p.latest_growth_record || null;
                const growthHtml = latest
                    ? `<div class="font-bold text-emerald-700">${formatNum(latest.tinggi_cm, 2)} cm • ${formatNum(latest.berat_gram, 2)} g</div>
                       <div class="text-[11px] text-gray-400 mt-0.5">${esc(latest.jumlah_daun ?? '-')} daun • pH ${esc(latest.ph_air ?? '-')} • EC ${esc(latest.ec ?? '-')}</div>`
                    : `<div class="font-medium text-gray-400">Belum ada catatan</div>
                       <div class="text-[11px] text-gray-400 mt-0.5">${esc(p.growth_record_count || 0)} log pertumbuhan</div>`;

                return `
                    <tr class="hover:bg-gray-50/70 transition group">
                        <td class="py-4 pr-4">
                            <div class="font-bold text-gray-900">${esc(p.kode_tanaman)} — ${esc(p.nama)}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">${esc(p.varietas || '-')} • ${esc(p.keterangan || '-')}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-gray-900">${esc(p.tanggal_tanam || '-')}</div>
                            <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">HST ${esc(p.umur_hari ?? 0)} (Panen: ${esc(p.tanggal_panen || '-')})</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${st.cls}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <span>${esc(st.label)}</span>
                            </span>
                        </td>
                        <td class="py-4 px-4">${growthHtml}</td>
                        <td class="py-4 pl-4 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <button type="button" onclick="showDetail(${p.id})" class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition cursor-pointer" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" onclick="editBatch(${p.id})" class="p-1.5 text-gray-400 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" onclick="deleteBatch(${p.id})" class="p-1.5 text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        if (infoEl) infoEl.textContent = `Menampilkan ${pageItems.length} dari ${total} batch tanaman`;
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
                btn.addEventListener('click', () => {
                    currentPage = i;
                    renderTable();
                });
                pageBtns.appendChild(btn);
            }
        }
    }

    async function syncPlantsFromApi() {
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

            updateMetricsAndFilters();
            renderActiveBedsGrid();
            renderTable();
        } catch (_) {
            // Pertahankan data SSR bila backend sementara tidak dapat dijangkau.
        }
    }

    window.setTabStatus = function (status) {
        currentTab = status;
        currentPage = 1;
        const tabActive = document.getElementById('tabActive');
        const tabFinished = document.getElementById('tabFinished');
        if (status === 'aktif') {
            tabActive.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer';
            tabFinished.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer';
        } else {
            tabFinished.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#0e5f38] text-white shadow-xs transition cursor-pointer';
            tabActive.className = 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 transition cursor-pointer';
        }
        renderTable();
    };

    function showModalError(msg) {
        const box = document.getElementById('modalErrorBox');
        if (!box) return;
        if (!msg) {
            box.classList.add('hidden');
            box.textContent = '';
            return;
        }
        box.textContent = msg;
        box.classList.remove('hidden');
    }

    window.openBatchModal = function (mode, plant = null) {
        document.getElementById('batchFormMode').value = mode;
        showModalError('');
        const btn = document.getElementById('btnSaveBatch');
        btn.disabled = false;

        if (mode === 'tambah') {
            document.getElementById('modal-title').textContent = 'Tambah Batch Tanaman Baru';
            btn.textContent = 'Simpan Batch';
            document.getElementById('editBatchId').value = '';
            document.getElementById('modalNama').value = 'Selada Keriting';
            document.getElementById('modalVarietas').value = '';
            document.getElementById('modalKodeTanaman').value = '';
            document.getElementById('modalStatus').value = 'pembibitan';
            document.getElementById('modalTanggal').value = getTodayDateString();
            document.getElementById('modalTanggalPanen').value = '';
            document.getElementById('modalKeterangan').value = '';
        } else {
            document.getElementById('modal-title').textContent = `Edit Tanaman ${plant.kode_tanaman || ''}`;
            btn.textContent = 'Simpan Perubahan';
            document.getElementById('editBatchId').value = plant.id;
            document.getElementById('modalNama').value = plant.nama || '';
            document.getElementById('modalVarietas').value = plant.varietas || '';
            document.getElementById('modalKodeTanaman').value = plant.kode_tanaman || '';
            document.getElementById('modalStatus').value = plant.status || 'pertumbuhan';
            document.getElementById('modalTanggal').value = plant.tanggal_tanam || getTodayDateString();
            document.getElementById('modalTanggalPanen').value = plant.tanggal_panen || '';
            document.getElementById('modalKeterangan').value = plant.keterangan || '';
        }

        document.getElementById('addBatchModal').classList.remove('hidden');
    };

    window.closeBatchModal = function () {
        document.getElementById('addBatchModal').classList.add('hidden');
    };

    window.handleSaveBatch = async function (e) {
        e.preventDefault();
        const mode = document.getElementById('batchFormMode').value;
        const nama = document.getElementById('modalNama').value.trim();
        const varietas = document.getElementById('modalVarietas').value.trim();
        const kode = document.getElementById('modalKodeTanaman').value.trim();
        const statusVal = document.getElementById('modalStatus').value;
        const tanggal = document.getElementById('modalTanggal').value;
        const tanggalPanen = document.getElementById('modalTanggalPanen').value;
        const keterangan = document.getElementById('modalKeterangan').value.trim();
        const btn = document.getElementById('btnSaveBatch');

        showModalError('');
        if (!nama || nama.length < 2) {
            showModalError('Nama tanaman minimal 2 karakter.');
            return;
        }

        const payload = {
            nama,
            varietas: varietas || null,
            kode_tanaman: kode || null,
            tanggal_tanam: tanggal || getTodayDateString(),
            tanggal_panen: tanggalPanen || null,
            status: statusVal,
            keterangan: keterangan || null,
        };

        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const url = mode === 'tambah' ? '/plants' : `/plants/${document.getElementById('editBatchId').value}`;
            const method = mode === 'tambah' ? 'POST' : 'PUT';
            const res = await apiRequest(url, { method, body: JSON.stringify(payload) });
            if (!res) return;

            if (res.ok) {
                await syncPlantsFromApi();
                window.closeBatchModal();
                if (!ACTIVE_STATUSES.includes(statusVal)) {
                    window.setTabStatus('selesai');
                } else {
                    window.setTabStatus('aktif');
                }
                showToast(mode === 'tambah' ? `Batch "${nama}" berhasil ditambahkan!` : `Batch "${nama}" berhasil diperbarui!`);
            } else {
                showModalError(extractApiError(res.json, 'Gagal menyimpan data tanaman.'));
            }
        } catch (_) {
            showModalError('Tidak dapat terhubung ke Backend API (port 8000).');
        } finally {
            btn.disabled = false;
            btn.textContent = mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Batch';
        }
    };

    function renderDetailHtml(p) {
        const st = statusMeta(p.status);
        const latest = p.latest_growth_record || null;
        const growthSection = latest
            ? `<div class="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl space-y-1.5">
                    <div class="text-[11px] font-bold text-emerald-800 uppercase">Catatan Pertumbuhan Terbaru (HST ${esc(latest.umur_tanaman ?? '-')})</div>
                    <div class="flex justify-between text-xs"><span class="text-gray-600">Tinggi / Daun / Berat:</span> <span class="font-semibold text-gray-900">${formatNum(latest.tinggi_cm, 2)} cm • ${esc(latest.jumlah_daun ?? '-')} daun • ${formatNum(latest.berat_gram, 2)} g</span></div>
                    <div class="flex justify-between text-xs"><span class="text-gray-600">pH / EC / Suhu Air:</span> <span class="font-semibold text-gray-900">pH ${esc(latest.ph_air ?? '-')} • EC ${esc(latest.ec ?? '-')} • ${esc(latest.suhu_air ?? '-')} °C</span></div>
                    <div class="flex justify-between text-xs"><span class="text-gray-600">Suhu / Kelembapan:</span> <span class="font-medium text-gray-800">${esc(latest.suhu_c ?? '-')} °C • ${esc(latest.kelembapan_persen ?? '-')}%</span></div>
                    <div class="text-[11px] text-gray-500 pt-1">Catatan (${esc(latest.recorded_by_nama || 'Staf')}): ${esc(latest.catatan || '-')}</div>
               </div>`
            : `<div class="p-3 bg-gray-50 rounded-xl text-xs text-gray-400">Belum ada catatan pertumbuhan untuk batch ini.</div>`;

        return `
            <div class="p-3 bg-gray-50 rounded-xl space-y-2">
                <div class="flex justify-between"><span class="text-gray-500">Kode Batch:</span> <span class="font-bold text-gray-900">${esc(p.kode_tanaman)}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Nama / Varietas:</span> <span class="font-medium text-gray-800">${esc(p.nama)} (${esc(p.varietas || '-')})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal Tanam:</span> <span class="font-medium text-gray-800">${esc(p.tanggal_tanam || '-')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Umur / Est. Panen:</span> <span class="font-bold text-emerald-600">HST ${esc(p.umur_hari ?? 0)} / ${esc(p.tanggal_panen || '-')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status Fase:</span> <span class="font-semibold text-gray-800">${esc(st.label)}</span></div>
                <div class="flex justify-between gap-3"><span class="text-gray-500 shrink-0">Lokasi / Ket.:</span> <span class="font-medium text-gray-800 text-right">${esc(p.keterangan || '-')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Dicatat Oleh:</span> <span class="font-medium text-gray-800">${esc(p.created_by_nama || '-')}</span></div>
            </div>
            ${growthSection}
        `;
    }

    window.showDetail = async function (id) {
        const cached = plantsData.find(b => Number(b.id) === Number(id));
        const contentEl = document.getElementById('detailContent');
        const titleEl = document.getElementById('detailTitle');
        if (cached) {
            if (titleEl) titleEl.textContent = `Detail ${cached.kode_tanaman}`;
            if (contentEl) contentEl.innerHTML = renderDetailHtml(cached);
            document.getElementById('detailBatchModal').classList.remove('hidden');
        }

        try {
            const res = await apiRequest(`/plants/${id}`, { method: 'GET' });
            if (res?.ok && res.json?.data) {
                const detail = res.json.data;
                if (titleEl) titleEl.textContent = `Detail ${detail.kode_tanaman}`;
                if (contentEl) contentEl.innerHTML = renderDetailHtml(detail);
            }
        } catch (_) {
            // Gunakan cache jika request detail gagal.
        }
    };

    window.closeDetailBatchModal = function () {
        document.getElementById('detailBatchModal').classList.add('hidden');
    };

    window.editBatch = function (id) {
        const plant = plantsData.find(b => Number(b.id) === Number(id));
        if (plant) window.openBatchModal('edit', plant);
    };

    window.deleteBatch = async function (id) {
        const plant = plantsData.find(b => Number(b.id) === Number(id));
        if (!plant || !confirm(`Apakah Anda yakin ingin menghapus batch ${plant.kode_tanaman} (${plant.nama}) dari database?`)) {
            return;
        }
        try {
            const res = await apiRequest(`/plants/${id}`, { method: 'DELETE' });
            if (!res) return;
            if (res.ok) {
                await syncPlantsFromApi();
                showToast(`Batch ${plant.kode_tanaman} berhasil dihapus.`);
            } else {
                showToast(extractApiError(res.json, 'Gagal menghapus batch tanaman.'));
            }
        } catch (_) {
            showToast('Gagal terhubung ke Backend API (port 8000).');
        }
    };

    window.exportSummary = function () {
        const rows = [
            ['Kode Tanaman', 'Nama', 'Varietas', 'Status', 'Tanggal Tanam', 'Umur (HST)', 'Tanggal Panen', 'Tinggi Terkini (cm)', 'Berat Terkini (g)', 'Keterangan'],
            ...plantsData.map(p => [
                p.kode_tanaman || '',
                p.nama || '',
                p.varietas || '',
                p.status || '',
                p.tanggal_tanam || '',
                p.umur_hari ?? 0,
                p.tanggal_panen || '',
                p.latest_growth_record?.tinggi_cm ?? '',
                p.latest_growth_record?.berat_gram ?? '',
                p.keterangan || '',
            ]),
        ];
        const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `manajemen-tanaman-hidara-${getTodayDateString()}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('batchSearchInput')?.addEventListener('input', () => { currentPage = 1; renderTable(); });
        document.getElementById('varietasFilter')?.addEventListener('change', () => { currentPage = 1; renderTable(); });
        document.getElementById('faseFilter')?.addEventListener('change', () => { currentPage = 1; renderTable(); });
        document.getElementById('btnPrevBatch')?.addEventListener('click', () => {
            if (currentPage > 1) { currentPage--; renderTable(); }
        });
        document.getElementById('btnNextBatch')?.addEventListener('click', () => {
            const totalPages = Math.ceil(getFilteredPlants().length / perPage) || 1;
            if (currentPage < totalPages) { currentPage++; renderTable(); }
        });

        updateMetricsAndFilters();
        renderActiveBedsGrid();
        renderTable();
        syncPlantsFromApi();
    });
})();
</script>
@endpush
