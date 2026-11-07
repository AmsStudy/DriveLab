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

    {{-- Stats KPI Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Sedang Mengerjakan --}}
        <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-4 relative overflow-hidden shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sedang Mengerjakan</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
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
                <span>Peserta Aktif</span>
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

        <div x-show="filteredActive.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                            + Waktu
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
        activeTab: 'active',
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

        // Modal Add Time
        showAddTimeModal: false,
        targetStudent: null,
        addedMinutes: 5,

        initMonitor() {
            this.fetchData();
            // Auto refresh every 5 seconds
            this.timer = setInterval(() => {
                this.fetchData();
            }, 5000);
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
                const res = await fetch(`{{ url('admin/quizzes/' . $quiz->id . '/monitor/data') }}`);
                const data = await res.json();
                if (data.success) {
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

        async remoteUnlock(item) {
            try {
                const res = await fetch(`{{ url('admin/quizzes/' . $quiz->id . '/attempts') }}/${item.id}/unlock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
        }
    };
}
</script>
@endsection
