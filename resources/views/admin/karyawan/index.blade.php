@extends('layouts.admin', ['active' => 'karyawan'])

@section('title', 'Manajemen Akun Karyawan')

@section('content')
<style>
    /* Transisi Halus Drawer Samping Kanan */
    #drawerBackdrop {
        transition: opacity 0.3s ease, visibility 0.3s ease;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #drawerBackdrop.open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    #drawerPanel {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        transform: translateX(100%);
    }
    #drawerPanel.open {
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

    <div class="flex items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Akun Karyawan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola akun staf kebun (role karyawan) langsung pada database melalui Backend API</p>
        </div>
        <div class="shrink-0">
            <button type="button"
                    id="btnTambahAkun"
                    onclick="openDrawer('tambah')"
                    style="background-color: #0e5f38;"
                    class="inline-flex items-center gap-1.5 sm:gap-2 hover:opacity-95 text-white text-xs sm:text-[13px] font-semibold px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl shadow-sm transition active:scale-95 cursor-pointer">
                <img src="/icons/icon_add_user.png" alt="Tambah Akun" class="w-3.5 h-3.5 sm:w-4 sm:h-4 object-contain invert brightness-0">
                <span>Tambah Akun</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
        {{-- Card 1: Total Karyawan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">TOTAL KARYAWAN</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statTotalKaryawan" class="text-3xl font-bold text-gray-900 tracking-tight">{{ $totalEmployees ?? count($employees ?? []) }}</span>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">Akun karyawan terdaftar pada tabel <code class="text-[11px] bg-gray-100 px-1 py-0.5 rounded">users</code></p>
        </div>

        {{-- Card 2: Akun Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">AKUN AKTIF</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statAkunAktif" class="text-3xl font-bold text-[#147a46] tracking-tight">{{ $totalEmployees ?? count($employees ?? []) }}</span>
                <span id="statPersenAktif" class="text-xs text-gray-500 font-medium">({{ ($totalEmployees ?? 0) > 0 ? '100% Terdaftar' : '0%' }})</span>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">Terautentikasi JWT ke Backend API (:8000)</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-4 sm:p-6 space-y-4">
        {{-- Input Pencarian --}}
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input type="text"
                   id="searchInput"
                   placeholder="Cari nama atau username karyawan..."
                   class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-11 pr-4 py-3 rounded-xl transition-all outline-none">
        </div>

        {{-- Tabel List Akun --}}
        <div class="overflow-hidden">
            {{-- Header Tabel --}}
            <div class="bg-[#f8fafc] text-gray-500 font-semibold text-[11px] tracking-wider uppercase rounded-xl py-3 px-4 sm:px-6 flex justify-between items-center">
                <span>NAMA &amp; USERNAME</span>
                <span>TINDAKAN</span>
            </div>

            {{-- Container Baris Akun (SSR awal + dirender dinamis via JS dari API) --}}
            <div id="accountsTableBody" class="divide-y divide-gray-50 mt-1">
                @forelse($employees ?? [] as $emp)
                    <div class="flex items-center justify-between py-3 sm:py-3.5 px-3 sm:px-6 hover:bg-gray-50/70 transition group gap-2 sm:gap-3">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-50 text-[#147a46] flex items-center justify-center font-bold text-xs shrink-0 uppercase select-none">
                                {{ strtoupper(substr((string) ($emp['nama'] ?? $emp['username'] ?? 'K'), 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13px] sm:text-[13.5px] font-semibold text-gray-800 truncate">{{ $emp['nama'] ?? $emp['username'] }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ '@' . ($emp['username'] ?? '-') }} • <span class="text-emerald-700 font-medium">{{ $emp['role'] ?? 'karyawan' }}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-2 text-gray-400 shrink-0">
                            <button type="button"
                                    data-edit-id="{{ $emp['id'] }}"
                                    title="Edit Akun"
                                    class="btn-action-edit p-1.5 hover:bg-emerald-50 rounded-lg transition cursor-pointer active:scale-95">
                                <img src="/icons/icon_edit.png" alt="Edit" class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition pointer-events-none">
                            </button>
                            <button type="button"
                                    data-delete-id="{{ $emp['id'] }}"
                                    title="Hapus Akun"
                                    class="btn-action-delete p-1.5 hover:bg-rose-50 rounded-lg transition cursor-pointer active:scale-95">
                                <img src="/icons/icon_trash.png" alt="Hapus" class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition pointer-events-none">
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400 text-xs">
                        Belum ada akun karyawan terdaftar di database.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Footer Tabel & Pagination --}}
        <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <span id="paginationInfo" class="text-gray-500 text-center sm:text-left">
                Menampilkan {{ min(5, count($employees ?? [])) }} dari {{ count($employees ?? []) }} akun terdaftar
            </span>

            <div class="flex items-center justify-center sm:justify-end gap-1.5 w-full sm:w-auto">
                <button type="button"
                        id="btnPrevPage"
                        class="px-2.5 py-1 text-gray-400 hover:text-gray-700 font-medium transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    Sebelumnya
                </button>
                <div id="paginationButtons" class="flex items-center gap-1">
                    {{-- Tombol nomor halaman dirender via JS --}}
                </div>
                <button type="button"
                        id="btnNextPage"
                        class="px-2.5 py-1 text-[#147a46] hover:text-[#0b4a2c] font-semibold transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    Selanjutnya
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Backdrop Gelap --}}
<div id="drawerBackdrop"
     class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-40 cursor-pointer"
     aria-hidden="true"></div>

{{-- Panel Drawer Samping Kanan --}}
<div id="drawerPanel"
     class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] bg-white shadow-2xl flex flex-col justify-between"
     role="dialog"
     aria-modal="true"
     aria-labelledby="drawerTitle">

    {{-- Drawer Header --}}
    <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <img id="drawerHeaderIcon" src="/icons/icon_add_user.png" alt="Tambah Akun" class="w-5 h-5 object-contain">
            </div>
            <div>
                <h2 id="drawerTitle" class="text-base font-bold text-gray-900 tracking-tight">
                    Tambah Akun Karyawan
                </h2>
                <p id="drawerSubtitle" class="text-[11px] text-gray-400 mt-0.5">Perubahan langsung tersimpan ke database</p>
            </div>
        </div>

        {{-- Tombol Tutup (X) --}}
        <button type="button"
                id="btnCloseDrawer"
                onclick="closeDrawer()"
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition cursor-pointer"
                aria-label="Tutup panel">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    {{-- Drawer Body: Form Inputs --}}
    <form id="accountForm" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5" onsubmit="return false;">
        <input type="hidden" id="formMode" value="tambah">
        <input type="hidden" id="editAccountId" value="">

        <div id="drawerErrorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium rounded-xl px-3.5 py-2.5"></div>

        {{-- Input 1: Nama Lengkap --}}
        <div>
            <label for="inputNama" class="block text-xs font-semibold text-gray-700 mb-2">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <img src="/icons/icon_users.png" alt="Nama Lengkap" class="w-4 h-4 object-contain opacity-50">
                </span>
                <input type="text"
                       id="inputNama"
                       placeholder="Contoh: Budi Santoso"
                       required
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-10 pr-4 py-2.5 rounded-xl transition-all outline-none">
            </div>
        </div>

        {{-- Input 2: Username --}}
        <div>
            <label for="inputUsername" class="block text-xs font-semibold text-gray-700 mb-2">
                Username <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <img src="/icons/icon_user.png" alt="Username" class="w-4 h-4 object-contain opacity-50">
                </span>
                <input type="text"
                       id="inputUsername"
                       placeholder="Contoh: budi_santoso"
                       required
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-10 pr-4 py-2.5 rounded-xl transition-all outline-none">
            </div>
            <p class="text-[11px] text-gray-400 mt-1">Minimal 3 karakter (huruf, angka, titik, garis bawah, tanda hubung, atau @)</p>
        </div>

        {{-- Input 3: Kata Sandi --}}
        <div>
            <label for="inputPassword" id="passwordLabel" class="block text-xs font-semibold text-gray-700 mb-2">
                Kata Sandi Awal <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <img src="/icons/icon_lock.png" alt="Kata Sandi" class="w-4 h-4 object-contain opacity-50">
                </span>
                <input type="password"
                       id="inputPassword"
                       placeholder="Minimal 6 karakter"
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-10 pr-10 py-2.5 rounded-xl transition-all outline-none">
                {{-- Tombol Toggle Lihat Sandi --}}
                <button type="button"
                        id="btnTogglePassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer"
                        tabindex="-1"
                        aria-label="Tampilkan atau sembunyikan kata sandi">
                    <svg id="eyeIcon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Info Role --}}
        <div class="bg-[#f8fafc] border border-gray-100 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-[13px] font-semibold text-gray-900">Role Akun</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Akses operasional tanaman, monitoring, prediksi &amp; diagnosis</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-[#147a46]">karyawan</span>
        </div>
    </form>

    {{-- Drawer Footer: Aksi Batal & Simpan --}}
    <div class="p-4 sm:p-6 border-t border-gray-100 flex items-center justify-end gap-3 bg-white">
        <button type="button"
                id="btnBatal"
                onclick="closeDrawer()"
                class="px-5 py-2.5 rounded-xl text-[13px] font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition cursor-pointer">
            Batal
        </button>
        <button type="button"
                id="btnSimpan"
                style="background-color: #0e5f38;"
                class="px-5 py-2.5 rounded-xl text-[13px] font-semibold text-white hover:opacity-95 shadow-sm transition active:scale-95 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
            <span id="btnSimpanText">Tambah Akun</span>
        </button>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toastNotification"
     class="fixed bottom-5 right-5 z-50 bg-gray-900 text-white text-xs font-medium px-4 py-3 rounded-xl shadow-lg flex items-center gap-2.5 translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
    <span id="toastMessage">Berhasil</span>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    const apiHost = window.location.hostname === '127.0.0.1' ? '127.0.0.1' : 'localhost';
    const BE_API_URL = `http://${apiHost}:8000/api/v1`;

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

        const res = await fetch(`${BE_API_URL}${path}`, {
            ...options,
            headers,
        });

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

    function initKaryawanPage() {
        let accounts = @json($employees ?? []);
        let currentPage = 1;
        const itemsPerPage = 5;
        let searchQuery = '';

        // DOM Elements
        const tableBody = document.getElementById('accountsTableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationButtons = document.getElementById('paginationButtons');
        const btnPrevPage = document.getElementById('btnPrevPage');
        const btnNextPage = document.getElementById('btnNextPage');
        const searchInput = document.getElementById('searchInput');

        const statTotalKaryawan = document.getElementById('statTotalKaryawan');
        const statAkunAktif = document.getElementById('statAkunAktif');
        const statPersenAktif = document.getElementById('statPersenAktif');

        // Drawer Elements
        const drawerBackdrop = document.getElementById('drawerBackdrop');
        const drawerPanel = document.getElementById('drawerPanel');
        const btnTambahAkun = document.getElementById('btnTambahAkun');
        const btnCloseDrawer = document.getElementById('btnCloseDrawer');
        const btnBatal = document.getElementById('btnBatal');
        const btnSimpan = document.getElementById('btnSimpan');
        const btnSimpanText = document.getElementById('btnSimpanText');

        const drawerTitle = document.getElementById('drawerTitle');
        const drawerHeaderIcon = document.getElementById('drawerHeaderIcon');
        const drawerErrorBox = document.getElementById('drawerErrorBox');
        const formMode = document.getElementById('formMode');
        const editAccountId = document.getElementById('editAccountId');
        const inputNama = document.getElementById('inputNama');
        const inputUsername = document.getElementById('inputUsername');
        const inputPassword = document.getElementById('inputPassword');
        const passwordLabel = document.getElementById('passwordLabel');

        // Password Toggle
        const btnTogglePassword = document.getElementById('btnTogglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        // Toast
        const toast = document.getElementById('toastNotification');
        const toastMsg = document.getElementById('toastMessage');
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
            if (!drawerErrorBox) return;
            if (!message) {
                drawerErrorBox.classList.add('hidden');
                drawerErrorBox.textContent = '';
                return;
            }
            drawerErrorBox.textContent = message;
            drawerErrorBox.classList.remove('hidden');
        }

        function setSubmitLoading(loading, mode) {
            if (!btnSimpan || !btnSimpanText) return;
            btnSimpan.disabled = loading;
            if (loading) {
                btnSimpanText.textContent = 'Menyimpan...';
            } else {
                btnSimpanText.textContent = mode === 'edit' ? 'Simpan Perubahan' : 'Tambah Akun';
            }
        }

        function updateStats() {
            const total = accounts.length;
            statTotalKaryawan.textContent = total;
            statAkunAktif.textContent = total;
            statPersenAktif.textContent = total > 0 ? '(100% Terdaftar)' : '(0%)';
        }

        function getFilteredAccounts() {
            const q = searchQuery.toLowerCase().trim();
            if (!q) return accounts;
            return accounts.filter(a =>
                String(a.username || '').toLowerCase().includes(q) ||
                String(a.nama || '').toLowerCase().includes(q)
            );
        }

        function renderTable() {
            const filtered = getFilteredAccounts();
            const totalItems = filtered.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
            const currentItems = filtered.slice(startIndex, endIndex);

            paginationInfo.textContent = `Menampilkan ${currentItems.length} dari ${totalItems} akun terdaftar`;

            if (currentItems.length === 0) {
                tableBody.innerHTML = `
                    <div class="py-12 text-center text-gray-400 text-xs">
                        ${searchQuery.trim() ? 'Tidak ada akun karyawan yang cocok dengan pencarian.' : 'Belum ada akun karyawan terdaftar di database.'}
                    </div>
                `;
            } else {
                tableBody.innerHTML = currentItems.map(acc => {
                    const displayNama = acc.nama || acc.username || '-';
                    const initial = String(displayNama).charAt(0) || 'K';
                    return `
                    <div class="flex items-center justify-between py-3 sm:py-3.5 px-3 sm:px-6 hover:bg-gray-50/70 transition group gap-2 sm:gap-3">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-50 text-[#147a46] flex items-center justify-center font-bold text-xs shrink-0 uppercase select-none">
                                ${escapeHtml(initial)}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13px] sm:text-[13.5px] font-semibold text-gray-800 truncate" title="${escapeHtml(displayNama)}">
                                    ${escapeHtml(displayNama)}
                                </p>
                                <p class="text-xs text-gray-500 truncate" title="${escapeHtml(acc.username)}">
                                    @${escapeHtml(acc.username)} • <span class="text-emerald-700 font-medium">${escapeHtml(acc.role || 'karyawan')}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-2 text-gray-400 shrink-0">
                            <button type="button"
                                    data-edit-id="${acc.id}"
                                    title="Edit Akun"
                                    class="btn-action-edit p-1.5 hover:bg-emerald-50 rounded-lg transition cursor-pointer active:scale-95">
                                <img src="/icons/icon_edit.png" alt="Edit" class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition pointer-events-none">
                            </button>
                            <button type="button"
                                    data-delete-id="${acc.id}"
                                    title="Hapus Akun"
                                    class="btn-action-delete p-1.5 hover:bg-rose-50 rounded-lg transition cursor-pointer active:scale-95">
                                <img src="/icons/icon_trash.png" alt="Hapus" class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition pointer-events-none">
                            </button>
                        </div>
                    </div>`;
                }).join('');
            }

            btnPrevPage.disabled = (currentPage === 1);
            btnNextPage.disabled = (currentPage === totalPages || totalPages === 0);

            paginationButtons.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = i;
                if (i === currentPage) {
                    btn.style.backgroundColor = '#0e5f38';
                    btn.className = 'w-7 h-7 flex items-center justify-center rounded-lg text-white text-xs font-semibold cursor-pointer';
                } else {
                    btn.className = 'w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 text-xs font-medium cursor-pointer transition';
                }
                btn.addEventListener('click', () => {
                    currentPage = i;
                    renderTable();
                });
                paginationButtons.appendChild(btn);
            }
        }

        async function fetchEmployeesFromApi() {
            try {
                const res = await apiRequest('/employees?per_page=100', { method: 'GET' });
                if (res?.ok && Array.isArray(res.json?.data)) {
                    accounts = res.json.data;
                    updateStats();
                    renderTable();
                }
            } catch (_) {
                // Pertahankan hasil SSR apabila fetch awal gagal.
            }
        }

        function openDrawer(mode, account = null) {
            formMode.value = mode;
            showDrawerError('');
            setSubmitLoading(false, mode);

            if (mode === 'tambah') {
                drawerTitle.textContent = 'Tambah Akun Karyawan';
                if (drawerHeaderIcon) {
                    drawerHeaderIcon.src = '/icons/icon_add_user.png';
                    drawerHeaderIcon.alt = 'Tambah Akun';
                }
                passwordLabel.innerHTML = 'Kata Sandi Awal <span class="text-rose-500">*</span>';
                inputPassword.required = true;
                inputPassword.placeholder = 'Minimal 6 karakter';
                btnSimpanText.textContent = 'Tambah Akun';

                editAccountId.value = '';
                inputNama.value = '';
                inputUsername.value = '';
                inputPassword.value = '';
            } else {
                drawerTitle.textContent = 'Edit Akun Karyawan';
                if (drawerHeaderIcon) {
                    drawerHeaderIcon.src = '/icons/icon_edit_user.png';
                    drawerHeaderIcon.alt = 'Edit Akun';
                }
                passwordLabel.innerHTML = 'Kata Sandi Baru <span class="text-gray-400 font-normal">(opsional)</span>';
                inputPassword.required = false;
                inputPassword.placeholder = 'Kosongkan jika tidak mengubah sandi';
                btnSimpanText.textContent = 'Simpan Perubahan';

                editAccountId.value = account.id;
                inputNama.value = account.nama || '';
                inputUsername.value = account.username || '';
                inputPassword.value = '';
            }

            drawerBackdrop.classList.add('open');
            drawerPanel.classList.add('open');
            setTimeout(() => inputNama.focus(), 250);
        }

        function closeDrawer() {
            drawerBackdrop.classList.remove('open');
            drawerPanel.classList.remove('open');
            showDrawerError('');
        }

        window.openDrawer = openDrawer;
        window.closeDrawer = closeDrawer;

        // Event Delegation for Table Actions
        tableBody.addEventListener('click', async function(e) {
            const editBtn = e.target.closest('.btn-action-edit');
            if (editBtn) {
                const id = parseInt(editBtn.dataset.editId, 10);
                const acc = accounts.find(a => Number(a.id) === id);
                if (acc) openDrawer('edit', acc);
                return;
            }

            const delBtn = e.target.closest('.btn-action-delete');
            if (delBtn) {
                const id = parseInt(delBtn.dataset.deleteId, 10);
                const acc = accounts.find(a => Number(a.id) === id);
                if (!acc || !confirm(`Apakah Anda yakin ingin menghapus akun @${acc.username} (${acc.nama}) dari database?`)) {
                    return;
                }

                try {
                    const res = await apiRequest(`/employees/${id}`, { method: 'DELETE' });
                    if (!res) return;
                    if (res.ok) {
                        await fetchEmployeesFromApi();
                        showToast(`Akun @${acc.username} berhasil dihapus`);
                    } else {
                        showToast(extractApiError(res.json, 'Gagal menghapus akun karyawan.'));
                    }
                } catch (_) {
                    showToast('Gagal terhubung ke Backend API (port 8000).');
                }
            }
        });

        // Event Listeners
        btnTambahAkun.addEventListener('click', () => openDrawer('tambah'));
        btnCloseDrawer.addEventListener('click', closeDrawer);
        btnBatal.addEventListener('click', closeDrawer);
        drawerBackdrop.addEventListener('click', closeDrawer);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDrawer();
        });

        // Form Submission (Create / Update via FastAPI BE)
        btnSimpan.addEventListener('click', async () => {
            const nama = inputNama.value.trim();
            const username = inputUsername.value.trim();
            const password = inputPassword.value;
            const mode = formMode.value;

            showDrawerError('');

            if (!nama || nama.length < 2) {
                showDrawerError('Nama lengkap minimal 2 karakter.');
                inputNama.focus();
                return;
            }
            if (!username || username.length < 3) {
                showDrawerError('Username minimal 3 karakter.');
                inputUsername.focus();
                return;
            }
            if (mode === 'tambah' && (!password || password.length < 6)) {
                showDrawerError('Kata sandi awal minimal 6 karakter.');
                inputPassword.focus();
                return;
            }
            if (mode === 'edit' && password && password.length < 6) {
                showDrawerError('Kata sandi baru minimal 6 karakter.');
                inputPassword.focus();
                return;
            }

            setSubmitLoading(true, mode);

            try {
                if (mode === 'tambah') {
                    const res = await apiRequest('/employees', {
                        method: 'POST',
                        body: JSON.stringify({
                            nama,
                            username,
                            password,
                            role: 'karyawan',
                        }),
                    });
                    if (!res) return;

                    if (res.ok) {
                        currentPage = 1;
                        await fetchEmployeesFromApi();
                        closeDrawer();
                        showToast(`Akun @${username} berhasil ditambahkan!`);
                    } else {
                        showDrawerError(extractApiError(res.json, 'Gagal menambahkan akun karyawan.'));
                    }
                } else {
                    const id = parseInt(editAccountId.value, 10);
                    const payload = { nama, username };
                    if (password) {
                        payload.password = password;
                    }

                    const res = await apiRequest(`/employees/${id}`, {
                        method: 'PUT',
                        body: JSON.stringify(payload),
                    });
                    if (!res) return;

                    if (res.ok) {
                        await fetchEmployeesFromApi();
                        closeDrawer();
                        showToast(`Perubahan akun @${username} berhasil disimpan!`);
                    } else {
                        showDrawerError(extractApiError(res.json, 'Gagal memperbarui data akun.'));
                    }
                }
            } catch (_) {
                showDrawerError('Tidak dapat terhubung ke server Backend API (port 8000).');
            } finally {
                setSubmitLoading(false, mode);
            }
        });

        // Toggle Password Visibility
        btnTogglePassword.addEventListener('click', () => {
            if (inputPassword.type === 'password') {
                inputPassword.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                inputPassword.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        });

        // Search input
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value;
            currentPage = 1;
            renderTable();
        });

        // Pagination navigation
        btnPrevPage.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        btnNextPage.addEventListener('click', () => {
            const totalPages = Math.ceil(getFilteredAccounts().length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Initial render + live sync from Backend API using cookie
        updateStats();
        renderTable();
        fetchEmployeesFromApi();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKaryawanPage);
    } else {
        initKaryawanPage();
    }
})();
</script>
@endpush
@endsection
