<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LabDrive — Sistem Arsip Dokumen Laboratorium Digital">
    <title>@yield('title', 'Dashboard') — LabDrive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        :root { --pink: #F72585; --pink2: #7209B7; }
        .sidebar-active { background: rgba(247,37,133,.12) !important; color: #F72585 !important; border-right: 2px solid #F72585; }
        .sidebar-active svg { color: #F72585 !important; }
    </style>
</head>
<body class="text-gray-100 min-h-screen" style="background:#0D0D14; font-family:'Inter',sans-serif;">

    {{-- Sidebar --}}
    <div class="flex min-h-screen">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 flex flex-col" style="background:#13131F; border-right:1px solid #1E1E2E;">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5" style="border-bottom:1px solid #1E1E2E;">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#F72585,#7209B7);box-shadow:0 0 16px rgba(247,37,133,.3);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg leading-none">LabDrive</h1>
                    <p class="text-xs mt-0.5" style="color:#6B7280;">Platform Distribusi Lab</p>
                </div>
            </div>

            {{-- User Info --}}
            <div class="px-4 py-3" style="border-bottom:1px solid #1E1E2E;">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors group cursor-pointer" style="background:rgba(247,37,133,.05);" onmouseover="this.style.background='rgba(247,37,133,.1)'" onmouseout="this.style.background='rgba(247,37,133,.05)'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform" style="background:linear-gradient(135deg,#F72585,#7209B7);">
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <span class="text-xs px-1.5 py-0.5 rounded-full" style="background:rgba(247,37,133,.15);color:#F472B6;">
                            {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                        </span>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <x-admin.nav-item route="admin.dashboard" icon="home">
                    Dashboard
                </x-admin.nav-item>

                <div class="pt-3 pb-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">Dokumen</p>
                </div>

                <x-admin.nav-item route="admin.soal.index" icon="document">
                    Arsip Dokumen
                </x-admin.nav-item>

                <x-admin.nav-item route="admin.categories.index" icon="tag">
                    Kategori
                </x-admin.nav-item>

                @if(auth()->user()->isSuperAdmin())
                <div class="pt-3 pb-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">Super Admin</p>
                </div>
                <x-admin.nav-item route="admin.users.index" icon="users">
                    Kelola Admin
                </x-admin.nav-item>
                @endif
            </nav>

            {{-- Logout --}}
            <div class="p-3" style="border-top:1px solid #1E1E2E;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-200 text-sm" style="color:#6B7280;" onmouseover="this.style.background='rgba(247,37,133,.08)';this.style.color='#F72585'" onmouseout="this.style.background='transparent';this.style.color='#6B7280'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 ml-64">
            {{-- Top Header --}}
            <header class="px-6 py-4 sticky top-0 z-40" style="background:rgba(13,13,20,.9);backdrop-filter:blur(12px);border-bottom:1px solid #1E1E2E;">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-xs text-gray-400 mt-0.5">@yield('page-description', '')</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-400">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="px-6 pt-4">
                @if(session('success'))
                    <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl mb-4" role="alert">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-4" role="alert">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.confirmAction = function(event, message, confirmText = 'Ya', isDanger = false) {
            event.preventDefault(); // Stop form submission
            const form = event.target.closest('form'); // Get the closest form
            
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#ef4444' : '#F72585',
                cancelButtonColor: '#374151', // Gray-700
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                background: '#111827', // Gray-900
                color: '#f3f4f6', // Gray-100
                customClass: {
                    popup: 'border border-gray-800 rounded-2xl',
                    confirmButton: 'rounded-xl px-4 py-2 text-sm font-medium',
                    cancelButton: 'rounded-xl px-4 py-2 text-sm font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        };
    </script>
    @stack('scripts')
</body>
</html>
