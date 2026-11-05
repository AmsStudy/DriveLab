@extends('layouts.admin')
@section('title', 'Kuis Interaktif')
@section('page-title', 'Kuis Interaktif (LabQuiz)')
@section('page-description', 'Kelola kuis interaktif tipe Quizizz, bangun bank soal, dan pantau leaderboard nilai mahasiswa')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Kuis --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Kuis</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalQuizzes) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Kuis interaktif dibuat</div>
        </div>

        {{-- Total Pertanyaan --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-sky-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pertanyaan</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalQuestions) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Soal pilihan ganda & benar-salah</div>
        </div>

        {{-- Total Pengerjaan --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-teal-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Peserta</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalAttempts) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Sesi kuis mahasiswa tercatat</div>
        </div>

        {{-- Rata-rata Skor --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rata-rata Nilai</p>
                    <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($avgScore, 1) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Rata-rata pengerjaan selesai</div>
        </div>
    </div>

    {{-- Filter & Actions Toolbar --}}
    <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                {{-- Search Box --}}
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari judul atau kode kuis..."
                           class="w-full pl-9 pr-3 py-2 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-200 placeholder-gray-500">
                </div>

                {{-- Status Filter --}}
                <select name="status" onchange="this.form.submit()"
                        class="cyber-select text-sm pl-3.5 pr-10 py-2">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                {{-- Soal Attachment Filter --}}
                @if(isset($soals) && $soals->count() > 0)
                <select name="soal_id" onchange="this.form.submit()"
                        class="cyber-select text-sm pl-3.5 pr-10 py-2 max-w-xs truncate">
                    <option value="">Semua Dokumen Soal</option>
                    @foreach($soals as $s)
                        <option value="{{ $s->id }}" {{ request('soal_id') == $s->id ? 'selected' : '' }}>{{ Str::limit($s->title, 25) }}</option>
                    @endforeach
                </select>
                @endif

                @if(request()->anyFilled(['search', 'status', 'soal_id']))
                    <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-gray-400 hover:text-emerald-400 px-2 py-2 flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openCreateQuizModal()"
                   class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Kuis Baru
                </button>
            </div>
        </form>
    </div>

    {{-- Quizzes Cards Grid --}}
    @if($quizzes->isEmpty())
        @if(request()->anyFilled(['search', 'status', 'soal_id']))
            <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-white mb-1">Tidak Ada Kuis yang Cocok</h3>
                <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">Tidak ditemukan kuis CBT yang sesuai dengan kata kunci atau filter pencarian Anda.</p>
                <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset Filter Pencarian
                </a>
            </div>
        @else
            <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-1">Belum Ada Kuis</h3>
                <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">Buat kuis interaktif tipe Quizizz untuk menguji pemahaman mahasiswa sebelum atau setelah praktikum.</p>
                <button type="button" onclick="openCreateQuizModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold hover:opacity-95 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Kuis Sekarang
                </button>
            </div>
        @endif
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($quizzes as $quiz)
                <div class="bg-[#0F1713] border border-[#1A2820] hover:border-[#283d31] rounded-2xl flex flex-col justify-between overflow-hidden transition-all duration-200 hover:-translate-y-0.5">
                    
                    {{-- Card Header --}}
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            {{-- Active Status & Toggle --}}
                            <form method="POST" action="{{ route('admin.quizzes.toggle', $quiz) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Klik untuk mengubah status aktif"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-all {{ $quiz->is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:bg-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $quiz->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-gray-500' }}"></span>
                                    {{ $quiz->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>

                            {{-- Access Code Badge with Copy --}}
                            <button type="button"
                                    onclick="copyToClipboard('{{ $quiz->access_code }}', 'Kode Kuis berhasil disalin!')"
                                    title="Klik untuk salin kode kuis"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#142019] border border-[#1E3327] hover:border-emerald-500/50 text-emerald-300 font-mono text-xs font-bold transition-all">
                                <span>{{ $quiz->access_code }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>

                        {{-- Title & Soal Link --}}
                        <h4 class="text-base font-bold text-white mb-1.5 line-clamp-1 hover:text-emerald-400 transition-colors">
                            <a href="{{ route('admin.quizzes.questions', $quiz) }}">{{ $quiz->title }}</a>
                        </h4>
                        <p class="text-xs text-gray-400 line-clamp-2 mb-4">{{ $quiz->description ?: 'Tidak ada deskripsi kuis.' }}</p>

                        {{-- Linked Document Soal Badge --}}
                        @if($quiz->soal)
                            <div class="mb-4 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 text-[11px] truncate">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="truncate">Dokumen: {{ $quiz->soal->title }}</span>
                            </div>
                        @endif

                        {{-- Metadata Metrics --}}
                        <div class="grid grid-cols-3 gap-2 py-3 border-y border-[#1A2820] text-center">
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $quiz->questions_count }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Pertanyaan</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $quiz->attempts_count }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Peserta</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-amber-400 block">{{ $quiz->avg_score !== null ? number_format($quiz->avg_score, 1) : '-' }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Avg Nilai</span>
                            </div>
                        </div>

                        {{-- Settings Pills --}}
                        <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] text-gray-400">
                            <span class="px-2 py-0.5 rounded-md bg-[#131E17] border border-[#1A2820]">
                                ⏱ {{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' Menit' : ($quiz->time_per_question > 0 ? $quiz->time_per_question . ' dtk/soal' : 'Tanpa Waktu') }}
                            </span>
                            @if($quiz->shuffle_questions)
                                <span class="px-2 py-0.5 rounded-md bg-[#131E17] border border-[#1A2820]">🔀 Acak Soal</span>
                            @endif
                            @if($quiz->show_leaderboard)
                                <span class="px-2 py-0.5 rounded-md bg-[#131E17] border border-[#1A2820]">🏆 Leaderboard</span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer Actions --}}
                    <div class="p-3 bg-[#090D0B] border-t border-[#1A2820] flex items-center justify-between gap-1.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            {{-- Button CMS Pertanyaan --}}
                            <a href="{{ route('admin.quizzes.questions', $quiz) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-400 text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Bank Soal
                            </a>

                            {{-- Button Live Monitor --}}
                            <a href="{{ route('admin.quizzes.monitor', $quiz) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/25 text-emerald-400 text-xs font-semibold transition-colors"
                               title="Pantau Mahasiswa Ujian Realtime">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                </span>
                                <span>Monitor</span>
                            </a>

                            {{-- Button Hasil & Leaderboard --}}
                            <a href="{{ route('admin.quizzes.results', $quiz) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#142019] hover:bg-[#1A2B22] border border-[#1E3327] text-gray-300 hover:text-white text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Hasil
                            </a>
                        </div>

                        <div class="flex items-center gap-1">
                            {{-- Copy Direct URL --}}
                            <button type="button"
                                    onclick="copyToClipboard('{{ route('quiz.lobby', $quiz->slug ?: $quiz->unique_token) }}', 'Link kuis mahasiswa berhasil disalin!')"
                                    title="Salin Link Mahasiswa"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-400 hover:bg-emerald-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>

                            {{-- Duplicate Quiz --}}
                            <form method="POST" action="{{ route('admin.quizzes.duplicate', $quiz) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Duplikasi kuis \'{{ addslashes($quiz->title) }}\' beserta seluruh {{ $quiz->questions_count }} butir soal?', 'Ya, Duplikasi Kuis', false)">
                                @csrf
                                <button type="submit" title="Duplikasi Kuis (Salin Soal & Pengaturan)"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-teal-400 hover:bg-teal-500/10 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </form>

                            {{-- Edit Quiz Settings (Pop-up Modal) --}}
                            <button type="button"
                                    onclick="openEditQuizModal({{ $quiz->id }})"
                                    title="Edit Pengaturan Kuis"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-amber-400 hover:bg-amber-500/10 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>

                            {{-- Delete Quiz --}}
                            <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus kuis \'{{ $quiz->title }}\' beserta seluruh pertanyaan dan riwayat pengerjaan?', 'Ya, Hapus Kuis', true)">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus Kuis"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $quizzes->links() }}
        </div>
    @endif

</div>

{{-- ─── Pop Up Modal: Buat Kuis Baru ─── --}}
<div id="createQuizModal" class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 hidden" style="z-index: 100;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeCreateQuizModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl z-10 overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#1A2820] bg-[#0F1713] flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white leading-tight">Buat Kuis Baru</h2>
                    <p class="text-xs text-gray-400">Atur parameter kuis interaktif tipe Quizizz untuk mahasiswa</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateQuizModal()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition-colors" title="Tutup Modal (Esc)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.quizzes.store') }}" id="createQuizForm" class="flex flex-col flex-1 min-h-0">
            @csrf

            {{-- Modal Body (Scrollable form content) --}}
            <div class="overflow-y-auto px-6 py-6 space-y-6 flex-1">
                @if($errors->any() && old('_method') !== 'PUT')
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm">
                        <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Section 1: Informasi Kuis --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h3 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Informasi Dasar Kuis</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Judul --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Judul Kuis <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('_method') !== 'PUT' ? old('title') : '' }}" required
                                   placeholder="Contoh: Kuis Pre-Test Modul 3: Struktur Data & Pointer"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">
                        </div>

                        {{-- Custom Kode Akses Kuis --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                                <span>Kode Akses Kuis (Custom Kode)</span>
                                <span class="text-[10px] text-gray-400 font-normal">Opsional &bull; Digunakan mahasiswa untuk bergabung</span>
                            </label>
                            <input type="text" name="access_code" value="{{ old('_method') !== 'PUT' ? old('access_code') : '' }}"
                                   maxlength="20"
                                   placeholder="Contoh: ALGO-01 atau MODUL2 (Kosongkan untuk acak otomatis: LAB-XXXXX)"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-emerald-400 placeholder-gray-600 font-mono font-bold uppercase tracking-wider">
                            <p class="text-[11px] text-gray-500 mt-1">Kode unik yang dibagikan ke mahasiswa. Huruf besar, angka, dan strip (maks. 20 karakter). Kosongkan jika ingin digenerate acak otomatis.</p>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Deskripsi / Petunjuk Mahasiswa
                            </label>
                            <textarea name="description" rows="3"
                                      placeholder="Tuliskan petunjuk kuis, materi yang diuji, atau instruksi pengerjaan..."
                                      class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">{{ old('_method') !== 'PUT' ? old('description') : '' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Tautkan ke Dokumen Soal --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                    Tautkan ke Dokumen Soal Praktikum (Opsional)
                                </label>
                                <select name="soal_id"
                                        class="cyber-select w-full pl-4 pr-10 py-2.5 text-sm cursor-pointer">
                                    <option value="">-- Tidak Ditautkan (Kuis Mandiri) --</option>
                                    @foreach($soals as $s)
                                        <option value="{{ $s->id }}" {{ (old('_method') !== 'PUT' && old('soal_id') == $s->id) ? 'selected' : '' }}>
                                            {{ $s->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Password Akses Mahasiswa --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                    Password Akses Soal (Opsional)
                                </label>
                                <input type="text" name="access_password" value="{{ old('_method') !== 'PUT' ? old('access_password') : '' }}"
                                       placeholder="Kosongkan jika mahasiswa bebas akses tanpa password"
                                       class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                            </div>
                        </div>

                        {{-- Daftar Pilihan Kelas --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                                <span>Daftar Pilihan Kelas (Opsional)</span>
                                <span class="text-[10px] text-gray-400 font-normal">Pisahkan dengan koma</span>
                            </label>
                            <input type="text" name="classes" value="{{ old('_method') !== 'PUT' ? old('classes') : '' }}"
                                   placeholder="Contoh: A1, A2, B1, B2"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600">
                            <p class="text-[11px] text-gray-500 mt-1">Jika diisi, mahasiswa wajib memilih salah satu kelas saat memulai kuis. Rekap nilai dan data peserta dapat difilter & diunduh per kelas.</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Jadwal & Waktu Akses --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider">Jadwal Akses & Batas Waktu</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Jadwal Mulai Akses (Opsional)
                            </label>
                            <input type="datetime-local" name="starts_at" value="{{ old('_method') !== 'PUT' ? old('starts_at') : '' }}"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Batas Akhir Akses (Opsional)
                            </label>
                            <input type="datetime-local" name="ends_at" value="{{ old('_method') !== 'PUT' ? old('ends_at') : '' }}"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Durasi Pengerjaan (Menit)
                            </label>
                            <input type="number" name="time_limit_minutes" value="{{ old('_method') !== 'PUT' ? old('time_limit_minutes', 15) : 15 }}" min="0" max="300"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                            <p class="text-[11px] text-gray-500 mt-1">Isi 0 jika tanpa durasi pengerjaan.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Timer Per Soal (Detik)
                            </label>
                            <input type="number" name="time_per_question" value="{{ old('_method') !== 'PUT' ? old('time_per_question', 0) : 0 }}" min="0" max="300"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                            <p class="text-[11px] text-gray-500 mt-1">Gaya Quizizz per soal. Isi 0 untuk nonaktif.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Maksimal Percobaan (Per NIM)
                            </label>
                            <input type="number" name="max_attempts" value="{{ old('_method') !== 'PUT' ? old('max_attempts', 1) : 1 }}" min="1" max="20" required
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Fitur Interaktif & Tampilan Hasil --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Fitur Interaktif & Tampilan Hasil</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="shuffle_questions" value="1" {{ (old('_method') !== 'PUT' && old('shuffle_questions', '1')) ? 'checked' : 'checked' }}
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Acak Urutan Soal (Shuffle)</span>
                                <span class="text-[11px] text-gray-400">Setiap mahasiswa menerima urutan soal yang berbeda.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="shuffle_options" value="1" {{ (old('_method') !== 'PUT' && old('shuffle_options', '1')) ? 'checked' : 'checked' }}
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Acak Pilihan Jawaban</span>
                                <span class="text-[11px] text-gray-400">Urutan pilihan A, B, C, D diacak secara otomatis.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="show_leaderboard" value="1" {{ (old('_method') !== 'PUT' && old('show_leaderboard', '1')) ? 'checked' : 'checked' }}
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Tampilkan Leaderboard</span>
                                <span class="text-[11px] text-gray-400">Mahasiswa dapat melihat peringkat mereka setelah selesai.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="show_review" value="1" {{ (old('_method') !== 'PUT' && old('show_review', '1')) ? 'checked' : 'checked' }}
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Tampilkan Pembahasan Soal</span>
                                <span class="text-[11px] text-gray-400">Mahasiswa dapat meninjau pembahasan jawaban yang tepat.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors md:col-span-2">
                            <input type="checkbox" name="is_active" value="1" {{ (old('_method') !== 'PUT' && old('is_active', '1')) ? 'checked' : 'checked' }}
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-emerald-400 block">Aktifkan Kuis Langsung</span>
                                <span class="text-[11px] text-gray-400">Jika dicentang, kuis langsung dapat diakses mahasiswa menggunakan kode akses.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Section 4: Keamanan Ujian & Anti-Cheat --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <h3 class="text-xs font-bold text-rose-400 uppercase tracking-wider">Keamanan Ujian & Mode Pengawasan</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        {{-- Mode 1: Ujian Ketat --}}
                        <label class="relative flex items-start gap-3 p-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 cursor-pointer transition-all" id="create_mode_strict_label">
                            <input type="radio" name="allow_open_book" value="0" checked onchange="toggleCreateOpenBook(this.value)"
                                   class="mt-1 text-emerald-500 focus:ring-emerald-500 bg-gray-900 border-gray-700">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">Mode Ujian Ketat (Anti-Cheat)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Default</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Mahasiswa dilarang berpindah tab. 1x switch: beku 60s, 2x switch: beku 90s, 3x: terkunci total.
                                </p>
                            </div>
                        </label>

                        {{-- Mode 2: Bisa Lihat Modul --}}
                        <label class="relative flex items-start gap-3 p-4 rounded-xl border border-[#1A2820] bg-[#0F1713] cursor-pointer transition-all" id="create_mode_open_label">
                            <input type="radio" name="allow_open_book" value="1" onchange="toggleCreateOpenBook(this.value)"
                                   class="mt-1 text-sky-500 focus:ring-sky-500 bg-gray-900 border-gray-700">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">Bisa Lihat Modul (Open Book)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">Bebas Akses</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Mahasiswa bebas berpindah tab untuk mempelajari modul tanpa penalti freeze.
                                </p>
                            </div>
                        </label>
                    </div>

                    {{-- Password Buka Kunci Darurat --}}
                    <div id="create_unlock_pw_wrapper" class="pt-3 border-t border-[#1A2820]">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                            Password Khusus Buka Kunci Ujian (Opsional)
                        </label>
                        <input type="text" name="admin_unlock_password" value="{{ old('_method') !== 'PUT' ? old('admin_unlock_password') : '' }}"
                               placeholder="Contoh: PENG4W45#2026 (Kosongkan jika ingin pakai password akun)"
                               class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-rose-500 focus:ring-1 focus:ring-rose-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                        <p class="text-[11px] text-gray-500 mt-1">
                            Digunakan membuka kuis mahasiswa yang terkunci jika melanggar Strike 3. Jika kosong, gunakan password akun Anda.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#1A2820] bg-[#0F1713] flex-shrink-0">
                <button type="button" onclick="closeCreateQuizModal()"
                        class="px-5 py-2.5 rounded-xl border border-[#1A2820] hover:bg-gray-800 text-gray-300 text-sm font-medium transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <span>Simpan & Lanjut Buat Soal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Pop Up Modal: Edit Pengaturan Kuis ─── --}}
<div id="editQuizModal" class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 hidden" style="z-index: 100;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeEditQuizModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl z-10 overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#1A2820] bg-[#0F1713] flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-amber-500/10 border border-amber-500/20 text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white leading-tight">Pengaturan Kuis</h2>
                    <p class="text-xs text-gray-400">Perbarui parameter kuis interaktif tanpa meninggalkan halaman</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a id="edit_modal_bank_soal_btn" href="#"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-400 text-xs font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Bank Soal
                </a>
                <button type="button" onclick="closeEditQuizModal()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition-colors" title="Tutup Modal (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="" id="editQuizForm" class="flex flex-col flex-1 min-h-0">
            @csrf
            @method('PUT')
            <input type="hidden" name="quiz_id" id="edit_quiz_id" value="{{ old('quiz_id') }}">

            {{-- Modal Body (Scrollable form content) --}}
            <div class="overflow-y-auto px-6 py-6 space-y-6 flex-1">
                @if($errors->any() && old('_method') === 'PUT')
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm">
                        <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Quick Access Bar inside Modal --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-xl p-3 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 font-semibold uppercase tracking-wider">Kode Akses:</span>
                        <span id="edit_modal_access_code_pill" class="px-2.5 py-0.5 rounded-lg bg-[#142019] border border-[#1E3327] text-emerald-300 font-mono font-bold"></span>
                        <button type="button" onclick="copyCurrentEditCode()" class="p-1 text-gray-400 hover:text-emerald-400 transition-colors" title="Salin Kode">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    <div>
                        <button type="button" onclick="copyCurrentEditLobbyLink()"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#142019] border border-[#1E3327] hover:border-emerald-500/40 text-gray-300 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span>Salin Link Mahasiswa</span>
                        </button>
                    </div>
                </div>

                {{-- Section 1: Informasi Kuis --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h3 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Informasi Dasar Kuis</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Judul --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Judul Kuis <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="title" id="edit_quiz_title" required
                                   placeholder="Contoh: Kuis Pre-Test Modul 3: Struktur Data & Pointer"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">
                        </div>

                        {{-- Custom Kode Akses Kuis --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                                <span>Kode Akses Kuis (Custom Kode) <span class="text-red-400">*</span></span>
                                <span class="text-[10px] text-gray-400 font-normal">Digunakan mahasiswa untuk bergabung</span>
                            </label>
                            <input type="text" name="access_code" id="edit_quiz_access_code" required
                                   maxlength="20"
                                   placeholder="Contoh: ALGO-01 atau MODUL2"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-emerald-400 placeholder-gray-600 font-mono font-bold uppercase tracking-wider">
                            <p class="text-[11px] text-gray-500 mt-1">Kode unik kuis. Huruf besar, angka, dan strip (maks. 20 karakter).</p>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Deskripsi / Petunjuk Mahasiswa
                            </label>
                            <textarea name="description" id="edit_quiz_description" rows="3"
                                      placeholder="Tuliskan petunjuk kuis, materi yang diuji, atau instruksi pengerjaan..."
                                      class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Tautkan ke Dokumen Soal --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                    Tautkan ke Dokumen Soal Praktikum (Opsional)
                                </label>
                                <select name="soal_id" id="edit_quiz_soal_id"
                                        class="cyber-select w-full pl-4 pr-10 py-2.5 text-sm cursor-pointer">
                                    <option value="">-- Tidak Ditautkan (Kuis Mandiri) --</option>
                                    @foreach($soals as $s)
                                        <option value="{{ $s->id }}">{{ $s->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Password Akses Mahasiswa --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                    Password Akses Soal (Opsional)
                                </label>
                                <input type="text" name="access_password" id="edit_quiz_access_password"
                                       placeholder="Kosongkan jika mahasiswa bebas akses tanpa password"
                                       class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                            </div>
                        </div>

                        {{-- Daftar Pilihan Kelas --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                                <span>Daftar Pilihan Kelas (Opsional)</span>
                                <span class="text-[10px] text-gray-400 font-normal">Pisahkan dengan koma</span>
                            </label>
                            <input type="text" name="classes" id="edit_quiz_classes"
                                   placeholder="Contoh: A1, A2, B1, B2"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600">
                            <p class="text-[11px] text-gray-500 mt-1">Jika diisi, mahasiswa wajib memilih salah satu kelas saat memulai kuis. Rekap nilai dan data peserta dapat difilter & diunduh per kelas.</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Jadwal & Waktu Akses --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider">Jadwal Akses & Batas Waktu</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Jadwal Mulai Akses (Opsional)
                            </label>
                            <input type="datetime-local" name="starts_at" id="edit_quiz_starts_at"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Batas Akhir Akses (Opsional)
                            </label>
                            <input type="datetime-local" name="ends_at" id="edit_quiz_ends_at"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Durasi Pengerjaan (Menit)
                            </label>
                            <input type="number" name="time_limit_minutes" id="edit_quiz_time_limit_minutes" min="0" max="300"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                            <p class="text-[11px] text-gray-500 mt-1">Isi 0 jika tanpa durasi pengerjaan.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Timer Per Soal (Detik)
                            </label>
                            <input type="number" name="time_per_question" id="edit_quiz_time_per_question" min="0" max="300"
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                            <p class="text-[11px] text-gray-500 mt-1">Gaya Quizizz per soal. Isi 0 untuk nonaktif.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                                Maksimal Percobaan (Per NIM)
                            </label>
                            <input type="number" name="max_attempts" id="edit_quiz_max_attempts" min="1" max="20" required
                                   class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Fitur Interaktif & Tampilan Hasil --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Fitur Interaktif & Tampilan Hasil</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="shuffle_questions" id="edit_quiz_shuffle_questions" value="1"
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Acak Urutan Soal (Shuffle)</span>
                                <span class="text-[11px] text-gray-400">Setiap mahasiswa menerima urutan soal yang berbeda.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="shuffle_options" id="edit_quiz_shuffle_options" value="1"
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Acak Pilihan Jawaban</span>
                                <span class="text-[11px] text-gray-400">Urutan pilihan A, B, C, D diacak secara otomatis.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="show_leaderboard" id="edit_quiz_show_leaderboard" value="1"
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Tampilkan Leaderboard</span>
                                <span class="text-[11px] text-gray-400">Mahasiswa dapat melihat peringkat mereka setelah selesai.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="show_review" id="edit_quiz_show_review" value="1"
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-white block">Tampilkan Pembahasan Soal</span>
                                <span class="text-[11px] text-gray-400">Mahasiswa dapat meninjau pembahasan jawaban yang tepat.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors md:col-span-2">
                            <input type="checkbox" name="is_active" id="edit_quiz_is_active" value="1"
                                   class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                            <div>
                                <span class="text-xs font-bold text-emerald-400 block">Aktifkan Kuis</span>
                                <span class="text-[11px] text-gray-400">Jika dicentang, kuis aktif dan dapat diakses mahasiswa menggunakan kode akses.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Section 4: Keamanan Ujian & Anti-Cheat --}}
                <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#1A2820]">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <h3 class="text-xs font-bold text-rose-400 uppercase tracking-wider">Keamanan Ujian & Mode Pengawasan</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        {{-- Mode 1: Ujian Ketat --}}
                        <label class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all" id="edit_mode_strict_label">
                            <input type="radio" name="allow_open_book" id="edit_allow_open_book_0" value="0" onchange="toggleEditOpenBook(this.value)"
                                   class="mt-1 text-emerald-500 focus:ring-emerald-500 bg-gray-900 border-gray-700">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">Mode Ujian Ketat (Anti-Cheat)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Default</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Mahasiswa dilarang berpindah tab. 1x switch: beku 60s, 2x switch: beku 90s, 3x: terkunci total.
                                </p>
                            </div>
                        </label>

                        {{-- Mode 2: Bisa Lihat Modul --}}
                        <label class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all" id="edit_mode_open_label">
                            <input type="radio" name="allow_open_book" id="edit_allow_open_book_1" value="1" onchange="toggleEditOpenBook(this.value)"
                                   class="mt-1 text-sky-500 focus:ring-sky-500 bg-gray-900 border-gray-700">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">Bisa Lihat Modul (Open Book)</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">Bebas Akses</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Mahasiswa bebas berpindah tab untuk mempelajari modul tanpa penalti freeze.
                                </p>
                            </div>
                        </label>
                    </div>

                    {{-- Password Buka Kunci Darurat --}}
                    <div id="edit_unlock_pw_wrapper" class="pt-3 border-t border-[#1A2820]">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                            Password Khusus Buka Kunci Ujian (Opsional)
                        </label>
                        <input type="text" name="admin_unlock_password" id="edit_quiz_admin_unlock_password"
                               placeholder="Contoh: PENG4W45#2026 (Kosongkan jika ingin pakai password akun)"
                               class="w-full px-4 py-2.5 bg-[#0F1713] border border-[#1A2820] focus:border-rose-500 focus:ring-1 focus:ring-rose-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                        <p class="text-[11px] text-gray-500 mt-1">
                            Digunakan membuka kuis mahasiswa yang terkunci jika melanggar Strike 3. Jika kosong, gunakan password akun Anda.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#1A2820] bg-[#0F1713] flex-shrink-0">
                <button type="button" onclick="closeEditQuizModal()"
                        class="px-5 py-2.5 rounded-xl border border-[#1A2820] hover:bg-gray-800 text-gray-300 text-sm font-medium transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="bg-gradient-to-r from-amber-500 to-emerald-600 hover:from-amber-600 hover:to-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-amber-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan Kuis</span>
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $quizzesForJs = $quizzes->getCollection();
    if (isset($extraEditQuiz) && $extraEditQuiz) {
        $quizzesForJs = $quizzesForJs->push($extraEditQuiz);
    }
    $quizzesMap = [];
    foreach ($quizzesForJs as $q) {
        $quizzesMap[$q->id] = [
            'id' => $q->id,
            'title' => $q->title,
            'access_code' => $q->access_code,
            'description' => $q->description ?? '',
            'soal_id' => (string)($q->soal_id ?? ''),
            'access_password' => $q->access_password ?? '',
            'starts_at' => $q->starts_at ? $q->starts_at->format('Y-m-d\TH:i') : '',
            'ends_at' => $q->ends_at ? $q->ends_at->format('Y-m-d\TH:i') : '',
            'time_limit_minutes' => $q->time_limit_minutes ?? 0,
            'time_per_question' => $q->time_per_question ?? 0,
            'max_attempts' => $q->max_attempts ?? 1,
            'shuffle_questions' => (bool)$q->shuffle_questions,
            'shuffle_options' => (bool)$q->shuffle_options,
            'show_leaderboard' => (bool)$q->show_leaderboard,
            'show_review' => (bool)$q->show_review,
            'allow_open_book' => (string)($q->allow_open_book ? '1' : '0'),
            'admin_unlock_password' => $q->admin_unlock_password ?? '',
            'classes' => is_array($q->classes) ? implode(', ', $q->classes) : ($q->classes ?? ''),
            'is_active' => (bool)$q->is_active,
            'update_url' => route('admin.quizzes.update', $q),
            'bank_soal_url' => route('admin.quizzes.questions', $q),
            'lobby_url' => route('quiz.lobby', $q->slug ?: $q->unique_token),
        ];
    }
@endphp

@push('scripts')
<script>
    window.quizzesData = @json($quizzesMap);
    let currentEditQuiz = null;

    function openCreateQuizModal() {
        const modal = document.getElementById('createQuizModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                const titleInput = modal.querySelector('input[name="title"]');
                if (titleInput) titleInput.focus();
            }, 100);
        }
    }

    function closeCreateQuizModal() {
        const modal = document.getElementById('createQuizModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleCreateOpenBook(val) {
        const wrapper = document.getElementById('create_unlock_pw_wrapper');
        const strictLabel = document.getElementById('create_mode_strict_label');
        const openLabel = document.getElementById('create_mode_open_label');
        if (val === '1') {
            if (wrapper) wrapper.classList.add('hidden');
            if (openLabel) {
                openLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-sky-500/40 bg-sky-500/10 cursor-pointer transition-all';
            }
            if (strictLabel) {
                strictLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-[#1A2820] bg-[#0F1713] cursor-pointer transition-all';
            }
        } else {
            if (wrapper) wrapper.classList.remove('hidden');
            if (strictLabel) {
                strictLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 cursor-pointer transition-all';
            }
            if (openLabel) {
                openLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-[#1A2820] bg-[#0F1713] cursor-pointer transition-all';
            }
        }
    }

    function openEditQuizModal(quizId) {
        const quiz = window.quizzesData ? window.quizzesData[quizId] : null;
        if (!quiz) {
            console.error('Quiz not found for ID:', quizId);
            return;
        }

        currentEditQuiz = quiz;
        const form = document.getElementById('editQuizForm');
        if (!form) return;

        form.action = quiz.update_url;
        document.getElementById('edit_quiz_id').value = quiz.id;
        document.getElementById('edit_quiz_title').value = quiz.title;
        document.getElementById('edit_quiz_access_code').value = quiz.access_code;
        document.getElementById('edit_quiz_description').value = quiz.description;

        const soalSelect = document.getElementById('edit_quiz_soal_id');
        if (soalSelect) {
            soalSelect.value = quiz.soal_id || '';
            soalSelect.dispatchEvent(new Event('change'));
        }

        document.getElementById('edit_quiz_access_password').value = quiz.access_password;
        document.getElementById('edit_quiz_classes').value = quiz.classes;
        document.getElementById('edit_quiz_starts_at').value = quiz.starts_at;
        document.getElementById('edit_quiz_ends_at').value = quiz.ends_at;
        document.getElementById('edit_quiz_time_limit_minutes').value = quiz.time_limit_minutes;
        document.getElementById('edit_quiz_time_per_question').value = quiz.time_per_question;
        document.getElementById('edit_quiz_max_attempts').value = quiz.max_attempts;

        document.getElementById('edit_quiz_shuffle_questions').checked = quiz.shuffle_questions;
        document.getElementById('edit_quiz_shuffle_options').checked = quiz.shuffle_options;
        document.getElementById('edit_quiz_show_leaderboard').checked = quiz.show_leaderboard;
        document.getElementById('edit_quiz_show_review').checked = quiz.show_review;
        document.getElementById('edit_quiz_is_active').checked = quiz.is_active;

        const rad0 = document.getElementById('edit_allow_open_book_0');
        const rad1 = document.getElementById('edit_allow_open_book_1');
        if (quiz.allow_open_book === '1') {
            if (rad1) rad1.checked = true;
            toggleEditOpenBook('1');
        } else {
            if (rad0) rad0.checked = true;
            toggleEditOpenBook('0');
        }

        document.getElementById('edit_quiz_admin_unlock_password').value = quiz.admin_unlock_password;

        // Quick Bar
        const codePill = document.getElementById('edit_modal_access_code_pill');
        if (codePill) codePill.textContent = quiz.access_code;

        const bankBtn = document.getElementById('edit_modal_bank_soal_btn');
        if (bankBtn) bankBtn.href = quiz.bank_soal_url;

        const modal = document.getElementById('editQuizModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeEditQuizModal() {
        const modal = document.getElementById('editQuizModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleEditOpenBook(val) {
        const wrapper = document.getElementById('edit_unlock_pw_wrapper');
        const strictLabel = document.getElementById('edit_mode_strict_label');
        const openLabel = document.getElementById('edit_mode_open_label');
        if (val === '1') {
            if (wrapper) wrapper.classList.add('hidden');
            if (openLabel) {
                openLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-sky-500/40 bg-sky-500/10 cursor-pointer transition-all';
            }
            if (strictLabel) {
                strictLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-[#1A2820] bg-[#0F1713] cursor-pointer transition-all';
            }
        } else {
            if (wrapper) wrapper.classList.remove('hidden');
            if (strictLabel) {
                strictLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 cursor-pointer transition-all';
            }
            if (openLabel) {
                openLabel.className = 'relative flex items-start gap-3 p-4 rounded-xl border border-[#1A2820] bg-[#0F1713] cursor-pointer transition-all';
            }
        }
    }

    function copyCurrentEditCode() {
        if (currentEditQuiz && currentEditQuiz.access_code) {
            copyToClipboard(currentEditQuiz.access_code, 'Kode Kuis berhasil disalin!');
        }
    }

    function copyCurrentEditLobbyLink() {
        if (currentEditQuiz && currentEditQuiz.lobby_url) {
            copyToClipboard(currentEditQuiz.lobby_url, 'Link kuis mahasiswa berhasil disalin!');
        }
    }

    // Escape listener for modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const createModal = document.getElementById('createQuizModal');
            if (createModal && !createModal.classList.contains('hidden')) {
                closeCreateQuizModal();
            }
            const editModal = document.getElementById('editQuizModal');
            if (editModal && !editModal.classList.contains('hidden')) {
                closeEditQuizModal();
            }
        }
    });

    // Auto-reopen on validation errors / redirects / action=create
    document.addEventListener('DOMContentLoaded', () => {
        @if(request('action') === 'create' || session('open_create_modal') || ($errors->any() && old('_method') !== 'PUT'))
            openCreateQuizModal();
        @elseif(session('edit_quiz_id') || ($errors->any() && old('_method') === 'PUT'))
            openEditQuizModal({{ (int)(session('edit_quiz_id') ?? old('quiz_id')) }});
        @endif
    });
</script>
@endpush

{{-- Clipboard helper --}}
<script>
    function copyToClipboard(text, successMessage) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(successMessage);
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
            showToast(successMessage);
        }
    }

    function showToast(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500,
            background: '#0F1713',
            color: '#f3f4f6',
            customClass: {
                popup: 'border border-emerald-500/30 rounded-xl'
            }
        });
    }
</script>
@endsection
