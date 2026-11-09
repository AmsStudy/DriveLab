<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>🎮 Live Arena Leaderboard &bull; {{ $quiz->title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800,900|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #050806;
            color: #F3F4F6;
            overflow-x: hidden;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        /* ─── 3D EMBOSSED METALLIC TYPOGRAPHY ─── */
        .text-gold-3d {
            background: linear-gradient(180deg, #FFFDF0 0%, #FDE047 30%, #F59E0B 65%, #92400E 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 10px rgba(245, 158, 11, 0.7));
        }
        .text-silver-3d {
            background: linear-gradient(180deg, #FFFFFF 0%, #E2E8F0 30%, #94A3B8 65%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 10px rgba(148, 163, 184, 0.7));
        }
        .text-bronze-3d {
            background: linear-gradient(180deg, #FFF7ED 0%, #FDBA74 30%, #EA580C 65%, #7C2D12 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 10px rgba(234, 88, 12, 0.7));
        }

        /* ─── ROTATING SUNBURST HALO BEHIND CHAMPION ─── */
        @keyframes spinHalo {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .halo-spin-gold {
            background: conic-gradient(from 0deg, transparent 0deg, rgba(245, 158, 11, 0.45) 60deg, transparent 120deg, rgba(245, 158, 11, 0.45) 180deg, transparent 240deg, rgba(245, 158, 11, 0.45) 300deg, transparent 360deg);
            animation: spinHalo 10s linear infinite;
        }
        .halo-spin-silver {
            background: conic-gradient(from 0deg, transparent 0deg, rgba(56, 189, 248, 0.35) 90deg, transparent 180deg, rgba(56, 189, 248, 0.35) 270deg, transparent 360deg);
            animation: spinHalo 14s linear infinite;
        }
        .halo-spin-bronze {
            background: conic-gradient(from 0deg, transparent 0deg, rgba(249, 115, 22, 0.35) 90deg, transparent 180deg, rgba(249, 115, 22, 0.35) 270deg, transparent 360deg);
            animation: spinHalo 14s linear infinite reverse;
        }

        /* ─── FLOATING BOUNCE FOR CROWN ─── */
        @keyframes floatBounce {
            0%, 100% { transform: translateY(0) scale(1) rotate(0deg); }
            50% { transform: translateY(-7px) scale(1.05) rotate(2deg); }
        }
        .animate-float {
            animation: floatBounce 2.5s ease-in-out infinite;
        }

        /* ─── RUNNING LASER BEAM ON PEDESTAL RIMS ─── */
        @keyframes laserRun {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        .laser-glow {
            position: relative;
            overflow: hidden;
        }
        .laser-glow::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 40%; height: 2px;
            background: linear-gradient(90deg, transparent, #FFFFFF, transparent);
            animation: laserRun 2.8s ease-in-out infinite;
            pointer-events: none;
            z-index: 20;
        }

        /* ─── VOLUMETRIC ARENA SPOTLIGHTS ─── */
        @keyframes spotlightLeft {
            0%, 100% { transform: rotate(-22deg); }
            50% { transform: rotate(-8deg); }
        }
        @keyframes spotlightRight {
            0%, 100% { transform: rotate(22deg); }
            50% { transform: rotate(8deg); }
        }
        .spotlight-left {
            position: absolute;
            top: -120px;
            left: 20%;
            width: 320px;
            height: 750px;
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.16) 0%, rgba(245, 158, 11, 0.05) 50%, transparent 85%);
            clip-path: polygon(46% 0%, 54% 0%, 100% 100%, 0% 100%);
            transform-origin: top center;
            animation: spotlightLeft 9s ease-in-out infinite;
            pointer-events: none;
        }
        .spotlight-right {
            position: absolute;
            top: -120px;
            right: 20%;
            width: 320px;
            height: 750px;
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.15) 0%, rgba(6, 182, 212, 0.05) 50%, transparent 85%);
            clip-path: polygon(46% 0%, 54% 0%, 100% 100%, 0% 100%);
            transform-origin: top center;
            animation: spotlightRight 9s ease-in-out infinite 1.5s;
            pointer-events: none;
        }

        /* ─── HOLOGRAPHIC SCANNER FOR EMPTY SLOTS ─── */
        @keyframes holoScan {
            0% { transform: translateY(-100%); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(200%); opacity: 0; }
        }
        .holo-scanline {
            animation: holoScan 2.4s ease-in-out infinite;
        }

        /* ─── ENERGY PULSE FOR PROGRESS BARS ─── */
        @keyframes energyPulse {
            0%, 100% { filter: brightness(1) drop-shadow(0 0 3px rgba(245, 158, 11, 0.4)); }
            50% { filter: brightness(1.3) drop-shadow(0 0 8px rgba(245, 158, 11, 0.8)); }
        }
        .energy-gold {
            background: linear-gradient(90deg, #D97706 0%, #F59E0B 40%, #FEF08A 70%, #10B981 100%);
            animation: energyPulse 2s ease-in-out infinite;
        }
        .energy-silver {
            background: linear-gradient(90deg, #475569 0%, #38BDF8 40%, #A5F3FC 70%, #10B981 100%);
            animation: energyPulse 2.2s ease-in-out infinite;
        }
        .energy-bronze {
            background: linear-gradient(90deg, #7C2D12 0%, #EA580C 40%, #FED7AA 70%, #10B981 100%);
            animation: energyPulse 2.2s ease-in-out infinite;
        }

        /* ─── CYBER PEDESTAL STYLING ─── */
        .pedestal-gold-cyber {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.28) 0%, rgba(146, 64, 14, 0.2) 30%, rgba(10, 16, 13, 0.98) 100%);
            border-top: 3px solid #FDE047;
            border-left: 1px solid rgba(245, 158, 11, 0.45);
            border-right: 1px solid rgba(245, 158, 11, 0.45);
            box-shadow: 0 0 40px -5px rgba(245, 158, 11, 0.35), inset 0 0 25px rgba(245, 158, 11, 0.15);
        }
        .pedestal-silver-cyber {
            background: linear-gradient(180deg, rgba(56, 189, 248, 0.22) 0%, rgba(51, 65, 85, 0.22) 30%, rgba(10, 16, 13, 0.98) 100%);
            border-top: 3px solid #38BDF8;
            border-left: 1px solid rgba(148, 163, 184, 0.4);
            border-right: 1px solid rgba(148, 163, 184, 0.4);
            box-shadow: 0 0 30px -5px rgba(56, 189, 248, 0.25), inset 0 0 20px rgba(56, 189, 248, 0.1);
        }
        .pedestal-bronze-cyber {
            background: linear-gradient(180deg, rgba(249, 115, 22, 0.22) 0%, rgba(124, 45, 18, 0.22) 30%, rgba(10, 16, 13, 0.98) 100%);
            border-top: 3px solid #FB923C;
            border-left: 1px solid rgba(249, 115, 22, 0.4);
            border-right: 1px solid rgba(249, 115, 22, 0.4);
            box-shadow: 0 0 30px -5px rgba(249, 115, 22, 0.25), inset 0 0 20px rgba(249, 115, 22, 0.1);
        }

        /* ─── TICKER & EQUALIZER ─── */
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 26s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }

        @keyframes eqBar {
            0%, 100% { height: 4px; }
            50% { height: 14px; }
        }
        .eq-bar-1 { animation: eqBar 0.7s ease-in-out infinite; }
        .eq-bar-2 { animation: eqBar 0.5s ease-in-out infinite 0.2s; }
        .eq-bar-3 { animation: eqBar 0.8s ease-in-out infinite 0.4s; }

        /* ─── CHALLENGER CARDS HOVER ─── */
        .challenger-card {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .challenger-card:hover {
            transform: translateY(-3px) scale(1.01);
            border-color: rgba(16, 185, 129, 0.6);
            box-shadow: 0 10px 25px -4px rgba(16, 185, 129, 0.25);
        }
    </style>
</head>
<body x-data="projectorArena()" x-init="initArena()" class="min-h-screen flex flex-col justify-between select-none relative bg-[#050806] text-gray-100 antialiased overflow-x-hidden">

    {{-- Canvas Partikel Ambient & Confetti (Offline Native 60fps Canvas) --}}
    <canvas id="arena-canvas" class="fixed inset-0 pointer-events-none z-50 w-full h-full"></canvas>

    {{-- Volumetric Stage Spotlights --}}
    <div class="spotlight-left"></div>
    <div class="spotlight-right"></div>

    {{-- Background Ambient Cyber Glow & Floor Mesh --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        {{-- Stadium Central Dome Glow --}}
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[1100px] h-[550px] bg-gradient-to-b from-amber-500/15 via-emerald-500/10 to-transparent blur-3xl rounded-full opacity-80"></div>
        {{-- Ambient Corner Lights --}}
        <div class="absolute top-1/3 -left-48 w-[450px] h-[450px] bg-cyan-500/12 blur-3xl rounded-full"></div>
        <div class="absolute top-1/3 -right-48 w-[450px] h-[450px] bg-amber-500/12 blur-3xl rounded-full"></div>
        {{-- Floor Grid Perspective Mesh --}}
        <div class="absolute inset-0 opacity-20"
             style="background-image: linear-gradient(rgba(16, 185, 129, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(16, 185, 129, 0.1) 1px, transparent 1px); background-size: 38px 38px;">
        </div>
        {{-- Bottom Vignette --}}
        <div class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#050806] via-[#050806]/80 to-transparent"></div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TOP NAVBAR / HUD HEADER BAR                                               --}}
    {{-- ========================================================================= --}}
    <header class="relative z-20 border-b border-[#1A2820]/90 bg-[#090F0C]/90 backdrop-blur-xl px-4 sm:px-8 py-3 flex items-center justify-between gap-4 shadow-2xl">
        {{-- Left: Live Arena Title & Dynamic Status --}}
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500 via-emerald-600 to-teal-700 flex items-center justify-center text-xl shadow-lg shadow-emerald-950/60 shrink-0 border border-white/20">
                🏆
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <template x-if="sessionStatus === 'paused'">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-500/25 text-amber-300 border border-amber-500/50 animate-pulse shadow-sm shadow-amber-500/20">
                            <span>⏸️</span> UJIAN DIJEDA (WAKTU BEKU)
                        </span>
                    </template>
                    <template x-if="sessionStatus !== 'paused'">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 shadow-sm shadow-emerald-500/20">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            LIVE ARENA
                        </span>
                    </template>
                    <span class="text-xs text-gray-400 font-mono tracking-tight" x-text="syncCounterText"></span>
                </div>
                <h1 class="text-base sm:text-lg font-extrabold text-white truncate heading-font tracking-tight mt-0.5" title="{{ $quiz->title }}">
                    {{ $quiz->title }}
                </h1>
            </div>
        </div>

        {{-- Center: Stats HUD Metrics Badges --}}
        <div class="hidden lg:flex items-center gap-3 bg-black/50 px-4 py-1.5 rounded-2xl border border-white/10 shadow-inner">
            <div class="flex items-center gap-2 px-2">
                <span class="text-base">👥</span>
                <div class="text-left">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Total Peserta</div>
                    <div class="text-sm font-black text-white font-mono leading-none mt-0.5" x-text="totalStudents">0</div>
                </div>
            </div>
            <div class="h-6 w-px bg-white/10"></div>
            <div class="flex items-center gap-2 px-2">
                <span class="text-base">🏁</span>
                <div class="text-left">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Telah Selesai</div>
                    <div class="text-sm font-black text-emerald-400 font-mono leading-none mt-0.5">
                        <span x-text="totalCompleted">0</span><span class="text-gray-500">/</span><span x-text="totalStudents">0</span>
                    </div>
                </div>
            </div>
            <div class="h-6 w-px bg-white/10"></div>
            <div class="flex items-center gap-2 px-2">
                <span class="text-base">📝</span>
                <div class="text-left">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Total Soal</div>
                    <div class="text-sm font-black text-amber-300 font-mono leading-none mt-0.5" x-text="totalQuestions">0</div>
                </div>
            </div>
            <div class="h-6 w-px bg-white/10"></div>
            <div class="flex items-center gap-2 px-2">
                <span class="text-base">⚡</span>
                <div class="text-left">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Rata-rata Progres</div>
                    <div class="text-sm font-black text-cyan-300 font-mono leading-none mt-0.5" x-text="averageProgress + '%'">0%</div>
                </div>
            </div>
        </div>

        {{-- Right: Quick Interactive Controls --}}
        <div class="flex items-center gap-2 shrink-0">
            {{-- Celebrate Button (Manual Hype Confetti Burst) --}}
            <button type="button" @click="celebrateHype()"
                    class="px-2.5 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-300 border border-amber-500/30 text-xs font-bold transition-all flex items-center gap-1 hover:scale-105 active:scale-95 shadow-sm"
                    title="Picu Ledakan Konfeti & Selebrasi Musik Arena">
                <span>🎉</span>
                <span class="hidden sm:inline">Selebrasi</span>
            </button>

            {{-- Link back to monitor --}}
            <a href="{{ route('admin.quizzes.monitor', $quiz) }}"
               class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 text-xs font-bold transition-all flex items-center gap-1.5 hover:scale-105 active:scale-95"
               title="Kembali ke Panel Pengawasan Admin">
                <span>🗗</span>
                <span class="hidden md:inline">Panel Monitor</span>
            </a>

            {{-- SFX Toggle with Dynamic Equalizer --}}
            <button type="button" @click="toggleSfx()"
                    :class="sfxEnabled ? 'bg-amber-500/20 text-amber-300 border-amber-500/40 shadow-sm shadow-amber-500/10' : 'bg-white/5 text-gray-400 border-white/10'"
                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center gap-1.5 hover:scale-105 active:scale-95"
                    title="Aktifkan / Nonaktifkan Sound Effect Game">
                <template x-if="sfxEnabled">
                    <div class="flex items-end gap-0.5 h-3.5">
                        <span class="w-1 bg-amber-400 rounded-full eq-bar-1"></span>
                        <span class="w-1 bg-amber-400 rounded-full eq-bar-2"></span>
                        <span class="w-1 bg-amber-400 rounded-full eq-bar-3"></span>
                    </div>
                </template>
                <template x-if="!sfxEnabled">
                    <span>🔇</span>
                </template>
                <span class="hidden sm:inline" x-text="sfxEnabled ? 'SFX ON' : 'SFX OFF'"></span>
            </button>

            {{-- Fullscreen Toggle --}}
            <button type="button" @click="toggleFullscreen()"
                    class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-xs font-bold transition-all flex items-center gap-1.5 hover:scale-105 active:scale-95"
                    title="Beralih ke Layar Penuh">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span class="hidden sm:inline" x-text="isFullscreen ? 'Normal' : 'Layar Penuh'"></span>
            </button>

            {{-- Refresh Manual Button --}}
            <button type="button" @click="fetchLeaderboard(true)"
                    class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 text-xs transition-all hover:scale-105 active:scale-95"
                    :disabled="isLoading" title="Segarkan Data Sekarang">
                <svg class="w-3.5 h-3.5" :class="{'animate-spin text-emerald-400': isLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- MAIN STAGE CONTENT (HERO PODIUM TOP 3 + PAPAN KLASEMEN TOP 15)           --}}
    {{-- ========================================================================= --}}
    <main class="relative z-10 flex-1 px-4 sm:px-6 lg:px-8 py-5 max-w-7xl mx-auto w-full flex flex-col justify-start gap-6">

        {{-- Loading Initial State --}}
        <template x-if="isLoading && leaderboard.length === 0">
            <div class="py-24 text-center">
                <div class="w-16 h-16 rounded-3xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce shadow-lg shadow-emerald-500/20">
                    🎮
                </div>
                <h3 class="text-xl font-black text-white heading-font">Menghubungkan ke Live Arena...</h3>
                <p class="text-xs text-gray-400 mt-1.5">Mengambil data posisi peserta ujian secara real-time...</p>
            </div>
        </template>

        {{-- Empty State (Belum ada peserta sama sekali) --}}
        <template x-if="!isLoading && leaderboard.length === 0">
            <div class="py-20 text-center max-w-lg mx-auto">
                <div class="w-20 h-20 rounded-3xl bg-amber-500/10 border-2 border-amber-500/30 flex items-center justify-center text-4xl mx-auto mb-4 animate-pulse shadow-lg shadow-amber-500/10">
                    🏁
                </div>
                <h3 class="text-2xl font-black text-white heading-font tracking-tight">Arena Belum Memiliki Peserta</h3>
                <p class="text-xs sm:text-sm text-gray-400 mt-2 leading-relaxed">
                    Belum ada mahasiswa yang memulai ujian ini. Minta peserta memasukkan kode akses dan menekan mulai kuis untuk tampil di papan live leaderboard.
                </p>
            </div>
        </template>

        {{-- Active Stage Content --}}
        <div x-show="leaderboard.length > 0" class="space-y-6">

            {{-- ================================================================= --}}
            {{-- ZONE 1: THE GRAND CHAMPIONS PODIUM (TOP 3 ESPORTS ARENA)          --}}
            {{-- ================================================================= --}}
            <div class="relative pt-2 pb-1">

                <div class="grid grid-cols-3 gap-2.5 sm:gap-6 items-end max-w-4xl mx-auto relative z-10">

                    {{-- 🥈 RANK #2: SILVER (LEFT) --}}
                    <div class="flex flex-col items-center">
                        <template x-if="leaderboard.length >= 2">
                            <div class="w-full flex flex-col items-center transition-all duration-500">
                                
                                {{-- Silver Avatar & Aura --}}
                                <div class="relative mb-2 flex flex-col items-center">
                                    {{-- Rotating Cyan Halo --}}
                                    <div class="absolute -inset-2.5 rounded-full halo-spin-silver blur-xs opacity-60 pointer-events-none"></div>

                                    {{-- Avatar Shield --}}
                                    <div class="w-16 sm:w-22 h-16 sm:h-22 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-cyan-300 via-slate-300 to-slate-500 p-1 shadow-[0_0_25px_rgba(56,189,248,0.4)] flex items-center justify-center relative z-10">
                                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-[#0B1410] flex items-center justify-center font-mono font-black text-xl sm:text-2xl text-cyan-200">
                                            <span x-text="getInitials(leaderboard[1].student_name)"></span>
                                        </div>
                                    </div>

                                    {{-- Silver Badge Pill --}}
                                    <div class="mt-2 relative z-10">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gradient-to-r from-slate-200 via-cyan-200 to-slate-300 text-slate-950 border border-white shadow-md">
                                            🥈 #2 RUNNER-UP
                                        </span>
                                    </div>
                                </div>

                                {{-- Name & Info --}}
                                <div class="text-center w-full px-1 mb-2.5">
                                    <div class="font-black text-white text-xs sm:text-base truncate heading-font tracking-tight" x-text="leaderboard[1].student_name"></div>
                                    <div class="flex items-center justify-center gap-1.5 text-[10px] sm:text-xs text-cyan-300 font-semibold truncate mt-0.5">
                                        <span x-text="'Kelas ' + (leaderboard[1].kelas || '-')"></span>
                                        <span>&bull;</span>
                                        <span :class="leaderboard[1].is_completed ? 'text-emerald-400 font-bold' : 'text-cyan-200'"
                                              x-text="leaderboard[1].is_completed ? '🏁 Selesai' : (leaderboard[1].answered_count + '/' + leaderboard[1].total_questions)"></span>
                                    </div>
                                    
                                    {{-- Delta Movement --}}
                                    <div class="mt-1 flex items-center justify-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                              :class="getDeltaClass(leaderboard[1].delta)"
                                              x-html="getDeltaHtml(leaderboard[1].delta)"></span>
                                    </div>
                                </div>

                                {{-- Silver Cyber Pedestal Base --}}
                                <div class="w-full h-36 sm:h-48 rounded-t-2xl sm:rounded-t-3xl pedestal-silver-cyber p-3 sm:p-4 flex flex-col justify-between text-center relative overflow-hidden laser-glow">
                                    {{-- 3D Silver Rank Typography --}}
                                    <div class="text-3xl sm:text-4xl font-black text-silver-3d font-mono tracking-wider">2ND</div>

                                    {{-- Holographic Center Crest --}}
                                    <div class="py-1 flex flex-col items-center justify-center">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-cyan-500/10 border border-cyan-400/30 flex items-center justify-center text-lg sm:text-xl shadow-[0_0_15px_rgba(56,189,248,0.2)]">
                                            ⚡
                                        </div>
                                    </div>
                                    
                                    {{-- Progress Energy Bar --}}
                                    <div class="w-full">
                                        <div class="text-[9px] sm:text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1 flex justify-between">
                                            <span>Progres Ujian</span>
                                            <span class="font-mono text-cyan-300 font-black" x-text="leaderboard[1].progress_percent + '%'"></span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-black/60 overflow-hidden border border-cyan-400/30 p-0.5">
                                            <div class="h-full energy-silver transition-all duration-500 rounded-full"
                                                 :style="'width: ' + leaderboard[1].progress_percent + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </template>

                        {{-- Holographic Teleporter Slot for Silver (Jika hanya ada 1 peserta) --}}
                        <template x-if="leaderboard.length < 2">
                            <div class="w-full flex flex-col items-center">
                                <div class="w-16 sm:w-22 h-16 sm:h-22 rounded-2xl sm:rounded-3xl border-2 border-dashed border-cyan-500/40 bg-cyan-950/20 flex items-center justify-center text-3xl mb-3 relative overflow-hidden animate-pulse">
                                    <span class="opacity-50">🥈</span>
                                    <div class="absolute inset-0 bg-gradient-to-b from-cyan-400/20 via-transparent to-transparent holo-scanline"></div>
                                </div>
                                <div class="w-full h-36 rounded-t-2xl bg-slate-900/40 border-t-2 border-dashed border-slate-700/60 p-4 flex flex-col items-center justify-center text-center">
                                    <span class="text-xs font-mono font-bold text-cyan-400">SLOT #2 TERBUKA</span>
                                    <span class="text-[10px] text-gray-500 mt-1">Siap Diperebutkan</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- 👑 RANK #1: GOLD CHAMPION (CENTER - ELEVATED & TALLEST) --}}
                    <div class="flex flex-col items-center">
                        <template x-if="leaderboard.length >= 1">
                            <div class="w-full flex flex-col items-center transition-all duration-500">
                                
                                {{-- Animated Floating Crown & Golden Aura --}}
                                <div class="relative mb-2 flex flex-col items-center">
                                    {{-- Floating Crown with Sparkles --}}
                                    <div class="text-3xl sm:text-4xl -mb-1 animate-float z-20 filter drop-shadow-[0_0_15px_rgba(245,158,11,0.9)]">
                                        👑
                                    </div>

                                    {{-- Rotating Golden Halo Ring --}}
                                    <div class="absolute top-6 -inset-3 rounded-full halo-spin-gold blur-xs opacity-80 pointer-events-none"></div>

                                    {{-- Avatar Shield (Grand Champion) --}}
                                    <div class="w-20 sm:w-28 h-20 sm:h-28 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-yellow-300 via-amber-400 to-yellow-600 p-1.5 shadow-[0_0_35px_rgba(245,158,11,0.6)] flex items-center justify-center relative z-10">
                                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-[#0B1410] flex items-center justify-center font-mono font-black text-2xl sm:text-4xl text-amber-300">
                                            <span x-text="getInitials(leaderboard[0].student_name)"></span>
                                        </div>
                                    </div>

                                    {{-- Gold Champion Ribbon Pill --}}
                                    <div class="mt-2 relative z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 text-slate-950 shadow-lg border border-white whitespace-nowrap animate-pulse">
                                            <span>🥇</span> #1 JUARA ARENA
                                        </span>
                                    </div>
                                </div>

                                {{-- Name & Info --}}
                                <div class="text-center w-full px-1 mb-2.5">
                                    <div class="font-black text-white text-sm sm:text-xl truncate heading-font tracking-tight text-amber-100 drop-shadow" x-text="leaderboard[0].student_name"></div>
                                    <div class="flex items-center justify-center gap-1.5 text-xs sm:text-sm text-amber-300 font-extrabold truncate mt-0.5">
                                        <span x-text="'Kelas ' + (leaderboard[0].kelas || '-')"></span>
                                        <span>&bull;</span>
                                        <span :class="leaderboard[0].is_completed ? 'text-emerald-400' : 'text-amber-200'"
                                              x-text="leaderboard[0].is_completed ? '🏁 Selesai' : (leaderboard[0].answered_count + '/' + leaderboard[0].total_questions + ' Soal')"></span>
                                    </div>
                                    
                                    {{-- Delta Movement --}}
                                    <div class="mt-1 flex items-center justify-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-black shadow-sm"
                                              :class="getDeltaClass(leaderboard[0].delta)"
                                              x-html="getDeltaHtml(leaderboard[0].delta)"></span>
                                    </div>
                                </div>

                                {{-- Gold Cyber Pedestal Base --}}
                                <div class="w-full h-48 sm:h-60 rounded-t-2xl sm:rounded-t-3xl pedestal-gold-cyber p-4 sm:p-5 flex flex-col justify-between text-center relative overflow-hidden laser-glow shadow-2xl">
                                    {{-- 3D Gold Rank Typography --}}
                                    <div class="text-4xl sm:text-6xl font-black text-gold-3d font-mono tracking-tight">1ST</div>

                                    {{-- Holographic Center Trophy Crest --}}
                                    <div class="py-1 flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-amber-400/15 border border-amber-400/40 flex items-center justify-center text-2xl sm:text-3xl shadow-[0_0_20px_rgba(245,158,11,0.3)]">
                                            🏆
                                        </div>
                                        <span class="mt-1 text-[9px] font-mono font-black text-amber-300 uppercase tracking-widest bg-amber-950/70 px-2 py-0.5 rounded-full border border-amber-500/30">
                                            PEMIMPIN
                                        </span>
                                    </div>
                                    
                                    {{-- Progress Energy Bar --}}
                                    <div class="w-full">
                                        <div class="text-[10px] sm:text-xs font-black text-amber-200 uppercase tracking-wider mb-1 flex justify-between">
                                            <span>Progres Ujian</span>
                                            <span class="font-mono text-amber-300 font-black" x-text="leaderboard[0].progress_percent + '%'"></span>
                                        </div>
                                        <div class="w-full h-3 rounded-full bg-black/70 overflow-hidden border border-amber-400/50 p-0.5 shadow-inner">
                                            <div class="h-full energy-gold transition-all duration-500 rounded-full"
                                                 :style="'width: ' + leaderboard[0].progress_percent + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>

                    {{-- 🥉 RANK #3: BRONZE (RIGHT) --}}
                    <div class="flex flex-col items-center">
                        <template x-if="leaderboard.length >= 3">
                            <div class="w-full flex flex-col items-center transition-all duration-500">
                                
                                {{-- Bronze Avatar & Aura --}}
                                <div class="relative mb-2 flex flex-col items-center">
                                    {{-- Rotating Bronze Halo --}}
                                    <div class="absolute -inset-2.5 rounded-full halo-spin-bronze blur-xs opacity-60 pointer-events-none"></div>

                                    {{-- Avatar Shield --}}
                                    <div class="w-16 sm:w-22 h-16 sm:h-22 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-amber-800 p-1 shadow-[0_0_25px_rgba(249,115,22,0.4)] flex items-center justify-center relative z-10">
                                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-[#0F1410] flex items-center justify-center font-mono font-black text-xl sm:text-2xl text-amber-300">
                                            <span x-text="getInitials(leaderboard[2].student_name)"></span>
                                        </div>
                                    </div>

                                    {{-- Bronze Badge Pill --}}
                                    <div class="mt-2 relative z-10">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gradient-to-r from-amber-600 via-orange-500 to-amber-700 text-white border border-white shadow-md">
                                            🥉 #3 KONTENDER
                                        </span>
                                    </div>
                                </div>

                                {{-- Name & Info --}}
                                <div class="text-center w-full px-1 mb-2.5">
                                    <div class="font-black text-white text-xs sm:text-base truncate heading-font tracking-tight" x-text="leaderboard[2].student_name"></div>
                                    <div class="flex items-center justify-center gap-1.5 text-[10px] sm:text-xs text-amber-400 font-semibold truncate mt-0.5">
                                        <span x-text="'Kelas ' + (leaderboard[2].kelas || '-')"></span>
                                        <span>&bull;</span>
                                        <span :class="leaderboard[2].is_completed ? 'text-emerald-400 font-bold' : 'text-amber-300'"
                                              x-text="leaderboard[2].is_completed ? '🏁 Selesai' : (leaderboard[2].answered_count + '/' + leaderboard[2].total_questions)"></span>
                                    </div>
                                    
                                    {{-- Delta Movement --}}
                                    <div class="mt-1 flex items-center justify-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                              :class="getDeltaClass(leaderboard[2].delta)"
                                              x-html="getDeltaHtml(leaderboard[2].delta)"></span>
                                    </div>
                                </div>

                                {{-- Bronze Cyber Pedestal Base --}}
                                <div class="w-full h-32 sm:h-40 rounded-t-2xl sm:rounded-t-3xl pedestal-bronze-cyber p-3 sm:p-4 flex flex-col justify-between text-center relative overflow-hidden laser-glow">
                                    {{-- 3D Bronze Rank Typography --}}
                                    <div class="text-2xl sm:text-3xl font-black text-bronze-3d font-mono tracking-wider">3RD</div>

                                    {{-- Holographic Center Crest --}}
                                    <div class="py-0.5 flex flex-col items-center justify-center">
                                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-orange-500/10 border border-orange-400/30 flex items-center justify-center text-base sm:text-lg shadow-[0_0_15px_rgba(249,115,22,0.2)]">
                                            🔥
                                        </div>
                                    </div>
                                    
                                    {{-- Progress Energy Bar --}}
                                    <div class="w-full">
                                        <div class="text-[9px] sm:text-[10px] font-bold text-amber-300 uppercase tracking-wider mb-1 flex justify-between">
                                            <span>Progres Ujian</span>
                                            <span class="font-mono text-orange-300 font-black" x-text="leaderboard[2].progress_percent + '%'"></span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-black/60 overflow-hidden border border-orange-400/30 p-0.5">
                                            <div class="h-full energy-bronze transition-all duration-500 rounded-full"
                                                 :style="'width: ' + leaderboard[2].progress_percent + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </template>

                        {{-- Holographic Teleporter Slot for Bronze (Jika < 3 peserta) --}}
                        <template x-if="leaderboard.length < 3">
                            <div class="w-full flex flex-col items-center">
                                <div class="w-16 sm:w-22 h-16 sm:h-22 rounded-2xl sm:rounded-3xl border-2 border-dashed border-orange-500/40 bg-orange-950/20 flex items-center justify-center text-3xl mb-3 relative overflow-hidden animate-pulse">
                                    <span class="opacity-50">🥉</span>
                                    <div class="absolute inset-0 bg-gradient-to-b from-orange-400/20 via-transparent to-transparent holo-scanline"></div>
                                </div>
                                <div class="w-full h-32 rounded-t-2xl bg-stone-900/40 border-t-2 border-dashed border-amber-900/60 p-4 flex flex-col items-center justify-center text-center">
                                    <span class="text-xs font-mono font-bold text-orange-400">SLOT #3 TERBUKA</span>
                                    <span class="text-[10px] text-gray-500 mt-1">Siap Diperebutkan</span>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>

            {{-- ================================================================= --}}
            {{-- ZONE 2: PAPAN KLASEMEN TOP 15 (PERINGKAT 4 SAMPAI 15)              --}}
            {{-- ================================================================= --}}
            <div class="rounded-3xl bg-[#090F0C]/85 border border-[#1A2820] p-4 sm:p-5 backdrop-blur-md shadow-2xl relative overflow-hidden">
                {{-- Section Title & Filter Info --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-white/5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-base text-emerald-300 shadow-sm">
                            ⚔️
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-white uppercase tracking-wider heading-font">
                                    Papan Klasemen Top 15 (Peringkat 4 - 15)
                                </h3>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 text-[10px] font-black font-mono border border-emerald-500/30"
                                      x-text="activeChallengersCount + ' Peserta'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-400 flex items-center gap-1.5 italic">
                        <span>🔒</span> Skor angka dirahasiakan &bull; Diurutkan otomatis berdasarkan akurasi & waktu
                    </div>
                </div>

                {{-- Tampilan saat ada peserta di peringkat 4 s/d 15 --}}
                <template x-if="leaderboard.length > 3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5">
                        <template x-for="(item, idx) in leaderboard.slice(3, 15)" :key="item.id">
                            <div class="p-3 rounded-2xl bg-[#0C1511]/90 border border-[#1A2C22] challenger-card flex items-center justify-between gap-3 shadow-md relative overflow-hidden group">
                                
                                {{-- Left: Rank Badge & Avatar Info --}}
                                <div class="flex items-center gap-2.5 min-w-0">
                                    {{-- Rank Pill --}}
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-mono font-black text-xs shrink-0 shadow-inner"
                                         :class="item.rank <= 5 ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-400/40' : (item.rank <= 10 ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-400/30' : 'bg-black/60 text-gray-300 border border-white/10')">
                                        <span x-text="'#' + item.rank"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-white text-xs sm:text-sm truncate heading-font group-hover:text-emerald-300 transition-colors" x-text="item.student_name"></div>
                                        <div class="flex items-center gap-1.5 text-[10px] text-gray-400 mt-0.5 truncate">
                                            <span x-text="'Kelas ' + (item.kelas || '-')"></span>
                                            <span>&bull;</span>
                                            <span :class="item.is_completed ? 'text-emerald-400 font-bold' : 'text-gray-400'"
                                                  x-text="item.is_completed ? '🏁 Selesai' : (item.answered_count + '/' + item.total_questions + ' Soal')"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Delta Movement & Mini Progress Bar --}}
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold"
                                          :class="getDeltaClass(item.delta)"
                                          x-html="getDeltaHtml(item.delta)"></span>
                                    
                                    {{-- Mini Progress Bar --}}
                                    <div class="w-16 h-1.5 rounded-full bg-black/60 overflow-hidden border border-white/10 mt-1">
                                        <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-300 transition-all duration-300 rounded-full"
                                             :style="'width: ' + item.progress_percent + '%'"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Tampilan saat peserta belum sampai peringkat 4 (Misal: 1 s/d 3 peserta saja) --}}
                <template x-if="leaderboard.length <= 3">
                    <div class="py-8 px-4 text-center rounded-2xl bg-black/30 border border-dashed border-white/10">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-2xl mb-3 text-emerald-400 animate-pulse">
                            🎯
                        </div>
                        <h4 class="text-sm font-bold text-white heading-font">Slot Papan Klasemen Top 15 Siap Bersaing</h4>
                        <p class="text-xs text-gray-400 max-w-md mx-auto mt-1 leading-relaxed">
                            Begitu peserta ke-4 dan seterusnya memulai dan menjawab soal, pergerakan posisi klasemen 4 sampai 15 akan langsung terupdate secara real-time di sini.
                        </p>
                        
                        {{-- Placeholder Slot Preview Badges --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-2 mt-4 max-w-3xl mx-auto opacity-50">
                            <template x-for="r in [4, 5, 6, 7, 8, 9]" :key="r">
                                <div class="p-2 rounded-xl bg-white/[0.02] border border-dashed border-white/10 text-center">
                                    <div class="text-[11px] font-mono font-bold text-gray-400" x-text="'#' + r"></div>
                                    <div class="text-[9px] text-gray-500 mt-0.5">Slot Terbuka</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </main>

    {{-- ========================================================================= --}}
    {{-- BOTTOM COMMENTATOR BROADCAST TICKER                                       --}}
    {{-- ========================================================================= --}}
    <footer class="relative z-20 border-t border-[#1A2820] bg-[#080D0A]/95 backdrop-blur-md py-2.5 px-4 overflow-hidden shadow-2xl">
        <div class="max-w-7xl mx-auto flex items-center gap-3">
            <div class="shrink-0 flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gradient-to-r from-amber-500/20 to-emerald-500/20 text-amber-300 text-xs font-black uppercase tracking-wider border border-amber-500/40 shadow-sm">
                <span class="text-sm">⚡</span> LIVE KOMENTATOR:
            </div>
            <div class="overflow-hidden whitespace-nowrap flex-1">
                <div class="animate-marquee text-xs font-semibold text-gray-300 space-x-8">
                    <span x-text="marqueeText"></span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ========================================================================= --}}
    {{-- JAVASCRIPT ARENA LOGIC, 60FPS AMBIENT PARTICLES & SFX SYNTHESIZER         --}}
    {{-- ========================================================================= --}}
    <script>
        function projectorArena() {
            return {
                quizTitle: @json($quiz->title),
                sessionStatus: 'started',
                totalStudents: 0,
                totalCompleted: 0,
                totalQuestions: {{ (int) $quiz->questions_count }},
                leaderboard: [],
                previousRanks: {}, // id => rank
                sfxEnabled: true,
                isFullscreen: false,
                isLoading: true,
                lastSyncTime: '',
                syncSecondsLeft: 4,
                audioCtx: null,
                lastAnnouncementId: null,
                marqueeText: 'Menyambungkan ke Live Arena... Persaingan peringkat berjalan secara dinamis!',
                pollingInterval: null,
                countdownInterval: null,
                activeParticles: [],
                ambientEmbers: [],
                canvas: null,
                ctx: null,

                initArena() {
                    this.fetchLeaderboard(true);
                    this.initCanvasEngine();

                    // Polling interval setiap 4 detik
                    this.pollingInterval = setInterval(() => {
                        this.fetchLeaderboard();
                    }, 4000);

                    // Countdown visual detik sinkronisasi
                    this.countdownInterval = setInterval(() => {
                        if (this.syncSecondsLeft > 1) {
                            this.syncSecondsLeft--;
                        } else {
                            this.syncSecondsLeft = 4;
                        }
                    }, 1000);

                    // Listener tombol fullscreen ESC
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });

                    // Unlock Web Audio API on first user interaction anywhere on the screen
                    document.addEventListener('click', () => {
                        this.initAudioContext();
                    }, { once: true });

                    // Session keep-alive heartbeat: cegah session timeout saat ujian berlangsung lama
                    setInterval(async () => {
                        try {
                            await fetch('{{ route('csrf.refresh') }}');
                        } catch (e) {}
                    }, 5 * 60 * 1000);
                },

                get syncCounterText() {
                    return `Pembaruan dalam ${this.syncSecondsLeft}s`;
                },

                get activeChallengersCount() {
                    return Math.max(0, this.leaderboard.length - 3);
                },

                get averageProgress() {
                    if (!this.leaderboard.length) return 0;
                    const total = this.leaderboard.reduce((acc, curr) => acc + (curr.progress_percent || 0), 0);
                    return Math.round(total / this.leaderboard.length);
                },

                async fetchLeaderboard(manual = false) {
                    if (manual) this.isLoading = true;

                    try {
                        const res = await fetch('{{ route('admin.quizzes.projector.data', $quiz) }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const data = await res.json();

                        if (data.success) {
                            this.sessionStatus = data.session_status;
                            this.totalStudents = data.total_students;
                            this.totalCompleted = data.total_completed;
                            this.totalQuestions = data.total_questions;

                            this.processLeaderboardDeltas(data.leaderboard);

                            if (data.latest_announcement && (!this.lastAnnouncementId || data.latest_announcement.id > this.lastAnnouncementId)) {
                                this.lastAnnouncementId = data.latest_announcement.id;
                                const prefix = data.latest_announcement.type === 'warning' ? '⚠️ [RALAT SOAL PENGAWAS]' : (data.latest_announcement.type === 'urgent' ? '🚨 [PERHATIAN DARURAT]' : '📢 [PENGUMUMAN PENGAWAS]');
                                this.updateMarquee(`${prefix}: "${data.latest_announcement.message}" (${data.latest_announcement.time})`);
                            }

                            this.lastSyncTime = new Date().toLocaleTimeString('id-ID');
                            this.syncSecondsLeft = 4;
                        }
                    } catch (err) {
                        console.warn('Projector fetch error:', err);
                    } finally {
                        this.isLoading = false;
                    }
                },

                processLeaderboardDeltas(newList) {
                    let leadChanged = false;
                    let bigMovers = [];

                    const processed = newList.map(item => {
                        const prevRank = this.previousRanks[item.id];
                        let delta = 0; // 0 = sama, >0 = naik, <0 = turun

                        if (prevRank !== undefined) {
                            delta = prevRank - item.rank;
                        }

                        // Deteksi pergantian juara 1
                        if (item.rank === 1 && prevRank !== undefined && prevRank !== 1) {
                            leadChanged = true;
                        }

                        if (delta >= 2) {
                            bigMovers.push({ name: item.student_name, delta: delta });
                        }

                        return {
                            ...item,
                            delta: delta
                        };
                    });

                    // Update memory previous ranks
                    const newRankMap = {};
                    processed.forEach(p => {
                        newRankMap[p.id] = p.rank;
                    });
                    this.previousRanks = newRankMap;

                    this.leaderboard = processed;

                    // Trigger Game SFX & Confetti jika ada peristiwa seru
                    if (leadChanged) {
                        this.triggerLeadChangeEffect(processed[0].student_name);
                    } else if (bigMovers.length > 0) {
                        this.playRankUpSfx();
                        this.updateMarquee(`⚡ ${bigMovers[0].name} melesat naik ${bigMovers[0].delta} peringkat! • Persaingan semakin ketat di papan atas!`);
                    } else if (this.totalCompleted > 0 && this.totalCompleted === this.totalStudents) {
                        this.updateMarquee(`🎉 Seluruh peserta telah menyelesaikan ujian! Selamat kepada para peraih peringkat teratas!`);
                    }
                },

                triggerLeadChangeEffect(leaderName) {
                    this.playLeaderTrumpetSfx();
                    this.fireConfetti();
                    this.updateMarquee(`👑 PERGANTIAN PEMIMPIN! ${leaderName} berhasil merebut posisi Peringkat 1! • ⚡ Siapakah yang akan menandinginya?`);
                },

                celebrateHype() {
                    this.fireConfetti();
                    this.playLeaderTrumpetSfx();
                    this.updateMarquee(`🎉 ARENA SELEBRASI AKTIF! Pertahankan performa terbaik hingga waktu ujian berakhir!`);
                },

                updateMarquee(text) {
                    this.marqueeText = text + ' • ' + this.marqueeText;
                },

                getInitials(name) {
                    if (!name) return '??';
                    const parts = name.trim().split(' ');
                    if (parts.length >= 2) {
                        return (parts[0][0] + parts[1][0]).toUpperCase();
                    }
                    return name.substring(0, 2).toUpperCase();
                },

                getDeltaClass(delta) {
                    if (delta > 0) return 'bg-emerald-500/25 text-emerald-300 border border-emerald-500/40';
                    if (delta < 0) return 'bg-rose-500/25 text-rose-300 border border-rose-500/40';
                    return 'bg-white/5 text-gray-400 border border-white/10';
                },

                getDeltaHtml(delta) {
                    if (delta > 0) return `▲ +${delta}`;
                    if (delta < 0) return `▼ ${delta}`;
                    return `— Stabil`;
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => console.warn(err));
                    } else {
                        if (document.exitFullscreen) document.exitFullscreen();
                    }
                },

                toggleSfx() {
                    this.sfxEnabled = !this.sfxEnabled;
                    if (this.sfxEnabled) {
                        this.initAudioContext();
                        this.playRankUpSfx();
                    }
                },

                // =============================================================
                // NATIVE WEB AUDIO SYNTHESIZER (100% Offline Tanpa MP3/WAV Eksternal)
                // =============================================================
                initAudioContext() {
                    if (!this.audioCtx) {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (AudioCtx) this.audioCtx = new AudioCtx();
                    }
                    if (this.audioCtx && this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                    }
                },

                playRankUpSfx() {
                    if (!this.sfxEnabled) return;
                    this.initAudioContext();
                    if (!this.audioCtx) return;

                    const now = this.audioCtx.currentTime;
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();

                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(523.25, now);
                    osc.frequency.setValueAtTime(659.25, now + 0.08);
                    osc.frequency.setValueAtTime(783.99, now + 0.16);

                    gain.gain.setValueAtTime(0.12, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

                    osc.connect(gain);
                    gain.connect(this.audioCtx.destination);

                    osc.start(now);
                    osc.stop(now + 0.36);
                },

                playLeaderTrumpetSfx() {
                    if (!this.sfxEnabled) return;
                    this.initAudioContext();
                    if (!this.audioCtx) return;

                    const now = this.audioCtx.currentTime;
                    [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                        const osc = this.audioCtx.createOscillator();
                        const gain = this.audioCtx.createGain();

                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(freq, now + (idx * 0.1));

                        gain.gain.setValueAtTime(0, now + (idx * 0.1));
                        gain.gain.linearRampToValueAtTime(0.15, now + (idx * 0.1) + 0.04);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + (idx * 0.1) + 0.4);

                        osc.connect(gain);
                        gain.connect(this.audioCtx.destination);

                        osc.start(now + (idx * 0.1));
                        osc.stop(now + (idx * 0.1) + 0.45);
                    });
                },

                // =============================================================
                // 60FPS AMBIENT PARTICLES & CONFETTI ENGINE (Native Offline Canvas)
                // =============================================================
                initCanvasEngine() {
                    this.canvas = document.getElementById('arena-canvas');
                    if (!this.canvas) return;
                    this.ctx = this.canvas.getContext('2d');

                    const resize = () => {
                        this.canvas.width = window.innerWidth;
                        this.canvas.height = window.innerHeight;
                    };
                    resize();
                    window.addEventListener('resize', resize);

                    // Buat 35 partikel glowing ambient ember (mengambang terus menerus di stage)
                    this.ambientEmbers = [];
                    for (let i = 0; i < 35; i++) {
                        this.ambientEmbers.push({
                            x: Math.random() * this.canvas.width,
                            y: Math.random() * this.canvas.height,
                            radius: Math.random() * 2 + 1,
                            vy: -(Math.random() * 0.4 + 0.2), // melayang ke atas lembut
                            vx: (Math.random() - 0.5) * 0.3,
                            alpha: Math.random() * 0.6 + 0.2,
                            color: Math.random() > 0.4 ? 'rgba(245, 158, 11,' : 'rgba(16, 185, 129,'
                        });
                    }

                    // Loop render animasi 60fps
                    const loop = () => {
                        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                        // 1. Gambar Ambient Embers
                        this.ambientEmbers.forEach(e => {
                            e.y += e.vy;
                            e.x += e.vx + Math.sin(e.y * 0.01) * 0.2;
                            if (e.y < 0) {
                                e.y = this.canvas.height + 10;
                                e.x = Math.random() * this.canvas.width;
                            }
                            this.ctx.beginPath();
                            this.ctx.arc(e.x, e.y, e.radius, 0, Math.PI * 2);
                            this.ctx.fillStyle = e.color + e.alpha + ')';
                            this.ctx.shadowBlur = 8;
                            this.ctx.shadowColor = e.color + '0.8)';
                            this.ctx.fill();
                            this.ctx.shadowBlur = 0;
                        });

                        // 2. Gambar Active Confetti Particles (Jika ada)
                        for (let i = this.activeParticles.length - 1; i >= 0; i--) {
                            const p = this.activeParticles[i];
                            p.x += p.vx;
                            p.y += p.vy;
                            p.vy += 0.32; // gravitasi
                            p.rotation += p.rotationSpeed;
                            p.opacity -= 0.008;

                            if (p.opacity > 0) {
                                this.ctx.save();
                                this.ctx.translate(p.x, p.y);
                                this.ctx.rotate((p.rotation * Math.PI) / 180);
                                this.ctx.globalAlpha = Math.max(0, p.opacity);
                                this.ctx.fillStyle = p.color;
                                this.ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                                this.ctx.restore();
                            } else {
                                this.activeParticles.splice(i, 1);
                            }
                        }

                        requestAnimationFrame(loop);
                    };

                    requestAnimationFrame(loop);
                },

                fireConfetti() {
                    if (!this.canvas) return;
                    const colors = ['#F59E0B', '#10B981', '#38BDF8', '#EC4899', '#8B5CF6', '#FCD34D'];

                    for (let i = 0; i < 95; i++) {
                        this.activeParticles.push({
                            x: this.canvas.width / 2,
                            y: this.canvas.height * 0.28,
                            vx: (Math.random() - 0.5) * 18,
                            vy: (Math.random() - 0.8) * 18,
                            size: Math.random() * 8 + 4,
                            color: colors[Math.floor(Math.random() * colors.length)],
                            rotation: Math.random() * 360,
                            rotationSpeed: (Math.random() - 0.5) * 10,
                            opacity: 1
                        });
                    }
                }
            };
        }
    </script>
</body>
</html>
