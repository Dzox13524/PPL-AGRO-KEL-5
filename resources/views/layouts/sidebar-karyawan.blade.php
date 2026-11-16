@props(['active' => 'tanaman'])

<aside id="mainSidebar"
       class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white border-r border-gray-100 flex flex-col justify-between shrink-0 h-full lg:h-screen lg:static lg:w-64 lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out select-none shadow-2xl lg:shadow-none">
    {{-- Top Section --}}
    <div class="p-6">
        {{-- Logo Brand & Mobile Close Button --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-[17px] font-bold text-gray-900 tracking-tight leading-tight">Hidara</h1>
                    <p class="text-[9.5px] font-bold tracking-widest text-[#147a46] uppercase">SMART FARM</p>
                </div>
            </div>

            <button type="button"
                    id="sidebarCloseBtn"
                    class="lg:hidden p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition cursor-pointer"
                    aria-label="Tutup navigasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Decorative Pill Bar --}}
        <div class="w-full h-2.5 bg-[#eaf1f8] rounded-full my-5" aria-hidden="true"></div>

        {{-- Navigation Menu --}}
        <nav class="space-y-1">
            {{-- 1. Manajemen Tanaman --}}
            <a href="{{ route('karyawan.tanaman') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] transition-all duration-150 {{ $active === 'tanaman' ? 'bg-[#e8f8f1] text-[#147a46] font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-normal' }}">
                <img src="/icons/icon_potted_plant.png" alt="Manajemen Tanaman" class="w-5 h-5 object-contain {{ $active === 'tanaman' ? '' : 'opacity-60' }}">
                <span>Manajemen Tanaman</span>
            </a>

            {{-- 2. Log Pertumbuhan --}}
            <a href="{{ route('karyawan.monitoring') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] transition-all duration-150 {{ $active === 'monitoring' ? 'bg-[#e8f8f1] text-[#147a46] font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-normal' }}">
                <img src="/icons/icon_growth_plant.png" alt="Monitoring dan Log Pertumbuhan" class="w-5 h-5 object-contain {{ $active === 'monitoring' ? '' : 'opacity-60' }}">
                <span>Log Pertumbuhan</span>
            </a>

            {{-- 3. Prediksi Pertumbuhan --}}
            <a href="{{ route('karyawan.prediksi') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] transition-all duration-150 {{ $active === 'prediksi' ? 'bg-[#e8f8f1] text-[#147a46] font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-normal' }}">
                <img src="/icons/icon_positive_dynamic.png" alt="Prediksi Yield & Biomassa" class="w-5 h-5 object-contain {{ $active === 'prediksi' ? '' : 'opacity-60' }}">
                <span>Prediksi Pertumbuhan</span>
            </a>

            {{-- 4. Diagnosis Penyakit --}}
            <a href="{{ route('karyawan.diagnosis') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] transition-all duration-150 {{ $active === 'diagnosis' ? 'bg-[#e8f8f1] text-[#147a46] font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-normal' }}">
                <img src="/icons/icon_diagnosis.png" alt="Diagnosis Penyakit (SVM)" class="w-5 h-5 object-contain {{ $active === 'diagnosis' ? '' : 'opacity-60' }}">
                <span>Diagnosis Penyakit</span>
            </a>
        </nav>
    </div>

    {{-- Bottom Section --}}
    <div class="p-4 space-y-2.5">
        {{-- Logout Button / Link --}}
        <form method="POST" action="/logout" id="logoutFormKaryawan">
            @csrf
            <button type="button"
                    onclick="if(confirm('Apakah Anda yakin ingin keluar?')) { document.getElementById('logoutFormKaryawan').submit(); }"
                    class="w-full bg-[#fff5f5] hover:bg-[#ffe3e3] border border-[#fed7d7] text-[#c53030] rounded-xl p-3 flex items-center justify-between text-[12.5px] font-medium transition cursor-pointer active:scale-98">
                <div class="flex items-center gap-2.5">
                    <img src="/icons/icon_logout.png" alt="Logout" class="w-4 h-4 object-contain" style="filter: invert(24%) sepia(82%) saturate(2250%) hue-rotate(342deg) brightness(85%) contrast(93%);">
                    <span>Logout</span>
                </div>
            </button>
        </form>
    </div>
</aside>
