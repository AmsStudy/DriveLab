@extends('layouts.admin')
@section('title', 'Live Exam Monitor: ' . $quiz->title)
@section('page-title', 'Live Exam Monitor')
@section('page-description', 'Pantau aktivitas pengerjaan ujian mahasiswa secara real-time di laboratorium')

@section('content')
<div x-data="liveExamMonitor({{ $quiz->id }})" x-init="initMonitor()" class="space-y-6">

    {{-- Top Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quizzes.index') }}"
               class="p-2 rounded-xl bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/40 transition-all shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $quiz->title }}</h3>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-[#142019] border border-emerald-200 dark:border-[#1E3327] text-emerald-800 dark:text-emerald-400 font-mono text-xs font-bold">{{ $quiz->access_code }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    <span class="inline-flex items-center gap-1.5 font-medium text-emerald-700 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400 animate-ping"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-600 dark:bg-emerald-400 -ml-3.5"></span>
                        Live Monitoring Aktif
                    </span>
                    <span>&bull;</span>
                    <span x-text="'Sinkronisasi terakhir: ' + lastSyncTime"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" @click="fetchData(true)"
                    class="p-2.5 rounded-xl bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm font-semibold transition-all flex items-center gap-2 shadow-xs"
                    :disabled="isLoading" title="Refresh data sekarang">
                <svg class="w-4 h-4" :class="{'animate-spin text-emerald-500': isLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span class="hidden sm:inline">Segarkan</span>
            </button>

            <a href="{{ route('admin.quizzes.projector', $quiz) }}" target="_blank"
               class="px-3 py-2 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:via-teal-500 hover:to-emerald-600 text-white text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 sm:gap-2 shadow-sm hover:shadow-md hover:shadow-emerald-500/20 border border-emerald-400/30 hover:scale-[1.02] active:scale-[0.98] group"
               title="Buka Layar Proyektor Real-Time untuk Ditampilkan di Kelas / Lab">
                <span class="text-sm transition-transform group-hover:scale-110">🎮</span>
                <span class="tracking-tight">Layar Proyektor</span>
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-black/35 text-emerald-200 border border-emerald-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    LIVE
                </span>
            </a>

            <a href="{{ route('admin.quizzes.questions', $quiz) }}"
               class="px-4 py-2.5 rounded-xl bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm font-semibold transition-all flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Bank Soal
            </a>

            <a href="{{ route('admin.quizzes.results', $quiz) }}"
               class="px-4 py-2.5 rounded-xl bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] hover:border-amber-500/40 text-gray-700 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 text-sm font-semibold transition-all flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Hasil & Rekap Nilai
            </a>
        </div>
    </div>

    {{-- ─── LIVE EXAM SESSION CONTROL BANNER (START / PAUSE / RESUME / END / WAITING ROOM) ─── --}}
    <div class="rounded-2xl p-4 sm:p-5 border transition-all duration-300 shadow-sm session-status-banner"
         :class="{
             'bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-amber-300 dark:border-amber-500/30': sessionStatus === 'waiting',
             'bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent border-emerald-300 dark:border-emerald-500/30': sessionStatus === 'started',
             'bg-gradient-to-r from-amber-500/15 via-orange-500/10 to-transparent border-amber-400 dark:border-amber-500/50 ring-2 ring-amber-500/20': sessionStatus === 'paused',
             'bg-gradient-to-r from-rose-500/10 via-rose-500/5 to-transparent border-rose-300 dark:border-rose-500/30': sessionStatus === 'ended'
         }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                {{-- Status Icon --}}
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm session-status-icon"
                     :class="{
                         'session-icon-waiting bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-500/40': sessionStatus === 'waiting',
                         'session-icon-started bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/40': sessionStatus === 'started',
                         'bg-amber-500/20 text-amber-500 border border-amber-500/50 animate-pulse': sessionStatus === 'paused',
                         'session-icon-ended bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-500/40': sessionStatus === 'ended'
                     }">
                    <template x-if="sessionStatus === 'waiting'">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                    <template x-if="sessionStatus === 'started'">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </template>
                    <template x-if="sessionStatus === 'paused'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                    <template x-if="sessionStatus === 'ended'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    </template>
                </div>

                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full session-status-badge"
                              :class="{
                                  'session-badge-waiting bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-400 border border-amber-300 dark:border-amber-500/30': sessionStatus === 'waiting',
                                  'session-badge-started bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30': sessionStatus === 'started',
                                  'bg-amber-500 text-gray-950 border border-amber-400 font-extrabold animate-pulse': sessionStatus === 'paused',
                                  'session-badge-ended bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30': sessionStatus === 'ended'
                              }"
                              x-text="sessionStatus === 'waiting' ? '⏳ Ruang Tunggu Aktif (Lobby)' : (sessionStatus === 'started' ? '🟢 Ujian Sedang Berlangsung' : (sessionStatus === 'paused' ? '⏸️ UJIAN DIJEDA DARURAT (PAUSED)' : '⛔ Ujian Telah Selesai'))">
                        </span>
                        <span class="text-xs font-mono text-gray-600 dark:text-gray-400 font-semibold"
                              x-show="sessionStatus === 'waiting'"
                              x-text="'(' + summary.total_active + ' Mahasiswa Siap di Ruang Tunggu)'"></span>
                        <span class="text-xs font-mono text-amber-600 dark:text-amber-400 font-bold"
                              x-show="sessionStatus === 'paused'"
                              x-text="'(Hitung Mundur Seluruh Layar Dibekukan)'"></span>
                    </div>

                    <h4 class="text-base font-extrabold session-banner-title mt-1.5">
                        <span x-show="sessionStatus === 'waiting'">Menunggu Pengawas Membuka Pengerjaan Soal</span>
                        <span x-show="sessionStatus === 'started'">Akses Soal Terbuka & Hitungan Mundur Waktu Berjalan</span>
                        <span x-show="sessionStatus === 'paused'">Ujian Sedang Dibekukan Sementara (Emergency Pause)</span>
                        <span x-show="sessionStatus === 'ended'">Akses Pengerjaan Ditutup & Nilai Telah Direkap</span>
                    </h4>

                    <p class="text-xs session-banner-desc mt-0.5 font-medium leading-relaxed">
                        <span x-show="sessionStatus === 'waiting'">Mahasiswa yang mengisi NIM berkumpul di Ruang Tunggu. Tekan tombol hijau di samping untuk memulai ujian serentak.</span>
                        <span x-show="sessionStatus === 'started'">Mahasiswa sedang aktif menjawab. Anda dapat menjeda ujian sewaktu-waktu jika terjadi kendala listrik/jaringan lab.</span>
                        <span x-show="sessionStatus === 'paused'">Layar ujian mahasiswa tertutup instruksi jeda. Waktu jeda TIDAK AKAN memotong sisa waktu ujian mahasiswa saat dilanjutkan.</span>
                        <span x-show="sessionStatus === 'ended'">Seluruh pengerjaan aktif telah dikumpulkan dan dinilai. Anda dapat membuka kembali ruang tunggu jika ada sesi susulan.</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap flex-shrink-0">
                {{-- Button START (When Waiting) --}}
                <button type="button"
                        x-show="sessionStatus === 'waiting'"
                        @click="confirmStartSession()"
                        :disabled="isSessionProcessing"
                        class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 flex items-center gap-2 transition-all cursor-pointer hover:scale-[1.02] active:scale-95 disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Mulai Kuis Sekarang</span>
                </button>

                {{-- Button PAUSE (When Started) --}}
                <button type="button"
                        x-show="sessionStatus === 'started'"
                        @click="confirmPauseSession()"
                        :disabled="isSessionProcessing"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-amber-500/20 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                        title="Jeda hitung mundur dan bekukan sementara layar seluruh mahasiswa">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Jeda Ujian (Pause)</span>
                </button>

                {{-- Button RESUME (When Paused) --}}
                <button type="button"
                        x-show="sessionStatus === 'paused'"
                        @click="confirmResumeSession()"
                        :disabled="isSessionProcessing"
                        class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/30 flex items-center gap-2 transition-all cursor-pointer hover:scale-[1.02] active:scale-95 disabled:opacity-50 animate-bounce">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>▶️ Lanjutkan Ujian (Resume)</span>
                </button>

                {{-- Button END (When Started OR Paused) --}}
                <button type="button"
                        x-show="sessionStatus === 'started' || sessionStatus === 'paused'"
                        @click="confirmEndSession()"
                        :disabled="isSessionProcessing"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-rose-500/20 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-95 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    <span>Hentikan Kuis</span>
                </button>

                {{-- Button RESET (When Ended) --}}
                <button type="button"
                        x-show="sessionStatus === 'ended'"
                        @click="confirmResetSession()"
                        :disabled="isSessionProcessing"
                        class="px-4 py-2.5 rounded-xl bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1E3327] hover:border-emerald-500/40 text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-semibold text-xs flex items-center gap-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Buka Ruang Tunggu Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── REAL-TIME BROADCAST CENTER (PENGUMUMAN & RALAT SOAL) ─── --}}
    <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-4 sm:p-5 shadow-xs transition-all">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-[#1A2820]/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500/20 to-emerald-500/20 border border-amber-500/30 flex items-center justify-center text-lg shadow-sm">
                    📢
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Broadcast Pengumuman & Ralat Soal</h4>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 uppercase tracking-wider">Real-Time</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pesan langsung muncul sebagai pop-up di layar seluruh mahasiswa yang sedang aktif ujian.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="isBroadcastHistoryOpen = !isBroadcastHistoryOpen"
                        class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-[#1E3327] hover:border-emerald-500/40 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <span>📜 Riwayat Siaran</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold" x-text="announcements.length">0</span>
                </button>
            </div>
        </div>

        {{-- Broadcast Form --}}
        <div class="mt-4 space-y-3">
            {{-- Type & Quick Preset Pills --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                {{-- Type Selector --}}
                <div class="flex items-center gap-1.5 p-1 rounded-xl bg-gray-100 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] w-fit">
                    <button type="button" @click="broadcastType = 'info'"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
                            :class="broadcastType === 'info' ? 'bg-sky-500 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                        <span>ℹ️ Info</span>
                    </button>
                    <button type="button" @click="broadcastType = 'warning'"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
                            :class="broadcastType === 'warning' ? 'bg-amber-500 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                        <span>⚠️ Ralat Soal</span>
                    </button>
                    <button type="button" @click="broadcastType = 'urgent'"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
                            :class="broadcastType === 'urgent' ? 'bg-rose-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                        <span>🚨 Darurat</span>
                    </button>
                </div>

                {{-- Fast Presets --}}
                <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
                    <span class="text-gray-400 text-xs hidden md:inline">Preset:</span>
                    <button type="button" @click="setBroadcastPreset('Waktu pengerjaan kuis tersisa 15 menit. Silakan periksa kembali jawaban Anda.', 'info')"
                            class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-[#142019] border border-gray-200 dark:border-[#1E3327] text-gray-600 dark:text-gray-300 hover:border-emerald-500/40 hover:text-emerald-500 transition-all cursor-pointer">
                        ⏰ Sisa 15 Menit
                    </button>
                    <button type="button" @click="setBroadcastPreset('Waktu tersisa 5 menit! Segera lengkapi jawaban sebelum lembar ujian tertutup otomatis.', 'warning')"
                            class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-[#142019] border border-gray-200 dark:border-[#1E3327] text-gray-600 dark:text-gray-300 hover:border-amber-500/40 hover:text-amber-500 transition-all cursor-pointer">
                        ⏰ Sisa 5 Menit
                    </button>
                    <button type="button" @click="setBroadcastPreset('Ralat Soal No. ... : Silakan perhatikan opsi jawaban...', 'warning')"
                            class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-[#142019] border border-gray-200 dark:border-[#1E3327] text-gray-600 dark:text-gray-300 hover:border-amber-500/40 hover:text-amber-500 transition-all cursor-pointer">
                        ⚠️ Ralat Soal
                    </button>
                    <button type="button" @click="setBroadcastPreset('Perhatian seluruh mahasiswa: Harap menjaga ketenangan dan tidak berbisik di dalam ruang lab.', 'info')"
                            class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-[#142019] border border-gray-200 dark:border-[#1E3327] text-gray-600 dark:text-gray-300 hover:border-sky-500/40 hover:text-sky-500 transition-all cursor-pointer">
                        🔇 Harap Tenang
                    </button>
                </div>
            </div>

            {{-- Message Input & Send Button --}}
            <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                <div class="relative flex-1">
                    <input type="text" x-model="broadcastMessage"
                           @keydown.enter.prevent="sendBroadcast()"
                           placeholder="Ketik pesan siaran pengumuman atau ralat soal... (Tekan Enter untuk kirim)"
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-xs sm:text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs transition-all">
                </div>

                <button type="button" @click="sendBroadcast()"
                        :disabled="isSendingBroadcast || !broadcastMessage.trim()"
                        class="px-5 py-2.5 rounded-xl text-white font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2 shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0"
                        :class="broadcastType === 'urgent' ? 'bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 shadow-rose-500/20' : (broadcastType === 'warning' ? 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 shadow-amber-500/20' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-500/20')">
                    <svg x-show="!isSendingBroadcast" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <svg x-show="isSendingBroadcast" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span x-text="isSendingBroadcast ? 'Mengirim...' : 'Kirim Siaran'"></span>
                </button>
            </div>
        </div>

        {{-- Collapsible Broadcast History --}}
        <div x-show="isBroadcastHistoryOpen" x-transition class="mt-4 pt-3 border-t border-gray-100 dark:border-[#1A2820]">
            <h5 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                <span>Daftar Pengumuman Aktif Sesi Ini</span>
                <span class="text-[11px] text-gray-400 font-normal">Mahasiswa dapat membuka kembali riwayat pengumuman ini di layar mereka</span>
            </h5>

            <template x-if="announcements.length === 0">
                <div class="py-4 text-center text-xs text-gray-400 dark:text-gray-500 bg-gray-50/50 dark:bg-[#090D0B]/50 rounded-xl border border-dashed border-gray-200 dark:border-[#1A2820]">
                    Belum ada pengumuman yang disiarkan pada sesi kuis ini.
                </div>
            </template>

            <div x-show="announcements.length > 0" class="space-y-2 max-h-56 overflow-y-auto pr-1">
                <template x-for="item in announcements" :key="item.id">
                    <div class="p-3 rounded-xl border flex items-start justify-between gap-3 transition-all"
                         :class="{
                             'bg-sky-50/60 dark:bg-sky-950/20 border-sky-200 dark:border-sky-500/30': item.type === 'info',
                             'bg-amber-50/60 dark:bg-amber-950/20 border-amber-200 dark:border-amber-500/30': item.type === 'warning',
                             'bg-rose-50/60 dark:bg-rose-950/20 border-rose-200 dark:border-rose-500/30': item.type === 'urgent'
                         }">
                        <div class="flex items-start gap-2.5">
                            <span class="text-base leading-none mt-0.5" x-text="item.type === 'urgent' ? '🚨' : (item.type === 'warning' ? '⚠️' : 'ℹ️')"></span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                          :class="{
                                              'bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300': item.type === 'info',
                                              'bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300': item.type === 'warning',
                                              'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300': item.type === 'urgent'
                                          }"
                                          x-text="item.type === 'urgent' ? 'Darurat' : (item.type === 'warning' ? 'Ralat Soal' : 'Info')"></span>
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-mono" x-text="item.time + ' (' + item.created_at_human + ')'"></span>
                                </div>
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mt-1" x-text="item.message"></p>
                            </div>
                        </div>

                        <button type="button" @click="deleteAnnouncement(item.id)"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all flex-shrink-0 cursor-pointer"
                                title="Hapus pengumuman ini">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Stats KPI Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Sedang Mengerjakan / Di Ruang Tunggu --}}
        <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-4 relative overflow-hidden shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                      x-text="sessionStatus === 'waiting' ? 'Di Ruang Tunggu' : 'Sedang Mengerjakan'">Sedang Mengerjakan</span>
                <span class="w-2.5 h-2.5 rounded-full"
                      :class="sessionStatus === 'waiting' ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500 animate-pulse'"></span>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white" x-text="summary.total_active">0</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal" x-text="'(' + summary.total_online + ' online)'"></span>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Telah Selesai</span>
            <span class="text-2xl font-bold text-teal-600 dark:text-teal-400 mt-2 block" x-text="summary.total_completed">0</span>
        </div>

        {{-- Terkunci Pinalti --}}
        <div class="bg-white dark:bg-[#0F1713] border rounded-2xl p-4 transition-colors shadow-xs"
             :class="summary.total_locked > 0 ? 'border-rose-400 dark:border-rose-500/50 bg-rose-50 dark:bg-rose-950/10' : 'border-gray-200 dark:border-[#1A2820]'">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider"
                      :class="summary.total_locked > 0 ? 'text-rose-700 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400'">Terkunci Pinalti</span>
                <span x-show="summary.total_locked > 0" class="text-xs bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 px-2 py-0.5 rounded-full font-bold">Perlu Asisten</span>
            </div>
            <span class="text-2xl font-bold mt-2 block"
                  :class="summary.total_locked > 0 ? 'text-rose-700 dark:text-rose-400' : 'text-gray-900 dark:text-gray-300'" x-text="summary.total_locked">0</span>
        </div>

        {{-- Waktu Pengerjaan --}}
        <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Batas Waktu Standar</span>
            <span class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-2 block">
                {{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' Menit' : 'Tanpa Batas' }}
            </span>
        </div>
    </div>

    {{-- Tabs & Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-[#1A2820] pb-3">
        <div class="flex items-center gap-2">
            <button type="button" @click="activeTab = 'active'"
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2"
                    :class="activeTab === 'active' ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                <span x-text="sessionStatus === 'waiting' ? 'Mahasiswa di Ruang Tunggu' : 'Peserta Aktif Mengerjakan'">Peserta Aktif</span>
                <span class="px-1.5 py-0.5 text-xs rounded-md bg-gray-100 dark:bg-[#090D0B] text-gray-700 dark:text-gray-300 font-bold" x-text="filteredActive.length">0</span>
            </button>
            <button type="button" @click="activeTab = 'completed'"
                    class="px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2"
                    :class="activeTab === 'completed' ? 'bg-teal-50 dark:bg-teal-500/15 text-teal-800 dark:text-teal-400 border border-teal-300 dark:border-teal-500/30 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">
                <span>Selesai / Mengumpulkan</span>
                <span class="px-1.5 py-0.5 text-xs rounded-md bg-gray-100 dark:bg-[#090D0B] text-gray-700 dark:text-gray-300 font-bold" x-text="filteredCompleted.length">0</span>
            </button>
        </div>

        <div class="flex items-center gap-3">
            {{-- Toggle Tampilan: Kartu (Grid) vs Tabel Ringkas (Table) --}}
            <div x-show="activeTab === 'active'" class="inline-flex items-center p-0.5 rounded-xl bg-gray-100 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820]">
                <button type="button" @click="viewMode = 'grid'"
                        class="p-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1"
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-[#142019] text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        title="Tampilan Grid Kartu">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="hidden md:inline text-[11px]">Kartu</span>
                </button>
                <button type="button" @click="viewMode = 'table'"
                        class="p-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1"
                        :class="viewMode === 'table' ? 'bg-white dark:bg-[#142019] text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        title="Tampilan Tabel Ringkas">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span class="hidden md:inline text-[11px]">Tabel</span>
                </button>
            </div>

            {{-- Quick Filter Pills for Active Tab --}}
            <div x-show="activeTab === 'active'" class="flex items-center gap-1.5 text-xs">
                <button type="button" @click="statusFilter = 'all'"
                        class="px-2.5 py-1 rounded-lg font-medium transition-colors border"
                        :class="statusFilter === 'all' ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 border-emerald-300 dark:border-emerald-500/30' : 'text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1A2820] hover:bg-gray-50 dark:hover:bg-[#1A2820]'">Semua</button>
                <button type="button" @click="statusFilter = 'locked'"
                        class="px-2.5 py-1 rounded-lg font-medium transition-colors border"
                        :class="statusFilter === 'locked' ? 'bg-rose-50 dark:bg-rose-500/15 text-rose-800 dark:text-rose-400 border-rose-300 dark:border-rose-500/30' : 'text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1A2820] hover:bg-gray-50 dark:hover:bg-[#1A2820]'">Terkunci</button>
                <button type="button" @click="statusFilter = 'online'"
                        class="px-2.5 py-1 rounded-lg font-medium transition-colors border"
                        :class="statusFilter === 'online' ? 'bg-teal-50 dark:bg-teal-500/15 text-teal-800 dark:text-teal-400 border-teal-300 dark:border-teal-500/30' : 'text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1A2820] hover:bg-gray-50 dark:hover:bg-[#1A2820]'">Online</button>
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full sm:w-60">
                <input type="text" x-model="searchQuery"
                       placeholder="Cari NIM atau Nama..."
                       class="w-full pl-8 pr-3 py-1.5 bg-white dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-xs text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 shadow-xs">
                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 absolute left-2.5 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- TAB 1: PESERTA AKTIF (SEDANG MENGERJAKAN) --}}
    <div x-show="activeTab === 'active'" x-transition>
        <template x-if="filteredActive.length === 0">
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-16 text-center text-gray-500 shadow-xs">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gray-100 dark:bg-gray-800/40 flex items-center justify-center text-gray-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-base font-semibold text-gray-800 dark:text-gray-300">Tidak ada mahasiswa yang sedang mengerjakan</p>
                <p class="text-xs text-gray-500 mt-1">Saat mahasiswa bergabung dan memulai ujian dengan kode <strong class="text-emerald-700 dark:text-emerald-400">{{ $quiz->access_code }}</strong>, status pengerjaan mereka akan otomatis muncul di sini secara real-time.</p>
            </div>
        </template>

        <div x-show="filteredActive.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="item in filteredActive" :key="item.id">
                <div class="bg-white dark:bg-[#0F1713] border rounded-2xl p-5 shadow-sm relative overflow-hidden transition-all hover:border-emerald-500/30"
                     :class="item.is_locked ? 'border-rose-400 dark:border-rose-500/50 bg-rose-50/50 dark:bg-rose-950/10' : (item.is_online ? 'border-gray-200 dark:border-[#1A2820]' : 'border-gray-200 dark:border-[#1A2820] opacity-80')">

                    {{-- Top Status Row --}}
                    <div class="flex items-center justify-between mb-3">
                        {{-- Online / Offline Badge --}}
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full"
                                  :class="item.is_online ? 'bg-emerald-500 dark:bg-emerald-400 animate-pulse' : 'bg-gray-400 dark:bg-gray-500'"></span>
                            <span class="text-xs font-semibold"
                                  :class="item.is_online ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'"
                                  x-text="item.is_online ? 'Online' : 'Idle / Offline'"></span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-mono" x-text="'&bull; ' + item.last_heartbeat_human"></span>
                        </div>

                        {{-- Lock Badge / Tab Switch Warning --}}
                        <div>
                            <template x-if="item.is_locked">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    TERKUNCI
                                </span>
                            </template>
                            <template x-if="!item.is_locked && item.tab_switch_count > 0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 dark:bg-amber-500/15 text-amber-800 dark:text-amber-400 border border-amber-300 dark:border-amber-500/30"
                                      x-text="item.tab_switch_count + 'x Switch'"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Student Name & NIM --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="item.student_name"></h4>
                            <template x-if="item.kelas">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-[#142019] border border-emerald-200 dark:border-[#1E3327] text-emerald-800 dark:text-emerald-400 font-mono text-[10px] font-bold flex-shrink-0" x-text="'Kelas ' + item.kelas"></span>
                            </template>
                        </div>
                        <p class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5" x-text="item.nim"></p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="space-y-1.5 mb-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Progres Jawaban</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono text-emerald-700 dark:text-emerald-400 font-bold" x-text="item.answered_count + '/' + item.total_questions"></span>
                                <template x-if="item.flagged_count > 0">
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-400 border border-amber-300 dark:border-amber-500/30 flex items-center gap-0.5"
                                          title="Jumlah soal ditandai Ragu-ragu">
                                        🚩 <span x-text="item.flagged_count"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-[#090D0B] overflow-hidden border border-gray-200 dark:border-[#1A2820]">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500"
                                 :style="'width: ' + item.progress_percent + '%'"></div>
                        </div>
                    </div>

                    {{-- Time Remaining & Details --}}
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-100 dark:border-[#1A2820] mb-4">
                        <span class="text-gray-500 dark:text-gray-400">Sisa Waktu:</span>
                        <div class="flex items-center gap-1.5 font-mono">
                            <span class="font-bold text-amber-600 dark:text-amber-400 text-sm" x-text="item.time_remaining_human"></span>
                            <template x-if="item.extra_time_minutes > 0">
                                <span class="text-[10px] bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-1 rounded font-bold"
                                      x-text="'+' + item.extra_time_minutes + 'm'"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Actions Toolbar --}}
                    <div class="flex items-center gap-2 pt-1">
                        {{-- Unlock Button (If Locked) --}}
                        <template x-if="item.is_locked">
                            <button type="button" @click="remoteUnlock(item)"
                                    class="flex-1 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500/20 dark:hover:bg-emerald-500/30 border border-emerald-600 dark:border-emerald-500/40 text-white dark:text-emerald-300 font-semibold text-xs transition-colors flex items-center justify-center gap-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                Buka Kunci
                            </button>
                        </template>

                        {{-- Add Time Button --}}
                        <button type="button" @click="openAddTimeModal(item)"
                                class="py-1.5 px-3 rounded-xl bg-gray-50 hover:bg-gray-100 dark:bg-[#090D0B] dark:hover:bg-[#142019] border border-gray-200 dark:border-[#1A2820] text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium text-xs transition-colors flex items-center gap-1 shadow-xs"
                                :class="{'flex-1': !item.is_locked}">
                            <svg class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Waktu
                        </button>

                        {{-- Force Submit Button --}}
                        <button type="button" @click="confirmForceSubmit(item)"
                                class="py-1.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 font-medium text-xs transition-colors flex items-center gap-1 shadow-xs"
                                title="Paksa Kumpulkan Ujian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Kumpul
                        </button>
                    </div>

                </div>
            </template>
        </div>

        {{-- TAMPILAN TABEL RINGKAS (UNTUK MONITORING CEPAT 300 MAHASISWA) --}}
        <div x-show="filteredActive.length > 0 && viewMode === 'table'" class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="border-b border-gray-200 dark:border-[#1A2820] text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-[#090D0B]">
                        <tr>
                            <th class="px-4 py-3.5 font-semibold">Mahasiswa</th>
                            <th class="px-3 py-3.5 font-semibold text-center">Status</th>
                            <th class="px-4 py-3.5 font-semibold text-center">Progres Jawaban</th>
                            <th class="px-4 py-3.5 font-semibold text-center">Sisa Waktu</th>
                            <th class="px-3 py-3.5 font-semibold text-center">Switch Tab</th>
                            <th class="px-4 py-3.5 font-semibold text-right">Aksi Pengawas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#1A2820]">
                        <template x-for="item in filteredActive" :key="item.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#142019]/40 transition-colors"
                                :class="item.is_locked ? 'bg-rose-50/40 dark:bg-rose-950/20' : ''">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-gray-900 dark:text-white" x-text="item.student_name"></div>
                                        <template x-if="item.kelas">
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-[#142019] border border-emerald-200 dark:border-[#1E3327] text-emerald-800 dark:text-emerald-400 font-mono text-[10px] font-bold" x-text="'Kelas ' + item.kelas"></span>
                                        </template>
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 dark:text-gray-400" x-text="item.nim"></div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                         :class="item.is_locked ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30' : (item.is_online ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-700')">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="item.is_locked ? 'bg-rose-500' : (item.is_online ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400')"></span>
                                        <span x-text="item.is_locked ? 'TERKUNCI' : (item.is_online ? 'Online' : 'Terputus')"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="font-mono font-bold text-xs text-gray-800 dark:text-gray-200" x-text="item.answered_count + '/' + item.total_questions"></span>
                                        <span class="text-[10px] text-gray-400 font-mono" x-text="'(' + item.progress_percent + '%)'"></span>
                                        <template x-if="item.flagged_count > 0">
                                            <span class="text-[10px] bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-400 px-1 rounded font-semibold" title="Ragu-ragu">🚩<span x-text="item.flagged_count"></span></span>
                                        </template>
                                    </div>
                                    <div class="w-24 h-1.5 mx-auto mt-1 rounded-full bg-gray-100 dark:bg-[#090D0B] overflow-hidden border border-gray-200 dark:border-[#1A2820]">
                                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300" :style="'width: ' + item.progress_percent + '%'"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-xs">
                                    <span class="font-bold text-amber-600 dark:text-amber-400" x-text="item.time_remaining_human"></span>
                                    <template x-if="item.extra_time_minutes > 0">
                                        <span class="text-[10px] bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 px-1 rounded font-bold ml-1" x-text="'+' + item.extra_time_minutes + 'm'"></span>
                                    </template>
                                </td>
                                <td class="px-3 py-3 text-center text-xs">
                                    <template x-if="item.tab_switch_count > 0">
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-amber-50 dark:bg-amber-500/15 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30" x-text="item.tab_switch_count + 'x'"></span>
                                    </template>
                                    <template x-if="!item.tab_switch_count">
                                        <span class="text-gray-400 dark:text-gray-500 font-mono text-[11px]">0</span>
                                    </template>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <template x-if="item.is_locked">
                                            <button type="button" @click="remoteUnlock(item)"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs" title="Buka Kunci Layar">
                                                Buka
                                            </button>
                                        </template>
                                        <button type="button" @click="openAddTimeModal(item)"
                                                class="px-2 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-[#142019] dark:hover:bg-[#1E3327] border border-gray-200 dark:border-[#1E3327] text-gray-700 dark:text-gray-300 text-xs font-semibold shadow-xs" title="Tambah Waktu Ujian">
                                            + Waktu
                                        </button>
                                        <button type="button" @click="confirmForceSubmit(item)"
                                                class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/15 dark:hover:bg-rose-500/25 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 text-xs font-semibold shadow-xs" title="Paksa Kumpulkan">
                                            Kumpul
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TAB 2: PESERTA SELESAI --}}
    <div x-show="activeTab === 'completed'" x-transition>
        <template x-if="filteredCompleted.length === 0">
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-16 text-center text-gray-500 shadow-xs">
                <p class="text-base font-semibold text-gray-800 dark:text-gray-300">Belum ada peserta yang menyelesaikan kuis</p>
                <p class="text-xs text-gray-500 mt-1">Setelah mahasiswa menekan tombol kumpulkan ujian atau waktu habis, data nilai mereka akan otomatis muncul di sini.</p>
            </div>
        </template>

        <div x-show="filteredCompleted.length > 0" class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="border-b border-gray-200 dark:border-[#1A2820] text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-[#090D0B]">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Mahasiswa</th>
                            <th class="px-4 py-3.5 font-semibold text-center">Nilai</th>
                            <th class="px-4 py-3.5 font-semibold text-center">Jawaban Dijawab</th>
                            <th class="px-4 py-3.5 font-semibold text-center">Pelanggaran</th>
                            <th class="px-4 py-3.5 font-semibold text-center">Status Submit</th>
                            <th class="px-4 py-3.5 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#1A2820]">
                        <template x-for="item in filteredCompleted" :key="item.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#142019]/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-gray-900 dark:text-white" x-text="item.student_name"></div>
                                        <template x-if="item.kelas">
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-[#142019] border border-emerald-200 dark:border-[#1E3327] text-emerald-800 dark:text-emerald-400 font-mono text-[10px] font-bold" x-text="'Kelas ' + item.kelas"></span>
                                        </template>
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 dark:text-gray-400" x-text="item.nim"></div>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold font-mono text-base"
                                    :class="item.score >= 70 ? 'text-emerald-700 dark:text-emerald-400' : (item.score >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400')"
                                    x-text="item.score"></td>
                                <td class="px-4 py-3.5 text-center font-mono text-xs text-gray-700 dark:text-gray-300"
                                    x-text="item.answered_count + ' / ' + item.total_questions"></td>
                                <td class="px-4 py-3.5 text-center">
                                    <template x-if="item.tab_switch_count > 0">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30"
                                              x-text="item.tab_switch_count + 'x Switch'"></span>
                                    </template>
                                    <template x-if="!item.tab_switch_count">
                                        <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">0 (Bersih)</span>
                                    </template>
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs">
                                    <template x-if="item.is_force_submitted">
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-amber-50 dark:bg-amber-500/15 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30">Force Submit</span>
                                    </template>
                                    <template x-if="!item.is_force_submitted">
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-50 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">Mandiri / Waktu</span>
                                    </template>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a :href="'{{ url('quiz/' . $quiz->unique_token . '/results') }}/' + item.id" target="_blank"
                                       class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 hover:underline">
                                        Detail Lembar Jawaban &rarr;
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL: TAMBAH WAKTU --}}
    <div x-show="showAddTimeModal" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 dark:bg-black/80 backdrop-blur-sm"
         style="z-index: 100;"
         x-transition>
        <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-6 max-w-sm w-full shadow-2xl space-y-4"
             @click.away="showAddTimeModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-[#1A2820]">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="text-amber-500 dark:text-amber-400">⏱️</span> Tambah Waktu Ujian
                </h4>
                <button type="button" @click="showAddTimeModal = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <p class="text-xs text-gray-600 dark:text-gray-300">Pilih durasi tambahan untuk:</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5" x-text="targetStudent ? targetStudent.student_name + ' (' + targetStudent.nim + ')' : ''"></p>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="addedMinutes = 5"
                        class="py-2.5 rounded-xl border text-xs font-bold transition-all"
                        :class="addedMinutes === 5 ? 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/40' : 'bg-gray-50 dark:bg-[#090D0B] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1A2820] hover:bg-gray-100 dark:hover:bg-[#1A2820]'">+5 Menit</button>
                <button type="button" @click="addedMinutes = 10"
                        class="py-2.5 rounded-xl border text-xs font-bold transition-all"
                        :class="addedMinutes === 10 ? 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/40' : 'bg-gray-50 dark:bg-[#090D0B] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1A2820] hover:bg-gray-100 dark:hover:bg-[#1A2820]'">+10 Menit</button>
                <button type="button" @click="addedMinutes = 15"
                        class="py-2.5 rounded-xl border text-xs font-bold transition-all"
                        :class="addedMinutes === 15 ? 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/40' : 'bg-gray-50 dark:bg-[#090D0B] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1A2820] hover:bg-gray-100 dark:hover:bg-[#1A2820]'">+15 Menit</button>
            </div>

            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Atau masukkan menit kustom:</label>
                <input type="number" min="1" max="60" x-model.number="addedMinutes"
                       class="w-full bg-gray-50 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-emerald-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="button" @click="showAddTimeModal = false"
                        class="flex-1 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-[#090D0B] dark:hover:bg-[#1A2820] border border-gray-200 dark:border-[#1A2820] text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-xs font-semibold">Batal</button>
                <button type="button" @click="submitAddTime()"
                        class="flex-1 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20">
                    Simpan & Tambah
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function liveExamMonitor(quizId) {
    return {
        quizId: quizId,
        sessionStatus: '{{ $quiz->session_status ?? 'waiting' }}',
        isSessionProcessing: false,
        activeTab: 'active',
        viewMode: 'grid',
        statusFilter: 'all',
        searchQuery: '',
        isLoading: false,
        timer: null,
        lastSyncTime: '-',
        summary: {
            total_active: 0,
            total_completed: 0,
            total_locked: 0,
            total_online: 0,
            quiz_time_limit: 0
        },
        activeAttempts: [],
        completedAttempts: [],

        // Broadcast Announcements
        announcements: [],
        broadcastMessage: '',
        broadcastType: 'info',
        isSendingBroadcast: false,
        isBroadcastHistoryOpen: false,

        // Modal Add Time
        showAddTimeModal: false,
        targetStudent: null,
        addedMinutes: 5,

        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        },

        initMonitor() {
            this.fetchData();
            // Auto refresh realtime setiap 4 detik tanpa reload halaman
            this.timer = setInterval(() => {
                this.fetchData();
            }, 4000);

            // Heartbeat penyegar token CSRF tiap 10 menit agar sesi admin tidak kadaluarsa
            setInterval(async () => {
                try {
                    const res = await fetch('{{ route('csrf.refresh') }}');
                    const data = await res.json();
                    if (data && data.csrf_token) {
                        const meta = document.querySelector('meta[name="csrf-token"]');
                        if (meta) meta.setAttribute('content', data.csrf_token);
                    }
                } catch (e) {}
            }, 10 * 60 * 1000);
        },

        get filteredActive() {
            return this.activeAttempts.filter(item => {
                const matchSearch = !this.searchQuery ||
                    item.student_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    item.nim.toLowerCase().includes(this.searchQuery.toLowerCase());

                if (!matchSearch) return false;

                if (this.statusFilter === 'locked') return item.is_locked;
                if (this.statusFilter === 'online') return item.is_online;
                return true;
            });
        },

        get filteredCompleted() {
            return this.completedAttempts.filter(item => {
                if (!this.searchQuery) return true;
                return item.student_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                       item.nim.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        },

        isFetching: false,
        async fetchData(manual = false) {
            if (this.isFetching) return;
            this.isFetching = true;
            if (manual) this.isLoading = true;
            try {
                const res = await fetch(`{{ route('admin.quizzes.monitor.data', $quiz) }}`);
                const data = await res.json();
                if (data.success) {
                    if (data.session_status) {
                        this.sessionStatus = data.session_status;
                    }
                    if (data.announcements) {
                        this.announcements = data.announcements;
                    }
                    this.summary = data.summary;
                    this.activeAttempts = data.active;
                    this.completedAttempts = data.completed;
                    const now = new Date();
                    this.lastSyncTime = now.toLocaleTimeString('id-ID');
                }
            } catch (err) {
                console.warn('Live monitor fetch error:', err);
            } finally {
                this.isFetching = false;
                if (manual) this.isLoading = false;
            }
        },

        confirmStartSession() {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Mulai Kuis Sekarang?',
                html: `<p class="text-sm ${isDark ? 'text-gray-300' : 'text-gray-700'}">Apakah Anda yakin ingin membuka akses soal untuk kuis <strong>{{ $quiz->title }}</strong>?</p><p class="text-xs ${isDark ? 'text-emerald-400' : 'text-emerald-700'} mt-2.5 font-semibold">Seluruh mahasiswa di Ruang Tunggu akan otomatis dialihkan ke lembar ujian, dan hitungan mundur waktu akan mulai berjalan serentak.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: isDark ? '#374151' : '#6B7280',
                confirmButtonText: '🚀 Ya, Mulai Kuis!',
                cancelButtonText: 'Batal',
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                customClass: {
                    popup: isDark ? 'border border-emerald-500/40 rounded-2xl shadow-2xl' : 'border border-emerald-200 rounded-2xl shadow-2xl'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isSessionProcessing = true;
                    try {
                        const res = await fetch(`{{ route('admin.quizzes.session.start', $quiz) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.sessionStatus = 'started';
                            Swal.fire({
                                icon: 'success',
                                title: 'Kuis Berhasil Dimulai!',
                                text: data.message,
                                timer: 2500,
                                showConfirmButton: false,
                                background: isDark ? '#0F1713' : '#FFFFFF',
                                color: isDark ? '#34D399' : '#059669',
                                customClass: { popup: isDark ? 'border border-emerald-500/40 rounded-xl' : 'border border-emerald-200 rounded-xl' }
                            });
                            this.fetchData(true);
                        } else {
                            Swal.fire('Gagal', data.message || 'Gagal memulai kuis.', 'error');
                        }
                    } catch (err) {
                        Swal.fire('Gagal', 'Terjadi kesalahan sistem saat memulai kuis.', 'error');
                    } finally {
                        this.isSessionProcessing = false;
                    }
                }
            });
        },

        confirmPauseSession() {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Jeda Ujian Massal Darurat?',
                html: `<p class="text-sm ${isDark ? 'text-gray-300' : 'text-gray-700'}">Apakah Anda yakin ingin menjeda ujian untuk kuis <strong>{{ $quiz->title }}</strong>?</p><div class="mt-3 p-3 rounded-xl ${isDark ? 'bg-amber-500/10 border border-amber-500/30 text-amber-300' : 'bg-amber-50 border border-amber-200 text-amber-800'} text-xs text-left space-y-1"><p class="font-bold">⚡ Dampak Jeda Darurat:</p><ul class="list-disc pl-4 space-y-0.5"><li>Hitungan mundur seluruh mahasiswa langsung dibekukan.</li><li>Layar ujian tertutup instruksi jeda dari pengawas.</li><li>Durasi jeda <b>TIDAK AKAN</b> mengurangi waktu ujian mahasiswa saat dilanjutkan.</li></ul></div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: isDark ? '#374151' : '#6B7280',
                confirmButtonText: '⏸️ Ya, Jeda Ujian Sekarang!',
                cancelButtonText: 'Batal',
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                customClass: { popup: isDark ? 'border border-amber-500/40 rounded-2xl shadow-2xl' : 'border border-amber-200 rounded-2xl shadow-2xl' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isSessionProcessing = true;
                    try {
                        const res = await fetch(`{{ route('admin.quizzes.session.pause', $quiz) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.sessionStatus = 'paused';
                            Swal.fire({
                                icon: 'success',
                                title: 'Ujian Berhasil Dijeda!',
                                text: data.message,
                                timer: 2500,
                                showConfirmButton: false,
                                background: isDark ? '#0F1713' : '#FFFFFF',
                                color: isDark ? '#F59E0B' : '#B45309',
                                customClass: { popup: isDark ? 'border border-amber-500/40 rounded-xl' : 'border border-amber-200 rounded-xl' }
                            });
                            this.fetchData(true);
                        } else {
                            Swal.fire('Gagal', data.message || 'Gagal menjeda kuis.', 'error');
                        }
                    } catch (err) {
                        Swal.fire('Gagal', 'Terjadi kesalahan sistem saat menjeda kuis.', 'error');
                    } finally {
                        this.isSessionProcessing = false;
                    }
                }
            });
        },

        confirmResumeSession() {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Lanjutkan Kembali Ujian?',
                html: `<p class="text-sm ${isDark ? 'text-gray-300' : 'text-gray-700'}">Akses pengerjaan soal akan dibuka kembali untuk seluruh mahasiswa.</p><p class="text-xs ${isDark ? 'text-emerald-400' : 'text-emerald-700'} mt-2.5 font-semibold">Waktu pengerjaan seluruh mahasiswa otomatis diperpanjang sesuai lama jeda sehingga tidak ada waktu yang terpotong.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: isDark ? '#374151' : '#6B7280',
                confirmButtonText: '▶️ Ya, Lanjutkan Ujian!',
                cancelButtonText: 'Batal',
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                customClass: { popup: isDark ? 'border border-emerald-500/40 rounded-2xl shadow-2xl' : 'border border-emerald-200 rounded-2xl shadow-2xl' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isSessionProcessing = true;
                    try {
                        const res = await fetch(`{{ route('admin.quizzes.session.resume', $quiz) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.sessionStatus = 'started';
                            Swal.fire({
                                icon: 'success',
                                title: 'Ujian Dilanjutkan!',
                                text: data.message,
                                timer: 2500,
                                showConfirmButton: false,
                                background: isDark ? '#0F1713' : '#FFFFFF',
                                color: isDark ? '#34D399' : '#059669',
                                customClass: { popup: isDark ? 'border border-emerald-500/40 rounded-xl' : 'border border-emerald-200 rounded-xl' }
                            });
                            this.fetchData(true);
                        } else {
                            Swal.fire('Gagal', data.message || 'Gagal melanjutkan kuis.', 'error');
                        }
                    } catch (err) {
                        Swal.fire('Gagal', 'Terjadi kesalahan sistem saat melanjutkan kuis.', 'error');
                    } finally {
                        this.isSessionProcessing = false;
                    }
                }
            });
        },

        confirmEndSession() {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Hentikan / Akhiri Kuis?',
                html: `<p class="text-sm ${isDark ? 'text-gray-300' : 'text-gray-700'}">Apakah Anda yakin ingin menghentikan sesi kuis <strong>{{ $quiz->title }}</strong> sekarang?</p><p class="text-xs ${isDark ? 'text-rose-400' : 'text-rose-700'} mt-2.5 font-semibold">Akses pengerjaan akan segera ditutup. Seluruh lembar ujian mahasiswa yang masih aktif akan otomatis dikumpulkan dan dinilai.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: isDark ? '#374151' : '#6B7280',
                confirmButtonText: '⛔ Ya, Hentikan Kuis!',
                cancelButtonText: 'Batal',
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                customClass: {
                    popup: isDark ? 'border border-rose-500/40 rounded-2xl shadow-2xl' : 'border border-rose-200 rounded-2xl shadow-2xl'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isSessionProcessing = true;
                    try {
                        const res = await fetch(`{{ route('admin.quizzes.session.end', $quiz) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.sessionStatus = 'ended';
                            Swal.fire({
                                icon: 'success',
                                title: 'Kuis Telah Dihentikan!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false,
                                background: isDark ? '#0F1713' : '#FFFFFF',
                                color: isDark ? '#34D399' : '#059669',
                                customClass: { popup: isDark ? 'border border-emerald-500/40 rounded-xl' : 'border border-emerald-200 rounded-xl' }
                            });
                            this.fetchData(true);
                        } else {
                            Swal.fire('Gagal', data.message || 'Gagal menghentikan kuis.', 'error');
                        }
                    } catch (err) {
                        Swal.fire('Gagal', 'Terjadi kesalahan sistem saat menghentikan kuis.', 'error');
                    } finally {
                        this.isSessionProcessing = false;
                    }
                }
            });
        },

        confirmResetSession() {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'Buka Kembali Ruang Tunggu?',
                html: `<p class="text-sm ${isDark ? 'text-gray-300' : 'text-gray-700'}">Status sesi kuis akan dikembalikan ke <strong>Ruang Tunggu</strong> sehingga peserta baru dapat bergabung kembali.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: isDark ? '#374151' : '#6B7280',
                confirmButtonText: 'Ya, Buka Ruang Tunggu',
                cancelButtonText: 'Batal',
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                customClass: {
                    popup: isDark ? 'border border-[#1A2820] rounded-2xl shadow-2xl' : 'border border-gray-200 rounded-2xl shadow-2xl'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isSessionProcessing = true;
                    try {
                        const res = await fetch(`{{ route('admin.quizzes.session.reset', $quiz) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.sessionStatus = 'waiting';
                            this.fetchData(true);
                        }
                    } catch (err) {
                        Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                    } finally {
                        this.isSessionProcessing = false;
                    }
                }
            });
        },

        async remoteUnlock(item) {
            try {
                const res = await fetch(`{{ url('admin/quizzes/' . $quiz->id . '/attempts') }}/${item.id}/unlock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2500,
                        background: '#0F1713',
                        color: '#34D399',
                        customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                    });
                    this.fetchData();
                }
            } catch (err) {
                Swal.fire('Gagal', 'Terjadi kesalahan saat membuka kunci.', 'error');
            }
        },

        openAddTimeModal(item) {
            this.targetStudent = item;
            this.addedMinutes = 5;
            this.showAddTimeModal = true;
        },

        async submitAddTime() {
            if (!this.targetStudent || !this.addedMinutes) return;

            try {
                const res = await fetch(`{{ url('admin/quizzes/' . $quiz->id . '/attempts') }}/${this.targetStudent.id}/add-time`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ minutes: this.addedMinutes })
                });
                const data = await res.json();
                if (data.success) {
                    this.showAddTimeModal = false;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        background: '#0F1713',
                        color: '#34D399',
                        customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                    });
                    this.fetchData();
                } else {
                    Swal.fire('Perhatian', data.message || 'Gagal menambah waktu', 'warning');
                }
            } catch (err) {
                Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error');
            }
        },

        confirmForceSubmit(item) {
            Swal.fire({
                title: 'Paksa Kumpulkan Ujian?',
                html: `Apakah Anda yakin ingin mengumpulkan ujian untuk <b>${item.student_name} (${item.nim})</b> sekarang?<br><small class="text-gray-400">Jawaban yang telah di-autosave sejauh ini akan dihitung sebagai skor final.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#1F2937',
                confirmButtonText: 'Ya, Paksa Kumpul',
                cancelButtonText: 'Batal',
                background: '#0F1713',
                color: '#fff',
                customClass: { popup: 'border border-[#1A2820] rounded-2xl' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(`{{ url('admin/quizzes/' . $quiz->id . '/attempts') }}/${item.id}/force-submit`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#0F1713',
                                color: '#34D399',
                                customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                            });
                            this.fetchData();
                        }
                    } catch (err) {
                        Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                    }
                }
            });
        },

        setBroadcastPreset(text, type = null) {
            this.broadcastMessage = text;
            if (type) this.broadcastType = type;
        },

        async sendBroadcast() {
            const msg = (this.broadcastMessage || '').trim();
            if (!msg) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pesan Kosong',
                    text: 'Silakan ketik pesan pengumuman sebelum mengirim.',
                    background: document.documentElement.classList.contains('dark') ? '#0F1713' : '#FFFFFF',
                    color: document.documentElement.classList.contains('dark') ? '#F3F4F6' : '#111827',
                });
                return;
            }

            this.isSendingBroadcast = true;
            try {
                const res = await fetch(`{{ route('admin.quizzes.announcements.store', $quiz) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({
                        message: msg,
                        type: this.broadcastType
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.broadcastMessage = '';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Pengumuman berhasil disiarkan!',
                        showConfirmButton: false,
                        timer: 3000,
                        background: '#0F1713',
                        color: '#34D399',
                        customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                    });
                    this.fetchData();
                } else {
                    Swal.fire('Gagal Mengirim', data.message || 'Terjadi kesalahan.', 'error');
                }
            } catch (err) {
                Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.isSendingBroadcast = false;
            }
        },

        async deleteAnnouncement(announcementId) {
            const isDark = document.documentElement.classList.contains('dark');
            const result = await Swal.fire({
                title: 'Hapus Pengumuman Ini?',
                text: 'Pemberitahuan ini akan dihapus dari riwayat siaran.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: isDark ? '#374151' : '#6B7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                background: isDark ? '#0F1713' : '#FFFFFF',
                color: isDark ? '#F3F4F6' : '#111827',
                customClass: { popup: isDark ? 'border border-[#1A2820] rounded-2xl' : 'border border-gray-200 rounded-2xl' }
            });

            if (result.isConfirmed) {
                try {
                    const res = await fetch(`{{ url('admin/quizzes/' . $quiz->id . '/announcements') }}/${announcementId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.announcements = this.announcements.filter(a => a.id !== announcementId);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Pengumuman dihapus',
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#0F1713',
                            color: '#34D399',
                            customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                        });
                    }
                } catch (err) {
                    Swal.fire('Gagal', 'Gagal menghapus pengumuman.', 'error');
                }
            }
        }
    };
}
</script>

@push('styles')
<style>
    /* Direct High-Contrast Rules for Session Control Banner */
    html:not(.dark) .session-status-banner .session-banner-title {
        color: #0F172A !important;
    }
    html:not(.dark) .session-status-banner .session-banner-desc {
        color: #334155 !important;
    }
    html.dark .session-status-banner .session-banner-title {
        color: #FFFFFF !important;
    }
    html.dark .session-status-banner .session-banner-desc {
        color: #9CA3AF !important;
    }

    /* Status Badges */
    html:not(.dark) .session-badge-waiting {
        background-color: #FEF3C7 !important;
        color: #92400E !important;
        border-color: #FCD34D !important;
    }
    html:not(.dark) .session-badge-started {
        background-color: #D1FAE5 !important;
        color: #065F46 !important;
        border-color: #6EE7B7 !important;
    }
    html:not(.dark) .session-badge-ended {
        background-color: #FFE4E6 !important;
        color: #9F1239 !important;
        border-color: #FDA4AF !important;
    }

    /* Status Icons */
    html:not(.dark) .session-icon-waiting {
        background-color: #FEF3C7 !important;
        color: #B45309 !important;
        border-color: #FCD34D !important;
    }
    html:not(.dark) .session-icon-started {
        background-color: #D1FAE5 !important;
        color: #047857 !important;
        border-color: #6EE7B7 !important;
    }
    html:not(.dark) .session-icon-ended {
        background-color: #FFE4E6 !important;
        color: #BE123C !important;
        border-color: #FDA4AF !important;
    }
</style>
@endpush
@endsection
