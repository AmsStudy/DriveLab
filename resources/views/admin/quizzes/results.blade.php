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
                                class="cyber-select text-xs pl-3.5 pr-10 py-2 cursor-pointer">
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

    {{-- Detail Attempt Modal (Pop-up Lembar Jawaban Mahasiswa) --}}
    <div x-show="showDetailModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
         style="display: none;">

        <div @click.away="showDetailModal = false"
             class="bg-[#0F1713] border border-[#1A2820] rounded-3xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden relative">
            
            {{-- Modal Header Bar --}}
            <div class="px-6 py-4 border-b border-[#1A2820] bg-[#090D0B]/80 backdrop-blur-md flex items-center justify-between flex-shrink-0 z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white leading-tight">Lembar Jawaban & Evaluasi Mahasiswa</h3>
                        <p class="text-xs text-gray-400" x-text="selectedAttempt ? selectedAttempt.student_name + ' (' + selectedAttempt.nim + ')' : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showDetailModal = false" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition-colors" title="Tutup Modal (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Scrollable Content --}}
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">
                <template x-if="selectedAttempt">
                    <div class="space-y-6">

                        {{-- ─── 1. HERO SCORE CELEBRATION CARD (Exact match to user reference image) ─── --}}
                        <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden shadow-2xl">
                            <div class="absolute -right-20 -top-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Pengerjaan Kuis Selesai</span>
                            </div>

                            <h1 class="text-xl sm:text-2xl font-bold text-white heading-font mb-1">
                                {{ $quiz->title }}
                            </h1>
                            <p class="text-xs text-gray-400 mb-6">
                                Mahasiswa: <strong class="text-white" x-text="selectedAttempt.student_name"></strong>
                                (<span x-text="selectedAttempt.nim"></span>)
                                <template x-if="selectedAttempt.kelas">
                                    <span class="ml-2 px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" x-text="'Kelas ' + selectedAttempt.kelas"></span>
                                </template>
                            </p>

                            {{-- Score Big Ring --}}
                            <div class="inline-flex flex-col items-center justify-center w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-gradient-to-tr from-[#131E17] to-[#1A2D23] border-4 border-emerald-500/50 shadow-2xl shadow-emerald-500/20 mb-4">
                                <span class="text-3xl sm:text-5xl font-extrabold text-white heading-font tracking-tight font-mono"
                                      x-text="parseFloat(selectedAttempt.score).toFixed(1)">
                                </span>
                                <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest mt-0.5">Skor Akhir</span>
                            </div>

                            {{-- Grade Feedback Message --}}
                            <div class="max-w-md mx-auto mb-6 text-sm font-bold">
                                <template x-if="selectedAttempt.score >= 85">
                                    <p class="text-emerald-300">🏆 Luar Biasa! Mahasiswa menunjukkan penguasaan materi praktikum yang sangat baik.</p>
                                </template>
                                <template x-if="selectedAttempt.score >= 70 && selectedAttempt.score < 85">
                                    <p class="text-teal-300">🎉 Bagus Sekali! Nilai Anda di atas standar kompetensi praktikum.</p>
                                </template>
                                <template x-if="selectedAttempt.score >= 50 && selectedAttempt.score < 70">
                                    <p class="text-amber-300">👍 Cukup Bagus! Mahasiswa telah memahami sebagian besar konsep materi.</p>
                                </template>
                                <template x-if="selectedAttempt.score < 50">
                                    <p class="text-rose-300">📚 Tetap Semangat! Mahasiswa disarankan mempelajari kembali materi sebelum praktikum berikutnya.</p>
                                </template>
                            </div>

                            {{-- 4 Metrics Breakdown Cards --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto pt-4 border-t border-[#1A2820]">
                                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820] text-center">
                                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Jawaban Benar</span>
                                    <span class="text-base font-bold text-emerald-400 mt-0.5 block" x-text="selectedAttempt.correct_answers_count + ' Soal'"></span>
                                </div>
                                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820] text-center">
                                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Jawaban Salah</span>
                                    <span class="text-base font-bold text-rose-400 mt-0.5 block" x-text="selectedAttempt.wrong_answers_count + ' Soal'"></span>
                                </div>
                                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820] text-center">
                                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Waktu Selesai</span>
                                    <span class="text-base font-bold text-sky-400 mt-0.5 block" x-text="selectedAttempt.formatted_duration || formatDuration(selectedAttempt.duration_seconds)"></span>
                                </div>
                                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820] text-center">
                                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Pengawas Anti-Cheat</span>
                                    <span class="text-base font-bold mt-0.5 block"
                                          :class="selectedAttempt.tab_switch_count === 0 ? 'text-emerald-400' : 'text-amber-400'"
                                          x-text="selectedAttempt.tab_switch_count === 0 ? '0 Switch 🛡️' : selectedAttempt.tab_switch_count + 'x Switch'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- ─── 2. REVIEW ANSWERS & EXPLANATIONS SECTION (No Leaderboard) ─── --}}
                        <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 space-y-5">
                            <div class="flex items-center gap-3 pb-3 border-b border-[#1A2820]">
                                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-400 text-lg">
                                    💡
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-white heading-font">Lembar Jawaban & Pembahasan Kuis</h2>
                                    <p class="text-xs text-gray-400">Tinjau evaluasi butir soal, pilihan jawaban mahasiswa, kunci jawaban yang tepat, dan catatan instruktur</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <template x-for="(ans, idx) in (selectedAttempt.answers || [])" :key="ans.id || idx">
                                    <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 shadow-sm transition-all"
                                         :class="ans.is_correct ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-rose-500'">

                                        {{-- Header Pertanyaan --}}
                                        <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b border-[#16221B] flex-wrap">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-xs font-bold text-gray-300 bg-[#142019] border border-[#1E3327] px-2.5 py-1 rounded-lg"
                                                      x-text="'Soal #' + (idx + 1)"></span>
                                                <span class="text-xs text-gray-400 font-medium"
                                                      x-text="(ans.is_correct ? '+' + ans.points_earned : '0') + ' / ' + (ans.question ? ans.question.points : 0) + ' Poin'"></span>
                                                <template x-if="ans.question && ans.question.question_type === 'multi_select'">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                        Pilihan Jamak
                                                    </span>
                                                </template>
                                            </div>

                                            <div>
                                                <template x-if="ans.is_correct">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        Jawaban Benar
                                                    </span>
                                                </template>
                                                <template x-if="!ans.is_correct">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Jawaban Salah
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Gambar Soal (Jika ada) --}}
                                        <template x-if="ans.question && ans.question.image_url">
                                            <div class="mb-3 rounded-2xl overflow-hidden border border-[#1A2820] bg-black/40 max-h-72 flex items-center justify-center p-2">
                                                <a :href="ans.question.image_url" target="_blank">
                                                    <img :src="ans.question.image_url" alt="Gambar Soal" class="max-h-64 w-auto object-contain rounded-xl">
                                                </a>
                                            </div>
                                        </template>

                                        {{-- Teks Pertanyaan --}}
                                        <div class="text-sm sm:text-base font-bold text-white mb-4 leading-relaxed"
                                             x-html="ans.question ? (ans.question.formatted_text || ans.question.question_text) : 'Pertanyaan'"></div>

                                        {{-- Options Grid --}}
                                        <template x-if="ans.question && ans.question.options && ans.question.options.length > 0">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                                                <template x-for="(opt, optIdx) in ans.question.options" :key="opt.id || optIdx">
                                                    <div class="p-3 rounded-xl border flex items-start justify-between gap-2 shadow-sm transition-all"
                                                         :class="opt.is_correct
                                                            ? 'border-2 border-emerald-500/60 bg-[#0E2018] text-white'
                                                            : (isOptSelected(ans, opt.id)
                                                                ? 'border-2 border-rose-500/60 bg-[#221013] text-white'
                                                                : 'border border-[#1A2820] bg-[#090D0B]/60 text-gray-400')">
                                                        
                                                        <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                                            <span class="w-6 h-6 rounded-lg font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5"
                                                                  :class="opt.is_correct
                                                                    ? 'bg-emerald-500 text-white'
                                                                    : (isOptSelected(ans, opt.id)
                                                                        ? 'bg-rose-500 text-white'
                                                                        : 'bg-[#142019] text-gray-400')"
                                                                  x-text="['A','B','C','D','E','F'][optIdx] || (optIdx + 1)"></span>

                                                            <div class="min-w-0 flex-1">
                                                                <template x-if="opt.image_url">
                                                                    <div class="mb-1.5 max-h-24 overflow-hidden rounded-lg border border-white/10 bg-black/40 p-1">
                                                                        <a :href="opt.image_url" target="_blank">
                                                                            <img :src="opt.image_url" alt="Gambar Opsi" class="max-h-20 w-auto object-contain">
                                                                        </a>
                                                                    </div>
                                                                </template>
                                                                <div class="font-semibold block leading-snug"
                                                                     :class="opt.is_correct ? 'text-emerald-100' : (isOptSelected(ans, opt.id) ? 'text-rose-100' : 'text-gray-300')"
                                                                     x-html="opt.formatted_text || opt.option_text"></div>
                                                            </div>
                                                        </div>

                                                        {{-- Selection Status Badges --}}
                                                        <div class="shrink-0 flex items-center">
                                                            <template x-if="opt.is_correct && isOptSelected(ans, opt.id)">
                                                                <span class="text-[10px] bg-emerald-500 text-white font-bold px-2 py-0.5 rounded-md flex items-center gap-1 shadow-sm">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                    Pilihan Mahasiswa
                                                                </span>
                                                            </template>
                                                            <template x-if="opt.is_correct && !isOptSelected(ans, opt.id)">
                                                                <span class="text-[10px] bg-emerald-600/90 text-white font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                    Kunci Benar
                                                                </span>
                                                            </template>
                                                            <template x-if="!opt.is_correct && isOptSelected(ans, opt.id)">
                                                                <span class="text-[10px] bg-rose-500 text-white font-bold px-2 py-0.5 rounded-md flex items-center gap-1 shadow-sm">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    Pilihan Mahasiswa
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Catatan Pembahasan --}}
                                        <template x-if="ans.question && ans.question.explanation">
                                            <div class="mt-4 p-3 rounded-xl bg-teal-500/5 border border-teal-500/20 text-xs text-teal-300">
                                                <span class="font-bold flex items-center gap-1.5 mb-1 text-teal-400">
                                                    <span>💡</span> Pembahasan:
                                                </span>
                                                <div x-text="ans.question.explanation" class="leading-relaxed text-gray-300"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-[#1A2820] bg-[#090D0B]/80 backdrop-blur-md flex justify-end flex-shrink-0">
                <button type="button" @click="showDetailModal = false"
                        class="px-5 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold transition-colors">
                    Tutup Lembar Jawaban
                </button>
            </div>
        </div>
    </div>

</div>

<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

<script>
    function resultsManager() {
        return {
            showDetailModal: false,
            selectedAttempt: null,
            viewAttemptDetail(attempt) {
                this.selectedAttempt = attempt;
                this.showDetailModal = true;
                this.$nextTick(() => {
                    if (window.Prism) {
                        Prism.highlightAll();
                    }
                });
            },
            isOptSelected(ans, optId) {
                if (!ans) return false;
                const targetId = Number(optId);
                if (ans.selected_option_id && Number(ans.selected_option_id) === targetId) return true;
                if (Array.isArray(ans.selected_option_ids)) {
                    return ans.selected_option_ids.map(Number).includes(targetId);
                }
                return false;
            },
            formatDuration(sec) {
                if (!sec) return '-';
                const m = Math.floor(sec / 60);
                const s = sec % 60;
                return m > 0 ? `${m}m ${s}s` : `${s} detik`;
            }
        };
    }
</script>
@endsection
