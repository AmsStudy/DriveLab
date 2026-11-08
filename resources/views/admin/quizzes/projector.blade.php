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
        /* Neon Glows */
        .glow-gold {
            box-shadow: 0 0 35px -5px rgba(245, 158, 11, 0.4), inset 0 0 20px rgba(245, 158, 11, 0.15);
        }
        .glow-silver {
            box-shadow: 0 0 30px -5px rgba(148, 163, 184, 0.35), inset 0 0 15px rgba(148, 163, 184, 0.1);
        }
        .glow-bronze {
            box-shadow: 0 0 30px -5px rgba(217, 119, 6, 0.35), inset 0 0 15px rgba(217, 119, 6, 0.1);
        }
        .glow-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glow-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.15);
        }
        /* Ticker Marquee Animation */
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 25s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body x-data="projectorArena()" x-init="initArena()" class="min-h-screen flex flex-col justify-between select-none relative bg-[#050806]">

    {{-- Canvas Partikel Confetti (Offline Native Canvas) --}}
    <canvas id="confetti-canvas" class="fixed inset-0 pointer-events-none z-50 w-full h-full"></canvas>

    {{-- Background Ambient Gradient & Grid Mesh --}}
    <div class="fixed inset-0 pointer-events-none opacity-40 z-0">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-emerald-500/15 via-amber-500/10 to-transparent blur-3xl rounded-full"></div>
        <div class="absolute bottom-0 left-0 right-0 h-64 bg-gradient-to-t from-emerald-950/20 to-transparent"></div>
        <div class="w-full h-full" style="background-image: radial-gradient(rgba(16, 185, 129, 0.08) 1px, transparent 1px); background-size: 32px 32px;"></div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TOP NAVBAR / HEADER BAR                                                   --}}
    {{-- ========================================================================= --}}
    <header class="relative z-20 border-b border-[#1A2820]/80 bg-[#090E0B]/90 backdrop-blur-xl px-4 sm:px-8 py-3.5 flex items-center justify-between gap-4">
        {{-- Left: Title & Badge --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-emerald-600 flex items-center justify-center text-xl shadow-lg shadow-emerald-900/30 shrink-0">
                🏆
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 -ml-3.5"></span>
                        LIVE ARENA
                    </span>
                    <span class="text-xs text-gray-400 font-mono" x-text="syncCounterText"></span>
                </div>
                <h1 class="text-base sm:text-lg font-extrabold text-white truncate heading-font tracking-tight mt-0.5" title="{{ $quiz->title }}">
                    {{ $quiz->title }}
                </h1>
            </div>
        </div>

        {{-- Center: Stats Quick Badges --}}
        <div class="hidden lg:flex items-center gap-6">
            <div class="text-center">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Peserta</div>
                <div class="text-lg font-extrabold text-white font-mono" x-text="totalStudents">0</div>
            </div>
            <div class="h-6 w-px bg-white/10"></div>
            <div class="text-center">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Telah Selesai</div>
                <div class="text-lg font-extrabold text-emerald-400 font-mono">
                    <span x-text="totalCompleted">0</span> / <span x-text="totalStudents">0</span>
                </div>
            </div>
            <div class="h-6 w-px bg-white/10"></div>
            <div class="text-center">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Soal</div>
                <div class="text-lg font-extrabold text-amber-400 font-mono" x-text="totalQuestions">0</div>
            </div>
        </div>

        {{-- Right: Interactive Controls --}}
        <div class="flex items-center gap-2 shrink-0">
            {{-- SFX Toggle --}}
            <button type="button" @click="toggleSfx()"
                    :class="sfxEnabled ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-white/5 text-gray-400 border-white/10'"
                    class="px-3 py-2 rounded-xl border text-xs font-bold transition-all flex items-center gap-1.5 hover:scale-105 active:scale-95"
                    title="Aktifkan/Matikan Suara SFX Game">
                <span x-text="sfxEnabled ? '🔊' : '🔇'"></span>
                <span class="hidden sm:inline" x-text="sfxEnabled ? 'SFX ON' : 'SFX OFF'"></span>
            </button>

            {{-- Fullscreen Toggle --}}
            <button type="button" @click="toggleFullscreen()"
                    class="px-3 py-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-xs font-bold transition-all flex items-center gap-1.5 hover:scale-105 active:scale-95"
                    title="Beralih ke Layar Penuh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span class="hidden sm:inline" x-text="isFullscreen ? 'Normal' : 'Layar Penuh'"></span>
            </button>

            {{-- Refresh Manual Button --}}
            <button type="button" @click="fetchLeaderboard(true)"
                    class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 border border-white/10 text-xs transition-all hover:scale-105 active:scale-95"
                    :disabled="isLoading" title="Segarkan Data Sekarang">
                <svg class="w-4 h-4" :class="{'animate-spin text-emerald-400': isLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- MAIN STAGE CONTENT (PODIUM & LEADERBOARD GRID)                            --}}
    {{-- ========================================================================= --}}
    <main class="relative z-10 flex-1 px-4 sm:px-8 py-6 max-w-7xl mx-auto w-full flex flex-col justify-center">

        {{-- Loading Initial State --}}
        <template x-if="isLoading && leaderboard.length === 0">
            <div class="py-24 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                    🎮
                </div>
                <h3 class="text-xl font-bold text-white heading-font">Menghubungkan ke Live Arena...</h3>
                <p class="text-sm text-gray-400 mt-1">Mengambil data posisi peserta ujian secara real-time...</p>
            </div>
        </template>

        {{-- Empty Participants State --}}
        <template x-if="!isLoading && leaderboard.length === 0">
            <div class="py-24 text-center max-w-md mx-auto">
                <div class="w-20 h-20 rounded-3xl bg-amber-500/10 border-2 border-amber-500/30 flex items-center justify-center text-4xl mx-auto mb-4 animate-pulse">
                    🏁
                </div>
                <h3 class="text-2xl font-extrabold text-white heading-font">Arena Belum Dimulai</h3>
                <p class="text-sm text-gray-400 mt-2 leading-relaxed">
                    Belum ada peserta yang memulai ujian ini. Minta mahasiswa memasukkan kode akses dan memulai pengerjaan kuis.
                </p>
            </div>
        </template>

        {{-- Active Stage Leaderboard --}}
        <div x-show="leaderboard.length > 0" class="space-y-8">

            {{-- ================================================================= --}}
            {{-- THE TOP 3 PODIUM (ESPORTS STADIUM PERSPECTIVE)                     --}}
            {{-- ================================================================= --}}
            <div class="pt-4 pb-2">
                <div class="grid grid-cols-3 gap-2 sm:gap-6 items-end max-w-4xl mx-auto">

                    {{-- 🥈 RANK #2: SILVER (LEFT) --}}
                    <div class="flex flex-col items-center">
                        <template x-if="leaderboard.length >= 2">
                            <div class="w-full flex flex-col items-center transition-all duration-500">
                                {{-- Avatar & Medal --}}
                                <div class="relative mb-2">
                                    <div class="w-14 sm:w-20 h-14 sm:h-20 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-slate-300 to-slate-500 p-1 glow-silver flex items-center justify-center shadow-xl">
                                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-[#0F1713] flex items-center justify-center font-black text-xl sm:text-2xl text-slate-200">
                                            <span x-text="getInitials(leaderboard[1].student_name)"></span>
                                        </div>
                                    </div>
                                    <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-300 text-slate-900 border border-white shadow">
                                        🥈 #2
                                    </span>
                                </div>

                                {{-- Name & Info --}}
                                <div class="text-center w-full px-1 mb-3">
                                    <div class="font-extrabold text-white text-xs sm:text-base truncate heading-font" x-text="leaderboard[1].student_name"></div>
                                    <div class="text-[10px] sm:text-xs text-slate-400 font-medium truncate mt-0.5" x-text="'Kelas ' + (leaderboard[1].kelas || '-')"></div>
                                    
                                    {{-- Delta Movement --}}
                                    <div class="mt-1 flex items-center justify-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                              :class="getDeltaClass(leaderboard[1].delta)"
                                              x-html="getDeltaHtml(leaderboard[1].delta)"></span>
                                    </div>
                                </div>

                                {{-- Silver Pillar Base --}}
                                <div class="w-full h-32 sm:h-44 rounded-t-2xl sm:rounded-t-3xl bg-gradient-to-t from-slate-900/90 via-slate-800/80 to-slate-700/60 border-t-2 border-x-2 border-slate-400/50 p-3 sm:p-4 flex flex-col justify-between text-center relative overflow-hidden shadow-2xl">
                                    <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>
                                    <div class="text-2xl sm:text-4xl font-black text-slate-300 font-mono">2ND</div>
                                    
                                    {{-- Energy Bar --}}
                                    <div class="w-full">
                                        <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1 flex justify-between">
                                            <span>Progres</span>
                                            <span x-text="leaderboard[1].progress_percent + '%'"></span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-black/40 overflow-hidden border border-white/10">
                                            <div class="h-full bg-gradient-to-r from-slate-400 to-cyan-400 transition-all duration-500 rounded-full"
                                                 :style="'width: ' + leaderboard[1].progress_percent + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty Placeholder for Silver --}}
                        <template x-if="leaderboard.length < 2">
                            <div class="w-full flex flex-col items-center opacity-40">
                                <div class="w-16 h-16 rounded-2xl bg-slate-800/50 border border-slate-700 flex items-center justify-center text-2xl mb-2">🥈</div>
                                <div class="w-full h-32 rounded-t-2xl bg-slate-900/40 border-t border-slate-800 flex items-center justify-center text-xs text-slate-500 font-bold">Menunggu...</div>
                            </div>
                        </template>
                    </div>

                    {{-- 👑 RANK #1: GOLD (CENTER - TALLEST & HIGHEST) --}}
                    <div class="flex flex-col items-center">
                        <template x-if="leaderboard.length >= 1">
                            <div class="w-full flex flex-col items-center transition-all duration-500">
                                {{-- Crown & Avatar --}}
                                <div class="relative mb-2">
                                    <div class="text-3xl sm:text-4xl -mb-2 animate-bounce text-amber-400 filter drop-shadow">👑</div>
                                    <div class="w-18 sm:w-28 h-18 sm:h-28 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-amber-300 via-amber-400 to-amber-600 p-1.5 glow-gold flex items-center justify-center shadow-2xl">
                                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-[#0D1410] flex items-center justify-center font-black text-2xl sm:text-4xl text-amber-300">
                                            <span x-text="getInitials(leaderboard[0].student_name)"></span>
                                        </div>
                                    </div>
                                    <span class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-400 to-amber-500 text-black border border-white shadow-lg animate-pulse">
                                        🥇 #1
                                    </span>
                                </div>

                                {{-- Name & Info --}}
                                <div class="text-center w-full px-1 mb-3 mt-1">
                                    <div class="font-extrabold text-white text-sm sm:text-xl truncate heading-font tracking-tight text-amber-100" x-text="leaderboard[0].student_name"></div>
                                    <div class="text-xs sm:text-sm text-amber-300/80 font-bold truncate mt-0.5" x-text="'Kelas ' + (leaderboard[0].kelas || '-')"></div>
                                    
                                    {{-- Delta Movement --}}
                                    <div class="mt-1 flex items-center justify-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-extrabold shadow-sm"
                                              :class="getDeltaClass(leaderboard[0].delta)"
                                              x-html="getDeltaHtml(leaderboard[0].delta)"></span>
                                    </div>
                                </div>

                                {{-- Gold Pillar Base --}}
                                <div class="w-full h-44 sm:h-60 rounded-t-2xl sm:rounded-t-3xl bg-gradient-to-t from-amber-950/90 via-amber-900/80 to-amber-700/60 border-t-4 border-x-2 border-amber-400 p-4 sm:p-5 flex flex-col justify-between text-center relative overflow-hidden shadow-2xl">
                                    <div class="absolute inset-0 bg-gradient-to-b from-amber-400/10 via-transparent to-transparent pointer-events-none"></div>
                                    <div class="text-3xl sm:text-5xl font-black text-amber-300 font-mono tracking-tight">1ST</div>
                                    
                                    {{-- Energy Bar --}}
                                    <div class="w-full">
                                        <div class="text-[10px] sm:text-xs font-black text-amber-200 uppercase tracking-wider mb-1 flex justify-between">
                                            <span>Progres Ujian</span>
                                            <span x-text="leaderboard[0].progress_percent + '%'"></span>
                                        </div>
                                        <div class="w-full h-3 rounded-full bg-black/50 overflow-hidden border border-amber-500/30 p-0.5">
                                            <div class="h-full bg-gradient-to-r from-amber-400 via-yellow-300 to-emerald-400 transition-all duration-500 rounded-full shadow-inner"
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
                                {{-- Avatar & Medal --}}
                                <div class="relative mb-2">
                                    <div class="w-14 sm:w-20 h-14 sm:h-20 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-amber-600 to-amber-800 p-1 glow-bronze flex items-center justify-center shadow-xl">
                                        <div class="w-full h-full rounded-xl sm:rounded-2xl bg-[#0F1713] flex items-center justify-center font-black text-xl sm:text-2xl text-amber-400">
                                            <span x-text="getInitials(leaderboard[2].student_name)"></span>
                                        </div>
                                    </div>
                                    <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-700 text-amber-100 border border-amber-400/50 shadow">
                                        🥉 #3
                                    </span>
                                </div>

                                {{-- Name & Info --}}
                                <div class="text-center w-full px-1 mb-3">
                                    <div class="font-extrabold text-white text-xs sm:text-base truncate heading-font" x-text="leaderboard[2].student_name"></div>
                                    <div class="text-[10px] sm:text-xs text-amber-500/80 font-medium truncate mt-0.5" x-text="'Kelas ' + (leaderboard[2].kelas || '-')"></div>
                                    
                                    {{-- Delta Movement --}}
                                    <div class="mt-1 flex items-center justify-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                              :class="getDeltaClass(leaderboard[2].delta)"
                                              x-html="getDeltaHtml(leaderboard[2].delta)"></span>
                                    </div>
                                </div>

                                {{-- Bronze Pillar Base --}}
                                <div class="w-full h-28 sm:h-36 rounded-t-2xl sm:rounded-t-3xl bg-gradient-to-t from-stone-900/90 via-stone-850/80 to-amber-950/60 border-t-2 border-x-2 border-amber-700/60 p-3 sm:p-4 flex flex-col justify-between text-center relative overflow-hidden shadow-2xl">
                                    <div class="absolute inset-0 bg-gradient-to-b from-amber-500/5 to-transparent pointer-events-none"></div>
                                    <div class="text-xl sm:text-3xl font-black text-amber-600 font-mono">3RD</div>
                                    
                                    {{-- Energy Bar --}}
                                    <div class="w-full">
                                        <div class="text-[10px] font-bold text-amber-400/80 uppercase tracking-wider mb-1 flex justify-between">
                                            <span>Progres</span>
                                            <span x-text="leaderboard[2].progress_percent + '%'"></span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-black/40 overflow-hidden border border-white/10">
                                            <div class="h-full bg-gradient-to-r from-amber-600 to-amber-400 transition-all duration-500 rounded-full"
                                                 :style="'width: ' + leaderboard[2].progress_percent + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty Placeholder for Bronze --}}
                        <template x-if="leaderboard.length < 3">
                            <div class="w-full flex flex-col items-center opacity-40">
                                <div class="w-16 h-16 rounded-2xl bg-amber-900/30 border border-amber-800/40 flex items-center justify-center text-2xl mb-2">🥉</div>
                                <div class="w-full h-28 rounded-t-2xl bg-stone-900/40 border-t border-stone-800 flex items-center justify-center text-xs text-amber-600/70 font-bold">Menunggu...</div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>

            {{-- ================================================================= --}}
            {{-- RUNNERS-UP CARDS (RANK 4 THROUGH 15)                               --}}
            {{-- ================================================================= --}}
            <template x-if="leaderboard.length > 3">
                <div>
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold uppercase tracking-widest text-emerald-400 heading-font flex items-center gap-1.5">
                                <span>🚀</span> PARA PENANTANG (CHALLENGERS)
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[10px] font-bold font-mono"
                                  x-text="(leaderboard.length - 3) + ' Peserta'"></span>
                        </div>
                        <div class="text-[11px] text-gray-400 italic">
                            *Skor angka dirahasiakan &bull; Diurutkan berdasarkan performa akurasi realtime
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="(item, idx) in leaderboard.slice(3, 15)" :key="item.id">
                            <div class="p-3.5 rounded-2xl bg-[#090F0C] border border-[#1A2820] hover:border-emerald-500/40 glow-card flex items-center justify-between gap-3 shadow-md relative overflow-hidden">
                                
                                {{-- Left: Rank & Avatar --}}
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-black/60 border border-white/10 flex items-center justify-center font-mono font-black text-sm text-gray-300 shrink-0">
                                        <span x-text="'#' + item.rank"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-white text-sm truncate heading-font" x-text="item.student_name"></div>
                                        <div class="flex items-center gap-2 text-[11px] text-gray-400 mt-0.5">
                                            <span x-text="'Kelas ' + (item.kelas || '-')"></span>
                                            <span>&bull;</span>
                                            <span :class="item.is_completed ? 'text-emerald-400 font-bold' : 'text-gray-400'"
                                                  x-text="item.is_completed ? '🏁 Selesai' : (item.answered_count + '/' + item.total_questions + ' Soal')"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: Delta Pill & Mini Energy Bar --}}
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                          :class="getDeltaClass(item.delta)"
                                          x-html="getDeltaHtml(item.delta)"></span>
                                    
                                    {{-- Mini Progress Bar --}}
                                    <div class="w-16 h-1.5 rounded-full bg-black/50 overflow-hidden border border-white/10 mt-1">
                                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300 rounded-full"
                                             :style="'width: ' + item.progress_percent + '%'"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>
    </main>

    {{-- ========================================================================= --}}
    {{-- BOTTOM COMMENTATOR MARQUEE TICKER                                         --}}
    {{-- ========================================================================= --}}
    <footer class="relative z-20 border-t border-[#1A2820] bg-[#080D0A]/95 backdrop-blur-md py-2.5 px-4 overflow-hidden">
        <div class="max-w-7xl mx-auto flex items-center gap-3">
            <div class="shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 text-xs font-black uppercase tracking-wider border border-amber-500/30">
                <span>⚡</span> LIVE KOMENTATOR:
            </div>
            <div class="overflow-hidden whitespace-nowrap flex-1">
                <div class="animate-marquee text-xs font-semibold text-gray-300 space-x-8">
                    <span x-text="marqueeText"></span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ========================================================================= --}}
    {{-- JAVASCRIPT ARENA LOGIC & SFX SYNTHESIZER                                  --}}
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
                marqueeText: 'Menyambungkan ke Live Arena... Persaingan peringkat berjalan secara dinamis!',
                pollingInterval: null,
                countdownInterval: null,

                initArena() {
                    this.fetchLeaderboard(true);

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
                },

                get syncCounterText() {
                    return `Pembaruan dalam ${this.syncSecondsLeft}s`;
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
                            delta = prevRank - item.rank; // misal prev 3, rank 1 => delta +2 (naik 2)
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
                    if (delta > 0) return 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                    if (delta < 0) return 'bg-rose-500/20 text-rose-300 border border-rose-500/30';
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
                // NATIVE WEB AUDIO SYNTHESIZER (Tanpa File MP3/WAV Eksternal!)
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
                    // Arpeggio nada C5 -> E5 -> G5
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
                    // Dual oscillator for rich brass fanfare
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
                // NATIVE CANVAS CONFETTI ENGINE (100% Offline & Ringan)
                // =============================================================
                fireConfetti() {
                    const canvas = document.getElementById('confetti-canvas');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;

                    const particles = [];
                    const colors = ['#F59E0B', '#10B981', '#3B82F6', '#EC4899', '#8B5CF6', '#FCD34D'];

                    for (let i = 0; i < 90; i++) {
                        particles.push({
                            x: canvas.width / 2,
                            y: canvas.height * 0.3,
                            vx: (Math.random() - 0.5) * 16,
                            vy: (Math.random() - 0.8) * 16,
                            size: Math.random() * 8 + 4,
                            color: colors[Math.floor(Math.random() * colors.length)],
                            rotation: Math.random() * 360,
                            rotationSpeed: (Math.random() - 0.5) * 10,
                            opacity: 1
                        });
                    }

                    let animationFrame;
                    const render = () => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        let alive = false;

                        particles.forEach(p => {
                            p.x += p.vx;
                            p.y += p.vy;
                            p.vy += 0.35; // gravitasi
                            p.rotation += p.rotationSpeed;
                            p.opacity -= 0.009;

                            if (p.opacity > 0) {
                                alive = true;
                                ctx.save();
                                ctx.translate(p.x, p.y);
                                ctx.rotate((p.rotation * Math.PI) / 180);
                                ctx.globalAlpha = Math.max(0, p.opacity);
                                ctx.fillStyle = p.color;
                                ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                                ctx.restore();
                            }
                        });

                        if (alive) {
                            animationFrame = requestAnimationFrame(render);
                        } else {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            cancelAnimationFrame(animationFrame);
                        }
                    };

                    render();
                }
            };
        }
    </script>
</body>
</html>
