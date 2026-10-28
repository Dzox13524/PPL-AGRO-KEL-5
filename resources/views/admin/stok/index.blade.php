@extends('layouts.admin', ['active' => 'stok'])

@section('title', 'Manajemen Stok')

@section('content')
<div class="max-w-350 mx-auto space-y-6">

    {{-- Top Title & Action Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Stok</h1>
        </div>
        <div>
            <button type="button" class="inline-flex items-center gap-1.5 bg-[#137a46] hover:bg-[#0f6338] text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm transition active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Tambah Stok</span>
            </button>
        </div>
    </div>

    {{-- Filter Tabs Bar --}}
    <div class="bg-white border border-gray-100 shadow-xs rounded-xl p-1.5 inline-flex items-center gap-1">
        <button type="button" class="bg-[#e6f0fa] text-[#1a5b8c] font-semibold text-xs px-4 py-2 rounded-lg transition">
            Semua Ringkasan
        </button>
        <button type="button" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50">
            Tanaman Ditanam
        </button>
        <button type="button" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50">
            Stok & Inventaris
        </button>
        <button type="button" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-4 py-2 rounded-lg transition hover:bg-gray-50">
            Riwayat Catatan
        </button>
    </div>

    {{-- 3 Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Card 1: Tanaman Aktif --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-xs text-gray-500 font-medium">Tanaman Aktif</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">4.200</span>
                <span class="text-xs text-gray-500">Lubang Tanam</span>
            </div>
            <p class="text-xs font-semibold text-emerald-600 mt-3">4 Bedeng beroperasi penuh</p>
        </div>

        {{-- Card 2: Total Stok Benih --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-xs text-gray-500 font-medium">Total Stok Benih</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">24.500</span>
                <span class="text-xs text-gray-500">Butir</span>
            </div>
            <p class="text-xs text-gray-500 mt-3">3 Varietas siap semai</p>
        </div>

        {{-- Card 3: Nutrisi AB Mix --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-xs text-gray-500 font-medium">Nutrisi AB Mix</p>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-gray-900 tracking-tight">185</span>
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
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Daftar Inventaris Barang</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar stok benih, nutrisi pekatan, dan media tanam.</p>
            </div>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" placeholder="Cari barang atau SKU..."
                    class="bg-[#f0f4f8] text-xs pl-9 pr-4 py-2 rounded-lg text-gray-700 placeholder-gray-400 outline-none w-64 focus:ring-1 focus:ring-emerald-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11.5px] font-medium text-gray-400">
                        <th class="py-3 font-medium">Nama Barang</th>
                        <th class="py-3 font-medium">Kategori</th>
                        <th class="py-3 font-medium">Jumlah Stok</th>
                        <th class="py-3 font-medium">Status</th>
                        <th class="py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    {{-- Row 1 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">Benih Selada Butterhead</p>
                            <p class="text-[11px] text-gray-400">SEED-BTH-01</p>
                        </td>
                        <td class="py-3.5 text-gray-600">Benih Unggul</td>
                        <td class="py-3.5 font-bold text-gray-900">12.000 Butir</td>
                        <td class="py-3.5">
                            <span class="text-xs font-semibold text-emerald-600">Tersedia</span>
                        </td>
                        <td class="py-3.5 text-right">
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Edit</button>
                        </td>
                    </tr>

                    {{-- Row 2 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">Benih Selada Romaine</p>
                            <p class="text-[11px] text-gray-400">SEED-ROM-02</p>
                        </td>
                        <td class="py-3.5 text-gray-600">Benih Unggul</td>
                        <td class="py-3.5 font-bold text-gray-900">8.500 Butir</td>
                        <td class="py-3.5">
                            <span class="text-xs font-semibold text-emerald-600">Tersedia</span>
                        </td>
                        <td class="py-3.5 text-right">
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Edit</button>
                        </td>
                    </tr>

                    {{-- Row 3 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">Nutrisi AB Mix Pekatan Sayuran Daun</p>
                            <p class="text-[11px] text-gray-400">NUT-AB-500L</p>
                        </td>
                        <td class="py-3.5 text-gray-600">Formula Nutrisi</td>
                        <td class="py-3.5 font-bold text-gray-900">185 Liter</td>
                        <td class="py-3.5">
                            <span class="text-xs font-semibold text-emerald-600">Tersedia</span>
                        </td>
                        <td class="py-3.5 text-right">
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Edit</button>
                        </td>
                    </tr>

                    {{-- Row 4 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">Formulasi pH Down (Asam Fosfat 85%)</p>
                            <p class="text-[11px] text-gray-400">PH-DOWN-10L</p>
                        </td>
                        <td class="py-3.5 text-gray-600">Regulator pH</td>
                        <td class="py-3.5 font-bold text-gray-900">28 Liter</td>
                        <td class="py-3.5">
                            <span class="text-xs font-semibold text-amber-600">Menipis</span>
                        </td>
                        <td class="py-3.5 text-right">
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Edit</button>
                        </td>
                    </tr>

                    {{-- Row 5 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900 text-xs">Rockwool Cultilene Slab</p>
                            <p class="text-[11px] text-gray-400">MED-RWL-100</p>
                        </td>
                        <td class="py-3.5 text-gray-600">Media Tanam</td>
                        <td class="py-3.5 font-bold text-gray-900">42 Slab</td>
                        <td class="py-3.5">
                            <span class="text-xs font-semibold text-emerald-600">Tersedia</span>
                        </td>
                        <td class="py-3.5 text-right">
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section: Riwayat Catatan Terakhir --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-xs p-6 mb-10">
        <div class="flex items-center justify-between pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Riwayat Catatan Terakhir</h3>
                <p class="text-xs text-gray-500 mt-0.5">Log mutasi inventaris dan penyesuaian stok terbaru.</p>
            </div>
            <button type="button" class="text-xs font-semibold text-gray-500 hover:text-gray-900 transition">
                Export CSV
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11.5px] font-medium text-gray-400">
                        <th class="py-3 font-medium">Waktu</th>
                        <th class="py-3 font-medium">Petugas</th>
                        <th class="py-3 font-medium">Barang</th>
                        <th class="py-3 font-medium">Perubahan</th>
                        <th class="py-3 font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    {{-- Log 1 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 text-gray-500">24 Okt, 08:30</td>
                        <td class="py-3.5 font-semibold text-gray-800">Agus Santoso</td>
                        <td class="py-3.5 text-gray-700">Nutrisi AB Mix Pekatan</td>
                        <td class="py-3.5 font-bold text-emerald-600">+25 Liter</td>
                        <td class="py-3.5 text-gray-500">Pengiriman supplier PT Agronutrisi</td>
                    </tr>

                    {{-- Log 2 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 text-gray-500">23 Okt, 15:10</td>
                        <td class="py-3.5 font-semibold text-gray-800">Rian Wahyudi</td>
                        <td class="py-3.5 text-gray-700">Benih Selada Butterhead</td>
                        <td class="py-3.5 font-bold text-red-600">-800 Butir</td>
                        <td class="py-3.5 text-gray-500">Penyemaian batch nursery bed B-01</td>
                    </tr>

                    {{-- Log 3 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 text-gray-500">22 Okt, 10:45</td>
                        <td class="py-3.5 font-semibold text-gray-800">Hendra Gunawan</td>
                        <td class="py-3.5 text-gray-700">pH Down Asam Fosfat</td>
                        <td class="py-3.5 font-bold text-red-600">-2 Liter</td>
                        <td class="py-3.5 text-gray-500">Koreksi pH reservoir utama</td>
                    </tr>

                    {{-- Log 4 --}}
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 text-gray-500">20 Okt, 16:00</td>
                        <td class="py-3.5 font-semibold text-gray-800">Agus Santoso</td>
                        <td class="py-3.5 text-gray-700">Rockwool Cultilene Slab</td>
                        <td class="py-3.5 font-bold text-amber-600">42 Slab</td>
                        <td class="py-3.5 text-gray-500">Stock opname mingguan gudang B</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection