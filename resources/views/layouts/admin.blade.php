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
    <script>
        (function() {
            try {
                if (localStorage.getItem('labsoal_sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();
    </script>
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
        .cyber-dropdown-trigger:hover {
            border-color: rgba(16, 185, 129, 0.5) !important;
            background-color: #0D1512 !important;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.18) !important;
        }
        .cyber-dropdown-menu::-webkit-scrollbar { width: 4px; }
        .cyber-dropdown-menu::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.35); border-radius: 4px; }
        .cyber-dropdown-menu::-webkit-scrollbar-thumb:hover { background: #10B981; }
        [x-cloak] { display: none !important; }

        /* ─── Admin Responsive Shell & Sidebar (Strict, Bulletproof Desktop & Mobile) ─── */
        :root {
            --sidebar-width: 16rem; /* 256px */
        }

        /* Subtle highlight on hamburger when sidebar is collapsed on desktop */
        html.sidebar-collapsed #sidebar-toggle-btn,
        body.sidebar-collapsed #sidebar-toggle-btn {
            background-color: rgba(16, 185, 129, 0.12) !important;
            border-color: rgba(16, 185, 129, 0.45) !important;
            color: #10B981 !important;
            box-shadow: 0 0 16px rgba(16, 185, 129, 0.22) !important;
        }
        html.sidebar-collapsed #sidebar-toggle-btn .hamburger-bar,
        body.sidebar-collapsed #sidebar-toggle-btn .hamburger-bar {
            background-color: #34D399 !important;
        }

        /* Desktop Mode (>= 1024px / lg) */
        @media (min-width: 1024px) {
            #sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: var(--sidebar-width) !important;
                transform: translateX(0);
                z-index: 40 !important;
                display: flex !important;
                box-shadow: none !important;
                transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.32s ease !important;
                will-change: transform;
            }
            html.sidebar-collapsed #sidebar,
            body.sidebar-collapsed #sidebar {
                transform: translateX(-100%) !important;
                box-shadow: none !important;
            }
            #main-content-wrapper {
                margin-left: var(--sidebar-width) !important;
                width: calc(100% - var(--sidebar-width)) !important;
                min-width: 0 !important;
                transition: margin-left 0.32s cubic-bezier(0.16, 1, 0.3, 1), width 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
                will-change: margin-left, width;
            }
            html.sidebar-collapsed #main-content-wrapper,
            body.sidebar-collapsed #main-content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            #mobile-sidebar-backdrop {
                display: none !important;
            }
            #mobile-hamburger-btn,
            #sidebar-toggle-btn {
                display: inline-flex !important;
            }
            #sidebar-close-btn {
                display: none !important;
            }
        }

        /* Tablet & Mobile Mode (< 1024px) */
        @media (max-width: 1023.98px) {
            #sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: min(280px, 86vw) !important;
                z-index: 60 !important;
                transform: translateX(-100%) !important;
                transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.32s ease !important;
                box-shadow: none !important;
                will-change: transform;
            }
            body.mobile-sidebar-open #sidebar,
            #sidebar.open {
                transform: translateX(0) !important;
                box-shadow: 0 0 60px rgba(0, 0, 0, 0.9), 0 0 30px rgba(16, 185, 129, 0.25) !important;
            }
            #main-content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 0 !important;
                transition: none !important;
            }
            #mobile-hamburger-btn,
            #sidebar-toggle-btn {
                display: inline-flex !important;
            }
            #sidebar-close-btn {
                display: inline-flex !important;
            }
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen overflow-x-hidden" style="background:#090D0B; font-family:'Inter',sans-serif;">

    {{-- Mobile Sidebar Backdrop --}}
    <div id="mobile-sidebar-backdrop"
         onclick="toggleMobileSidebar(false)"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md transition-opacity duration-300 opacity-0 pointer-events-none"
         style="display: none;">
    </div>

    {{-- Sidebar Shell --}}
    <div class="flex min-h-screen relative">
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 flex flex-col"
               style="background:#0F1713; border-right:1px solid #1A2820;">
            {{-- Logo & Mobile Close --}}
            <div class="flex items-center justify-between px-5 py-3.5" style="border-bottom:1px solid #1A2820; height: 65px;">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 p-1" style="background:rgba(16,185,129,0.08); border:1px solid rgba(52,211,153,0.25); box-shadow:0 0 15px rgba(16,185,129,0.15);">
                        <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs Logo" class="w-full h-full object-contain" draggable="false">
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-white font-extrabold text-base leading-none tracking-wider truncate" style="font-family:'Orbitron',sans-serif;">IC-LABS</h1>
                        <p class="text-[10px] mt-1 font-semibold uppercase tracking-widest truncate" style="font-family:'Chakra Petch',sans-serif; color:#34D399;">REPOSITORY</p>
                    </div>
                </div>

                {{-- Mobile Close Drawer Button (Only shown on mobile / tablet drawer) --}}
                <button type="button"
                        id="sidebar-close-btn"
                        onclick="toggleMobileSidebar(false)"
                        class="p-2 rounded-xl text-gray-400 hover:text-white bg-gray-800/60 hover:bg-rose-500/20 hover:text-rose-400 border border-gray-700/60 hover:border-rose-500/40 transition-all flex items-center justify-center cursor-pointer lg:hidden flex-shrink-0"
                        title="Tutup Menu (Esc)">
                    <svg class="w-5 h-5 transition-transform duration-200 hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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
        <main id="main-content-wrapper" class="flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300">
            {{-- Top Header --}}
            <header class="px-4 sm:px-6 py-3.5 sticky top-0 z-30 flex items-center" style="background:rgba(9,13,11,.95);backdrop-filter:blur(16px);border-bottom:1px solid #1A2820; height: 65px;">
                <div class="flex items-center justify-between gap-3 w-full">
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                        {{-- Master Cyber Hamburger Toggle Button --}}
                        <button type="button"
                                id="sidebar-toggle-btn"
                                onclick="toggleSidebar()"
                                class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-400 bg-[#0F1713] hover:bg-[#142019] border border-[#1A2820] hover:border-emerald-500/40 active:scale-95 transition-all flex-shrink-0 cursor-pointer shadow-sm hover:shadow-[0_0_16px_rgba(16,185,129,0.18)] group focus:outline-none focus:border-emerald-500/50"
                                aria-label="Buka atau Tutup Menu Navigasi"
                                title="Buka / Tutup Sidebar (Ctrl+B)">
                            <div class="w-5 h-3.5 flex flex-col justify-between items-center">
                                <span class="hamburger-bar block h-[2px] w-5 rounded-full bg-gray-400 group-hover:bg-emerald-400 transition-colors duration-200"></span>
                                <span class="hamburger-bar block h-[2px] w-3.5 group-hover:w-5 self-start rounded-full bg-gray-400 group-hover:bg-emerald-400 transition-all duration-200"></span>
                                <span class="hamburger-bar block h-[2px] w-5 rounded-full bg-gray-400 group-hover:bg-emerald-400 transition-colors duration-200"></span>
                            </div>
                        </button>

                        {{-- Subtle Cyber Vertical Separator --}}
                        <div class="h-6 w-px bg-[#1A2820] flex-shrink-0"></div>

                        {{-- Page Title & Description --}}
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-white leading-tight truncate">@yield('page-title', 'Dashboard')</h2>
                            @hasSection('page-description')
                                <p class="text-xs text-gray-400 mt-0.5 truncate hidden sm:block">@yield('page-description')</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                        @yield('header-actions')

                        @php
                            $tzAbbr = match(config('app.timezone')) {
                                'Asia/Jakarta' => 'WIB',
                                'Asia/Makassar' => 'WITA',
                                'Asia/Jayapura' => 'WIT',
                                default => 'WITA'
                            };
                        @endphp
                        {{-- Real-Time Cyber Clock & Date Widget (Stacked Minimalist) --}}
                        <div id="realtime-clock-widget"
                             class="flex items-center gap-2.5 px-3 py-1 rounded-xl border border-[#1A2820] bg-[#0F1713]/80 hover:border-emerald-500/35 transition-colors whitespace-nowrap select-none"
                             title="Waktu Real-time Server ({{ config('app.timezone') }})">
                            {{-- Pulsing Live Green Status Dot --}}
                            <span class="relative flex h-2 w-2 flex-shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>

                            {{-- Stack: Jam di Atas, Tanggal di Bawah --}}
                            <div class="flex flex-col text-left leading-none">
                                <div class="flex items-center gap-1 font-mono font-bold text-xs sm:text-[13px] text-emerald-400 tracking-wider">
                                    <span id="live-time-display">{{ now()->format('H:i:s') }}</span>
                                    <span class="text-[9px] font-sans font-semibold text-emerald-500/70">{{ $tzAbbr }}</span>
                                </div>
                                <span id="live-date-display" class="text-[10px] text-gray-400 font-medium mt-1 tracking-normal">
                                    {{ now()->isoFormat('ddd, D MMM YYYY') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages are handled dynamically via modern SweetAlert2 popups below --}}

            {{-- Page Content --}}
            <div class="p-4 sm:p-6 flex-1 min-w-0">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- ─── UNIFIED CYBER LOADING POPUP COMPONENT ─── --}}
    @include('components.cyber-loading')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                    window.showLoading(
                        isDanger ? 'MEMPROSES TINDAKAN...' : 'MENYIMPAN PERUBAHAN...',
                        'Mohon tunggu sebentar, sistem sedang memproses data...'
                    );
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

        // Automatically show cyber loading spinner when any standard form is submitted
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
                    window.showLoading(
                        'MENYIMPAN DATA...',
                        'Sedang memproses dan menyimpan perubahan data ke server...'
                    );
                }
            }, 10);
        });

        // Automatically show cyber loading for ZIP download and CSV export links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href*="download-submissions-zip"], a[href*="export-submissions"], a[href*="export-csv"]');
            if (link) {
                const isZip = link.href.includes('download-submissions-zip');
                window.showLoading(
                    isZip ? 'MENYIAPKAN ARSIP ZIP...' : 'MENGEKSPOR DATA REKAP...',
                    isZip ? 'Sedang mengompresi dan menyiapkan berkas tugas mahasiswa...' : 'Sedang menyusun dan mengunduh berkas rekap data...'
                );
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

    {{-- Universal Cyber Select Dropdown Enhancer for Admin --}}
    <script>
    function initCyberSelects(root = document) {
        root.querySelectorAll('select.cyber-select').forEach(select => {
            if (select.dataset.cyberEnhanced === 'true') return;
            select.dataset.cyberEnhanced = 'true';

            // Hide original select visually but keep it in DOM for forms & events
            select.style.display = 'none';

            // Wrapper container
            const isFull = select.classList.contains('w-full') || (select.getAttribute('style') && select.getAttribute('style').includes('width'));
            const wrapper = document.createElement('div');
            wrapper.className = 'cyber-custom-dropdown relative inline-block text-left ' + (isFull ? 'w-full' : '');

            // Selected option
            let selectedOption = select.options[select.selectedIndex] || select.options[0];

            // Trigger button
            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'cyber-dropdown-trigger flex items-center justify-between gap-2.5 transition-all cursor-pointer rounded-xl ' +
                (isFull ? 'w-full ' : '') +
                (select.classList.contains('text-xs') ? 'text-xs py-2 px-3.5 ' : 'text-sm py-2 px-3.5 ');
            trigger.style.backgroundColor = '#090D0B';
            trigger.style.borderColor = '#1A2820';
            trigger.style.borderWidth = '1px';
            trigger.style.borderStyle = 'solid';
            trigger.style.color = '#E5E7EB';

            const labelSpan = document.createElement('span');
            labelSpan.className = 'truncate ' + (selectedOption && selectedOption.value ? 'text-gray-100 font-semibold' : 'text-gray-400');
            labelSpan.textContent = selectedOption ? selectedOption.textContent : 'Pilih...';

            const chevron = document.createElement('div');
            chevron.className = 'chevron-icon text-emerald-400 transition-transform duration-200 flex-shrink-0';
            chevron.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>`;

            trigger.appendChild(labelSpan);
            trigger.appendChild(chevron);
            wrapper.appendChild(trigger);

            // Floating Options Menu
            const menu = document.createElement('div');
            menu.className = 'cyber-dropdown-menu hidden absolute left-0 mt-2 z-50 p-1.5 rounded-2xl border backdrop-blur-xl shadow-2xl max-h-60 overflow-y-auto ' +
                (isFull ? 'w-full right-0' : 'min-w-[190px] w-max');
            menu.style.background = 'rgba(15, 23, 19, 0.98)';
            menu.style.borderColor = 'rgba(16, 185, 129, 0.35)';
            menu.style.boxShadow = '0 20px 45px -10px rgba(0,0,0,0.9), 0 0 25px rgba(16,185,129,0.18)';

            function renderOptions() {
                menu.innerHTML = '';
                Array.from(select.options).forEach((opt, idx) => {
                    const isSelected = select.selectedIndex === idx;
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'cyber-dropdown-item w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm transition-all cursor-pointer mb-1 ' +
                        (isSelected ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30' : 'text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-300');

                    const itemContent = document.createElement('span');
                    itemContent.className = 'flex items-center gap-2 truncate';
                    itemContent.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${isSelected ? 'bg-emerald-400 shadow-[0_0_8px_#10B981]' : 'bg-gray-600'}"></span><span>${opt.textContent}</span>`;

                    const checkIcon = document.createElement('span');
                    checkIcon.className = 'check-icon flex-shrink-0 ' + (isSelected ? 'text-emerald-400' : 'opacity-0');
                    checkIcon.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;

                    item.appendChild(itemContent);
                    item.appendChild(checkIcon);

                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        select.selectedIndex = idx;
                        select.value = opt.value;
                        labelSpan.textContent = opt.textContent;
                        labelSpan.className = 'truncate ' + (opt.value ? 'text-gray-100 font-semibold' : 'text-gray-400');

                        closeMenu();

                        // Fire native events
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        select.dispatchEvent(new Event('input', { bubbles: true }));
                    });

                    menu.appendChild(item);
                });
            }

            renderOptions();

            function openMenu() {
                document.querySelectorAll('.cyber-dropdown-menu').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
                document.querySelectorAll('.cyber-dropdown-trigger').forEach(t => {
                    if (t !== trigger) {
                        t.classList.remove('is-open');
                        t.style.borderColor = '#1A2820';
                        t.style.boxShadow = '';
                        const ch = t.querySelector('.chevron-icon');
                        if (ch) ch.style.transform = '';
                    }
                });

                renderOptions();
                menu.classList.remove('hidden');
                trigger.classList.add('is-open');
                trigger.style.borderColor = '#10B981';
                trigger.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.25), 0 0 18px rgba(16, 185, 129, 0.22)';
                chevron.style.transform = 'rotate(180deg)';
            }

            function closeMenu() {
                menu.classList.add('hidden');
                trigger.classList.remove('is-open');
                trigger.style.borderColor = '#1A2820';
                trigger.style.boxShadow = '';
                chevron.style.transform = '';
            }

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                if (menu.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeMenu();
                }
            });

            select.addEventListener('change', () => {
                const current = select.options[select.selectedIndex];
                if (current) {
                    labelSpan.textContent = current.textContent;
                    labelSpan.className = 'truncate ' + (current.value ? 'text-gray-100 font-semibold' : 'text-gray-400');
                }
            });

            wrapper.appendChild(menu);
            select.parentNode.insertBefore(wrapper, select.nextSibling);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initCyberSelects();
    });
    window.initCyberSelects = initCyberSelects;

    // ── Universal Responsive Sidebar Controls (Desktop Collapsible + Mobile Drawer) ──
    window.toggleSidebar = function(forceState = null) {
        if (window.innerWidth >= 1024) {
            // Desktop Mode: Toggle Collapsed state
            const isCurrentlyCollapsed = document.body.classList.contains('sidebar-collapsed') ||
                                         document.documentElement.classList.contains('sidebar-collapsed');
            const shouldCollapse = forceState !== null ? !forceState : !isCurrentlyCollapsed;

            if (shouldCollapse) {
                document.body.classList.add('sidebar-collapsed');
                document.documentElement.classList.add('sidebar-collapsed');
                try { localStorage.setItem('labsoal_sidebar_collapsed', 'true'); } catch(e) {}
            } else {
                document.body.classList.remove('sidebar-collapsed');
                document.documentElement.classList.remove('sidebar-collapsed');
                try { localStorage.setItem('labsoal_sidebar_collapsed', 'false'); } catch(e) {}
            }

            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            if (toggleBtn) {
                toggleBtn.setAttribute('title', shouldCollapse ? 'Tampilkan Sidebar (Ctrl+B)' : 'Sembunyikan Sidebar (Ctrl+B)');
            }
        } else {
            // Mobile Mode: Toggle Mobile Drawer
            const isOpen = document.body.classList.contains('mobile-sidebar-open');
            const shouldOpen = forceState !== null ? forceState : !isOpen;
            window.toggleMobileSidebar(shouldOpen);
        }
    };

    window.toggleMobileSidebar = function(open) {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        if (!sidebar || !backdrop) return;

        if (open) {
            document.body.classList.add('mobile-sidebar-open');
            sidebar.classList.add('open');
            backdrop.style.display = 'block';
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
            });
            document.body.style.overflow = 'hidden';
        } else {
            document.body.classList.remove('mobile-sidebar-open');
            sidebar.classList.remove('open');
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                if (!document.body.classList.contains('mobile-sidebar-open')) {
                    backdrop.style.display = 'none';
                }
            }, 320);
            document.body.style.overflow = '';
        }
    };

    // Keyboard Shortcuts: Ctrl + B / Cmd + B to toggle, Escape to close mobile drawer
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
            e.preventDefault();
            window.toggleSidebar();
        } else if (e.key === 'Escape' && document.body.classList.contains('mobile-sidebar-open')) {
            window.toggleMobileSidebar(false);
        }
    });

    // Sync state on initial page load
    document.addEventListener('DOMContentLoaded', function() {
        try {
            if (localStorage.getItem('labsoal_sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
                document.body.classList.add('sidebar-collapsed');
                document.documentElement.classList.add('sidebar-collapsed');
                const toggleBtn = document.getElementById('sidebar-toggle-btn');
                if (toggleBtn) {
                    toggleBtn.setAttribute('title', 'Tampilkan Sidebar (Ctrl+B)');
                }
            }
        } catch(e) {}
    });

    // Reset when resizing window across breakpoints
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            if (document.body.classList.contains('mobile-sidebar-open')) {
                window.toggleMobileSidebar(false);
            }
            if (localStorage.getItem('labsoal_sidebar_collapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } else {
            document.body.classList.remove('sidebar-collapsed');
            document.documentElement.classList.remove('sidebar-collapsed');
        }
    });

    // ── Real-Time Cyber Clock Synchronizer ──
    (function initRealtimeClock() {
        const serverEpochMs = {{ now()->getTimestampMs() }};
        const clientStartMs = Date.now();
        const serverOffsetMs = serverEpochMs - clientStartMs;
        const targetTimezone = "{{ config('app.timezone', 'Asia/Makassar') }}";

        let timeFormatter;
        let dateFormatter;
        try {
            timeFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: targetTimezone,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            dateFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: targetTimezone,
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        } catch (e) {}

        function updateClock() {
            const now = new Date(Date.now() + serverOffsetMs);
            const timeEl = document.getElementById('live-time-display');
            const dateEl = document.getElementById('live-date-display');

            if (timeEl) {
                if (timeFormatter) {
                    try {
                        timeEl.textContent = timeFormatter.format(now).replace(/\./g, ':');
                    } catch (e) {
                        const h = String(now.getHours()).padStart(2, '0');
                        const m = String(now.getMinutes()).padStart(2, '0');
                        const s = String(now.getSeconds()).padStart(2, '0');
                        timeEl.textContent = `${h}:${m}:${s}`;
                    }
                } else {
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    timeEl.textContent = `${h}:${m}:${s}`;
                }
            }

            if (dateEl && dateFormatter) {
                try {
                    dateEl.textContent = dateFormatter.format(now);
                } catch (e) {}
            }
        }

        updateClock();
        setInterval(updateClock, 1000);
    })();
    </script>
    @stack('scripts')
</body>
</html>
