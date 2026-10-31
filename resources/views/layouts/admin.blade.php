<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IC-Labs Repository — Sistem Arsip Dokumen & Evaluasi Laboratorium Digital">
    <title>@yield('title', 'Dashboard') — IC-Labs Repository</title>
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
        /* Prevent dropdown arrow overlap with text across all select elements */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding-right: 2.5rem !important;
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

            {{-- Flash Messages --}}
            <div class="px-6 pt-4">
                @if(session('success'))
                    <div class="flash-msg flex items-center gap-3 px-4 py-3 rounded-xl mb-4" style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);" role="alert">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,.2);">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-emerald-300 font-medium flex-1">{{ session('success') }}</p>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-400 transition-colors ml-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash-msg flex items-center gap-3 bg-red-500/10 border border-red-500/25 px-4 py-3 rounded-xl mb-4" role="alert">
                        <div class="w-7 h-7 rounded-lg bg-red-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-red-300 font-medium flex-1">{{ session('error') }}</p>
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-400 transition-colors ml-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="flash-msg flex items-center gap-3 bg-amber-500/10 border border-amber-500/25 px-4 py-3 rounded-xl mb-4" role="alert">
                        <div class="w-7 h-7 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-amber-300 font-medium flex-1">{{ session('warning') }}</p>
                        <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-400 transition-colors ml-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <div class="p-6 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.confirmAction = function(event, message, confirmText = 'Ya', isDanger = false) {
            event.preventDefault();
            const form = event.target.closest('form');

            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#ef4444' : '#10B981',
                cancelButtonColor: '#1A2820',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                background: '#0F1713',
                color: '#f3f4f6',
                customClass: {
                    popup: 'border border-[#1A2820] rounded-2xl',
                    confirmButton: 'rounded-xl px-4 py-2 text-sm font-semibold',
                    cancelButton: 'rounded-xl px-4 py-2 text-sm font-semibold'
                }
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        };

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

        // Auto-dismiss flash after 5s
        document.querySelectorAll('.flash-msg').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity .4s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>
