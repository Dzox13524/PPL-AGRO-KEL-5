@extends('layouts.admin', ['active' => 'stok'])

@section('title', 'Manajemen Stok')

@section('content')
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

<div class="max-w-350 mx-auto space-y-6">

    {{-- Top Title & Action Button --}}
    <div class="flex items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Stok</h1>
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

    {{-- Filter Tabs Bar (Horizontal scroll on mobile) --}}
    <div class="overflow-x-auto max-w-full no-scrollbar pb-1 -mx-1 px-1 sm:mx-0 sm:px-0">
        <div class="bg-white border border-gray-100 shadow-xs rounded-xl p-1.5 inline-flex items-center gap-1 min-w-max">
            <button type="button" class="bg-[#e6f0fa] text-[#1a5b8c] font-semibold text-xs px-4 py-2 rounded-lg transition shrink-0 cursor-pointer">
                Semua Ringkasan
            </button>
            <button type="button" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer">
                Tanaman Ditanam
            </button>
            <button type="button" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer">
                Stok & Inventaris
            </button>
            <button type="button" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50 shrink-0 cursor-pointer">
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
                <span id="statTanamanAktif" class="text-3xl font-extrabold text-gray-900 tracking-tight">4.200</span>
                <span class="text-xs text-gray-500">Lubang Tanam</span>
            </div>
            <p class="text-xs font-semibold text-emerald-600 mt-3">4 Bedeng beroperasi penuh</p>
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
                <span id="statTotalBenih" class="text-3xl font-extrabold text-gray-900 tracking-tight">24.500</span>
                <span class="text-xs text-gray-500">Butir</span>
            </div>
            <p class="text-xs text-gray-500 mt-3">3 Varietas siap semai</p>
        </div>

        {{-- Card 3: Nutrisi AB Mix --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 font-medium">Nutrisi AB Mix</p>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <img src="/icons/icon_pharma.png" alt="Nutrisi" class="w-4 h-4 object-contain" style="filter: invert(31%) sepia(98%) saturate(2206%) hue-rotate(213deg) brightness(98%) contrast(97%);">
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span id="statNutrisiAbMix" class="text-3xl font-extrabold text-gray-900 tracking-tight">185</span>
                <span class="text-xs text-gray-500">Liter Pekatan</span>
            </div>
            <p class="text-xs text-gray-500 mt-3">Estimasi cadangan 32 hari</p>
        </div>
    </div>

    {{-- Section: Tanaman Sedang Ditanam --}}
    <div class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Tanaman Sedang Ditanam</h2>
                <p class="text-xs text-gray-500">Status fase vegetatif dan estimasi masa panen per bedeng.</p>
            </div>
            <span class="text-xs font-semibold text-gray-500">4 Bedeng Aktif</span>
        </div>

        {{-- 4 Bedeng Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Bedeng A-01 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_a01.jpg" alt="Bedeng A-01" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng A-01</h3>
                        <span class="text-xs font-bold text-emerald-600">7 hari lagi</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Butterhead · 1.200 lubang</p>
                    <p class="text-xs text-gray-400 mt-2.5">Fase: Vegetatif (H-28/35)</p>
                </div>
            </div>

            {{-- Bedeng A-02 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_a02.jpg" alt="Bedeng A-02" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng A-02</h3>
                        <span class="text-xs font-medium text-gray-500">14 hari lagi</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Romaine · 1.000 lubang</p>
                    <p class="text-xs text-gray-400 mt-2.5">Fase: Vegetatif (H-21/35)</p>
                </div>
            </div>

            {{-- Bedeng B-01 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_b01.jpg" alt="Bedeng B-01" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng B-01</h3>
                        <span class="text-xs font-bold text-emerald-600">8 hari lagi</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Lollo Bionda · 800 netpot</p>
                    <p class="text-xs text-gray-400 mt-2.5">Fase: Persemaian (H-6/14)</p>
                </div>
            </div>

            {{-- Bedeng B-02 --}}
            <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-xs hover:shadow-md transition group">
                <div class="h-36 w-full overflow-hidden bg-gray-100">
                    <img src="/images/beds/bed_b02.jpg" alt="Bedeng B-02" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-sm">Bedeng B-02</h3>
                        <span class="text-xs font-medium text-gray-500">23 hari lagi</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Selada Butterhead · 1.200 lubang</p>
                    <p class="text-xs text-gray-400 mt-2.5">Fase: Pindah Tanam (H-12/35)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Daftar Inventaris Barang --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Daftar Inventaris Barang</h3>
            </div>
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text"
                       id="searchStockInput"
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
                    {{-- Di-render secara dinamis via JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section: Riwayat Catatan Terakhir --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-4 sm:p-6 mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Riwayat Catatan Terakhir</h3>
                <p class="text-xs text-gray-500 mt-0.5">Log mutasi inventaris dan penyesuaian stok terbaru.</p>
            </div>
            <button type="button" class="self-start sm:self-auto text-xs font-semibold text-gray-500 hover:text-gray-900 transition cursor-pointer">
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
                    {{-- Di-render secara dinamis via JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Backdrop Gelap Drawer --}}
<div id="stockDrawerBackdrop"
     class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-40 cursor-pointer"
     aria-hidden="true"></div>

{{-- Panel Drawer Samping Kanan (Form Tambah / Edit Stok) --}}
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

        {{-- Radio Group: 3 Kategori (Tanaman, Bibit, Nutrisi) --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2">
                Pilih Kategori <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-3 gap-2.5" id="categoryRadioContainer">
                {{-- Radio 1: Tanaman (Icon: icon_plant.png, Hijau) --}}
                <label class="category-radio-card cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative border-emerald-600 bg-emerald-50/70 text-emerald-900 ring-2 ring-emerald-500/30 shadow-xs" data-category="tanaman">
                    <input type="radio" name="kategoriStok" value="tanaman" class="sr-only" checked>
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_plant.png" alt="Tanaman" class="w-4 h-4 object-contain" style="filter: invert(48%) sepia(79%) saturate(452%) hue-rotate(86deg) brightness(98%) contrast(92%);">
                    </div>
                    <span class="text-xs font-bold leading-tight">Tanaman</span>
                </label>

                {{-- Radio 2: Bibit (Icon: icon_seed.png, Kuning) --}}
                <label class="category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative" data-category="bibit">
                    <input type="radio" name="kategoriStok" value="bibit" class="sr-only">
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_seed.png" alt="Bibit" class="w-4 h-4 object-contain" style="filter: invert(78%) sepia(61%) saturate(1478%) hue-rotate(359deg) brightness(98%) contrast(92%);">
                    </div>
                    <span class="text-xs font-bold leading-tight">Bibit</span>
                </label>

                {{-- Radio 3: Nutrisi (Icon: icon_pharma.png, Biru) --}}
                <label class="category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative" data-category="nutrisi">
                    <input type="radio" name="kategoriStok" value="nutrisi" class="sr-only">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center pointer-events-none">
                        <img src="/icons/icon_pharma.png" alt="Nutrisi" class="w-4 h-4 object-contain" style="filter: invert(31%) sepia(98%) saturate(2206%) hue-rotate(213deg) brightness(98%) contrast(97%);">
                    </div>
                    <span class="text-xs font-bold leading-tight">Nutrisi</span>
                </label>
            </div>
        </div>

        {{-- Input 1: Nama (Dinamis sesuai kategori) --}}
        <div>
            <label for="inputNama" id="labelNama" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Nama Tanaman <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <img id="inputNamaIcon" src="/icons/icon_plant.png" alt="Kategori" class="w-4 h-4 object-contain" style="filter: invert(48%) sepia(79%) saturate(452%) hue-rotate(86deg) brightness(98%) contrast(92%);">
                </span>
                <input type="text"
                       id="inputNama"
                       required
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-10 pr-4 py-2.5 rounded-xl transition-all outline-none">
            </div>
        </div>

        {{-- Input 2: Jenis (Dinamis sesuai kategori) --}}
        <div>
            <label for="inputJenis" id="labelJenis" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Jenis Tanaman <span class="text-rose-500">*</span>
            </label>
            <select id="inputJenis"
                    required
                    class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                {{-- Opsi diisi otomatis via JS --}}
            </select>
        </div>

        {{-- Input 3 & 4: Jumlah Stok & Satuan (Grid 2 Kolom, Dinamis sesuai kategori) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
                <label for="inputJumlahStok" id="labelJumlahStok" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Jumlah Stok <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       id="inputJumlahStok"
                       required
                       min="0"
                       step="any"
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none">
            </div>

            <div>
                <label for="inputSatuan" id="labelSatuan" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Satuan <span class="text-rose-500">*</span>
                </label>
                <select id="inputSatuan"
                        required
                        class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 px-3.5 py-2.5 rounded-xl transition-all outline-none">
                    {{-- Opsi diisi otomatis via JS --}}
                </select>
            </div>
        </div>

        {{-- Input 5: Tanggal Pencatatan --}}
        <div>
            <label for="inputTanggal" id="labelTanggal" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Tanggal Pencatatan <span class="text-rose-500">*</span>
            </label>
            <input type="date"
                   id="inputTanggal"
                   required
                   class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 px-3.5 py-2.5 rounded-xl transition-all outline-none">
        </div>

        {{-- Input 6: Keterangan (Dinamis sesuai kategori) --}}
        <div>
            <label for="inputKeterangan" id="labelKeterangan" class="block text-xs font-semibold text-gray-700 mb-1.5">
                Keterangan
            </label>
            <textarea id="inputKeterangan"
                      rows="3"
                      class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 px-3.5 py-2.5 rounded-xl transition-all outline-none resize-none"></textarea>
        </div>
    </form>

    {{-- Drawer Footer: Aksi Batal & Simpan --}}
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
                class="px-5 py-2.5 rounded-xl text-[13px] font-semibold text-white hover:opacity-95 shadow-sm transition active:scale-95 cursor-pointer">
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
    function initStockPage() {
        // Konfigurasi Kategori Dinamis (Tanaman, Bibit, Nutrisi)
        const categoryConfig = {
            tanaman: {
                categoryName: 'Tanaman',
                labelNama: 'Nama Tanaman',
                labelJenis: 'Jenis Tanaman',
                iconFile: '/icons/icon_plant.png',
                iconFilter: 'invert(48%) sepia(79%) saturate(452%) hue-rotate(86deg) brightness(98%) contrast(92%)',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative border-emerald-600 bg-emerald-50/70 text-emerald-900 ring-2 ring-emerald-500/30 shadow-xs',
                badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                jenisOptions: [
                    'Sayuran Daun Hijau',
                    'Sayuran Daun Merah',
                    'Sayuran Buah',
                    'Herbal & Microgreens',
                    'Tanaman Hias Hidroponik'
                ],
                satuanOptions: [
                    'Lubang Tanam',
                    'Netpot',
                    'Batang',
                    'Polybag',
                    'Tray'
                ],
                skuPrefix: 'TNM'
            },
            bibit: {
                categoryName: 'Bibit',
                labelNama: 'Nama Bibit',
                labelJenis: 'Jenis Bibit',
                iconFile: '/icons/icon_seed.png',
                iconFilter: 'invert(78%) sepia(61%) saturate(1478%) hue-rotate(359deg) brightness(98%) contrast(92%)',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative border-amber-500 bg-amber-50/70 text-amber-900 ring-2 ring-amber-400/30 shadow-xs',
                badgeClass: 'bg-amber-50 text-amber-700 border-amber-200/60',
                jenisOptions: [
                    'Benih F1 Hybrid Unggul',
                    'Benih Standar / Open Pollinated',
                    'Benih Organik Bersertifikat',
                    'Benih Pelet (Coated Seed)',
                    'Bibit Siap Semai'
                ],
                satuanOptions: [
                    'Butir',
                    'Gram',
                    'Pack / Sachet',
                    'Kaleng',
                    'Kg'
                ],
                skuPrefix: 'SEED'
            },
            nutrisi: {
                categoryName: 'Nutrisi',
                labelNama: 'Nama Nutrisi',
                labelJenis: 'Jenis Nutrisi',
                iconFile: '/icons/icon_pharma.png',
                iconFilter: 'invert(31%) sepia(98%) saturate(2206%) hue-rotate(213deg) brightness(98%) contrast(97%)',
                activeClass: 'category-radio-card cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative border-blue-600 bg-blue-50/70 text-blue-900 ring-2 ring-blue-500/30 shadow-xs',
                badgeClass: 'bg-blue-50 text-blue-700 border-blue-200/60',
                jenisOptions: [
                    'Formula AB Mix Sayuran Daun',
                    'Formula AB Mix Sayuran Buah',
                    'Regulator pH (pH Down)',
                    'Regulator pH (pH Up)',
                    'Unsur Makro Kalsium Nitrat',
                    'Media Tanam & Rockwool'
                ],
                satuanOptions: [
                    'Liter',
                    'Kg',
                    'Gram',
                    'Mililiter (ml)',
                    'Slab',
                    'Botol'
                ],
                skuPrefix: 'NUT'
            }
        };

        // Data Stok Inventaris
        let stocks = [
            { id: 1, name: 'Selada Butterhead Organik', category: 'Tanaman', jenis: 'Sayuran Daun Hijau', sku: 'TNM-BTH-01', stock: 2400, unit: 'Lubang Tanam', status: 'Tersedia', date: '2026-10-24', note: 'Bedeng A-01 & A-02' },
            { id: 2, name: 'Pakcoy White Nauli F1', category: 'Tanaman', jenis: 'Sayuran Daun Hijau', sku: 'TNM-PKC-02', stock: 1800, unit: 'Lubang Tanam', status: 'Tersedia', date: '2026-10-24', note: 'Bedeng B-01 & B-02' },
            { id: 3, name: 'Benih Selada Butterhead Unggul', category: 'Bibit', jenis: 'Benih F1 Hybrid Unggul', sku: 'SEED-BTH-01', stock: 12000, unit: 'Butir', status: 'Tersedia', date: '2026-10-23', note: 'Kemasan kaleng vacuum' },
            { id: 4, name: 'Benih Selada Romaine Standard', category: 'Bibit', jenis: 'Benih Standar / Open Pollinated', sku: 'SEED-ROM-02', stock: 8500, unit: 'Butir', status: 'Tersedia', date: '2026-10-22', note: 'Supplier Panah Merah' },
            { id: 5, name: 'Nutrisi AB Mix Pekatan Sayuran Daun', category: 'Nutrisi', jenis: 'Formula AB Mix Sayuran Daun', sku: 'NUT-AB-500L', stock: 185, unit: 'Liter', status: 'Tersedia', date: '2026-10-24', note: 'Stok pekatan jerigen 20L' },
            { id: 6, name: 'Formulasi pH Down (Asam Fosfat 85%)', category: 'Nutrisi', jenis: 'Regulator pH (pH Down)', sku: 'PH-DOWN-10L', stock: 28, unit: 'Liter', status: 'Menipis', date: '2026-10-20', note: 'Koreksi keasaman tandon' }
        ];

        // Riwayat Catatan Mutasi
        let recentLogs = [
            { id: 1, time: '24 Okt, 08:30', officer: 'Agus Santoso', item: 'Nutrisi AB Mix Pekatan', change: '+25 Liter', type: 'plus', note: 'Pengiriman supplier PT Agronutrisi' },
            { id: 2, time: '23 Okt, 15:10', officer: 'Rian Wahyudi', item: 'Benih Selada Butterhead Unggul', change: '-800 Butir', type: 'minus', note: 'Penyemaian batch nursery bed B-01' },
            { id: 3, time: '22 Okt, 10:45', officer: 'Hendra Gunawan', item: 'Formulasi pH Down', change: '-2 Liter', type: 'minus', note: 'Koreksi pH reservoir utama' },
            { id: 4, time: '20 Okt, 16:00', officer: 'Agus Santoso', item: 'Selada Butterhead Organik', change: '+1.200 Lubang', type: 'plus', note: 'Pindah tanam ke Bedeng A-01' }
        ];

        let searchQuery = '';

        // DOM Elements
        const inventoryTableBody = document.getElementById('inventoryTableBody');
        const recentLogsBody = document.getElementById('recentLogsBody');
        const searchStockInput = document.getElementById('searchStockInput');

        const statTanamanAktif = document.getElementById('statTanamanAktif');
        const statTotalBenih = document.getElementById('statTotalBenih');
        const statNutrisiAbMix = document.getElementById('statNutrisiAbMix');

        // Drawer Elements
        const drawerBackdrop = document.getElementById('stockDrawerBackdrop');
        const drawerPanel = document.getElementById('stockDrawerPanel');
        const btnTambahStok = document.getElementById('btnTambahStok');
        const btnCloseDrawer = document.getElementById('btnCloseStockDrawer');
        const btnBatal = document.getElementById('btnBatalStock');
        const btnSimpan = document.getElementById('btnSimpanStock');
        const btnSimpanText = document.getElementById('btnSimpanStockText');
        const drawerTitle = document.getElementById('stockDrawerTitle');

        const formMode = document.getElementById('stockFormMode');
        const editStockId = document.getElementById('editStockId');

        // Dynamic Form Elements
        const categoryCards = document.querySelectorAll('.category-radio-card');
        const inputNamaIcon = document.getElementById('inputNamaIcon');
        const labelNama = document.getElementById('labelNama');
        const inputNama = document.getElementById('inputNama');
        const labelJenis = document.getElementById('labelJenis');
        const inputJenis = document.getElementById('inputJenis');
        const labelJumlahStok = document.getElementById('labelJumlahStok');
        const inputJumlahStok = document.getElementById('inputJumlahStok');
        const labelSatuan = document.getElementById('labelSatuan');
        const inputSatuan = document.getElementById('inputSatuan');
        const labelTanggal = document.getElementById('labelTanggal');
        const inputTanggal = document.getElementById('inputTanggal');
        const labelKeterangan = document.getElementById('labelKeterangan');
        const inputKeterangan = document.getElementById('inputKeterangan');

        // Toast Elements
        const toast = document.getElementById('stockToastNotification');
        const toastMsg = document.getElementById('stockToastMessage');
        let toastTimeout;

        function showToast(message) {
            clearTimeout(toastTimeout);
            toastMsg.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            toastTimeout = setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function getTodayDateString() {
            const today = new Date(Date.now() - new Date().getTimezoneOffset() * 60000);
            return today.toISOString().split('T')[0];
        }

        function formatLogDate(dateStr) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const timeStr = `${hours}:${mins}`;

            if (!dateStr) return `Hari ini, ${timeStr}`;

            const parts = dateStr.split('-');
            if (parts.length !== 3) return `Hari ini, ${timeStr}`;

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const day = parseInt(parts[2], 10);
            const monthIdx = parseInt(parts[1], 10) - 1;
            const monthName = months[monthIdx] || '';

            const todayStr = getTodayDateString();
            if (dateStr === todayStr) {
                return `Hari ini, ${timeStr}`;
            }
            return `${day} ${monthName}, ${timeStr}`;
        }

        // Fungsi Switch Kategori & Dynamic Fields
        function updateCategoryFields(catKey, preserveValues = false) {
            const config = categoryConfig[catKey] || categoryConfig.tanaman;

            // Update radio cards visual & checked state
            categoryCards.forEach(card => {
                const isSelected = card.dataset.category === catKey;
                const radioInput = card.querySelector('input[type="radio"]');
                if (radioInput) radioInput.checked = isSelected;

                if (isSelected) {
                    card.className = config.activeClass;
                } else {
                    card.className = 'category-radio-card cursor-pointer border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5 relative';
                }
            });

            // Update icon pada input Nama sesuai kategori dan warna filternya
            if (inputNamaIcon) {
                inputNamaIcon.src = config.iconFile;
                inputNamaIcon.style.filter = config.iconFilter;
            }

            // Update Label Nama
            if (labelNama) labelNama.innerHTML = `${config.labelNama} <span class="text-rose-500">*</span>`;
            if (inputNama) inputNama.placeholder = '';

            // Update Label & Select Jenis
            if (labelJenis) labelJenis.innerHTML = `${config.labelJenis} <span class="text-rose-500">*</span>`;
            if (inputJenis) {
                const currentJenis = preserveValues ? inputJenis.value : '';
                inputJenis.innerHTML = `<option value="" disabled ${!currentJenis ? 'selected' : ''}>Pilih ${config.labelJenis}</option>` +
                    config.jenisOptions.map(opt => `<option value="${escapeHtml(opt)}" ${currentJenis === opt ? 'selected' : ''}>${escapeHtml(opt)}</option>`).join('');
                if (currentJenis && config.jenisOptions.includes(currentJenis)) {
                    inputJenis.value = currentJenis;
                } else if (!preserveValues && config.jenisOptions.length > 0) {
                    inputJenis.selectedIndex = 1; // default ke opsi pertama
                }
            }

            if (inputJumlahStok) inputJumlahStok.placeholder = '';

            // Update Select Satuan
            if (inputSatuan) {
                const currentSatuan = preserveValues ? inputSatuan.value : '';
                inputSatuan.innerHTML = config.satuanOptions.map(opt => `<option value="${escapeHtml(opt)}" ${currentSatuan === opt ? 'selected' : ''}>${escapeHtml(opt)}</option>`).join('');
                if (currentSatuan && config.satuanOptions.includes(currentSatuan)) {
                    inputSatuan.value = currentSatuan;
                }
            }

            if (inputKeterangan) inputKeterangan.placeholder = '';
        }

        // Update Ringkasan Metrik
        function updateMetricSummary() {
            // Tanaman Aktif (Card 1)
            const totalTanaman = stocks
                .filter(s => (s.category && s.category.toLowerCase().includes('tanaman')) || s.unit === 'Lubang Tanam' || s.unit === 'Netpot')
                .reduce((acc, curr) => acc + (Number(curr.stock) || 0), 0);
            if (statTanamanAktif) statTanamanAktif.textContent = formatNumber(totalTanaman);

            // Total Stok Benih / Bibit (Card 2)
            const totalBenih = stocks
                .filter(s => (s.category && (s.category.toLowerCase().includes('bibit') || s.category.toLowerCase().includes('benih'))) || s.unit === 'Butir')
                .reduce((acc, curr) => acc + (Number(curr.stock) || 0), 0);
            if (statTotalBenih) statTotalBenih.textContent = formatNumber(totalBenih);

            // Nutrisi AB Mix (Card 3)
            const nutrisiItems = stocks.filter(s =>
                (s.name && s.name.toLowerCase().includes('ab mix')) ||
                (s.category && s.category.toLowerCase().includes('nutrisi'))
            );
            const abMix = stocks.find(s => s.name.toLowerCase().includes('ab mix'));
            const totalNutrisi = nutrisiItems.reduce((acc, curr) => acc + (Number(curr.stock) || 0), 0);
            if (statNutrisiAbMix) {
                statNutrisiAbMix.textContent = formatNumber(abMix ? abMix.stock : totalNutrisi);
            }
        }

        function getFilteredStocks() {
            if (!searchQuery.trim()) return stocks;
            const q = searchQuery.toLowerCase().trim();
            return stocks.filter(s =>
                s.name.toLowerCase().includes(q) ||
                (s.sku && s.sku.toLowerCase().includes(q)) ||
                (s.category && s.category.toLowerCase().includes(q)) ||
                (s.jenis && s.jenis.toLowerCase().includes(q))
            );
        }

        function renderInventoryTable() {
            const filtered = getFilteredStocks();

            if (filtered.length === 0) {
                inventoryTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 text-xs">
                            Tidak ada barang atau inventaris yang sesuai dengan "${escapeHtml(searchQuery)}".
                        </td>
                    </tr>
                `;
                return;
            }

            inventoryTableBody.innerHTML = filtered.map(item => {
                let statusBadge = '';
                if (item.status === 'Tersedia') {
                    statusBadge = '<span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Tersedia</span>';
                } else if (item.status === 'Menipis') {
                    statusBadge = '<span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menipis</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Habis</span>';
                }

                let catConf = categoryConfig.tanaman;
                const catLower = (item.category || '').toLowerCase();
                if (catLower.includes('bibit') || catLower.includes('benih')) {
                    catConf = categoryConfig.bibit;
                } else if (catLower.includes('nutrisi')) {
                    catConf = categoryConfig.nutrisi;
                }

                return `
                    <tr class="hover:bg-gray-50/70 transition group">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">${escapeHtml(item.name)}</p>
                            <p class="text-[11px] text-gray-400 font-mono mt-0.5">${escapeHtml(item.jenis ? item.jenis + ' · ' + (item.sku || '') : (item.sku || ''))}</p>
                        </td>
                        <td class="py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold border ${catConf.badgeClass}">
                                <img src="${catConf.iconFile}" alt="${escapeHtml(item.category)}" class="w-3.5 h-3.5 object-contain" style="filter: ${catConf.iconFilter};">
                                ${escapeHtml(item.category)}
                            </span>
                        </td>
                        <td class="py-3.5 font-bold text-gray-900">${formatNumber(item.stock)} <span class="font-normal text-gray-500 text-[11px]">${escapeHtml(item.unit)}</span></td>
                        <td class="py-3.5">${statusBadge}</td>
                        <td class="py-3.5 text-right">
                            <div class="inline-flex items-center gap-2">
                                <button type="button"
                                        data-edit-id="${item.id}"
                                        class="btn-edit-stock text-xs text-emerald-700 hover:text-emerald-900 font-medium px-2 py-1 rounded hover:bg-emerald-50 transition cursor-pointer">
                                    Edit
                                </button>
                                <button type="button"
                                        data-delete-id="${item.id}"
                                        class="btn-delete-stock text-xs text-gray-400 hover:text-rose-600 px-1 py-1 rounded hover:bg-rose-50 transition cursor-pointer"
                                        title="Hapus Barang">
                                    <img src="/icons/icon_trash.png" alt="Hapus" class="w-3.5 h-3.5 object-contain opacity-50 hover:opacity-100 pointer-events-none">
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderRecentLogs() {
            if (!recentLogsBody) return;
            recentLogsBody.innerHTML = recentLogs.map(log => {
                let changeColor = 'text-gray-900 font-bold';
                if (log.type === 'plus') changeColor = 'text-emerald-600 font-bold';
                else if (log.type === 'minus') changeColor = 'text-red-600 font-bold';
                else if (log.type === 'warn') changeColor = 'text-amber-600 font-bold';

                return `
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 text-gray-500 whitespace-nowrap text-xs">${escapeHtml(log.time)}</td>
                        <td class="py-3.5 font-semibold text-gray-800 text-xs">${escapeHtml(log.officer)}</td>
                        <td class="py-3.5 text-gray-700 text-xs">${escapeHtml(log.item)}</td>
                        <td class="py-3.5 ${changeColor} text-xs">${escapeHtml(log.change)}</td>
                        <td class="py-3.5 text-gray-500 text-xs">${escapeHtml(log.note)}</td>
                    </tr>
                `;
            }).join('');
        }

        // Drawer Open / Close Functions
        function openStockDrawer(mode, stock = null) {
            formMode.value = mode;
            const todayStr = getTodayDateString();

            if (mode === 'tambah') {
                drawerTitle.textContent = 'Tambah Stok Barang';
                btnSimpanText.textContent = 'Tambah Stok';

                editStockId.value = '';
                updateCategoryFields('tanaman', false);
                inputNama.value = '';
                inputJumlahStok.value = '';
                inputTanggal.value = todayStr;
                inputKeterangan.value = '';
            } else {
                drawerTitle.textContent = 'Edit Stok Barang';
                btnSimpanText.textContent = 'Simpan Perubahan';

                editStockId.value = stock.id;

                let categoryKey = 'tanaman';
                const catLower = (stock.category || '').toLowerCase();
                if (catLower.includes('bibit') || catLower.includes('benih')) {
                    categoryKey = 'bibit';
                } else if (catLower.includes('nutrisi')) {
                    categoryKey = 'nutrisi';
                }

                updateCategoryFields(categoryKey, false);

                inputNama.value = stock.name || '';
                if (stock.jenis) inputJenis.value = stock.jenis;
                inputJumlahStok.value = stock.stock;
                if (stock.unit) inputSatuan.value = stock.unit;
                inputTanggal.value = stock.date || todayStr;
                inputKeterangan.value = stock.note || '';
            }

            // Show Drawer (Slide from right)
            drawerBackdrop.classList.add('open');
            drawerPanel.classList.add('open');

            setTimeout(() => inputNama.focus(), 250);
        }

        function closeStockDrawer() {
            drawerBackdrop.classList.remove('open');
            drawerPanel.classList.remove('open');
        }

        window.openStockDrawer = openStockDrawer;
        window.closeStockDrawer = closeStockDrawer;

        // Event delegation for Category Radio selection
        categoryCards.forEach(card => {
            card.addEventListener('click', function() {
                const cat = this.dataset.category;
                if (cat) {
                    updateCategoryFields(cat, true);
                }
            });
        });

        // Event delegation for Edit and Delete buttons
        inventoryTableBody.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit-stock');
            if (editBtn) {
                const id = parseInt(editBtn.dataset.editId, 10);
                const item = stocks.find(s => s.id === id);
                if (item) openStockDrawer('edit', item);
                return;
            }

            const delBtn = e.target.closest('.btn-delete-stock');
            if (delBtn) {
                const id = parseInt(delBtn.dataset.deleteId, 10);
                const item = stocks.find(s => s.id === id);
                if (item && confirm(`Apakah Anda yakin ingin menghapus "${item.name}" dari inventaris?`)) {
                    stocks = stocks.filter(s => s.id !== id);
                    renderInventoryTable();
                    updateMetricSummary();
                    showToast(`"${item.name}" berhasil dihapus.`);
                }
                return;
            }
        });

        // Event Listeners for drawer controls
        if (btnTambahStok) btnTambahStok.addEventListener('click', () => openStockDrawer('tambah'));
        if (btnCloseDrawer) btnCloseDrawer.addEventListener('click', closeStockDrawer);
        if (btnBatal) btnBatal.addEventListener('click', closeStockDrawer);
        if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeStockDrawer);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeStockDrawer();
        });

        // Search stock input listener
        if (searchStockInput) {
            searchStockInput.addEventListener('input', function(e) {
                searchQuery = e.target.value;
                renderInventoryTable();
            });
        }

        // Form Submit
        if (btnSimpan) {
            btnSimpan.addEventListener('click', function() {
                const checkedRadio = document.querySelector('input[name="kategoriStok"]:checked');
                const catKey = checkedRadio ? checkedRadio.value : 'tanaman';
                const config = categoryConfig[catKey] || categoryConfig.tanaman;

                const name = inputNama.value.trim();
                const jenis = inputJenis.value;
                const stockVal = parseFloat(inputJumlahStok.value);
                const unit = inputSatuan.value;
                const tanggalVal = inputTanggal.value;
                const note = inputKeterangan.value.trim();
                const mode = formMode.value;

                if (!name) {
                    alert(`${config.labelNama} wajib diisi!`);
                    inputNama.focus();
                    return;
                }
                if (!jenis) {
                    alert(`${config.labelJenis} wajib dipilih!`);
                    inputJenis.focus();
                    return;
                }
                if (isNaN(stockVal) || stockVal < 0) {
                    alert('Jumlah stok harus berupa angka positif!');
                    inputJumlahStok.focus();
                    return;
                }
                if (!unit) {
                    alert('Satuan wajib dipilih!');
                    inputSatuan.focus();
                    return;
                }
                if (!tanggalVal) {
                    alert('Tanggal pencatatan wajib diisi!');
                    inputTanggal.focus();
                    return;
                }

                // Kalkulasi Status
                let status = 'Tersedia';
                if (stockVal <= 0) {
                    status = 'Habis';
                } else if ((catKey === 'bibit' && stockVal < 2000) || (catKey === 'nutrisi' && stockVal < 30) || (catKey === 'tanaman' && stockVal < 500)) {
                    status = 'Menipis';
                }

                const logTime = formatLogDate(tanggalVal);

                if (mode === 'tambah') {
                    const newId = stocks.length > 0 ? Math.max(...stocks.map(s => s.id)) + 1 : 1;
                    const randomCode = Math.floor(100 + Math.random() * 900);
                    const sku = `${config.skuPrefix}-${randomCode}`;

                    const newItem = {
                        id: newId,
                        name: name,
                        category: config.categoryName,
                        jenis: jenis,
                        sku: sku,
                        stock: stockVal,
                        unit: unit,
                        status: status,
                        date: tanggalVal,
                        note: note
                    };

                    stocks.unshift(newItem);

                    recentLogs.unshift({
                        id: Date.now(),
                        time: logTime,
                        officer: 'Admin Hidara',
                        item: name,
                        change: `+${formatNumber(stockVal)} ${unit}`,
                        type: 'plus',
                        note: note || `Pencatatan stok ${config.categoryName.toLowerCase()} baru`
                    });

                    renderInventoryTable();
                    renderRecentLogs();
                    updateMetricSummary();
                    closeStockDrawer();
                    showToast(`"${name}" berhasil ditambahkan ke inventaris!`);
                } else {
                    const id = parseInt(editStockId.value, 10);
                    const item = stocks.find(s => s.id === id);
                    if (item) {
                        const oldStock = item.stock;
                        const diff = stockVal - oldStock;

                        item.name = name;
                        item.category = config.categoryName;
                        item.jenis = jenis;
                        item.stock = stockVal;
                        item.unit = unit;
                        item.status = status;
                        item.date = tanggalVal;
                        item.note = note;

                        if (diff !== 0) {
                            recentLogs.unshift({
                                id: Date.now(),
                                time: logTime,
                                officer: 'Admin Hidara',
                                item: name,
                                change: diff > 0 ? `+${formatNumber(diff)} ${unit}` : `${formatNumber(diff)} ${unit}`,
                                type: diff > 0 ? 'plus' : 'minus',
                                note: note || `Penyesuaian stok ${config.categoryName.toLowerCase()}`
                            });
                            renderRecentLogs();
                        }

                        renderInventoryTable();
                        updateMetricSummary();
                        closeStockDrawer();
                        showToast(`Perubahan pada "${name}" berhasil disimpan!`);
                    }
                }
            });
        }

        // Inisialisasi awal
        updateCategoryFields('tanaman', false);
        renderInventoryTable();
        renderRecentLogs();
        updateMetricSummary();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStockPage);
    } else {
        initStockPage();
    }
})();
</script>
@endpush

</div>
@endsection