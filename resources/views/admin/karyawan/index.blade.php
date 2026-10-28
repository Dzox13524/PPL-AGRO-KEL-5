@extends('layouts.admin', ['active' => 'karyawan'])

@section('title', 'Manajemen Akun Karyawan — Hidara')

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

    /* Custom Toggle Switch */
    .switch-checkbox:checked ~ .switch-track {
        background-color: #0e5f38 !important;
    }
    .switch-checkbox:checked ~ .switch-track .switch-thumb {
        transform: translateX(20px);
    }
</style>

<div class="max-w-[1400px] mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Akun Karyawan</h1>
            <p class="text-[13px] text-gray-500 mt-1 max-w-2xl leading-relaxed">
                Kelola kredensial akses, hak peran teknisi, dan status aktif staf operasional farm dengan standar keamanan biometrik IoT &amp; role-based control.
            </p>
        </div>
        <div class="shrink-0">
            <button type="button"
                    id="btnTambahAkun"
                    onclick="openDrawer('tambah')"
                    style="background-color: #0e5f38;"
                    class="inline-flex items-center gap-2 hover:opacity-95 text-white text-[13px] font-semibold px-4 py-2.5 rounded-xl shadow-sm transition active:scale-95 cursor-pointer">
                <img src="/icons/icon_add_user.png" alt="Tambah Akun" class="w-4 h-4 object-contain invert brightness-0">
                <span>Tambah Akun</span>
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
        {{-- Card 1: Total Karyawan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">TOTAL KARYAWAN</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statTotalKaryawan" class="text-3xl font-bold text-gray-900 tracking-tight">8</span>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">Staf terdaftar di Node Cisarua</p>
        </div>

        {{-- Card 2: Akun Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 hover:shadow-sm transition">
            <p class="text-[11px] font-semibold tracking-wider text-gray-400 uppercase">AKUN AKTIF</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span id="statAkunAktif" class="text-3xl font-bold text-[#147a46] tracking-tight">8</span>
                <span id="statPersenAktif" class="text-xs text-gray-500 font-medium">(100% Aktif)</span>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">Semua akun terverifikasi 2FA</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 space-y-4">
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
                   placeholder="Cari karyawan atau ID staf..."
                   class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-11 pr-4 py-3 rounded-xl transition-all outline-none">
        </div>

        {{-- Tabel List Akun --}}
        <div class="overflow-hidden">
            {{-- Header Tabel --}}
            <div class="bg-[#f8fafc] text-gray-500 font-semibold text-[11px] tracking-wider uppercase rounded-xl py-3 px-6 flex justify-between items-center">
                <span>USERNAME</span>
                <span class="pr-6">TINDAKAN</span>
            </div>

            {{-- Container Baris Akun --}}
            <div id="accountsTableBody" class="divide-y divide-gray-50 mt-1">
                {{-- Di-render secara dinamis oleh JavaScript --}}
            </div>
        </div>

        {{-- Footer Tabel & Pagination --}}
        <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <span id="paginationInfo" class="text-gray-500">
                Menampilkan 5 dari 8 akun terdaftar
            </span>

            <div class="flex items-center gap-1.5 self-end sm:self-auto">
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
    <div class="flex items-center justify-between p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <img id="drawerHeaderIcon" src="/icons/icon_add_user.png" alt="Tambah Akun" class="w-5 h-5 object-contain">
            </div>
            <div>
                <h2 id="drawerTitle" class="text-base font-bold text-gray-900 tracking-tight">
                    Tambah Akun Karyawan
                </h2>
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
    <form id="accountForm" class="flex-1 overflow-y-auto p-6 space-y-5" onsubmit="return false;">
        <input type="hidden" id="formMode" value="tambah">
        <input type="hidden" id="editAccountId" value="">

        {{-- Input 1: Username --}}
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
                       required
                       placeholder="Masukkan username baru"
                       class="w-full bg-[#f1f5f9]/70 hover:bg-[#f1f5f9] focus:bg-white border border-transparent focus:border-emerald-500 text-[13.5px] text-gray-800 placeholder-gray-400 pl-10 pr-4 py-2.5 rounded-xl transition-all outline-none">
            </div>
        </div>

        {{-- Input 2: Kata Sandi --}}
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
                       placeholder="Minimal 8 karakter unik..."
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

        {{-- Input 3: Status Akun Aktif (Toggle Switch) --}}
        <div class="bg-[#f8fafc] border border-gray-100 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-[13px] font-semibold text-gray-900">Status Akun Aktif</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer select-none">
                <input type="checkbox" id="inputStatusAktif" class="sr-only switch-checkbox" checked>
                <div class="switch-track w-11 h-6 bg-gray-200 rounded-full p-0.5 transition-colors duration-200 ease-in-out relative">
                    <div class="switch-thumb w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-200 ease-in-out"></div>
                </div>
            </label>
        </div>
    </form>

    {{-- Drawer Footer: Aksi Batal & Simpan --}}
    <div class="p-6 border-t border-gray-100 flex items-center justify-end gap-3 bg-white">
        <button type="button"
                id="btnBatal"
                onclick="closeDrawer()"
                class="px-5 py-2.5 rounded-xl text-[13px] font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition cursor-pointer">
            Batal
        </button>
        <button type="button"
                id="btnSimpan"
                style="background-color: #0e5f38;"
                class="px-5 py-2.5 rounded-xl text-[13px] font-semibold text-white hover:opacity-95 shadow-sm transition active:scale-95 cursor-pointer">
            <span id="btnSimpanText">Tambah Akun</span>
        </button>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toastNotification"
     class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 flex items-center gap-3 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-lg pointer-events-none">
    <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <span id="toastMessage">Operasi berhasil</span>
</div>

@push('scripts')
<script>
(function() {
    function initKaryawanPage() {
        // Data State Awal (Static UI Sesuai Gambar)
        let accounts = [
            { id: 1, username: 'agus.santoso@gm200.farm', active: true },
            { id: 2, username: 'rian.nutrisi@gm200.farm', active: true },
            { id: 3, username: 'dewi.harvest@gm200.farm', active: true },
            { id: 4, username: 'budi.gh1@gm200.farm', active: true },
            { id: 5, username: 'siti.qc@gm200.farm', active: true },
            { id: 6, username: 'ahmad.teknisi@gm200.farm', active: true },
            { id: 7, username: 'ratna.irigasi@gm200.farm', active: true },
            { id: 8, username: 'fajar.iot@gm200.farm', active: true },
        ];

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
        const formMode = document.getElementById('formMode');
        const editAccountId = document.getElementById('editAccountId');
        const inputUsername = document.getElementById('inputUsername');
        const inputPassword = document.getElementById('inputPassword');
        const inputStatusAktif = document.getElementById('inputStatusAktif');
        const passwordLabel = document.getElementById('passwordLabel');

        // Password Toggle
        const btnTogglePassword = document.getElementById('btnTogglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        // Toast
        const toast = document.getElementById('toastNotification');
        const toastMsg = document.getElementById('toastMessage');
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

        // Render Statistics & Table
        function updateStats() {
            const total = accounts.length;
            const activeCount = accounts.filter(a => a.active).length;
            const percentage = total > 0 ? Math.round((activeCount / total) * 100) : 0;

            statTotalKaryawan.textContent = total;
            statAkunAktif.textContent = activeCount;
            statPersenAktif.textContent = `(${percentage}% Aktif)`;
        }

        function getFilteredAccounts() {
            if (!searchQuery.trim()) return accounts;
            const q = searchQuery.toLowerCase().trim();
            return accounts.filter(a => a.username.toLowerCase().includes(q));
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

            // Update info text
            paginationInfo.textContent = `Menampilkan ${currentItems.length} dari ${totalItems} akun terdaftar`;

            // Render rows
            if (currentItems.length === 0) {
                tableBody.innerHTML = `
                    <div class="py-12 text-center text-gray-400 text-xs">
                        Tidak ada akun karyawan yang cocok dengan pencarian.
                    </div>
                `;
            } else {
                tableBody.innerHTML = currentItems.map(acc => `
                    <div class="flex items-center justify-between py-4 px-6 hover:bg-gray-50/70 transition group">
                        <div class="flex items-center gap-3">
                            <span class="text-[13.5px] font-medium ${acc.active ? 'text-gray-800' : 'text-gray-400 line-through'}">
                                ${escapeHtml(acc.username)}
                            </span>
                            ${!acc.active ? '<span class="text-[10px] font-semibold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-md">Nonaktif</span>' : ''}
                        </div>
                        <div class="flex items-center gap-2.5 text-gray-400">
                            {{-- Tombol Edit --}}
                            <button type="button"
                                    data-edit-id="${acc.id}"
                                    title="Edit Akun"
                                    class="btn-action-edit p-1.5 hover:bg-emerald-50 rounded-lg transition cursor-pointer">
                                <img src="/icons/icon_edit.png" alt="Edit" class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition pointer-events-none">
                            </button>

                            {{-- Tombol Kunci / Toggle Status --}}
                            <button type="button"
                                    data-lock-id="${acc.id}"
                                    title="${acc.active ? 'Kunci / Nonaktifkan Akun' : 'Aktifkan Akun'}"
                                    class="btn-action-lock p-1.5 ${acc.active ? 'hover:bg-amber-50' : 'bg-amber-50'} rounded-lg transition cursor-pointer">
                                <img src="/icons/icon_lock.png" alt="Kunci" class="w-4 h-4 object-contain ${acc.active ? 'opacity-60 hover:opacity-100' : 'opacity-100'} transition pointer-events-none">
                            </button>

                            {{-- Tombol Hapus --}}
                            <button type="button"
                                    data-delete-id="${acc.id}"
                                    title="Hapus Akun"
                                    class="btn-action-delete p-1.5 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                <img src="/icons/icon_trash.png" alt="Hapus" class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition pointer-events-none">
                            </button>
                        </div>
                    </div>
                `).join('');
            }

            // Render Pagination Controls
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

        // Drawer Open / Close Functions
        function openDrawer(mode, account = null) {
            formMode.value = mode;

            if (mode === 'tambah') {
                drawerTitle.textContent = 'Tambah Akun Karyawan';
                if (drawerHeaderIcon) {
                    drawerHeaderIcon.src = '/icons/icon_add_user.png';
                    drawerHeaderIcon.alt = 'Tambah Akun';
                }
                passwordLabel.innerHTML = 'Kata Sandi Awal <span class="text-rose-500">*</span>';
                inputPassword.required = true;
                inputPassword.placeholder = 'Minimal 8 karakter unik...';
                btnSimpanText.textContent = 'Tambah Akun';

                editAccountId.value = '';
                inputUsername.value = '';
                inputPassword.value = '';
                inputStatusAktif.checked = true;
            } else {
                drawerTitle.textContent = 'Edit Akun Karyawan';
                if (drawerHeaderIcon) {
                    drawerHeaderIcon.src = '/icons/icon_edit_user.png';
                    drawerHeaderIcon.alt = 'Edit Akun';
                }
                passwordLabel.innerHTML = 'Kata Sandi Baru';
                inputPassword.required = false;
                inputPassword.placeholder = 'Kosongkan jika tidak diubah...';
                btnSimpanText.textContent = 'Simpan Perubahan';

                editAccountId.value = account.id;
                inputUsername.value = account.username;
                inputPassword.value = '';
                inputStatusAktif.checked = account.active;
            }

            // Show Drawer (Slide from right)
            drawerBackdrop.classList.add('open');
            drawerPanel.classList.add('open');

            setTimeout(() => inputUsername.focus(), 250);
        }

        function closeDrawer() {
            drawerBackdrop.classList.remove('open');
            drawerPanel.classList.remove('open');
        }

        window.openDrawer = openDrawer;
        window.closeDrawer = closeDrawer;

        // Event Delegation for Table Actions
        tableBody.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-action-edit');
            if (editBtn) {
                const id = parseInt(editBtn.dataset.editId, 10);
                const acc = accounts.find(a => a.id === id);
                if (acc) openDrawer('edit', acc);
                return;
            }

            const lockBtn = e.target.closest('.btn-action-lock');
            if (lockBtn) {
                const id = parseInt(lockBtn.dataset.lockId, 10);
                const acc = accounts.find(a => a.id === id);
                if (acc) {
                    acc.active = !acc.active;
                    updateStats();
                    renderTable();
                    showToast(acc.active ? `Akun ${acc.username} telah diaktifkan` : `Akun ${acc.username} dinonaktifkan`);
                }
                return;
            }

            const delBtn = e.target.closest('.btn-action-delete');
            if (delBtn) {
                const id = parseInt(delBtn.dataset.deleteId, 10);
                const acc = accounts.find(a => a.id === id);
                if (acc && confirm(`Apakah Anda yakin ingin menghapus akun ${acc.username}?`)) {
                    accounts = accounts.filter(a => a.id !== id);
                    updateStats();
                    renderTable();
                    showToast(`Akun ${acc.username} berhasil dihapus`);
                }
                return;
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

        // Form Submission
        btnSimpan.addEventListener('click', () => {
            const username = inputUsername.value.trim();
            const password = inputPassword.value;
            const isActive = inputStatusAktif.checked;
            const mode = formMode.value;

            if (!username) {
                alert('Username wajib diisi!');
                inputUsername.focus();
                return;
            }

            if (mode === 'tambah') {
                if (!password || password.length < 6) {
                    alert('Kata sandi awal minimal 6 karakter!');
                    inputPassword.focus();
                    return;
                }

                const newId = accounts.length > 0 ? Math.max(...accounts.map(a => a.id)) + 1 : 1;
                accounts.unshift({
                    id: newId,
                    username: username,
                    active: isActive
                });

                updateStats();
                currentPage = 1;
                renderTable();
                closeDrawer();
                showToast('Akun karyawan baru berhasil ditambahkan!');
            } else {
                const id = parseInt(editAccountId.value, 10);
                const acc = accounts.find(a => a.id === id);
                if (acc) {
                    acc.username = username;
                    acc.active = isActive;
                    updateStats();
                    renderTable();
                    closeDrawer();
                    showToast('Perubahan data akun berhasil disimpan!');
                }
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
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        }

        // Initial render
        updateStats();
        renderTable();
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