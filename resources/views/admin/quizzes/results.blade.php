@extends('layouts.admin')
@section('title', 'Hasil Kuis: ' . $quiz->title)
@section('page-title', 'Hasil & Leaderboard Mahasiswa')
@section('page-description', 'Pantau nilai mahasiswa, akurasi pengerjaan, dan catatan anti-cheat tab switch')

@section('content')
<div x-data="resultsManager()" class="space-y-6">

    {{-- Top Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quizzes.index') }}"
               class="p-2 rounded-xl bg-[#0F1713] border border-[#1A2820] text-gray-400 hover:text-emerald-400 hover:border-emerald-500/40 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-white">{{ $quiz->title }}</h3>
                    <span class="px-2 py-0.5 rounded-md bg-[#142019] border border-[#1E3327] text-emerald-400 font-mono text-xs font-bold">{{ $quiz->access_code }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">
                    Total {{ $totalParticipants }} sesi selesai &bull; Durasi rata-rata {{ gmdate('i\m s\s', (int)$avgDuration) }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quizzes.monitor', $quiz) }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-300 text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Live Monitor Ujian
            </a>

            <a href="{{ route('admin.quizzes.questions', $quiz) }}"
               class="px-4 py-2.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/40 text-gray-300 hover:text-emerald-400 text-sm font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Bank Soal
            </a>

            @if($totalParticipants > 0)
                @if($quiz->hasClasses())
                    {{-- Dropdown Export CSV per Kelas --}}
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" type="button"
                                class="px-4 py-2.5 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 border border-teal-500/30 text-teal-300 text-sm font-semibold transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Export CSV</span>
                            <svg class="w-3.5 h-3.5 ml-0.5 text-teal-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 mt-2 w-60 rounded-2xl shadow-2xl py-2 z-30 border backdrop-blur-md"
                             style="background:rgba(15,23,19,0.96);border-color:rgba(16,185,129,0.25);box-shadow:0 20px 30px -10px rgba(0,0,0,0.8), 0 0 15px rgba(16,185,129,0.08);">
                            <div class="px-4 py-1.5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">Rekap Hasil Kuis</div>
                            <a href="{{ route('admin.quizzes.export-csv', $quiz) }}"
                               class="flex items-center justify-between px-4 py-2 text-xs text-emerald-400 hover:bg-emerald-500/10 font-semibold transition-colors">
                                <span>Semua Kelas</span>
                                <span class="text-[10px] text-gray-500 font-mono">{{ $totalParticipants }}</span>
                            </a>
                            <div class="border-t my-1.5" style="border-color:#1A2820;"></div>
                            @foreach($quiz->getClassesList() as $cls)
                                @php $cCount = $quiz->attempts()->whereNotNull('completed_at')->where('kelas', $cls)->count(); @endphp
                                <a href="{{ route('admin.quizzes.export-csv', [$quiz, 'kelas' => $cls]) }}"
                                   class="flex items-center justify-between px-4 py-2 text-xs text-gray-300 hover:text-emerald-300 hover:bg-emerald-500/10 transition-colors">
                                    <span>Kelas {{ $cls }}</span>
                                    <span class="text-[10px] font-mono {{ $cCount > 0 ? 'text-emerald-400' : 'text-gray-600' }}">{{ $cCount }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route('admin.quizzes.export-csv', $quiz) }}"
                       class="px-4 py-2.5 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 border border-teal-500/30 text-teal-300 text-sm font-semibold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export CSV
                    </a>
                @endif

                <form method="POST" action="{{ route('admin.quizzes.reset-attempts', $quiz) }}"
                      onsubmit="confirmAction(event, 'Reset SEMUA riwayat pengerjaan mahasiswa untuk kuis ini? Tindakan ini tidak dapat dibatalkan.', 'Ya, Reset Semua', true)">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-400 text-sm font-semibold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Reset Data
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Stats KPI Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Total Peserta --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Peserta Selesai</span>
            <span class="text-2xl font-bold text-white mt-1 block">{{ $totalParticipants }}</span>
        </div>

        {{-- Skor Tertinggi --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Nilai Tertinggi</span>
            <span class="text-2xl font-bold text-emerald-400 mt-1 block">{{ number_format($highestScore, 1) }}</span>
        </div>

        {{-- Rata-Rata Nilai --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Rata-Rata Nilai</span>
            <span class="text-2xl font-bold text-amber-400 mt-1 block">{{ number_format($avgScore, 1) }}</span>
        </div>

        {{-- Skor Terendah --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Nilai Terendah</span>
            <span class="text-2xl font-bold text-rose-400 mt-1 block">{{ number_format($lowestScore, 1) }}</span>
        </div>
    </div>

    {{-- Filter Search --}}
    <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.quizzes.results', $quiz) }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 w-full sm:w-auto flex-1">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari NIM atau Nama Mahasiswa..."
                           class="w-full pl-9 pr-3 py-2 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-200 placeholder-gray-500">
                </div>

                @if($quiz->hasClasses())
                    <div class="relative flex-shrink-0">
                        <select name="kelas" onchange="this.form.submit()"
                                class="px-3.5 py-2 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 rounded-xl text-xs text-gray-200 cursor-pointer">
                            <option value="all" {{ request('kelas') === 'all' || !request('kelas') ? 'selected' : '' }}>Semua Kelas ({{ $totalParticipants }})</option>
                            @foreach($quiz->getClassesList() as $cls)
                                @php $clsCount = $quiz->attempts()->whereNotNull('completed_at')->where('kelas', $cls)->count(); @endphp
                                <option value="{{ $cls }}" {{ request('kelas') === $cls ? 'selected' : '' }}>Kelas {{ $cls }} ({{ $clsCount }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit"
                        class="px-4 py-2 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-400 rounded-xl text-sm font-semibold transition-colors cursor-pointer">
                    Cari
                </button>

                @if(request('search') || (request('kelas') && request('kelas') !== 'all'))
                    <a href="{{ route('admin.quizzes.results', $quiz) }}" class="text-xs text-gray-400 hover:text-emerald-400 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Table --}}
    <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl overflow-hidden">
        @if($attempts->isEmpty())
            <div class="py-16 text-center text-gray-500">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gray-800/40 flex items-center justify-center text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-400">Belum ada riwayat pengerjaan mahasiswa untuk kuis ini.</p>
                <p class="text-xs text-gray-500 mt-1">Bagikan kode akses <strong>{{ $quiz->access_code }}</strong> atau link kuis kepada mahasiswa.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="border-b border-[#1A2820] text-xs text-gray-400 uppercase tracking-wider bg-[#090D0B]">
                        <tr>
                            <th class="px-5 py-3.5 text-center">Rank</th>
                            <th class="px-5 py-3.5">Mahasiswa</th>
                            @if($quiz->hasClasses())
                                <th class="px-4 py-3.5 text-center">Kelas</th>
                            @endif
                            <th class="px-5 py-3.5 text-center">Nilai (0-100)</th>
                            <th class="px-5 py-3.5 text-center">Akurasi</th>
                            <th class="px-5 py-3.5 text-center">Durasi</th>
                            <th class="px-5 py-3.5 text-center">Anti-Cheat Tab</th>
                            <th class="px-5 py-3.5 text-center">Waktu Selesai</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1A2820]">
                        @php
                            $startRank = ($attempts->currentPage() - 1) * $attempts->perPage() + 1;
                        @endphp
                        @foreach($attempts as $idx => $att)
                            @php
                                $rank = $startRank + $idx;
                            @endphp
                            <tr class="hover:bg-[#131E17] transition-colors">
                                {{-- Rank --}}
                                <td class="px-5 py-4 text-center">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-500/20 text-amber-300 font-bold text-xs border border-amber-500/40">🥇 1</span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-400/20 text-gray-200 font-bold text-xs border border-gray-400/40">🥈 2</span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-700/20 text-amber-500 font-bold text-xs border border-amber-700/40">🥉 3</span>
                                    @else
                                        <span class="font-mono text-gray-400 font-semibold text-xs">#{{ $rank }}</span>
                                    @endif
                                </td>

                                {{-- Student --}}
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-white">{{ $att->student_name }}</div>
                                    <div class="text-xs font-mono text-gray-400">{{ $att->nim }}</div>
                                </td>

                                @if($quiz->hasClasses())
                                    <td class="px-4 py-4 text-center text-xs">
                                        @if($att->kelas)
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold inline-block"
                                                  style="background:rgba(16,185,129,.12);color:#34D399;border:1px solid rgba(16,185,129,.25);">
                                                Kelas {{ $att->kelas }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 font-mono text-xs">-</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Score --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-xl font-bold text-sm {{ $att->score >= 80 ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : ($att->score >= 60 ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30') }}">
                                        {{ number_format($att->score, 1) }}
                                    </span>
                                </td>

                                {{-- Accuracy --}}
                                <td class="px-5 py-4 text-center text-xs">
                                    <span class="text-emerald-400 font-bold">{{ $att->correct_answers_count }} Benar</span>
                                    <span class="text-gray-500 mx-1">/</span>
                                    <span class="text-rose-400 font-bold">{{ $att->wrong_answers_count }} Salah</span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">({{ $att->total_points_earned }} / {{ $att->total_points_possible }} poin)</div>
                                </td>

                                {{-- Duration --}}
                                <td class="px-5 py-4 text-center font-mono text-xs text-gray-300">
                                    {{ $att->formattedDuration() }}
                                </td>

                                {{-- Anti-Cheat Tab Switch / Lock Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($att->is_locked)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-300 bg-rose-500/25 px-2.5 py-1 rounded-full border border-rose-500/50 animate-pulse">
                                            🔒 TERKUNCI (Strike 3)
                                        </span>
                                    @elseif($quiz->allow_open_book)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-sky-400 bg-sky-500/10 px-2.5 py-1 rounded-full border border-sky-500/20">
                                            📖 Modul Aktif (Bebas)
                                        </span>
                                    @elseif($att->tab_switch_count === 0)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">
                                            🛡️ Bersih (0)
                                        </span>
                                    @elseif($att->tab_switch_count <= 2)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20">
                                            ⚠️ {{ $att->tab_switch_count }}x Switch
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-400 bg-rose-500/15 px-2.5 py-1 rounded-full border border-rose-500/30">
                                            🚨 {{ $att->tab_switch_count }}x Switch
                                        </span>
                                    @endif
                                </td>

                                {{-- Completed Time --}}
                                <td class="px-5 py-4 text-center text-xs text-gray-400">
                                    {{ $att->completed_at ? $att->completed_at->format('d M Y, H:i') : '-' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Remote Unlock Button if attempt is locked --}}
                                        @if($att->is_locked)
                                            <form method="POST" action="{{ route('admin.quizzes.attempts.unlock', [$quiz, $att]) }}" class="inline"
                                                  onsubmit="confirmAction(event, 'Buka kunci pengerjaan untuk mahasiswa {{ $att->student_name }} ({{ $att->nim }})? Mahasiswa akan dapat melanjutkan kuis kembali.', 'Ya, Buka Kunci', false)">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/40 text-emerald-400 text-xs font-semibold flex items-center gap-1 transition-colors"
                                                        title="Buka Kunci Mahasiswa">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Buka Kunci</span>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Detail Modal Button --}}
                                        <button type="button"
                                                @click="viewAttemptDetail(@js($att))"
                                                class="px-2.5 py-1.5 rounded-lg bg-[#142019] hover:bg-[#1E3327] border border-[#1E3327] text-gray-300 hover:text-emerald-400 text-xs font-medium transition-colors"
                                                title="Lihat Rincian Jawaban">
                                            Rincian
                                        </button>

                                        {{-- Delete Attempt --}}
                                        <form method="POST" action="{{ route('admin.quizzes.attempts.destroy', [$quiz, $att]) }}" class="inline"
                                              onsubmit="confirmAction(event, 'Hapus sesi pengerjaan oleh mahasiswa {{ $att->student_name }} ({{ $att->nim }})?', 'Ya, Hapus', true)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 transition-colors" title="Hapus Riwayat">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#1A2820]">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Attempt Modal --}}
    <div x-show="showDetailModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">

        <div @click.away="showDetailModal = false"
             class="bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            
            <div class="px-6 py-4 border-b border-[#1A2820] flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white">Rincian Hasil Pengerjaan</h3>
                    <p class="text-xs text-gray-400" x-text="selectedAttempt ? selectedAttempt.student_name + ' (' + selectedAttempt.nim + ')' : ''"></p>
                </div>
                <button type="button" @click="showDetailModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <template x-if="selectedAttempt">
                    <div class="space-y-4">
                        {{-- Summary Pill --}}
                        <div class="grid grid-cols-4 gap-2 p-3 rounded-xl bg-[#090D0B] border border-[#1A2820] text-center text-xs">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase">Skor Akhir</span>
                                <span class="font-bold text-base text-emerald-400" x-text="selectedAttempt.score"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase">Jawaban Benar</span>
                                <span class="font-bold text-base text-white" x-text="selectedAttempt.correct_answers_count"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase">Jawaban Salah</span>
                                <span class="font-bold text-base text-rose-400" x-text="selectedAttempt.wrong_answers_count"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase">Tab Switch</span>
                                <span class="font-bold text-base text-amber-400" x-text="selectedAttempt.tab_switch_count"></span>
                            </div>
                        </div>

                        {{-- Answers Breakdown --}}
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-gray-300 uppercase tracking-wider">Daftar Jawaban Mahasiswa:</h4>
                            
                            <template x-for="(ans, aIdx) in selectedAttempt.answers" :key="ans.id">
                                <div class="p-4 rounded-xl border space-y-3"
                                     :class="ans.is_correct ? 'bg-emerald-500/5 border-emerald-500/30' : 'bg-rose-500/5 border-rose-500/30'">
                                    
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold font-mono text-gray-400" x-text="'Soal #' + (aIdx + 1)"></span>
                                            <template x-if="ans.question && ans.question.question_type === 'multi_select'">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                    Pilihan Jamak
                                                </span>
                                            </template>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                              :class="ans.is_correct ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'"
                                              x-text="ans.is_correct ? '✓ Benar (+' + ans.points_earned + ' Poin)' : '✗ Salah (0 Poin)'"></span>
                                    </div>

                                    <p class="text-xs font-semibold text-white" x-text="ans.question ? ans.question.question_text : 'Pertanyaan'"></p>

                                    {{-- Question Image if present --}}
                                    <template x-if="ans.question && ans.question.image_url">
                                        <div class="my-2">
                                            <img :src="ans.question.image_url" alt="Gambar Soal"
                                                 class="max-h-56 rounded-xl border border-[#1A2820] bg-[#090D0B] object-contain">
                                        </div>
                                    </template>

                                    {{-- Options breakdown --}}
                                    <template x-if="ans.question && ans.question.options && ans.question.options.length > 0">
                                        <div class="space-y-1.5 pt-1">
                                            <template x-for="(opt, optIdx) in ans.question.options" :key="opt.id">
                                                <div class="flex items-start gap-2.5 p-2 rounded-lg text-xs transition-colors border"
                                                     :class="opt.is_correct
                                                        ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-200'
                                                        : (isOptSelected(ans, opt.id)
                                                            ? 'bg-rose-500/10 border-rose-500/40 text-rose-200'
                                                            : 'bg-[#090D0B]/60 border-[#1A2820] text-gray-400')">
                                                    
                                                    <span class="w-5 h-5 rounded-md flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5"
                                                          :class="opt.is_correct ? 'bg-emerald-500/20 text-emerald-300' : (isOptSelected(ans, opt.id) ? 'bg-rose-500/20 text-rose-300' : 'bg-[#142019] text-gray-400')"
                                                          x-text="String.fromCharCode(65 + optIdx)"></span>

                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="font-medium" x-text="opt.option_text"></span>
                                                            <template x-if="opt.is_correct">
                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                                    ✓ Kunci
                                                                </span>
                                                            </template>
                                                            <template x-if="isOptSelected(ans, opt.id)">
                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold"
                                                                      :class="opt.is_correct ? 'bg-emerald-500/30 text-emerald-200' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'">
                                                                    Dipilih Mahasiswa
                                                                </span>
                                                            </template>
                                                        </div>

                                                        <template x-if="opt.image_url">
                                                            <div class="mt-1.5">
                                                                <img :src="opt.image_url" alt="Gambar Opsi"
                                                                     class="max-h-36 rounded-lg border border-[#1A2820] bg-[#090D0B] object-contain">
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="ans.question && ans.question.explanation">
                                        <p class="text-[11px] text-teal-300/80 mt-2 p-2 rounded-lg bg-teal-500/5 border border-teal-500/20">
                                            💡 <strong>Pembahasan:</strong> <span x-text="ans.question.explanation"></span>
                                        </p>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 border-t border-[#1A2820] flex justify-end">
                <button type="button" @click="showDetailModal = false"
                        class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function resultsManager() {
        return {
            showDetailModal: false,
            selectedAttempt: null,
            viewAttemptDetail(attempt) {
                this.selectedAttempt = attempt;
                this.showDetailModal = true;
            },
            isOptSelected(ans, optId) {
                if (!ans) return false;
                if (ans.selected_option_id === optId) return true;
                if (Array.isArray(ans.selected_option_ids) && ans.selected_option_ids.includes(optId)) return true;
                return false;
            }
        };
    }
</script>
@endsection
