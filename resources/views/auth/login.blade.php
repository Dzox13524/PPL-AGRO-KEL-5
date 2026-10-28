<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke Hidara - Sistem cerdas pertanian hidroponik dan analitik budidaya selada presisi tinggi.">
    <title>Login — Hidara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-[#f0faf5] text-[#1a2e24] antialiased">

    {{-- ── Background Canvas ── --}}
    <div class="fixed inset-0 overflow-hidden z-0" aria-hidden="true">

        {{-- Grid --}}
        <div class="absolute inset-0"
             style="background-image: linear-gradient(rgba(26,158,95,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(26,158,95,0.04) 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        {{-- Center radial glow --}}
        <div class="absolute rounded-full pointer-events-none animate-[pulseGlow_5s_ease-in-out_infinite_alternate]"
             style="top:50%; left:50%; transform:translate(-50%,-50%); width:600px; height:600px;
                    background: radial-gradient(circle, rgba(26,158,95,0.18) 0%, rgba(26,158,95,0.08) 40%, transparent 70%);">
        </div>

        {{-- Blob 1 --}}
        <div class="absolute rounded-full opacity-45 animate-[floatBlob_12s_ease-in-out_infinite_alternate]"
             style="width:420px; height:420px; top:-100px; left:-120px; filter:blur(80px);
                    background: radial-gradient(circle, #a8e6c8, #c5f0db);">
        </div>

        {{-- Blob 2 --}}
        <div class="absolute rounded-full opacity-45 animation-delay-4 animate-[floatBlob_12s_ease-in-out_infinite_alternate]"
             style="width:320px; height:320px; bottom:-60px; right:-80px; filter:blur(80px);
                    background: radial-gradient(circle, #b8f0d5, #d4f7e8);">
        </div>

        {{-- Blob 3 --}}
        <div class="absolute rounded-full opacity-45 animation-delay-8 animate-[floatBlob_12s_ease-in-out_infinite_alternate]"
             style="width:200px; height:200px; top:40%; left:10%; filter:blur(80px);
                    background: radial-gradient(circle, #c8f5e2, #e0faf0);">
        </div>
    </div>

    {{-- ── Page ── --}}
    <main class="relative z-10 min-h-screen flex items-center justify-center px-6 py-8">
        <div class="w-full max-w-sm text-center animate-[cardEntrance_0.6s_cubic-bezier(0.34,1.56,0.64,1)_both]">

            {{-- Title --}}
            <h1 class="text-[26px] font-semibold tracking-tight text-[#1a2e24] mb-2">
                Login to <span class="text-primary font-bold">Hidara</span>
            </h1>
            <p class="text-[14px] text-gray-500 leading-relaxed max-w-70 mx-auto mb-7">
                Sistem cerdas pertanian hidroponik dan analitik budidaya selada presisi tinggi.
            </p>

            {{-- Step Tabs --}}
            <div id="stepTabs"
                 class="inline-flex items-center bg-white/70 backdrop-blur border border-[#d0e8db] rounded-full p-1 mb-5"
                 role="tablist" aria-label="Langkah login">

                <div id="tab-username"
                     class="step-tab flex items-center gap-1.5 px-4.5 py-1.5 rounded-full text-[14px] font-medium bg-[#1a9e5f] text-white! shadow-sm transition-all duration-200 select-none cursor-default"
                     role="tab" aria-selected="true" aria-controls="step-username">
                    <span class="w-1.5 h-1.5 rounded-full bg-white opacity-100"></span>
                    Username
                </div>

                <div id="tab-password"
                     class="step-tab flex items-center gap-1.5 px-4.5 py-1.5 rounded-full text-[14px] font-medium
                            text-[#7aab90] transition-all duration-200 select-none cursor-default"
                     role="tab" aria-selected="false" aria-controls="step-password">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                    Password
                </div>
            </div>

            {{-- Form Panel (transparent) --}}
            <div class="form-panel">
                <form id="loginForm" method="POST" action="/login" novalidate>
                    @csrf

                    {{-- ── STEP 1: Username ── --}}
                    <div class="step-content active" id="step-username" role="tabpanel" aria-labelledby="tab-username">

                        <div class="relative flex items-center">
                            {{-- User icon --}}
                            <span class="input-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-[#7aab90]
                                         flex items-center pointer-events-none transition-colors duration-200"
                                  aria-hidden="true">
                                <img src="/icons/icon_user.png" alt="User" class="w-4 h-4 object-contain opacity-60">
                            </span>

                            <input type="text" id="username" name="username"
                                   class="w-full h-12 pl-10.5 pr-13 bg-white border border-[#d0e8db] rounded-full
                                          text-[14px] text-[#1a2e24] placeholder-[#b0ccbe] outline-none
                                          transition-all duration-200
                                          focus:border-[#1a9e5f] focus:ring-3 focus:ring-[rgba(26,158,95,0.12)]"
                                   placeholder="Username" autocomplete="username" autofocus required
                                   aria-label="Masukkan username">

                            {{-- Next button --}}
                            <button type="button" id="btnNext"
                                    class="input-btn absolute right-1.5 top-1/2 -translate-y-1/2
                                           w-9 h-9 rounded-full border-none cursor-pointer flex items-center justify-center
                                           bg-linear-to-br from-[#1a9e5f] to-[#22c577]
                                           shadow-[0_2px_8px_rgba(26,158,95,0.25)]
                                           transition-all duration-200
                                           hover:scale-108 hover:shadow-[0_4px_14px_rgba(26,158,95,0.35)]
                                           active:scale-95"
                                    aria-label="Lanjut ke langkah berikutnya">
                                <span class="btn-arrow">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </span>
                                <span class="btn-spinner w-4 h-4 border-2 border-white/35 border-t-white rounded-full animate-[spin_0.7s_linear_infinite]"></span>
                            </button>
                        </div>

                        {{-- Error --}}
                        <div class="error-msg items-center gap-1.5 mt-2.5 px-3 py-2
                                    bg-[#fff3f3] border border-[#ffd0d0] rounded-lg text-[12.5px] text-[#c94040]"
                             id="usernameError" role="alert">
                            <svg class="shrink-0" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span id="usernameErrorText">Username tidak boleh kosong.</span>
                        </div>

                        {{-- Hint --}}
                        <p class="flex items-center justify-center gap-1.5 mt-3 text-[13px] text-[#7aab90]">
                            Tekan
                            <kbd class="inline-flex items-center gap-1 px-2 py-0.5
                                        bg-white/90 border border-[#d0e8db] rounded-md text-[11.5px] font-semibold
                                        text-[#4a7a62] shadow-sm font-sans">
                                Enter
                                <img src="/icons/icon_enter.png" alt="enter" class="w-3.5 h-3.5 object-contain opacity-75">
                            </kbd>
                            atau panah untuk lanjut
                        </p>
                    </div>

                    {{-- ── STEP 2: Password ── --}}
                    <div class="step-content" id="step-password" role="tabpanel" aria-labelledby="tab-password">

                        {{-- Username badge --}}
                        <div id="usernameBadge"
                             class="inline-flex items-center gap-2 px-4 py-2 mb-[18px]
                                    bg-[#e8f8f1] border border-[#b2dfc9] rounded-full
                                    text-[13.5px] font-medium text-[#157a4a]"
                             aria-live="polite">
                            <img src="/icons/icon_user.png" alt="User" class="w-3.5 h-3.5 object-contain opacity-75">
                            <span id="usernameBadgeText">username</span>
                        </div>

                        <div class="relative flex items-center">
                            {{-- Lock icon --}}
                            <span class="input-icon absolute left-[14px] top-1/2 -translate-y-1/2 text-[#7aab90]
                                         flex items-center pointer-events-none transition-colors duration-200"
                                  aria-hidden="true">
                                <img src="/icons/icon_lock.png" alt="Lock" class="w-4 h-4 object-contain opacity-60">
                            </span>

                            <input type="password" id="password" name="password"
                                   class="w-full h-12 pl-[42px] pr-[88px] bg-white border border-[#d0e8db] rounded-full
                                          text-[14px] text-[#1a2e24] placeholder-[#b0ccbe] outline-none
                                          transition-all duration-200
                                          focus:border-[#1a9e5f] focus:ring-3 focus:ring-[rgba(26,158,95,0.12)]"
                                   placeholder="Password" autocomplete="current-password" required
                                   aria-label="Masukkan password">

                            {{-- Toggle password visibility --}}
                            <button type="button" id="togglePw"
                                    class="absolute right-[50px] top-1/2 -translate-y-1/2
                                           bg-transparent border-none cursor-pointer
                                           text-[#7aab90] flex items-center p-1 rounded
                                           transition-colors duration-200 hover:text-[#1a9e5f]"
                                    aria-label="Tampilkan password">
                                <svg id="eyeIcon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>

                            {{-- Login button --}}
                            <button type="submit" id="btnLogin"
                                    class="input-btn absolute right-1.5 top-1/2 -translate-y-1/2
                                           w-9 h-9 rounded-full border-none cursor-pointer flex items-center justify-center
                                           bg-gradient-to-br from-[#1a9e5f] to-[#22c577]
                                           shadow-[0_2px_8px_rgba(26,158,95,0.25)]
                                           transition-all duration-200
                                           hover:scale-108 hover:shadow-[0_4px_14px_rgba(26,158,95,0.35)]
                                           active:scale-95"
                                    aria-label="Masuk ke Hidara">
                                <span class="btn-arrow">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </span>
                                <span class="btn-spinner w-4 h-4 border-2 border-white/35 border-t-white rounded-full animate-[spin_0.7s_linear_infinite]"></span>
                            </button>
                        </div>

                        {{-- Error --}}
                        <div class="error-msg items-center gap-1.5 mt-2.5 px-3 py-2
                                    bg-[#fff3f3] border border-[#ffd0d0] rounded-lg text-[12.5px] text-[#c94040]"
                             id="passwordError" role="alert">
                            <svg class="shrink-0" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span id="passwordErrorText">Password tidak boleh kosong.</span>
                        </div>

                        {{-- Hint --}}
                        <p class="flex items-center justify-center gap-1.5 mt-3 text-[13px] text-[#7aab90]">
                            Tekan
                            <kbd class="inline-flex items-center gap-1 px-2 py-0.5
                                        bg-white/90 border border-[#d0e8db] rounded-md text-[11.5px] font-semibold
                                        text-[#4a7a62] shadow-sm font-sans">
                                Enter
                                <img src="/icons/icon_enter.png" alt="enter" class="w-3.5 h-3.5 object-contain opacity-75">
                            </kbd>
                            untuk masuk
                        </p>

                        {{-- Back --}}
                        <button type="button" id="btnBack"
                                class="inline-flex items-center gap-1.5 mt-3.5 text-[13px]
                                       text-[#7aab90] bg-transparent border-none cursor-pointer font-sans
                                       transition-colors duration-200 hover:text-[#1a9e5f]"
                                aria-label="Kembali ke langkah username">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Kembali
                        </button>
                    </div>

                    {{-- Success Overlay --}}
                    <div class="success-overlay flex-col items-center justify-center py-5"
                         id="successOverlay" role="status" aria-live="assertive">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3.5
                                    bg-gradient-to-br from-[#1a9e5f] to-[#22c577]
                                    shadow-[0_8px_24px_rgba(26,158,95,0.35)]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <p class="text-[15px] font-semibold text-[#1a2e24]">Berhasil masuk!</p>
                        <p class="text-[12.5px] text-[#7aab90] mt-1">Mengarahkan ke dashboard…</p>
                    </div>

                </form>
            </div>

        </div>
    </main>

    <script>
    (function () {
        'use strict';

        const usernameInput    = document.getElementById('username');
        const passwordInput    = document.getElementById('password');
        const btnNext          = document.getElementById('btnNext');
        const btnBack          = document.getElementById('btnBack');
        const btnLogin         = document.getElementById('btnLogin');
        const togglePw         = document.getElementById('togglePw');
        const eyeIcon          = document.getElementById('eyeIcon');
        const stepUsername     = document.getElementById('step-username');
        const stepPassword     = document.getElementById('step-password');
        const tabUsername      = document.getElementById('tab-username');
        const tabPassword      = document.getElementById('tab-password');
        const usernameBadgeTxt = document.getElementById('usernameBadgeText');
        const usernameError    = document.getElementById('usernameError');
        const usernameErrTxt   = document.getElementById('usernameErrorText');
        const passwordError    = document.getElementById('passwordError');
        const passwordErrTxt   = document.getElementById('passwordErrorText');
        const loginForm        = document.getElementById('loginForm');
        const successOverlay   = document.getElementById('successOverlay');

        let currentStep = 1;

        /* ── Tab styling helpers ── */
        const TAB_ACTIVE    = ['bg-[#1a9e5f]','!text-white','shadow-sm'];
        const TAB_COMPLETED = ['text-[#1a9e5f]'];
        const TAB_INACTIVE  = ['text-[#7aab90]'];

        function setTabActive(tab) {
            tab.classList.remove(...TAB_INACTIVE, ...TAB_COMPLETED);
            tab.classList.add(...TAB_ACTIVE);
            tab.setAttribute('aria-selected','true');
            tab.querySelector('.w-1\\.5')?.classList.replace('opacity-60','opacity-100');
        }
        function setTabCompleted(tab) {
            tab.classList.remove(...TAB_ACTIVE);
            tab.classList.add(...TAB_COMPLETED);
            tab.setAttribute('aria-selected','false');
        }
        function setTabInactive(tab) {
            tab.classList.remove(...TAB_ACTIVE, ...TAB_COMPLETED);
            tab.classList.add(...TAB_INACTIVE);
            tab.setAttribute('aria-selected','false');
        }

        /* ── Utilities ── */
        function showError(el, txtEl, msg) {
            txtEl.textContent = msg;
            el.classList.add('show');
            clearTimeout(el._t);
            el._t = setTimeout(() => el.classList.remove('show'), 4000);
        }
        function hideError(el) { el.classList.remove('show'); }
        function setLoading(btn, on) { btn.classList.toggle('loading', on); btn.disabled = on; }

        /* ── Step transitions ── */
        function goToStep2() {
            const val = usernameInput.value.trim();
            if (!val)        { showError(usernameError, usernameErrTxt, 'Username tidak boleh kosong.'); usernameInput.focus(); return; }
            if (val.length < 3) { showError(usernameError, usernameErrTxt, 'Username minimal 3 karakter.'); usernameInput.focus(); return; }

            hideError(usernameError);
            setLoading(btnNext, true);

            setTimeout(() => {
                setLoading(btnNext, false);
                currentStep = 2;
                usernameBadgeTxt.textContent = val;

                stepUsername.classList.remove('active');
                stepPassword.classList.add('active','slide-back');
                setTimeout(() => stepPassword.classList.remove('slide-back'), 400);

                setTabCompleted(tabUsername);
                setTabActive(tabPassword);

                setTimeout(() => passwordInput.focus(), 50);
            }, 380);
        }

        function goToStep1() {
            currentStep = 1;
            hideError(passwordError);

            stepPassword.classList.remove('active');
            stepUsername.classList.add('active');

            setTabActive(tabUsername);
            setTabInactive(tabPassword);

            setTimeout(() => usernameInput.focus(), 50);
        }

        /* ── Login ── */
        function doLogin() {
            const pw = passwordInput.value;
            if (!pw) { showError(passwordError, passwordErrTxt, 'Password tidak boleh kosong.'); passwordInput.focus(); return; }

            hideError(passwordError);
            setLoading(btnLogin, true);

            const formData = new FormData(loginForm);

            fetch(loginForm.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': formData.get('_token') || '', 'Accept': 'application/json' },
                body: formData,
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.redirect) {
                    stepPassword.classList.remove('active');
                    successOverlay.classList.add('show');
                    setTimeout(() => { window.location.href = data.redirect; }, 1200);
                } else {
                    setLoading(btnLogin, false);
                    showError(passwordError, passwordErrTxt, data.message || 'Username atau password salah.');
                    passwordInput.focus(); passwordInput.select();
                }
            })
            .catch(() => {
                setLoading(btnLogin, false);
                showError(passwordError, passwordErrTxt, 'Terjadi kesalahan jaringan. Coba lagi.');
            });
        }

        /* ── Events ── */
        btnNext.addEventListener('click', goToStep2);
        btnBack.addEventListener('click', goToStep1);

        togglePw.addEventListener('click', () => {
            const hidden = passwordInput.type === 'password';
            passwordInput.type = hidden ? 'text' : 'password';
            togglePw.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
            eyeIcon.innerHTML = hidden
                ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
                : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        });

        usernameInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === 'ArrowRight') { e.preventDefault(); goToStep2(); }
        });
        passwordInput.addEventListener('keydown', e => {
            if (e.key === 'Enter')     { e.preventDefault(); doLogin(); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); goToStep1(); }
        });
        loginForm.addEventListener('submit', e => { e.preventDefault(); currentStep === 1 ? goToStep2() : doLogin(); });

        usernameInput.addEventListener('input', () => hideError(usernameError));
        passwordInput.addEventListener('input', () => hideError(passwordError));

        usernameInput.focus();
    }());
    </script>
</body>
</html>