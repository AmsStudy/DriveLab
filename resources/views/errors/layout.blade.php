<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Terjadi Kesalahan') — IC-LABS Repository</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@700;800;900&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <x-theme-head-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --err-bg: #050907;
            --err-card-bg: rgba(15, 23, 19, 0.85);
            --err-card-border: rgba(16, 185, 129, 0.22);
            --err-card-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 40px rgba(16, 185, 129, 0.08);
            --err-text-title: #FFFFFF;
            --err-text-body: #9CA3AF;
            --err-grid-line: rgba(16, 185, 129, 0.04);
            --err-code-from: #34D399;
            --err-code-to: #059669;
        }

        html:not(.dark) {
            --err-bg: #F4F7F5;
            --err-card-bg: rgba(255, 255, 255, 0.95);
            --err-card-border: #D8E5DE;
            --err-card-shadow: 0 20px 40px rgba(0, 0, 0, 0.06), 0 0 30px rgba(16, 185, 129, 0.06);
            --err-text-title: #0F1F17;
            --err-text-body: #4B5563;
            --err-grid-line: rgba(16, 185, 129, 0.06);
            --err-code-from: #059669;
            --err-code-to: #0D9488;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--err-bg);
            color: var(--err-text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        .cyber-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(to right, var(--err-grid-line) 1px, transparent 1px),
                linear-gradient(to bottom, var(--err-grid-line) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
        }

        .glow-1 {
            width: 480px;
            height: 480px;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: radial-gradient(circle, rgba(16, 185, 129, 0.16) 0%, transparent 70%);
        }

        .glow-2 {
            width: 360px;
            height: 360px;
            bottom: 50px;
            right: 10%;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.12) 0%, transparent 70%);
        }

        .error-card {
            background: var(--err-card-bg);
            border: 1px solid var(--err-card-border);
            box-shadow: var(--err-card-shadow);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .font-orbitron { font-family: 'Orbitron', monospace; }
        .font-chakra { font-family: 'Chakra Petch', sans-serif; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }

        .error-code-text {
            background: linear-gradient(135deg, var(--err-code-from), var(--err-code-to));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(16, 185, 129, 0.25);
        }
    </style>
</head>
<body class="selection:bg-emerald-500/30 selection:text-emerald-300">

    {{-- Background Cyber Effects --}}
    <div class="cyber-grid"></div>
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    {{-- Header --}}
    <header class="relative z-10 w-full border-b border-[#1A2820]/60 dark:border-[#1A2820]/60 light:border-[#E2EAE5] px-4 sm:px-8 py-3.5 flex items-center justify-between backdrop-blur-md">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group text-decoration-none">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center p-1.5 shadow-md transition-all duration-300 group-hover:scale-105"
                 style="background: linear-gradient(135deg, #10B981, #0D9488); box-shadow: 0 0 16px rgba(16,185,129,.35);">
                <img src="{{ asset('images/iclabs-logo.png') }}"
                     alt="IC-Labs"
                     class="w-full h-full object-contain filter brightness-0 invert"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\'w-5 h-5 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z\'/></svg>'">
            </div>
            <div>
                <span class="font-orbitron font-extrabold text-sm sm:text-base tracking-wider block leading-tight text-white dark:text-white" style="color: var(--err-text-title);">IC-LABS</span>
                <span class="text-[10px] tracking-widest font-mono text-emerald-400 dark:text-emerald-400 font-semibold uppercase">Repository &bull; System</span>
            </div>
        </a>

        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-mono font-medium border border-rose-500/30 text-rose-400 bg-rose-500/10">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                SYSTEM RESPONSE
            </span>
            <x-theme-switcher :compact="true" />
        </div>
    </header>

    {{-- Main Content Container --}}
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-lg error-card rounded-3xl p-6 sm:p-10 text-center relative overflow-hidden transition-all duration-300">
            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-80"></div>

            {{-- Error Graphic / Code --}}
            <div class="mb-5 flex flex-col items-center">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl flex items-center justify-center mb-4 transition-transform duration-300 hover:scale-105"
                     style="background: rgba(16, 185, 129, 0.08); border: 1px solid var(--err-card-border); box-shadow: 0 0 25px rgba(16,185,129,0.12);">
                    @yield('icon')
                </div>

                <div class="font-orbitron font-black text-6xl sm:text-7xl tracking-tighter error-code-text leading-none select-none">
                    @yield('code', 'ERR')
                </div>
            </div>

            {{-- Error Title & Message --}}
            <h1 class="font-chakra font-bold text-xl sm:text-2xl mb-3 tracking-wide" style="color: var(--err-text-title);">
                @yield('heading', 'Terjadi Gangguan pada Permintaan')
            </h1>

            <p class="text-sm leading-relaxed mb-6 font-normal max-w-md mx-auto" style="color: var(--err-text-body);">
                @yield('message', 'Sistem tidak dapat memproses permintaan ini. Silakan periksa kembali alamat atau kembali ke halaman beranda.')
            </p>

            {{-- Optional Diagnostic Info --}}
            <div class="mb-7 px-4 py-2.5 rounded-xl border border-dashed text-xs font-mono inline-flex items-center gap-2"
                 style="background: rgba(16, 185, 129, 0.03); border-color: var(--err-card-border); color: var(--err-text-body);">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Status: <strong>@yield('code', '400')</strong> &bull; Waktu: {{ now()->format('d M Y, H:i:s') }}</span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-white font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-lg cursor-pointer"
                   style="background: linear-gradient(135deg, #10B981, #0D9488); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Ke Beranda</span>
                </a>

                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href='{{ route('home') }}'"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 border cursor-pointer"
                        style="background: rgba(16, 185, 129, 0.05); border-color: var(--err-card-border); color: var(--err-text-title);">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </button>

                @auth
                <a href="{{ route('admin.dashboard') }}"
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 border cursor-pointer"
                   style="background: rgba(16, 185, 129, 0.05); border-color: var(--err-card-border); color: var(--err-text-title);">
                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard Admin</span>
                </a>
                @endauth
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="relative z-10 w-full py-4 text-center text-xs border-t border-[#1A2820]/40 dark:border-[#1A2820]/40 light:border-[#E2EAE5]"
            style="color: var(--err-text-body);">
        <p>&copy; {{ date('Y') }} IC-LABS &bull; Laboratorium Informatika. Seluruh hak cipta dilindungi.</p>
    </footer>

</body>
</html>

