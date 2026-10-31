<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IC-Labs Repository — Sistem Arsip Dokumen & Evaluasi Laboratorium Digital">
    <title>@yield('title', 'Dashboard') — IC-Labs Repository</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        :root { --emerald: #10B981; --mint: #34D399; --teal: #0D9488; --surface: #0F1713; --border: #1A2820; }
        .sidebar-active { background: rgba(16,185,129,.12) !important; color: #10B981 !important; border-right: 2px solid #10B981; }
        .sidebar-active svg { color: #10B981 !important; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #1A2820; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #2A3830; }
        @keyframes fadeSlideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .flash-msg { animation: fadeSlideDown .3s ease forwards; }
        @keyframes glow-pulse { 0%,100% { box-shadow:0 0 14px rgba(16,185,129,.3); } 50% { box-shadow:0 0 24px rgba(16,185,129,.55); } }
        .logo-icon { animation: glow-pulse 3s ease-in-out infinite; }

        /* ─── Cyber Loading Spinner Animations ("Wah" & Futuristik) ─── */
        @keyframes cyberSpinCW {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes cyberSpinCCW {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }
        @keyframes cyberCorePulse {
            0%, 100% { transform: scale(0.92); opacity: 0.85; filter: drop-shadow(0 0 8px rgba(16,185,129,0.4)); }
            50% { transform: scale(1.08); opacity: 1; filter: drop-shadow(0 0 18px rgba(16,185,129,0.85)); }
        }
        @keyframes cyberShimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        .cyber-spin-cw {
            animation: cyberSpinCW 2s cubic-bezier(0.4, 0.1, 0.2, 1) infinite;
        }
        .cyber-spin-ccw {
            animation: cyberSpinCCW 2.6s linear infinite;
        }
        .cyber-spin-fast {
            animation: cyberSpinCW 1.1s ease-in-out infinite;
        }
        .cyber-core-pulse {
            animation: cyberCorePulse 1.8s ease-in-out infinite;
        }
        .cyber-loading-shimmer-bar {
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, #10B981, #34D399, transparent);
            animation: cyberShimmer 1.4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* ─── SweetAlert2 Cyber Theme Customization ─── */
        .cyber-swal-popup {
            background: rgba(15, 23, 19, 0.96) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border-radius: 24px !important;
            padding: 26px 28px !important;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(16, 185, 129, 0.15) !important;
        }
        .cyber-swal-popup.cyber-swal-success {
            border: 1px solid rgba(16, 185, 129, 0.45) !important;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(16, 185, 129, 0.22) !important;
        }
        .cyber-swal-popup.cyber-swal-error {
            border: 1px solid rgba(239, 68, 68, 0.45) !important;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(239, 68, 68, 0.22) !important;
        }
        .cyber-swal-popup.cyber-swal-warning {
            border: 1px solid rgba(245, 158, 11, 0.45) !important;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(245, 158, 11, 0.22) !important;
        }
        .cyber-swal-popup.cyber-swal-info {
            border: 1px solid rgba(14, 165, 233, 0.45) !important;
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(14, 165, 233, 0.22) !important;
        }
        .cyber-swal-title {
            font-family: 'Chakra Petch', 'Inter', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1.25rem !important;
            color: #FFFFFF !important;
            letter-spacing: -0.01em !important;
            margin-bottom: 6px !important;
        }
        .cyber-swal-text {
            font-family: 'Inter', sans-serif !important;
            font-size: 0.9rem !important;
            line-height: 1.55 !important;
            color: #D1D5DB !important;
        }
        .cyber-swal-btn {
            border-radius: 12px !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 10px 24px !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }
        .cyber-swal-btn-success {
            background: linear-gradient(135deg, #10B981, #0D9488) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 18px rgba(16, 185, 129, 0.35) !important;
        }
        .cyber-swal-btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(16, 185, 129, 0.5) !important;
        }
        .cyber-swal-btn-error {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 18px rgba(239, 68, 68, 0.35) !important;
        }
        .cyber-swal-btn-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 18px rgba(245, 158, 11, 0.35) !important;
        }
        .cyber-swal-btn-info {
            background: linear-gradient(135deg, #0EA5E9, #0284C7) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 18px rgba(14, 165, 233, 0.35) !important;
        }
        .cyber-swal-btn-cancel {
            background: #1A2820 !important;
            color: #9CA3AF !important;
            border: 1px solid #2A3830 !important;
        }
        .cyber-swal-btn-cancel:hover {
            background: #25382D !important;
            color: #F3F4F6 !important;
        }

        /* Fix SweetAlert icon backgrounds for Dark Mode */
        .swal2-icon.swal2-success {
            border-color: #10B981 !important;
            color: #10B981 !important;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.3) !important;
        }
        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #10B981 !important;
        }
        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }
        .swal2-icon.swal2-success .swal2-success-fix,
        .swal2-icon.swal2-success .swal2-success-circular-line-left,
        .swal2-icon.swal2-success .swal2-success-circular-line-right {
            background-color: transparent !important;
        }
        .swal2-icon.swal2-error {
            border-color: #EF4444 !important;
            color: #EF4444 !important;
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.3) !important;
        }
        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #EF4444 !important;
        }
        .swal2-icon.swal2-warning {
            border-color: #F59E0B !important;
            color: #F59E0B !important;
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.3) !important;
        }
        .swal2-icon.swal2-info {
            border-color: #0EA5E9 !important;
            color: #0EA5E9 !important;
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.3) !important;
        }
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #10B981, #34D399) !important;
            height: 3px !important;
        }

        /* Global Custom Themed Dropdowns for Admin */
        select:not([multiple]) {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: #090D0B;
            border-color: #1A2820;
            color: #E5E7EB;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2310B981' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem 1rem;
            padding-right: 2.5rem !important;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        select:not([multiple]):hover {
            border-color: rgba(16, 185, 129, 0.45) !important;
        }
        select:not([multiple]):focus {
            border-color: #10B981 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
        }
        select:not([multiple]) option {
            background-color: #0F1713;
            color: #F3F4F6;
            padding: 8px 12px;
        }
        select:not([multiple]) option:disabled {
            color: #6B7280;
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen" style="background:#090D0B; font-family:'Inter',sans-serif;">

    {{-- Sidebar --}}
    <div class="flex min-h-screen">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col" style="background:#0F1713; border-right:1px solid #1A2820;">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #1A2820;">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 p-1" style="background:rgba(16,185,129,0.08); border:1px solid rgba(52,211,153,0.25); box-shadow:0 0 15px rgba(16,185,129,0.15);">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs Logo" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <h1 class="text-white font-extrabold text-base leading-none tracking-wider" style="font-family:'Orbitron',sans-serif;">IC-LABS</h1>
                    <p class="text-[10px] mt-1 font-semibold uppercase tracking-widest" style="font-family:'Chakra Petch',sans-serif; color:#34D399;">REPOSITORY</p>
                </div>
            </div>

            {{-- User Info --}}
            <div class="px-3 py-3" style="border-bottom:1px solid #1A2820;">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl p-2.5 group cursor-pointer" style="background:rgba(16,185,129,.06);" onmouseover="this.style.background='rgba(16,185,129,.12)'" onmouseout="this.style.background='rgba(16,185,129,.06)'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#10B981,#0D9488);">
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white truncate leading-none mb-1">{{ auth()->user()->name }}</p>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold" style="background:rgba(16,185,129,.15);color:#34D399;">
                            {{ auth()->user()->isSuperAdmin() ? '⚡ Super Admin' : '👤 Admin' }}
                        </span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-600 group-hover:text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
                <x-admin.nav-item route="admin.dashboard" icon="home">
                    Dashboard
                </x-admin.nav-item>

                <div class="pt-4 pb-1.5 px-3">
                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Dokumen</p>
                </div>

                <x-admin.nav-item route="admin.soal.index" icon="document">
                    Arsip Dokumen
                </x-admin.nav-item>

                <x-admin.nav-item route="admin.categories.index" icon="tag">
                    Kategori
                </x-admin.nav-item>

                <x-admin.nav-item route="admin.quizzes.index" icon="quiz">
                    Kuis Interaktif
                </x-admin.nav-item>

                <x-admin.nav-item route="admin.soal.trash" icon="trash">
                    Recycle Bin
                </x-admin.nav-item>

                @if(auth()->user()->isSuperAdmin())
                <div class="pt-4 pb-1.5 px-3">
                    <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Super Admin</p>
                </div>
                <x-admin.nav-item route="admin.users.index" icon="users">
                    Kelola Admin
                </x-admin.nav-item>
                @endif
            </nav>

            {{-- Portal Link --}}
            <div class="px-3 py-2" style="border-top:1px solid #1A2820;">
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-colors"
                   style="color:#4B7A65;"
                   onmouseover="this.style.background='rgba(16,185,129,.07)';this.style.color='#10B981'"
                   onmouseout="this.style.background='transparent';this.style.color='#4B7A65'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Portal Mahasiswa
                </a>
            </div>

            {{-- Logout --}}
            <div class="p-3" style="border-top:1px solid #1A2820;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors" style="color:#6B7280;"
                            onmouseover="this.style.background='rgba(239,68,68,.08)';this.style.color='#F87171'"
                            onmouseout="this.style.background='transparent';this.style.color='#6B7280'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            {{-- Top Header --}}
            <header class="px-6 py-3.5 sticky top-0 z-40" style="background:rgba(9,13,11,.95);backdrop-filter:blur(16px);border-bottom:1px solid #1A2820;">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-white leading-none">@yield('page-title', 'Dashboard')</h2>
                        @hasSection('page-description')
                            <p class="text-xs text-gray-500 mt-0.5">@yield('page-description')</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('header-actions')
                        <div class="flex items-center gap-2 text-xs text-gray-500 px-3 py-1.5 rounded-lg border" style="background:rgba(15,23,19,.8);border-color:#1A2820;">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ now()->isoFormat('D MMM YYYY') }}
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages are handled dynamically via modern SweetAlert2 popups below --}}

            {{-- Page Content --}}
            <div class="p-6 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- ─── CYBER SCI-FI LOADING MODAL OVERLAY ("Wah" & Futuristik) ─── --}}
    <div id="cyberLoadingOverlay" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 backdrop-blur-md transition-opacity duration-300 opacity-0 pointer-events-none" aria-live="assertive" role="alert">
        <div class="relative p-8 md:p-10 rounded-3xl bg-[#0F1713]/95 border border-emerald-500/40 shadow-[0_25px_80px_rgba(0,0,0,0.85),0_0_50px_rgba(16,185,129,0.25)] max-w-sm w-full mx-4 text-center overflow-hidden transform transition-transform duration-300 scale-95" id="cyberLoadingBox">
            
            {{-- Sci-Fi Tech Corner Accents --}}
            <div class="absolute top-0 left-0 w-5 h-5 border-t-2 border-l-2 border-emerald-400 rounded-tl-xl pointer-events-none"></div>
            <div class="absolute top-0 right-0 w-5 h-5 border-t-2 border-r-2 border-emerald-400 rounded-tr-xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-5 h-5 border-b-2 border-l-2 border-emerald-400 rounded-bl-xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-5 h-5 border-b-2 border-r-2 border-emerald-400 rounded-br-xl pointer-events-none"></div>

            {{-- Ambient Cyber Glow --}}
            <div class="absolute -top-20 -left-20 w-40 h-40 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Spectacular Concentric Orbit Spinner --}}
            <div class="relative w-28 h-28 mx-auto mb-6 flex items-center justify-center">
                {{-- Outer Ring (Clockwise) --}}
                <div class="cyber-spin-cw absolute inset-0 rounded-full border-2 border-transparent border-t-emerald-400 border-r-emerald-500/50 shadow-[0_0_20px_rgba(16,185,129,0.4)]"></div>
                
                {{-- Outer Orbit Satellites (Glowing laser particles) --}}
                <div class="cyber-spin-cw absolute inset-0 rounded-full">
                    <span class="absolute -top-1 left-1/2 -translate-x-1/2 w-2.5 h-2.5 rounded-full bg-emerald-300 shadow-[0_0_12px_#34D399,0_0_4px_#FFF]"></span>
                    <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-teal-300 shadow-[0_0_10px_#14B8A6]"></span>
                </div>

                {{-- Mid Ring (Counter-Clockwise Dashed) --}}
                <div class="cyber-spin-ccw absolute inset-3 rounded-full border-2 border-dashed border-teal-400/80 shadow-[0_0_15px_rgba(13,148,136,0.35)]"></div>

                {{-- Inner Ring (Fast Clockwise) --}}
                <div class="cyber-spin-fast absolute inset-6 rounded-full border-2 border-transparent border-b-mint-400 border-l-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.5)]"></div>

                {{-- Center Core Pulsing Beacon --}}
                <div class="cyber-core-pulse relative w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/30 to-teal-500/10 border border-emerald-400/50 flex items-center justify-center shadow-[0_0_18px_rgba(16,185,129,0.6)]">
                    <svg class="w-5 h-5 text-emerald-300 filter drop-shadow-[0_0_6px_rgba(52,211,153,0.8)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>

            {{-- Status Chip --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 mb-3 shadow-[0_0_12px_rgba(16,185,129,0.15)]">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-[10px] font-mono font-bold tracking-widest text-emerald-300 uppercase">SYSTEM PROCESSING</span>
            </div>

            {{-- Dynamic Titles and Descriptions --}}
            <h3 id="cyberLoadingTitle" class="text-base md:text-lg font-bold text-white tracking-wider uppercase mb-1.5" style="font-family:'Chakra Petch',sans-serif; text-shadow:0 0 12px rgba(16,185,129,0.35);">
                MEMPROSES PERMINTAAN
            </h3>
            <p id="cyberLoadingDesc" class="text-xs text-gray-400 font-medium leading-relaxed max-w-xs mx-auto mb-4">
                Mohon tunggu sebentar, sistem sedang memproses data...
            </p>

            {{-- Shimmering Laser Progress Bar --}}
            <div class="w-full h-1 bg-[#1A2820] rounded-full overflow-hidden relative">
                <div class="cyber-loading-shimmer-bar"></div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Cyber Loading Modal Controls
        window.showLoading = function(title = 'MEMPROSES PERMINTAAN', desc = 'Mohon tunggu sebentar, sistem sedang memproses data...') {
            const overlay = document.getElementById('cyberLoadingOverlay');
            const box = document.getElementById('cyberLoadingBox');
            const titleEl = document.getElementById('cyberLoadingTitle');
            const descEl = document.getElementById('cyberLoadingDesc');
            
            if (overlay && box) {
                if (titleEl) titleEl.textContent = title;
                if (descEl) descEl.textContent = desc;
                overlay.classList.remove('hidden');
                overlay.classList.remove('pointer-events-none');
                void overlay.offsetWidth; // Force reflow
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }
        };

        window.hideLoading = function() {
            const overlay = document.getElementById('cyberLoadingOverlay');
            const box = document.getElementById('cyberLoadingBox');
            if (overlay && box) {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    overlay.classList.add('pointer-events-none');
                }, 300);
            }
        };

        // Auto-dismiss loading if user uses browser back/forward buttons (BFcache)
        window.addEventListener('pageshow', function() {
            window.hideLoading();
        });

        // Global Confirmation Dialog with Cyber Styling & Loading Animation
        window.confirmAction = function(event, message, confirmText = 'Ya', isDanger = false, customTitle = null) {
            event.preventDefault();
            const form = event.target.closest('form');
            if (!form) return;

            Swal.fire({
                title: customTitle || (isDanger ? 'Konfirmasi Tindakan' : 'Konfirmasi'),
                text: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                customClass: {
                    popup: 'cyber-swal-popup ' + (isDanger ? 'cyber-swal-error' : 'cyber-swal-success'),
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn ' + (isDanger ? 'cyber-swal-btn-error' : 'cyber-swal-btn-success'),
                    cancelButton: 'cyber-swal-btn cyber-swal-btn-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.showLoading('MEMPROSES TINDAKAN...', 'Permintaan sedang dieksekusi oleh server...');
                    form.submit();
                }
            });
        };

        // Global Toast Notification
        window.showToast = function(message, icon = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: 2500,
                background: '#0F1713',
                color: '#f3f4f6',
                customClass: {
                    popup: 'border border-emerald-500/30 rounded-xl'
                }
            });
        };

        // Copy to clipboard helper
        window.copyToClipboard = function(text, successMessage = 'Berhasil disalin!') {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    window.showToast(successMessage);
                });
            } else {
                const input = document.createElement('textarea');
                input.value = text;
                input.style.position = 'fixed';
                input.style.opacity = '0';
                document.body.appendChild(input);
                input.focus();
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                window.showToast(successMessage);
            }
        };

        // Automatically show loading spinner when any standard form is submitted
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.getAttribute('data-no-loading') !== null || form.classList.contains('no-loading')) {
                return;
            }
            if (form.checkValidity && !form.checkValidity()) {
                return;
            }

            const title = form.getAttribute('data-loading-title') || 'MEMPROSES DATA...';
            const desc = form.getAttribute('data-loading-desc') || 'Mohon tunggu, sistem sedang memproses data...';

            setTimeout(() => {
                if (!e.defaultPrevented) {
                    window.showLoading(title, desc);
                }
            }, 10);
        });

        // Automatically show loading for ZIP download links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href*="download-submissions-zip"]');
            if (link) {
                window.showLoading('MENYIAPKAN ARSIP ZIP...', 'Mengompres seluruh berkas tugas ke format ZIP...');
                setTimeout(() => {
                    window.hideLoading();
                }, 4000);
            }
        });
    </script>

    {{-- ─── SweetAlert2 Modern Popups for Flash Notifications ─── --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Oke, Mengerti',
                timer: 4500,
                timerProgressBar: true,
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-success',
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-success'
                }
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: @json(session('error')),
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-error',
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-error'
                }
            });
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian Sistem',
                text: @json(session('warning')),
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-warning',
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-warning'
                }
            });
        });
    </script>
    @endif

    @if(session('info') || session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: @json(session('info') ?? session('status')),
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-info',
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-info'
                }
            });
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errorHtml = `<div class="text-left text-sm space-y-2 mt-2">
                @foreach($errors->all() as $err)
                    <div class="flex items-start gap-2 text-rose-300">
                        <span class="text-rose-500 font-bold">&bull;</span>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>`;
            Swal.fire({
                icon: 'error',
                title: 'Periksa Kembali Inputan',
                html: errorHtml,
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Perbaiki',
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-error',
                    title: 'cyber-swal-title',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-error'
                }
            });
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>
