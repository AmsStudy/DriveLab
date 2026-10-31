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

        /* ─── Ultra-Reliable Pure Cyber Orbit Spinner ("Wah", Centered & Futuristik) ─── */
        @keyframes cyberSpinRingCW {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes cyberSpinRingCCW {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes cyberCoreBreathing {
            0% { transform: scale(0.8); opacity: 0.75; box-shadow: 0 0 10px #10B981; }
            100% { transform: scale(1.15); opacity: 1; box-shadow: 0 0 24px #34D399, 0 0 40px rgba(16,185,129,0.85); }
        }

        .pure-cyber-loader {
            position: relative;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pure-cyber-loader .spinner-ring-1 {
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 4px solid rgba(16, 185, 129, 0.15);
            border-top: 4px solid #10B981;
            border-right: 4px solid #34D399;
            box-shadow: 0 0 22px rgba(16, 185, 129, 0.6), inset 0 0 12px rgba(16, 185, 129, 0.25);
            animation: cyberSpinRingCW 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .pure-cyber-loader .spinner-dot {
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            animation: cyberSpinRingCW 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .pure-cyber-loader .spinner-dot::before {
            content: '';
            position: absolute;
            top: -5px;
            left: calc(50% - 5px);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #FFFFFF;
            box-shadow: 0 0 14px #34D399, 0 0 6px #10B981;
        }
        .pure-cyber-loader .spinner-ring-2 {
            position: absolute;
            width: 66px;
            height: 66px;
            border-radius: 50%;
            border: 3px dashed #0D9488;
            box-shadow: 0 0 16px rgba(13, 148, 136, 0.45);
            animation: cyberSpinRingCCW 2.2s linear infinite;
        }
        .pure-cyber-loader .spinner-ring-3 {
            position: absolute;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-bottom: 3px solid #6EE7B7;
            border-left: 3px solid #10B981;
            box-shadow: 0 0 12px rgba(52, 211, 153, 0.6);
            animation: cyberSpinRingCW 0.85s ease-in-out infinite;
        }
        .pure-cyber-loader .spinner-core {
            position: relative;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: radial-gradient(circle, #34D399 0%, #10B981 60%, #0D9488 100%);
            box-shadow: 0 0 18px #10B981, 0 0 30px rgba(16, 185, 129, 0.8);
            animation: cyberCoreBreathing 1.2s ease-in-out infinite alternate;
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

        /* Global Custom Cyber Themed Dropdowns for Admin */
        select:not([multiple]),
        .cyber-select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-color: #090D0B !important;
            border: 1px solid #1A2820 !important;
            border-radius: 0.75rem !important;
            color: #E5E7EB !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3E%3Cpath d='M6 8L10 12L14 8' stroke='%2310B981' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.85rem center !important;
            background-size: 1.15rem 1.15rem !important;
            padding-right: 2.75rem !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.4) !important;
        }
        select:not([multiple]):hover,
        .cyber-select:hover {
            background-color: #0D1512 !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.18), inset 0 1px 2px rgba(0, 0, 0, 0.3) !important;
        }
        select:not([multiple]):focus,
        .cyber-select:focus {
            background-color: #070C09 !important;
            border-color: #10B981 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25), 0 0 18px rgba(16, 185, 129, 0.22) !important;
        }
        select:not([multiple]) option,
        .cyber-select option {
            background-color: #0F1713 !important;
            color: #F3F4F6 !important;
            padding: 10px 14px !important;
            font-family: 'Inter', sans-serif !important;
        }
        select:not([multiple]) option:disabled,
        .cyber-select option:disabled {
            color: #6B7280 !important;
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

    {{-- ─── PURE CYBER LOADING SPINNER (100% CENTERED & MINIMALIST) ─── --}}
    <div id="cyberLoadingOverlay" style="position:fixed;top:0;left:0;right:0;bottom:0;width:100vw;height:100vh;margin:0;padding:0;z-index:99999;display:none;align-items:center;justify-content:center;background:rgba(5,9,7,0.8);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);" aria-live="assertive" role="alert">
        <div style="position:relative;display:flex;align-items:center;justify-content:center;margin:auto;" id="cyberLoadingBox">
            
            {{-- Ambient Radial Glow Aura in Center --}}
            <div style="position:absolute;width:160px;height:160px;border-radius:50%;background:rgba(16,185,129,0.18);filter:blur(36px);pointer-events:none;"></div>

            {{-- Multi-Ring Cyber Orbit Spinner --}}
            <div class="pure-cyber-loader">
                <div class="spinner-ring-1"></div>
                <div class="spinner-dot"></div>
                <div class="spinner-ring-2"></div>
                <div class="spinner-ring-3"></div>
                <div class="spinner-core"></div>
            </div>

        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Cyber Loading Controls (Centered & Pure Spinner Only)
        window.showLoading = function() {
            const overlay = document.getElementById('cyberLoadingOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
            }
        };

        window.hideLoading = function() {
            const overlay = document.getElementById('cyberLoadingOverlay');
            if (overlay) {
                overlay.style.display = 'none';
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
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
                    window.showLoading();
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

        // Automatically show pure centered loading spinner when any standard form is submitted
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.getAttribute('data-no-loading') !== null || form.classList.contains('no-loading')) {
                return;
            }
            if (form.checkValidity && !form.checkValidity()) {
                return;
            }

            setTimeout(() => {
                if (!e.defaultPrevented) {
                    window.showLoading();
                }
            }, 10);
        });

        // Automatically show pure loading for ZIP download links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href*="download-submissions-zip"]');
            if (link) {
                window.showLoading();
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
