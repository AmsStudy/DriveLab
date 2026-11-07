<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Tunggu Ujian: {{ $quiz->title }} &mdash; IC-Labs</title>
    <x-theme-head-script />
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Orbitron:wght@700;800;900&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #090D0B;
            --card: #0F1713;
            --border: #1A2820;
            --emerald: #10B981;
            --emerald-glow: rgba(16, 185, 129, 0.2);
        }
        html:not(.dark) {
            --bg: #F4F7F5;
            --card: #FFFFFF;
            --border: #E2EAE5;
            --emerald: #059669;
            --emerald-glow: rgba(16, 185, 129, 0.15);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #f3f4f6;
            min-height: 100vh;
            overflow-x: hidden;
        }
        html:not(.dark) body {
            color: #0F1F17 !important;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.08), transparent 70%), #0F1713;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
        }
        html:not(.dark) .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.06), transparent 70%), #FFFFFF !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05) !important;
        }

        /* ─── Cyber Quantum Reactor Spinner (Ultra "Wah" & Futuristik) ─── */
        @keyframes orbitCW {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes orbitCCW {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes pulseGlowWave {
            0%, 100% {
                transform: scale(0.92);
                box-shadow: 0 0 15px rgba(16, 185, 129, 0.3), inset 0 0 10px rgba(16, 185, 129, 0.2);
            }
            50% {
                transform: scale(1.08);
                box-shadow: 0 0 35px rgba(52, 211, 153, 0.7), 0 0 60px rgba(16, 185, 129, 0.4), inset 0 0 18px rgba(52, 211, 153, 0.4);
            }
        }
        @keyframes floatingOrb {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(180deg); }
        }

        .quantum-reactor {
            position: relative;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
        }

        /* Ring 1 - Outermost Multi-Segment Ring with Glowing Orbits */
        .reactor-ring-outer {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px dashed rgba(16, 185, 129, 0.25);
            animation: orbitCW 18s linear infinite;
        }
        .reactor-ring-outer::before {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #34D399;
            box-shadow: 0 0 12px #34D399, 0 0 20px #10B981;
        }
        .reactor-ring-outer::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #06B6D4;
            box-shadow: 0 0 12px #06B6D4;
        }

        /* Ring 2 - Tech Circuit Gradient Ring */
        .reactor-ring-mid {
            position: absolute;
            inset: 14px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top: 3px solid #10B981;
            border-right: 3px solid #34D399;
            border-bottom: 3px solid transparent;
            border-left: 3px solid rgba(16, 185, 129, 0.3);
            filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.6));
            animation: orbitCW 3.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Ring 3 - Counter Rotating Segment */
        .reactor-ring-inner {
            position: absolute;
            inset: 28px;
            border-radius: 50%;
            border: 2.5px solid transparent;
            border-bottom: 2.5px solid #06B6D4;
            border-left: 2.5px solid #10B981;
            filter: drop-shadow(0 0 8px rgba(6, 182, 212, 0.5));
            animation: orbitCCW 2.4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Ring 4 - Ambient Particle Track */
        .reactor-ring-dotted {
            position: absolute;
            inset: 40px;
            border-radius: 50%;
            border: 2px dotted rgba(52, 211, 153, 0.45);
            animation: orbitCW 7s linear infinite;
        }

        /* Center Glowing Core */
        .reactor-core {
            position: relative;
            width: 58px;
            height: 58px;
            border-radius: 20px;
            background: radial-gradient(circle at 35% 35%, #10B981 0%, #064E3B 100%);
            border: 2px solid rgba(52, 211, 153, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            animation: pulseGlowWave 2s ease-in-out infinite alternate;
        }
        html:not(.dark) .reactor-core {
            background: radial-gradient(circle at 35% 35%, #059669 0%, #047857 100%);
            border: 2px solid rgba(16, 185, 129, 0.6);
            box-shadow: 0 8px 24px rgba(5, 150, 105, 0.35);
        }

        /* Light Mode Specific Overrides for Reactor Rings */
        html:not(.dark) .reactor-ring-outer {
            border-color: rgba(5, 150, 105, 0.3);
        }
        html:not(.dark) .reactor-ring-outer::before {
            background: #059669;
            box-shadow: 0 0 10px rgba(5, 150, 105, 0.7);
        }
        html:not(.dark) .reactor-ring-outer::after {
            background: #0891B2;
            box-shadow: 0 0 10px rgba(8, 145, 178, 0.7);
        }
        html:not(.dark) .reactor-ring-mid {
            border-top-color: #059669;
            border-right-color: #10B981;
            border-left-color: rgba(5, 150, 105, 0.35);
            filter: drop-shadow(0 2px 8px rgba(5, 150, 105, 0.4));
        }
        html:not(.dark) .reactor-ring-inner {
            border-bottom-color: #0891B2;
            border-left-color: #059669;
            filter: drop-shadow(0 2px 6px rgba(8, 145, 178, 0.35));
        }
        html:not(.dark) .reactor-ring-dotted {
            border-color: rgba(5, 150, 105, 0.4);
        }

        /* Direct Styling for Instruction Cards (Immune to Tailwind cascade / cache bugs) */
        .waiting-rule-box {
            background-color: #F9FAFB !important;
            border: 1px solid #E5E7EB !important;
            transition: all 0.2s ease !important;
        }
        .waiting-rule-box:hover {
            border-color: rgba(16, 185, 129, 0.5) !important;
            background-color: #F0FDF4 !important;
        }
        .waiting-rule-title {
            color: #111827 !important;
        }
        .waiting-rule-desc {
            color: #4B5563 !important;
        }

        html.dark .waiting-rule-box,
        html[data-theme="dark"] .waiting-rule-box {
            background-color: #0D1612 !important;
            border: 1px solid #1E3327 !important;
        }
        html.dark .waiting-rule-box:hover,
        html[data-theme="dark"] .waiting-rule-box:hover {
            border-color: rgba(16, 185, 129, 0.45) !important;
            background-color: #13221A !important;
        }
        html.dark .waiting-rule-title,
        html[data-theme="dark"] .waiting-rule-title {
            color: #F9FAFB !important;
        }
        html.dark .waiting-rule-desc,
        html[data-theme="dark"] .waiting-rule-desc {
            color: #9CA3AF !important;
        }

        /* Dynamic Digital Equalizer / Activity Bars */
        .audio-wave-bar {
            width: 3px;
            border-radius: 9999px;
            background: #10B981;
            animation: audioWave 1.2s ease-in-out infinite alternate;
        }
        html:not(.dark) .audio-wave-bar {
            background: #059669;
        }
        @keyframes audioWave {
            0% { height: 6px; opacity: 0.4; }
            100% { height: 22px; opacity: 1; }
        }

        /* Background Moving Gradient Orbs */
        @keyframes floatBackdrop1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(25px, -20px) scale(1.1); }
        }
        @keyframes floatBackdrop2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, 25px) scale(1.15); }
        }
        .backdrop-orb-1 {
            animation: floatBackdrop1 10s ease-in-out infinite;
        }
        .backdrop-orb-2 {
            animation: floatBackdrop2 12s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen selection:bg-emerald-500 selection:text-white"
      x-data="studentWaitingRoom({{ $quiz->id }}, {{ $attempt->id }}, '{{ $quiz->unique_token }}')"
      x-init="initWaiting()">

    {{-- Top Navbar --}}
    <header class="border-b border-gray-200 dark:border-[#1A2820] bg-white/80 dark:bg-[#090D0B]/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-wider leading-none block text-gray-900 dark:text-white" style="font-family:'Orbitron',sans-serif;">IC-LABS</span>
                    <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold tracking-widest block mt-0.5" style="font-family:'Chakra Petch',sans-serif;">RUANG TUNGGU UJIAN</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#142019] border border-gray-200 dark:border-[#1E3327] text-gray-700 dark:text-gray-300 font-mono text-xs font-bold">
                    Kode: <strong class="text-emerald-600 dark:text-emerald-400">{{ $quiz->access_code }}</strong>
                </span>
                <x-theme-switcher :compact="true" />
            </div>
        </div>
    </header>

    {{-- Starting Overlay Animation (When admin clicks Start) --}}
    <div x-show="isStarting"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 bg-[#090D0B]/95 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center">
        <div class="w-20 h-20 rounded-3xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 mb-6 shadow-2xl shadow-emerald-500/30 animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </div>
        <h2 class="text-3xl font-extrabold text-white heading-font mb-3">UJIAN TELAH DIMULAI!</h2>
        <p class="text-emerald-400 text-sm font-semibold max-w-md mx-auto mb-6">
            Pengawas telah membuka akses soal. Mengalihkan Anda langsung ke lembar ujian, mohon bersiap...
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-mono text-xs animate-pulse">
            <svg class="w-4 h-4 animate-spin text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Menyiapkan Lembar Soal...</span>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 py-8 md:py-12 flex flex-col justify-center relative">

        {{-- Background Ambient Gradient Orbs --}}
        <div class="absolute -top-12 -left-16 w-80 h-80 rounded-full bg-emerald-500/10 dark:bg-emerald-500/5 blur-3xl pointer-events-none backdrop-orb-1"></div>
        <div class="absolute -bottom-12 -right-16 w-80 h-80 rounded-full bg-teal-500/10 dark:bg-teal-500/5 blur-3xl pointer-events-none backdrop-orb-2"></div>

        <div class="space-y-6 relative z-10">

            {{-- Main Hero Waiting Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 sm:p-10 glow-card relative overflow-hidden text-center shadow-xl">
                {{-- Decorative Ambient Glows inside card --}}
                <div class="absolute -right-24 -top-24 w-80 h-80 bg-emerald-500/15 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-24 -bottom-24 w-80 h-80 bg-teal-500/10 dark:bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Status Chip with Glowing Aura --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-8 shadow-xs relative">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Ruang Tunggu Ujian Aktif</span>
                </div>

                {{-- ─── QUANTUM REACTOR SPINNER ("WAH" & FUTURISTIC ANIMATION) ─── --}}
                <div class="quantum-reactor">
                    {{-- Outer dashed counter-spinning orbit ring with twin satellites --}}
                    <div class="reactor-ring-outer"></div>

                    {{-- Middle cyber glowing arc ring --}}
                    <div class="reactor-ring-mid"></div>

                    {{-- Inner high-speed counter-rotating cyan/emerald ring --}}
                    <div class="reactor-ring-inner"></div>

                    {{-- Particle dotted track --}}
                    <div class="reactor-ring-dotted"></div>

                    {{-- Center Pulsing Glowing Core with Tech Icon --}}
                    <div class="reactor-core shadow-2xl">
                        <svg class="w-7 h-7 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Interactive Live Radar Sweep Signal Bar --}}
                <div class="flex items-center justify-center gap-1.5 mb-5 opacity-80">
                    <span class="audio-wave-bar" style="animation-delay: 0.1s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.3s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.5s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.2s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.4s;"></span>
                    <span class="text-[11px] font-mono font-bold tracking-widest uppercase text-emerald-600 dark:text-emerald-400 mx-2">
                        STANDBY &bull; SYNCING
                    </span>
                    <span class="audio-wave-bar" style="animation-delay: 0.2s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.4s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.1s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.5s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.3s;"></span>
                </div>

                {{-- Quiz Title --}}
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white heading-font mb-3 tracking-tight">
                    {{ $quiz->title }}
                </h1>

                {{-- Status Descriptions --}}
                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto mb-6 leading-relaxed">
                    Data Anda telah terverifikasi dan siap mengikuti ujian laboratorium.
                    <span class="text-emerald-700 dark:text-emerald-400 font-semibold block mt-1.5 text-xs sm:text-sm">
                        Halaman ini akan otomatis beralih sendiri ke lembar soal saat pengawas menekan tombol <strong>Mulai</strong>. Anda tidak perlu me-refresh browser.
                    </span>
                </p>

                {{-- Waiting Room Live Participants Pill with Pulse --}}
                <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-emerald-50/90 dark:bg-[#090D0B] border border-emerald-300 dark:border-[#1E3327] shadow-xs mb-8">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600 dark:bg-emerald-500"></span>
                    </span>
                    <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">Peserta di Ruang Tunggu:</span>
                    <span class="font-mono font-bold text-sm text-emerald-800 dark:text-emerald-400" x-text="waitingCount + ' Mahasiswa'">{{ $waitingCount }} Mahasiswa</span>
                </div>

                {{-- Student Identity Info Card --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-xl mx-auto pt-6 border-t border-gray-200 dark:border-[#1A2820] text-left">
                    <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">Nama Mahasiswa</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white mt-0.5 block truncate">{{ $attempt->student_name }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">NIM</span>
                        <span class="text-xs font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-0.5 block">{{ $attempt->nim }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">Kelas & Status</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-gray-200 mt-0.5 block">
                            {{ $attempt->kelas ? 'Kelas ' . $attempt->kelas : 'Reguler' }} &bull; <span class="text-emerald-700 dark:text-emerald-400 font-bold">Siap</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Exam Integrity & Instructions Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-gray-100 dark:border-[#1A2820]">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-sm text-amber-600 dark:text-amber-400">
                        💡
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Petunjuk Selama Menunggu</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            ⏱️
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Hitungan Mundur Waktu</strong>
                            <span class="waiting-rule-desc">Waktu ujian ({{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' menit' : 'tanpa batas' }}) akan mulai berjalan serentak begitu pengawas menekan tombol Mulai.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/15 border border-rose-300 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            🛡️
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Sistem Anti-Cheat CBT</strong>
                            <span class="waiting-rule-desc">Dilarang berpindah tab, membuka jendela lain, atau keluar dari layar penuh guna mencegah sanksi pembekuan dan penguncian ujian otomatis.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-500/15 border border-teal-300 dark:border-teal-500/30 text-teal-700 dark:text-teal-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            💾
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Penyimpanan Otomatis</strong>
                            <span class="waiting-rule-desc">Setiap pilihan jawaban otomatis tersimpan di cache lokal browser dan disinkronkan ke server pengawas secara berkala.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-500/15 border border-amber-300 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            🔔
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Kontrol Penuh Pengawas</strong>
                            <span class="waiting-rule-desc">Pengawas dapat menyesuaikan waktu tambahan atau mengakhiri sesi kapan saja sesuai arahan instruktur laboratorium.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <footer class="py-6 border-t border-gray-200 dark:border-[#1A2820] text-center text-xs text-gray-500 dark:text-gray-600 mt-auto">
        &copy; {{ date('Y') }} IC-Labs &bull; Platform Distribusi & Evaluasi Laboratorium
    </footer>

    <script>
    function studentWaitingRoom(quizId, attemptId, quizToken) {
        return {
            quizId: quizId,
            attemptId: attemptId,
            quizToken: quizToken,
            waitingCount: {{ (int)$waitingCount }},
            isStarting: false,
            pollTimer: null,

            initWaiting() {
                // Poll every ~2.5s with slight jitter to smoothly distribute server load across 300+ concurrent students
                const jitter = Math.floor(Math.random() * 600) - 300; // 2200ms - 2800ms
                this.pollTimer = setInterval(() => {
                    this.checkStatus();
                }, 2500 + jitter);
            },

            async checkStatus() {
                try {
                    const res = await fetch(`{{ url('quiz') }}/${this.quizToken}/waiting-status/${this.attemptId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.waiting_count !== undefined) {
                        this.waitingCount = data.waiting_count;
                    }

                    // If instructor started the quiz, redirect immediately without student refreshing!
                    if (data.session_status === 'started' && data.redirect) {
                        clearInterval(this.pollTimer);
                        this.isStarting = true;
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 900);
                    } else if (data.session_status === 'ended' && data.redirect) {
                        clearInterval(this.pollTimer);
                        window.location.href = data.redirect;
                    }
                } catch (err) {
                    console.warn('Waiting room status check error:', err);
                }
            }
        };
    }
    </script>
</body>
</html>

