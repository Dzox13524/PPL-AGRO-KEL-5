@extends('layouts.admin', ['active' => 'stok'])

@section('title', 'Manajemen Stok')

@section('content')
@php
    $bedImages = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];
    $formatQty = function ($val) {
        $s = number_format((float) $val, 2, ',', '.');
        return rtrim(rtrim($s, '0'), ',');
    };
@endphp

<style>
    /* Transisi Halus Drawer Samping Kanan */
    #stockDrawerBackdrop {
        transition: opacity 0.3s ease, visibility 0.3s ease;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #stockDrawerBackdrop.open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    #stockDrawerPanel {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        transform: translateX(100%);
    }
    #stockDrawerPanel.open {
        transform: translateX(0);
    }
</style>

<div class="max-w-[1400px] mx-auto space-y-6">
    @if(!empty($apiError))
        <div id="apiWarningBanner" class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-xs font-medium flex items-center justify-between gap-3">
            <span>{{ $apiError }}</span>
            <button type="button" onclick="window.location.reload()" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 rounded-md font-semibold cursor-pointer">Muat Ulang</button>
        </div>
    @endif

    {{-- Top Title & Action Button --}}
    <div class="flex items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Stok</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola stok bibit, nutrisi, media tanam, batch tanaman, dan riwayat mutasi di database</p>
        </div>
        <div class="shrink-0">
            <button type="button"
                    id="btnTambahStok"
                    onclick="openStockDrawer('tambah')"
                    style="background-color: #0e5f38;"
                    class="inline-flex items-center gap-1.5 sm:gap-2 hover:opacity-95 text-white text-xs sm:text-[13px] font-semibold px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl shadow-sm transition active:scale-95 cursor-pointer">
                <img src="/icons/icon_add_stock.png" alt="Tambah Stok" class="w-3.5 h-3.5 sm:w-4 sm:h-4 object-contain invert brightness-0">
                <span>Tambah Stok</span>
            </button>
        </div>
    </div>

    {{-- Filter Tabs Bar --}}
    <div class="overflow-x-auto max-w-full no-scrollbar pb-1 -mx-1 px-1 sm:mx-0 sm:px-0">
        <div id="stockFilterTabs" class="bg-white border border-gray-100 shadow-xs rounded-xl p-1.5 inline-flex items-center gap-1 min-w-max">
            <button type="button" data-filter="all" class="filter-tab bg-[#e6f0fa] text-[#1a5b8c] font-semibold text-xs px-4 py-2 rounded-lg transition shrink-0 cursor-pointer">
                Semua Ringkasan
            </button>
            <button type="button" data-filter="planted" class="filter-tab text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer">
                Tanaman Ditanam
            </button>
            <button type="button" data-filter="inventory" class="filter-tab text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer">
                Stok &amp; Inventaris
            </button>
            <button type="button" data-filter="movements" class="filter-tab text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer">
                Riwayat Catatan
            </button>
        </div>
    </div>

    {{-- 3 Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Card 1: Tanaman Aktif --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 font-medium">Tanaman Aktif</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <img src="/icons/icon_plant.png" alt="Tanaman" class="w-4 h-4 object-contain" style="filter: invert(48%) sepia(79%) saturate(452%) hue-rotate(86deg) brightness(98%) contrast(92%);">
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span id="statTanamanAktif" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['tanaman_value'] ?? '0' }}</span>
                <span id="statTanamanUnit" class="text-xs text-gray-500">{{ $metrics['tanaman_unit'] ?? 'Lubang Tanam' }}</span>
            </div>
            <p id="statTanamanSub" class="text-xs font-semibold text-emerald-600 mt-3">{{ $metrics['tanaman_sub'] ?? '0 Batch aktif' }}</p>
        </div>

        {{-- Card 2: Total Stok Benih --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 font-medium">Total Stok Benih</p>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <img src="/icons/icon_seed.png" alt="Bibit" class="w-4 h-4 object-contain" style="filter: invert(78%) sepia(61%) saturate(1478%) hue-rotate(359deg) brightness(98%) contrast(92%);">
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span id="statTotalBenih" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['benih_value'] ?? '0' }}</span>
                <span id="statBenihUnit" class="text-xs text-gray-500">{{ $metrics['benih_unit'] ?? 'biji' }}</span>
            </div>
            <p id="statBenihSub" class="text-xs text-gray-500 mt-3">{{ $metrics['benih_sub'] ?? '0 jenis bibit' }}</p>
        </div>

        {{-- Card 3: Nutrisi & Media --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 font-medium">Ketersediaan Nutrisi</p>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <img src="/icons/icon_pharma.png" alt="Nutrisi" class="w-4 h-4 object-contain" style="filter: invert(31%) sepia(98%) saturate(2206%) hue-rotate(213deg) brightness(98%) contrast(97%);">
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                <span id="statNutrisiAbMix" class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $metrics['nutrisi_value'] ?? '0' }}</span>
                <span id="statNutrisiUnit" class="text-xs text-gray-500">{{ $metrics['nutrisi_unit'] ?? 'Unit' }}</span>
            </div>
            <p id="statNutrisiSub" class="text-xs text-gray-500 mt-3">{{ $metrics['nutrisi_sub'] ?? 'Memuat stok nutrisi...' }}</p>
        </div>
    </div>

    {{-- Section: Tanaman Sedang Ditanam --}}
    <div id="sectionPlanted" class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Tanaman Sedang Ditanam</h2>
                <p class="text-xs text-gray-500">Status fase vegetatif dan estimasi masa panen per batch dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">plants</code>.</p>
            </div>
            <span id="plantedCountBadge" class="text-xs font-semibold text-gray-500">{{ count($plants ?? []) }} Batch Aktif</span>
        </div>

        {{-- Plant Cards Grid --}}
        <div id="plantedCardsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($plants ?? [] as $index => $plant)
                @php
                    $img = $bedImages[$index % count($bedImages)];
                    $days = $plant['hari_menuju_panen'] ?? null;
                    if ($days !== null && $days < 0) {
                        $daysText = 'Siap panen (' . abs((int) $days) . ' hr lewat)';
                        $daysCls = 'text-emerald-700 font-bold';
                    } elseif ($days === 0) {
                        $daysText = 'Panen hari ini';
                        $daysCls = 'text-emerald-700 font-bold';
                    } elseif ($days !== null) {
                        $daysText = $days . ' hari lagi';
                        $daysCls = $days <= 7 ? 'text-emerald-600 font-bold' : 'text-gray-500 font-medium';
                    } else {
                        $daysText = '-';
                        $daysCls = 'text-gray-400';
                    }
                @endphp
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group flex flex-col">
                    <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                        <img src="/images/beds/{{ $img }}" alt="{{ $plant['kode_tanaman'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-[#137a46] text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">
                            HST {{ $plant['umur_hari'] ?? 0 }}
                        </span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-bold text-gray-900 text-sm">{{ $plant['kode_tanaman'] }}</h3>
                                <span class="text-xs {{ $daysCls }}">{{ $daysText }}</span>
                            </div>
                            <p class="text-xs font-medium text-gray-700 mt-1">{{ $plant['nama'] }}{{ !empty($plant['varietas']) ? ' · ' . $plant['varietas'] : '' }}</p>
                            <p class="text-[11px] text-gray-500 mt-1 line-clamp-2">{{ $plant['keterangan'] ?? '-' }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-100">
                            <span class="text-[11px] text-gray-400 capitalize">{{ str_replace('_', ' ', (string) ($plant['status'] ?? 'aktif')) }}</span>
                            <div class="inline-flex items-center gap-2">
                                <button type="button"
                                        data-edit-plant-id="{{ $plant['id'] }}"
                                        class="btn-edit-plant text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 cursor-pointer">
                                    Edit
                                </button>
                                <button type="button"
                                        data-delete-plant-id="{{ $plant['id'] }}"
                                        class="btn-delete-plant text-[11px] font-semibold text-rose-600 hover:text-rose-800 cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl border border-gray-100 p-6 text-center text-xs text-gray-400">
                    Belum ada data tanaman yang sedang ditanam di database.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Section: Daftar Inventaris Barang --}}
    <div id="sectionInventory" class="bg-white rounded-xl border border-gray-100 shadow-xs p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Daftar Inventaris Barang</h3>
                <p class="text-xs text-gray-500 mt-0.5">Data langsung dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">inventory_items</code></p>
            </div>
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text"
                       id="searchStockInput"
                       placeholder="Cari nama barang atau jenis..."
                       class="bg-[#f0f4f8] text-xs pl-9 pr-4 py-2 rounded-lg text-gray-700 placeholder-gray-400 outline-none w-full focus:ring-1 focus:ring-emerald-500">
            </div>
        </div>

        <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
            <table class="w-full text-left border-collapse min-w-[560px]">
                <thead>
                    <tr class="border-b border-gray-100 text-[11.5px] font-medium text-gray-400">
                        <th class="py-3 font-medium">Nama Barang</th>
                        <th class="py-3 font-medium">Kategori</th>
                        <th class="py-3 font-medium">Jumlah Stok</th>
                        <th class="py-3 font-medium">Status</th>
                        <th class="py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody" class="divide-y divide-gray-50 text-xs">
                    @forelse($stocks ?? [] as $item)
                        @php
                            $qty = (float) ($item['jumlah_stok'] ?? 0);
                            $minQty = (float) ($item['stok_minimum'] ?? 0);
                            $jenis = (string) ($item['jenis'] ?? 'lainnya');
                            $isOut = $qty <= 0;
                            $isLow = !$isOut && (!empty($item['is_low_stock']) || $qty <= $minQty);
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition group">
                            <td class="py-3.5">
                                <p class="font-bold text-gray-900 text-xs">{{ $item['nama_barang'] }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $item['keterangan'] ?: 'Min. ' . $formatQty($minQty) . ' ' . $item['satuan'] }}</p>
                            </td>
                            <td class="py-3.5 capitalize font-semibold text-gray-700">{{ $jenis }}</td>
                            <td class="py-3.5 font-bold text-gray-900">{{ $formatQty($qty) }} <span class="font-normal text-gray-500 text-[11px]">{{ $item['satuan'] }}</span></td>
                            <td class="py-3.5">
                                @if($isOut)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Habis</span>
                                @elseif($isLow)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menipis</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Tersedia</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" data-edit-id="{{ $item['id'] }}" class="btn-edit-stock text-xs text-emerald-700 hover:text-emerald-900 font-medium px-2 py-1 rounded hover:bg-emerald-50 transition cursor-pointer">Edit</button>
                                    <button type="button" data-delete-id="{{ $item['id'] }}" class="btn-delete-stock text-xs text-gray-400 hover:text-rose-600 px-1 py-1 rounded hover:bg-rose-50 transition cursor-pointer" title="Hapus Barang">
                                        <img src="/icons/icon_trash.png" alt="Hapus" class="w-3.5 h-3.5 object-contain opacity-50 hover:opacity-100 pointer-events-none">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400 text-xs">Belum ada data inventaris barang di database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section: Riwayat Catatan Terakhir --}}
    <div id="sectionMovements" class="bg-white rounded-xl border border-gray-100 shadow-xs p-4 sm:p-6 mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Riwayat Catatan Terakhir</h3>
                <p class="text-xs text-gray-500 mt-0.5">Log mutasi inventaris dari tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">stock_movements</code>.</p>
            </div>
            <button type="button" id="btnExportMovementsCsv" class="self-start sm:self-auto text-xs font-semibold text-gray-500 hover:text-gray-900 transition cursor-pointer">
                Export CSV
            </button>
        </div>

        <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
            <table class="w-full text-left border-collapse min-w-[560px]">
                <thead>
                    <tr class="border-b border-gray-100 text-[11.5px] font-medium text-gray-400">
                        <th class="py-3 font-medium">Waktu</th>
                        <th class="py-3 font-medium">Petugas</th>
                        <th class="py-3 font-medium">Barang</th>
                        <th class="py-3 font-medium">Perubahan</th>
                        <th class="py-3 font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="recentLogsBody" class="divide-y divide-gray-50 text-xs">
                    @forelse($movements ?? [] as $mv)
                        @php
                            $isMasuk = ($mv['jenis_pergerakan'] ?? '') === 'masuk';
                            $sign = $isMasuk ? '+' : '-';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 text-gray-500 whitespace-nowrap text-xs">{{ !empty($mv['tanggal']) ? \Carbon\Carbon::parse($mv['tanggal'])->format('d M Y, H:i') : '-' }}</td>
                            <td class="py-3.5 font-semibold text-gray-800 text-xs">{{ $mv['recorded_by_nama'] ?? 'Sistem' }}</td>
                            <td class="py-3.5 text-gray-700 text-xs">{{ $mv['nama_barang'] ?? ('Item #' . ($mv['inventory_item_id'] ?? '-')) }}</td>
                            <td class="py-3.5 {{ $isMasuk ? 'text-emerald-600' : 'text-red-600' }} font-bold text-xs">{{ $sign }}{{ $formatQty($mv['jumlah'] ?? 0) }} {{ $mv['satuan'] ?? '' }}</td>
                            <td class="py-3.5 text-gray-500 text-xs">{{ $mv['keterangan'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada riwayat mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Backdrop Gelap Drawer --}}
<div id="stockDrawerBackdrop"
     class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-40 cursor-pointer"
     aria-hidden="true"></div>

{{-- Panel Drawer Samping Kanan (Form Tambah / Edit Stok & Tanaman) --}}
<div id="stockDrawerPanel"
     class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] bg-white shadow-2xl flex flex-col justify-between"
     role="dialog"
     aria-modal="true"
     aria-labelledby="stockDrawerTitle">

    {{-- Drawer Header --}}
    <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <img id="stockDrawerHeaderIcon" src="/icons/icon_add_stock.png" alt="Tambah Stok" class="w-5 h-5 object-contain">
            </div>
            <div>
                <h2 id="stockDrawerTitle" class="text-base font-bold text-gray-900 tracking-tight">
                    Tambah Stok Barang
                </h2>
                <p class="text-[11px] text-gray-400 mt-0.5">Terhubung langsung ke API Backend (:8000)</p>
            </div>
        </div>

        {{-- Tombol Tutup (X) --}}
        <button type="button"
                id="btnCloseStockDrawer"
                onclick="closeStockDrawer()"
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition cursor-pointer"
                aria-label="Tutup panel">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    {{-- Drawer Body: Form Inputs --}}
    <form id="stockForm" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" onsubmit="return false;">
        <input type="hidden" id="stockFormMode" value="tambah">
        <input type="hidden" id="editStockId" value="">

        <div id="stockDrawerError" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium rounded-xl px-3.5 py-2.5"></div>

        {{-- Radio Group: Kategori --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2">
                Pilih Kategori <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="categoryRadioContainer">
                <label class="category-radio-card cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5 border-amber-500 bg-amber-50/70 text-amber-900 ring-2 ring-amber-400/30 shadow-xs" data-category="bibit">
                    <input type="radio" name="kategoriStok" value="bibit" class="sr-only" checked>
                    <div class="w-7 h-7 rounded-lg bg-yellow-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_seed.png" alt="Bibit" class="w-4 h-4 object-contain" style="filter: invert(78%) sepia(61%) saturate(1478%) hue-rotate(359deg) brightness(98%) contrast(92%);">
                    </div>
                    <span class="text-[11px] font-bold leading-tight">Bibit</span>
                </label>

                <label class="category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5" data-category="nutrisi">
                    <input type="radio" name="kategoriStok" value="nutrisi" class="sr-only">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_pharma.png" alt="Nutrisi" class="w-4 h-4 object-contain" style="filter: invert(31%) sepia(98%) saturate(2206%) hue-rotate(213deg) brightness(98%) contrast(97%);">
                    </div>
                    <span class="text-[11px] font-bold leading-tight">Nutrisi</span>
                </label>

                <label class="category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5" data-category="lainnya">
                    <input type="radio" name="kategoriStok" value="lainnya" class="sr-only">
                    <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_box.png" alt="Lainnya" class="w-4 h-4 object-contain opacity-70">
                    </div>
                    <span class="text-[11px] font-bold leading-tight">Media/Lain</span>
                </label>

                <label class="category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5" data-category="tanaman">
                    <input type="radio" name="kategoriStok" value="tanaman" class="sr-only">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_plant.png" alt="Tanaman" class="w-4 h-4 object-contain" style="filter: invert(48%) sepia(79%) saturate(452%) hue-rotate(86deg) brightness(98%) contrast(92%);">
                    </div>
                    <span class="text-[11px] font-bold leading-tight">Tanaman</span>
                </label>
            </div>
        </div>

        {{-- Input Nama Barang / Nama Tanaman --}}
        <div>
            <label for="inputNama" id="labelNama" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Nama Barang <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <img id="inputNamaIcon" src="/icons/icon_seed.png" alt="Kategori" class="w-4 h-4 object-contain">
                </span>
                <input type="text"
                       id="inputNama"
                       required
                       placeholder="Contoh: Bibit Selada Butterhead"
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-10 pr-4 py-2.5 rounded-xl transition-all outline-none">
            </div>
        </div>

        {{-- Fields Khusus Inventaris (bibit / nutrisi / lainnya) --}}
        <div id="inventoryFieldsGroup" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="inputJumlahStok" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Jumlah Stok <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           id="inputJumlahStok"
                           min="0"
                           step="any"
                           placeholder="0"
                           class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                </div>

                <div>
                    <label for="inputSatuan" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Satuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="inputSatuan"
                           list="satuanSuggestions"
                           placeholder="Contoh: biji / pack / kg"
                           class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                    <datalist id="satuanSuggestions"></datalist>
                </div>
            </div>

            <div>
                <label for="inputStokMinimum" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Batas Stok Minimum
                </label>
                <input type="number"
                       id="inputStokMinimum"
                       min="0"
                       step="any"
                       placeholder="0"
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                <p class="text-[11px] text-gray-400 mt-1">Barang dengan stok &le; stok minimum otomatis berstatus "Menipis".</p>
            </div>
        </div>

        {{-- Fields Khusus Batch Tanaman (tanaman) --}}
        <div id="plantFieldsGroup" class="hidden space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="inputVarietas" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Varietas
                    </label>
                    <input type="text"
                           id="inputVarietas"
                           placeholder="Contoh: Grand Rapids"
                           class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                </div>
                <div>
                    <label for="inputKodeTanaman" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Kode Tanaman
                    </label>
                    <input type="text"
                           id="inputKodeTanaman"
                           placeholder="Otomatis jika dikosongkan"
                           class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="inputTanggal" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Tanggal Tanam <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           id="inputTanggal"
                           class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                </div>
                <div>
                    <label for="inputTanggalPanen" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Estimasi Panen
                    </label>
                    <input type="date"
                           id="inputTanggalPanen"
                           class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                </div>
            </div>

            <div>
                <label for="inputPlantStatus" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Status Fase
                </label>
                <select id="inputPlantStatus"
                        class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                    <option value="pembibitan">Pembibitan</option>
                    <option value="pertumbuhan">Pertumbuhan</option>
                    <option value="siap_panen">Siap Panen</option>
                    <option value="dipanen">Dipanen</option>
                    <option value="mati">Mati</option>
                </select>
            </div>
        </div>

        {{-- Keterangan (dipakai baik untuk inventaris maupun tanaman) --}}
        <div>
            <label for="inputKeterangan" id="labelKeterangan" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Keterangan
            </label>
            <textarea id="inputKeterangan"
                      rows="3"
                      placeholder="Contoh: Greenhouse A - Rak 1 (120 lubang NFT) atau catatan stok"
                      class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none resize-none"></textarea>
        </div>
    </form>

    {{-- Drawer Footer --}}
    <div class="p-4 sm:p-6 border-t border-gray-100 flex items-center justify-end gap-3 bg-white">
        <button type="button"
                id="btnBatalStock"
                onclick="closeStockDrawer()"
                class="px-5 py-2.5 rounded-xl text-[13px] font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition cursor-pointer">
            Batal
        </button>
        <button type="button"
                id="btnSimpanStock"
                style="background-color: #0e5f38;"
                class="px-5 py-2.5 rounded-xl text-[13px] font-semibold text-white hover:opacity-95 shadow-sm transition active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
            <span id="btnSimpanStockText">Tambah Stok</span>
        </button>
    </div>
</div>

{{-- Toast Notification --}}
<div id="stockToastNotification"
     class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-auto sm:right-6 max-w-sm mx-auto z-50 transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-3 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-lg pointer-events-none">
    <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <span id="stockToastMessage">Operasi berhasil</span>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    const apiHost = window.location.hostname === '127.0.0.1' ? '127.0.0.1' : 'localhost';
    const BE_API_URL = `http://${apiHost}:8000/api/v1`;
    const BED_IMAGES = ['bed_a01.jpg', 'bed_a02.jpg', 'bed_b01.jpg', 'bed_b02.jpg'];

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

    function initStockPage() {
        const categoryConfig = {
            bibit: {
                categoryName: 'Bibit',
                labelNama: 'Nama Barang Bibit',
                iconFile: '/icons/icon_seed.png',
                iconFilter: 'invert(78%) sepia(61%) saturate(1478%) hue-rotate(359deg) brightness(98%) contrast(92%)',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5 border-amber-500 bg-amber-50/70 text-amber-900 ring-2 ring-amber-400/30 shadow-xs',
                badgeClass: 'bg-amber-50 text-amber-700 border-amber-200/60',
                satuanOptions: ['biji', 'pack', 'gram', 'kg', 'kaleng'],
            },
            nutrisi: {
                categoryName: 'Nutrisi',
                labelNama: 'Nama Barang Nutrisi',
                iconFile: '/icons/icon_pharma.png',
                iconFilter: 'invert(31%) sepia(98%) saturate(2206%) hue-rotate(213deg) brightness(98%) contrast(97%)',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5 border-blue-600 bg-blue-50/70 text-blue-900 ring-2 ring-blue-500/30 shadow-xs',
                badgeClass: 'bg-blue-50 text-blue-700 border-blue-200/60',
                satuanOptions: ['pack', 'liter', 'kg', 'gram', 'ml', 'botol'],
            },
            lainnya: {
                categoryName: 'Lainnya',
                labelNama: 'Nama Media / Barang Lainnya',
                iconFile: '/icons/icon_box.png',
                iconFilter: 'none',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5 border-slate-600 bg-slate-100 text-slate-900 ring-2 ring-slate-400/30 shadow-xs',
                badgeClass: 'bg-gray-100 text-gray-700 border-gray-200/60',
                satuanOptions: ['slab', 'pcs', 'pack', 'unit', 'meter'],
            },
            tanaman: {
                categoryName: 'Tanaman',
                labelNama: 'Nama Tanaman',
                iconFile: '/icons/icon_plant.png',
                iconFilter: 'invert(48%) sepia(79%) saturate(452%) hue-rotate(86deg) brightness(98%) contrast(92%)',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5 border-emerald-600 bg-emerald-50/70 text-emerald-900 ring-2 ring-emerald-500/30 shadow-xs',
                badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                satuanOptions: [],
            },
        };

        let overview = @json($overview ?? []);
        let plants = @json($plants ?? []);
        let stocks = @json($stocks ?? []);
        let movements = @json($movements ?? []);
        let searchQuery = '';

        // DOM Elements
        const sectionPlanted = document.getElementById('sectionPlanted');
        const sectionInventory = document.getElementById('sectionInventory');
        const sectionMovements = document.getElementById('sectionMovements');
        const plantedCardsGrid = document.getElementById('plantedCardsGrid');
        const plantedCountBadge = document.getElementById('plantedCountBadge');
        const inventoryTableBody = document.getElementById('inventoryTableBody');
        const recentLogsBody = document.getElementById('recentLogsBody');
        const searchStockInput = document.getElementById('searchStockInput');

        const statTanamanAktif = document.getElementById('statTanamanAktif');
        const statTanamanUnit = document.getElementById('statTanamanUnit');
        const statTanamanSub = document.getElementById('statTanamanSub');
        const statTotalBenih = document.getElementById('statTotalBenih');
        const statBenihUnit = document.getElementById('statBenihUnit');
        const statBenihSub = document.getElementById('statBenihSub');
        const statNutrisiAbMix = document.getElementById('statNutrisiAbMix');
        const statNutrisiUnit = document.getElementById('statNutrisiUnit');
        const statNutrisiSub = document.getElementById('statNutrisiSub');

        // Drawer Elements
        const drawerBackdrop = document.getElementById('stockDrawerBackdrop');
        const drawerPanel = document.getElementById('stockDrawerPanel');
        const btnTambahStok = document.getElementById('btnTambahStok');
        const btnCloseDrawer = document.getElementById('btnCloseStockDrawer');
        const btnBatal = document.getElementById('btnBatalStock');
        const btnSimpan = document.getElementById('btnSimpanStock');
        const btnSimpanText = document.getElementById('btnSimpanStockText');
        const drawerTitle = document.getElementById('stockDrawerTitle');
        const drawerError = document.getElementById('stockDrawerError');

        const formMode = document.getElementById('stockFormMode');
        const editStockId = document.getElementById('editStockId');
        const categoryCards = document.querySelectorAll('.category-radio-card');
        const inputNamaIcon = document.getElementById('inputNamaIcon');
        const labelNama = document.getElementById('labelNama');
        const inputNama = document.getElementById('inputNama');

        const inventoryFieldsGroup = document.getElementById('inventoryFieldsGroup');
        const plantFieldsGroup = document.getElementById('plantFieldsGroup');
        const inputJumlahStok = document.getElementById('inputJumlahStok');
        const inputSatuan = document.getElementById('inputSatuan');
        const satuanSuggestions = document.getElementById('satuanSuggestions');
        const inputStokMinimum = document.getElementById('inputStokMinimum');

        const inputVarietas = document.getElementById('inputVarietas');
        const inputKodeTanaman = document.getElementById('inputKodeTanaman');
        const inputTanggal = document.getElementById('inputTanggal');
        const inputTanggalPanen = document.getElementById('inputTanggalPanen');
        const inputPlantStatus = document.getElementById('inputPlantStatus');
        const inputKeterangan = document.getElementById('inputKeterangan');

        const toast = document.getElementById('stockToastNotification');
        const toastMsg = document.getElementById('stockToastMessage');
        let toastTimeout;

        function showToast(message) {
            if (!toast || !toastMsg) return;
            clearTimeout(toastTimeout);
            toastMsg.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            toastTimeout = setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        function showDrawerError(message) {
            if (!drawerError) return;
            if (!message) {
                drawerError.classList.add('hidden');
                drawerError.textContent = '';
                return;
            }
            drawerError.textContent = message;
            drawerError.classList.remove('hidden');
        }

        function setSubmitLoading(loading, mode) {
            if (!btnSimpan || !btnSimpanText) return;
            btnSimpan.disabled = loading;
            btnSimpanText.textContent = loading ? 'Menyimpan...' : (mode === 'edit' ? 'Simpan Perubahan' : 'Tambah Data');
        }

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatQty(val) {
            const num = Number(val);
            if (!Number.isFinite(num)) return '0';
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
        }

        function getTodayDateString() {
            const today = new Date(Date.now() - new Date().getTimezoneOffset() * 60000);
            return today.toISOString().split('T')[0];
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

        function updateCategoryFields(catKey, preserveValues = false) {
            const config = categoryConfig[catKey] || categoryConfig.bibit;
            const isPlant = catKey === 'tanaman';

            categoryCards.forEach(card => {
                const isSelected = card.dataset.category === catKey;
                const radio = card.querySelector('input[type="radio"]');
                if (radio) radio.checked = isSelected;
                card.className = isSelected
                    ? config.activeClass
                    : 'category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-1.5';
            });

            if (inputNamaIcon) {
                inputNamaIcon.src = config.iconFile;
                inputNamaIcon.style.filter = config.iconFilter;
            }
            if (labelNama) {
                labelNama.innerHTML = `${config.labelNama} <span class="text-rose-500">*</span>`;
            }

            if (inventoryFieldsGroup) inventoryFieldsGroup.classList.toggle('hidden', isPlant);
            if (plantFieldsGroup) plantFieldsGroup.classList.toggle('hidden', !isPlant);

            if (!isPlant && satuanSuggestions) {
                satuanSuggestions.innerHTML = config.satuanOptions
                    .map(u => `<option value="${escapeHtml(u)}"></option>`)
                    .join('');
                if (!preserveValues && inputSatuan && !inputSatuan.value && config.satuanOptions.length > 0) {
                    inputSatuan.value = config.satuanOptions[0];
                }
            }
        }

        function updateMetricSummary() {
            let totalLubang = 0;
            (plants || []).forEach(p => {
                const m = String(p.keterangan || '').match(/(\d+)\s*lubang/i);
                if (m) totalLubang += Number(m[1]);
            });
            const varietasCount = Object.keys(overview?.by_varietas || {}).length;

            if (statTanamanAktif) statTanamanAktif.textContent = totalLubang > 0 ? formatQty(totalLubang) : String(plants.length);
            if (statTanamanUnit) statTanamanUnit.textContent = totalLubang > 0 ? 'Lubang Tanam' : 'Batch Aktif';
            if (statTanamanSub) {
                statTanamanSub.textContent = `${plants.length} Batch aktif${varietasCount > 0 ? ` (${varietasCount} varietas)` : ''}`;
            }

            const bibitItems = (stocks || []).filter(s => s.jenis === 'bibit');
            const totalBibit = bibitItems.reduce((acc, s) => acc + (Number(s.jumlah_stok) || 0), 0);
            const lowBibit = bibitItems.filter(s => s.is_low_stock || Number(s.jumlah_stok) <= Number(s.stok_minimum || 0)).length;
            if (statTotalBenih) statTotalBenih.textContent = formatQty(totalBibit);
            if (statBenihUnit) statBenihUnit.textContent = bibitItems[0]?.satuan || 'biji';
            if (statBenihSub) {
                statBenihSub.textContent = `${bibitItems.length} jenis bibit • ${lowBibit > 0 ? `${lowBibit} stok menipis` : 'stok aman'}`;
            }

            const nutrisiItems = (stocks || []).filter(s => s.jenis === 'nutrisi').sort((a, b) => {
                const aAb = String(a.nama_barang || '').toLowerCase().includes('ab mix') ? 0 : 1;
                const bAb = String(b.nama_barang || '').toLowerCase().includes('ab mix') ? 0 : 1;
                return aAb - bAb;
            });
            const primaryNut = nutrisiItems[0] || null;
            const secondaryNut = nutrisiItems[1] || null;
            const lainnyaCount = (stocks || []).filter(s => s.jenis === 'lainnya').length;

            if (statNutrisiAbMix) statNutrisiAbMix.textContent = primaryNut ? formatQty(primaryNut.jumlah_stok) : '0';
            if (statNutrisiUnit) statNutrisiUnit.textContent = primaryNut ? `${primaryNut.satuan} ${primaryNut.nama_barang}` : 'Unit';
            const subParts = [];
            if (secondaryNut) {
                const shortName = String(secondaryNut.nama_barang || 'Nutrisi').split('(')[0].trim();
                subParts.push(`${shortName}: ${formatQty(secondaryNut.jumlah_stok)} ${secondaryNut.satuan}`);
            }
            subParts.push(`${lainnyaCount} item media/lainnya`);
            if (statNutrisiSub) statNutrisiSub.textContent = subParts.join(' • ');
        }

        function renderPlantedCards() {
            if (plantedCountBadge) plantedCountBadge.textContent = `${plants.length} Batch Aktif`;
            if (!plantedCardsGrid) return;

            if (!Array.isArray(plants) || plants.length === 0) {
                plantedCardsGrid.innerHTML = '<div class="col-span-full bg-white rounded-xl border border-gray-100 p-6 text-center text-xs text-gray-400">Belum ada data tanaman yang sedang ditanam di database.</div>';
                return;
            }

            plantedCardsGrid.innerHTML = plants.map((plant, idx) => {
                const img = BED_IMAGES[idx % BED_IMAGES.length];
                const d = plant.hari_menuju_panen;
                let daysText = '-';
                let daysCls = 'text-gray-400';
                if (d !== null && d !== undefined) {
                    const num = Number(d);
                    if (num < 0) {
                        daysText = `Siap panen (${Math.abs(num)} hr lewat)`;
                        daysCls = 'text-emerald-700 font-bold';
                    } else if (num === 0) {
                        daysText = 'Panen hari ini';
                        daysCls = 'text-emerald-700 font-bold';
                    } else {
                        daysText = `${num} hari lagi`;
                        daysCls = num <= 7 ? 'text-emerald-600 font-bold' : 'text-gray-500 font-medium';
                    }
                }
                const statusLabel = String(plant.status || 'aktif').replace(/_/g, ' ');
                return `
                    <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group flex flex-col">
                        <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                            <img src="/images/beds/${img}" alt="${escapeHtml(plant.kode_tanaman)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <span class="absolute top-2.5 right-2.5 bg-[#137a46] text-white text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">
                                HST ${escapeHtml(plant.umur_hari ?? 0)}
                            </span>
                        </div>
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-gray-900 text-sm">${escapeHtml(plant.kode_tanaman)}</h3>
                                    <span class="text-xs ${daysCls}">${escapeHtml(daysText)}</span>
                                </div>
                                <p class="text-xs font-medium text-gray-700 mt-1">${escapeHtml(plant.nama)}${plant.varietas ? ' · ' + escapeHtml(plant.varietas) : ''}</p>
                                <p class="text-[11px] text-gray-500 mt-1 line-clamp-2">${escapeHtml(plant.keterangan || '-')}</p>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-100">
                                <span class="text-[11px] text-gray-400 capitalize">${escapeHtml(statusLabel)}</span>
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" data-edit-plant-id="${plant.id}" class="btn-edit-plant text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 cursor-pointer">Edit</button>
                                    <button type="button" data-delete-plant-id="${plant.id}" class="btn-delete-plant text-[11px] font-semibold text-rose-600 hover:text-rose-800 cursor-pointer">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function getFilteredStocks() {
            const q = searchQuery.toLowerCase().trim();
            if (!q) return stocks;
            return stocks.filter(s =>
                String(s.nama_barang || '').toLowerCase().includes(q) ||
                String(s.jenis || '').toLowerCase().includes(q) ||
                String(s.satuan || '').toLowerCase().includes(q) ||
                String(s.keterangan || '').toLowerCase().includes(q)
            );
        }

        function renderInventoryTable() {
            const filtered = getFilteredStocks();
            if (!inventoryTableBody) return;

            if (filtered.length === 0) {
                inventoryTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 text-xs">
                            ${searchQuery.trim() ? `Tidak ada barang yang cocok dengan "${escapeHtml(searchQuery)}".` : 'Belum ada data inventaris barang di database.'}
                        </td>
                    </tr>`;
                return;
            }

            inventoryTableBody.innerHTML = filtered.map(item => {
                const qty = Number(item.jumlah_stok || 0);
                const minQty = Number(item.stok_minimum || 0);
                const isOut = qty <= 0;
                const isLow = !isOut && (Boolean(item.is_low_stock) || qty <= minQty);
                const catConf = categoryConfig[item.jenis] || categoryConfig.lainnya;

                let statusBadge = '<span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Tersedia</span>';
                if (isOut) {
                    statusBadge = '<span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Habis</span>';
                } else if (isLow) {
                    statusBadge = '<span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menipis</span>';
                }

                return `
                    <tr class="hover:bg-gray-50/70 transition group">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">${escapeHtml(item.nama_barang)}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">${escapeHtml(item.keterangan || `Min. ${formatQty(minQty)} ${item.satuan}`)}</p>
                        </td>
                        <td class="py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold border capitalize ${catConf.badgeClass}">
                                <img src="${catConf.iconFile}" alt="${escapeHtml(item.jenis)}" class="w-3.5 h-3.5 object-contain" style="filter: ${catConf.iconFilter};">
                                ${escapeHtml(item.jenis)}
                            </span>
                        </td>
                        <td class="py-3.5 font-bold text-gray-900">${escapeHtml(formatQty(qty))} <span class="font-normal text-gray-500 text-[11px]">${escapeHtml(item.satuan)}</span></td>
                        <td class="py-3.5">${statusBadge}</td>
                        <td class="py-3.5 text-right">
                            <div class="inline-flex items-center gap-2">
                                <button type="button" data-edit-id="${item.id}" class="btn-edit-stock text-xs text-emerald-700 hover:text-emerald-900 font-medium px-2 py-1 rounded hover:bg-emerald-50 transition cursor-pointer">Edit</button>
                                <button type="button" data-delete-id="${item.id}" class="btn-delete-stock text-xs text-gray-400 hover:text-rose-600 px-1 py-1 rounded hover:bg-rose-50 transition cursor-pointer" title="Hapus Barang">
                                    <img src="/icons/icon_trash.png" alt="Hapus" class="w-3.5 h-3.5 object-contain opacity-50 hover:opacity-100 pointer-events-none">
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        function renderRecentLogs() {
            if (!recentLogsBody) return;
            if (!Array.isArray(movements) || movements.length === 0) {
                recentLogsBody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada riwayat mutasi stok.</td></tr>';
                return;
            }

            recentLogsBody.innerHTML = movements.map(mv => {
                const isMasuk = mv.jenis_pergerakan === 'masuk';
                const sign = isMasuk ? '+' : '-';
                const cls = isMasuk ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold';
                return `
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 text-gray-500 whitespace-nowrap text-xs">${escapeHtml(formatDateTimeId(mv.tanggal))}</td>
                        <td class="py-3.5 font-semibold text-gray-800 text-xs">${escapeHtml(mv.recorded_by_nama || 'Sistem')}</td>
                        <td class="py-3.5 text-gray-700 text-xs">${escapeHtml(mv.nama_barang || `Item #${mv.inventory_item_id}`)}</td>
                        <td class="py-3.5 ${cls} text-xs">${sign}${escapeHtml(formatQty(mv.jumlah))} ${escapeHtml(mv.satuan || '')}</td>
                        <td class="py-3.5 text-gray-500 text-xs">${escapeHtml(mv.keterangan || '-')}</td>
                    </tr>`;
            }).join('');
        }

        async function syncStockPageWithApi() {
            try {
                const [ovRes, stRes, mvRes] = await Promise.all([
                    apiRequest('/stocks/planted-overview', { method: 'GET' }),
                    apiRequest('/stocks?per_page=100', { method: 'GET' }),
                    apiRequest('/stocks/movements?limit=30', { method: 'GET' }),
                ]);

                if (ovRes?.ok && ovRes.json?.data) {
                    overview = ovRes.json.data;
                    plants = Array.isArray(overview.plants) ? overview.plants : [];
                }
                if (stRes?.ok && Array.isArray(stRes.json?.data)) {
                    stocks = stRes.json.data;
                }
                if (mvRes?.ok && Array.isArray(mvRes.json?.data)) {
                    movements = mvRes.json.data;
                }

                renderPlantedCards();
                renderInventoryTable();
                renderRecentLogs();
                updateMetricSummary();
            } catch (_) {
                // Pertahankan hasil SSR apabila fetch awal gagal.
            }
        }

        function setCategoryLockForEdit(locked, activeCat) {
            categoryCards.forEach(card => {
                const isPlantCard = card.dataset.category === 'tanaman';
                const shouldDisable = locked && ((activeCat === 'tanaman' && !isPlantCard) || (activeCat !== 'tanaman' && isPlantCard));
                card.classList.toggle('opacity-40', shouldDisable);
                card.classList.toggle('pointer-events-none', shouldDisable);
            });
        }

        function openStockDrawer(mode, record = null, forcedCategory = null) {
            formMode.value = mode;
            showDrawerError('');
            setSubmitLoading(false, mode);
            const todayStr = getTodayDateString();

            if (mode === 'tambah') {
                const initCat = forcedCategory || 'bibit';
                drawerTitle.textContent = 'Tambah Stok Barang';
                editStockId.value = '';
                inputNama.value = '';
                inputJumlahStok.value = '';
                inputSatuan.value = '';
                inputStokMinimum.value = '0';
                inputVarietas.value = '';
                inputKodeTanaman.value = '';
                inputTanggal.value = todayStr;
                inputTanggalPanen.value = '';
                inputPlantStatus.value = 'pembibitan';
                inputKeterangan.value = '';
                setCategoryLockForEdit(false, initCat);
                updateCategoryFields(initCat, false);
            } else {
                const catKey = forcedCategory || record?.jenis || 'bibit';
                editStockId.value = record.id;
                setCategoryLockForEdit(true, catKey);
                updateCategoryFields(catKey, false);

                if (catKey === 'tanaman') {
                    drawerTitle.textContent = `Edit Tanaman ${record.kode_tanaman || ''}`;
                    inputNama.value = record.nama || '';
                    inputVarietas.value = record.varietas || '';
                    inputKodeTanaman.value = record.kode_tanaman || '';
                    inputTanggal.value = record.tanggal_tanam || todayStr;
                    inputTanggalPanen.value = record.tanggal_panen || '';
                    inputPlantStatus.value = record.status || 'pertumbuhan';
                    inputKeterangan.value = record.keterangan || '';
                } else {
                    drawerTitle.textContent = 'Edit Stok Barang';
                    inputNama.value = record.nama_barang || '';
                    inputJumlahStok.value = record.jumlah_stok ?? 0;
                    inputSatuan.value = record.satuan || '';
                    inputStokMinimum.value = record.stok_minimum ?? 0;
                    inputKeterangan.value = record.keterangan || '';
                }
            }

            drawerBackdrop.classList.add('open');
            drawerPanel.classList.add('open');
            setTimeout(() => inputNama.focus(), 250);
        }

        function closeStockDrawer() {
            drawerBackdrop.classList.remove('open');
            drawerPanel.classList.remove('open');
            showDrawerError('');
        }

        window.openStockDrawer = openStockDrawer;
        window.closeStockDrawer = closeStockDrawer;

        // Filter Tabs Bar
        document.querySelectorAll('#stockFilterTabs .filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.dataset.filter || 'all';
                document.querySelectorAll('#stockFilterTabs .filter-tab').forEach(t => {
                    const active = t === tab;
                    t.className = active
                        ? 'filter-tab bg-[#e6f0fa] text-[#1a5b8c] font-semibold text-xs px-4 py-2 rounded-lg transition shrink-0 cursor-pointer'
                        : 'filter-tab text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer';
                });

                if (sectionPlanted) sectionPlanted.classList.toggle('hidden', filter !== 'all' && filter !== 'planted');
                if (sectionInventory) sectionInventory.classList.toggle('hidden', filter !== 'all' && filter !== 'inventory');
                if (sectionMovements) sectionMovements.classList.toggle('hidden', filter !== 'all' && filter !== 'movements');
            });
        });

        categoryCards.forEach(card => {
            card.addEventListener('click', function() {
                const cat = this.dataset.category;
                if (cat) {
                    updateCategoryFields(cat, true);
                }
            });
        });

        // Edit / Delete Inventory Table
        if (inventoryTableBody) {
            inventoryTableBody.addEventListener('click', async function(e) {
                const editBtn = e.target.closest('.btn-edit-stock');
                if (editBtn) {
                    const id = parseInt(editBtn.dataset.editId, 10);
                    const item = stocks.find(s => Number(s.id) === id);
                    if (item) openStockDrawer('edit', item, item.jenis);
                    return;
                }

                const delBtn = e.target.closest('.btn-delete-stock');
                if (delBtn) {
                    const id = parseInt(delBtn.dataset.deleteId, 10);
                    const item = stocks.find(s => Number(s.id) === id);
                    if (!item || !confirm(`Apakah Anda yakin ingin menghapus "${item.nama_barang}" dari database?`)) return;

                    try {
                        const res = await apiRequest(`/stocks/${id}`, { method: 'DELETE' });
                        if (!res) return;
                        if (res.ok) {
                            await syncStockPageWithApi();
                            showToast(`"${item.nama_barang}" berhasil dihapus.`);
                        } else {
                            showToast(extractApiError(res.json, 'Gagal menghapus barang inventaris.'));
                        }
                    } catch (_) {
                        showToast('Gagal terhubung ke Backend API (port 8000).');
                    }
                }
            });
        }

        // Edit / Delete Plant Cards
        if (plantedCardsGrid) {
            plantedCardsGrid.addEventListener('click', async function(e) {
                const editBtn = e.target.closest('.btn-edit-plant');
                if (editBtn) {
                    const id = parseInt(editBtn.dataset.editPlantId, 10);
                    const plant = plants.find(p => Number(p.id) === id);
                    if (plant) openStockDrawer('edit', plant, 'tanaman');
                    return;
                }

                const delBtn = e.target.closest('.btn-delete-plant');
                if (delBtn) {
                    const id = parseInt(delBtn.dataset.deletePlantId, 10);
                    const plant = plants.find(p => Number(p.id) === id);
                    if (!plant || !confirm(`Apakah Anda yakin ingin menghapus tanaman ${plant.kode_tanaman}?`)) return;

                    try {
                        const res = await apiRequest(`/plants/${id}`, { method: 'DELETE' });
                        if (!res) return;
                        if (res.ok) {
                            await syncStockPageWithApi();
                            showToast(`Tanaman ${plant.kode_tanaman} berhasil dihapus.`);
                        } else {
                            showToast(extractApiError(res.json, 'Gagal menghapus data tanaman.'));
                        }
                    } catch (_) {
                        showToast('Gagal terhubung ke Backend API (port 8000).');
                    }
                }
            });
        }

        if (btnTambahStok) btnTambahStok.addEventListener('click', () => openStockDrawer('tambah'));
        if (btnCloseDrawer) btnCloseDrawer.addEventListener('click', closeStockDrawer);
        if (btnBatal) btnBatal.addEventListener('click', closeStockDrawer);
        if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeStockDrawer);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeStockDrawer(); });

        if (searchStockInput) {
            searchStockInput.addEventListener('input', function(e) {
                searchQuery = e.target.value;
                renderInventoryTable();
            });
        }

        // Submit Form via Backend API
        if (btnSimpan) {
            btnSimpan.addEventListener('click', async function() {
                const checkedRadio = document.querySelector('input[name="kategoriStok"]:checked');
                const catKey = checkedRadio ? checkedRadio.value : 'bibit';
                const mode = formMode.value;
                const nama = inputNama.value.trim();
                const keterangan = inputKeterangan.value.trim();

                showDrawerError('');

                if (!nama || nama.length < 2) {
                    showDrawerError('Nama minimal 2 karakter.');
                    inputNama.focus();
                    return;
                }

                setSubmitLoading(true, mode);

                try {
                    if (catKey === 'tanaman') {
                        const payload = {
                            nama,
                            varietas: inputVarietas.value.trim() || null,
                            kode_tanaman: inputKodeTanaman.value.trim() || null,
                            tanggal_tanam: inputTanggal.value || getTodayDateString(),
                            tanggal_panen: inputTanggalPanen.value || null,
                            status: inputPlantStatus.value || 'pembibitan',
                            keterangan: keterangan || null,
                        };

                        const url = mode === 'tambah' ? '/plants' : `/plants/${editStockId.value}`;
                        const method = mode === 'tambah' ? 'POST' : 'PUT';
                        const res = await apiRequest(url, { method, body: JSON.stringify(payload) });
                        if (!res) return;

                        if (res.ok) {
                            await syncStockPageWithApi();
                            closeStockDrawer();
                            showToast(mode === 'tambah' ? `Batch "${nama}" berhasil ditambahkan!` : `Data "${nama}" berhasil diperbarui!`);
                        } else {
                            showDrawerError(extractApiError(res.json, 'Gagal menyimpan data tanaman.'));
                        }
                    } else {
                        const qty = parseFloat(inputJumlahStok.value);
                        const satuan = inputSatuan.value.trim();
                        const minQty = inputStokMinimum.value !== '' ? parseFloat(inputStokMinimum.value) : 0;

                        if (Number.isNaN(qty) || qty < 0) {
                            showDrawerError('Jumlah stok harus berupa angka >= 0.');
                            inputJumlahStok.focus();
                            setSubmitLoading(false, mode);
                            return;
                        }
                        if (!satuan) {
                            showDrawerError('Satuan barang wajib diisi.');
                            inputSatuan.focus();
                            setSubmitLoading(false, mode);
                            return;
                        }
                        if (Number.isNaN(minQty) || minQty < 0) {
                            showDrawerError('Stok minimum harus berupa angka >= 0.');
                            inputStokMinimum.focus();
                            setSubmitLoading(false, mode);
                            return;
                        }

                        if (mode === 'tambah') {
                            const res = await apiRequest('/stocks', {
                                method: 'POST',
                                body: JSON.stringify({
                                    nama_barang: nama,
                                    jenis: catKey,
                                    satuan,
                                    jumlah_stok: qty,
                                    stok_minimum: minQty,
                                    keterangan: keterangan || null,
                                }),
                            });
                            if (!res) return;

                            if (res.ok) {
                                await syncStockPageWithApi();
                                closeStockDrawer();
                                showToast(`"${nama}" berhasil ditambahkan ke database!`);
                            } else {
                                showDrawerError(extractApiError(res.json, 'Gagal menambahkan stok barang.'));
                            }
                        } else {
                            const id = parseInt(editStockId.value, 10);
                            const payload = {
                                nama_barang: nama,
                                jenis: catKey,
                                satuan,
                                stok_minimum: minQty,
                                jumlah_stok: qty,
                                keterangan: keterangan || null,
                                keterangan_pergerakan: keterangan || `Penyesuaian stok ${nama} via web admin`,
                            };
                            const res = await apiRequest(`/stocks/${id}`, {
                                method: 'PUT',
                                body: JSON.stringify(payload),
                            });
                            if (!res) return;

                            if (res.ok) {
                                await syncStockPageWithApi();
                                closeStockDrawer();
                                showToast(`Perubahan pada "${nama}" berhasil disimpan!`);
                            } else {
                                showDrawerError(extractApiError(res.json, 'Gagal memperbarui stok barang.'));
                            }
                        }
                    }
                } catch (_) {
                    showDrawerError('Tidak dapat terhubung ke server Backend API (port 8000).');
                } finally {
                    setSubmitLoading(false, mode);
                }
            });
        }

        const exportBtn = document.getElementById('btnExportMovementsCsv');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                const rows = [
                    ['Waktu', 'Petugas', 'Barang', 'Jenis Pergerakan', 'Jumlah', 'Satuan', 'Keterangan'],
                    ...(movements || []).map(mv => [
                        mv.tanggal || '',
                        mv.recorded_by_nama || 'Sistem',
                        mv.nama_barang || `Item #${mv.inventory_item_id}`,
                        mv.jenis_pergerakan || '',
                        mv.jumlah ?? 0,
                        mv.satuan || '',
                        mv.keterangan || '',
                    ]),
                ];
                const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `riwayat-stok-hidara-${getTodayDateString()}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        }

        // Initial render + live sync from Backend API using cookie
        updateCategoryFields('bibit', false);
        renderPlantedCards();
        renderInventoryTable();
        renderRecentLogs();
        updateMetricSummary();
        syncStockPageWithApi();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStockPage);
    } else {
        initStockPage();
    }
})();
</script>
@endpush
@endsection
