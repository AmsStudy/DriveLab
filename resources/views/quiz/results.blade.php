<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis: {{ $quiz->title }} &mdash; LabDrive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <style>
        :root {
            --bg: #090D0B;
            --card: #0F1713;
            --border: #1A2820;
            --emerald: #10B981;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #f3f4f6;
            min-height: 100vh;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .podium-1 {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.25) 0%, rgba(245, 158, 11, 0.05) 100%);
            border-top: 3px solid #f59e0b;
        }
        .podium-2 {
            background: linear-gradient(180deg, rgba(156, 163, 175, 0.25) 0%, rgba(156, 163, 175, 0.05) 100%);
            border-top: 3px solid #9ca3af;
        }
        .podium-3 {
            background: linear-gradient(180deg, rgba(180, 83, 9, 0.25) 0%, rgba(180, 83, 9, 0.05) 100%);
            border-top: 3px solid #b45309;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    {{-- Top App Bar --}}
    <header class="border-b border-[#1A2820] bg-[#090D0B]/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-white font-bold">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 font-bold text-sm">
                    ⚡
                </div>
                <span class="text-base tracking-tight heading-font font-bold">Lab<span class="text-emerald-400">Quiz</span></span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}"
                   class="px-3.5 py-1.5 rounded-xl border border-[#1A2820] hover:bg-gray-800 text-xs text-gray-300 font-medium transition-colors">
                    Beranda
                </a>
            </div>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-8 space-y-8">

        {{-- Hero Score Celebration Card --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-10 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute -right-20 -top-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Pengerjaan Kuis Selesai
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-white heading-font mb-1">
                {{ $quiz->title }}
            </h1>
            <p class="text-xs text-gray-400 mb-6">Mahasiswa: <strong class="text-white">{{ $attempt->student_name }}</strong> ({{ $attempt->nim }})</p>

            {{-- Score Big Ring --}}
            <div class="inline-flex flex-col items-center justify-center w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-gradient-to-tr from-[#131E17] to-[#1A2D23] border-4 border-emerald-500/50 shadow-2xl shadow-emerald-500/20 mb-4">
                <span class="text-3xl sm:text-5xl font-extrabold text-white heading-font tracking-tight font-mono">
                    {{ number_format($attempt->score, 1) }}
                </span>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest mt-0.5">Skor Akhir</span>
            </div>

            {{-- Grade Feedback --}}
            <div class="max-w-md mx-auto mb-6">
                @if($attempt->score >= 85)
                    <p class="text-sm font-bold text-emerald-300">🏆 Luar Biasa! Anda menunjukkan penguasaan materi yang sangat baik.</p>
                @elseif($attempt->score >= 70)
                    <p class="text-sm font-bold text-teal-300">🎉 Bagus Sekali! Nilai Anda di atas standar kompetensi praktikum.</p>
                @elseif($attempt->score >= 50)
                    <p class="text-sm font-bold text-amber-300">👍 Cukup Bagus! Anda telah memahami sebagian besar konsep.</p>
                @else
                    <p class="text-sm font-bold text-rose-300">📚 Tetap Semangat! Pelajari kembali modul sebelum praktikum dimulai.</p>
                @endif
            </div>

            {{-- Metrics Breakdown Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto pt-4 border-t border-[#1A2820]">
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Jawaban Benar</span>
                    <span class="text-base font-bold text-emerald-400 mt-0.5 block">{{ $attempt->correct_answers_count }} Soal</span>
                </div>
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Jawaban Salah</span>
                    <span class="text-base font-bold text-rose-400 mt-0.5 block">{{ $attempt->wrong_answers_count }} Soal</span>
                </div>
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Waktu Selesai</span>
                    <span class="text-base font-bold text-sky-400 mt-0.5 block">{{ $attempt->formattedDuration() }}</span>
                </div>
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-[10px] text-gray-400 uppercase font-semibold block">Pengawas Anti-Cheat</span>
                    <span class="text-base font-bold mt-0.5 block {{ $attempt->tab_switch_count === 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ $attempt->tab_switch_count === 0 ? '0 Switch 🛡️' : $attempt->tab_switch_count . 'x Switch' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Podium & Leaderboard Section --}}
        @if($quiz->show_leaderboard && $leaderboard)
            <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-white heading-font flex items-center gap-2">
                            <span>🏆 Leaderboard Mahasiswa</span>
                        </h2>
                        <p class="text-xs text-gray-400">Peringkat berdasarkan perolehan nilai dan kecepatan pengerjaan</p>
                    </div>

                    {{-- Student Current Rank Pill --}}
                    <div class="px-3.5 py-1.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 font-bold text-xs">
                        Peringkat Anda: #{{ $rank }} dari {{ $totalParticipants }} Peserta
                    </div>
                </div>

                {{-- Podium Top 3 (if at least 1 attempt) --}}
                @if($leaderboard->count() >= 2)
                    <div class="grid grid-cols-3 gap-2 sm:gap-4 items-end max-w-lg mx-auto mb-8 pt-4">
                        {{-- 2nd Place --}}
                        <div class="podium-2 rounded-2xl p-3 sm:p-4 text-center flex flex-col justify-end h-36 sm:h-44">
                            <span class="text-xl sm:text-2xl mb-1">🥈</span>
                            <span class="text-xs sm:text-sm font-bold text-white truncate block">{{ $leaderboard[1]->student_name ?? '-' }}</span>
                            <span class="text-[10px] text-gray-400 font-mono block">{{ $leaderboard[1]->nim ?? '' }}</span>
                            <span class="text-xs font-bold text-gray-200 mt-1 block">{{ $leaderboard[1]->score ?? 0 }} Pts</span>
                            <span class="text-[9px] text-gray-400 mt-0.5 block font-mono">Rank 2</span>
                        </div>

                        {{-- 1st Place (Gold) --}}
                        <div class="podium-1 rounded-2xl p-3 sm:p-4 text-center flex flex-col justify-end h-44 sm:h-52 ring-2 ring-amber-500/40">
                            <span class="text-2xl sm:text-3xl mb-1 animate-bounce">👑</span>
                            <span class="text-xs sm:text-sm font-bold text-amber-300 truncate block">{{ $leaderboard[0]->student_name ?? '-' }}</span>
                            <span class="text-[10px] text-gray-400 font-mono block">{{ $leaderboard[0]->nim ?? '' }}</span>
                            <span class="text-sm font-bold text-white mt-1 block">{{ $leaderboard[0]->score ?? 0 }} Pts</span>
                            <span class="text-[10px] font-bold text-amber-400 mt-0.5 block font-mono">Juara 1</span>
                        </div>

                        {{-- 3rd Place --}}
                        <div class="podium-3 rounded-2xl p-3 sm:p-4 text-center flex flex-col justify-end h-32 sm:h-36">
                            <span class="text-lg sm:text-xl mb-1">🥉</span>
                            <span class="text-xs sm:text-sm font-bold text-white truncate block">{{ $leaderboard[2]->student_name ?? '-' }}</span>
                            <span class="text-[10px] text-gray-400 font-mono block">{{ $leaderboard[2]->nim ?? '' }}</span>
                            <span class="text-xs font-bold text-gray-200 mt-1 block">{{ $leaderboard[2]->score ?? 0 }} Pts</span>
                            <span class="text-[9px] text-gray-400 mt-0.5 block font-mono">Rank 3</span>
                        </div>
                    </div>
                @endif

                {{-- Leaderboard Top 10 List --}}
                <div class="divide-y divide-[#1A2820] bg-[#090D0B] rounded-2xl border border-[#1A2820] overflow-hidden">
                    @foreach($leaderboard as $idx => $entry)
                        <div class="p-3.5 sm:px-5 flex items-center justify-between gap-3 {{ $entry->id === $attempt->id ? 'bg-emerald-500/10 border-l-4 border-l-emerald-500' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-lg text-xs font-mono font-bold flex items-center justify-center {{ $idx === 0 ? 'bg-amber-500/20 text-amber-300' : ($idx === 1 ? 'bg-gray-400/20 text-gray-300' : ($idx === 2 ? 'bg-amber-700/20 text-amber-500' : 'text-gray-500')) }}">
                                    #{{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="text-xs sm:text-sm font-semibold text-white block">
                                        {{ $entry->student_name }}
                                        @if($entry->id === $attempt->id)
                                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] bg-emerald-500 text-white font-bold">Anda</span>
                                        @endif
                                    </span>
                                    <span class="text-[11px] font-mono text-gray-400">{{ $entry->nim }}</span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-xs sm:text-sm font-bold text-white block">{{ number_format($entry->score, 1) }}</span>
                                <span class="text-[10px] font-mono text-gray-400">{{ $entry->formattedDuration() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Review Answers & Explanations (if quiz allows show_review) --}}
        @if($quiz->show_review && $reviewAnswers)
            <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 space-y-4">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-white heading-font">💡 Pembahasan & Kunci Jawaban</h2>
                    <p class="text-xs text-gray-400">Tinjau hasil jawaban Anda beserta kunci dan catatan pembahasan</p>
                </div>

                <div class="space-y-4">
                    @foreach($reviewAnswers as $idx => $ans)
                        <div class="p-5 rounded-2xl border {{ $ans->is_correct ? 'bg-emerald-500/5 border-emerald-500/30' : 'bg-rose-500/5 border-rose-500/30' }}">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-bold font-mono text-gray-400">Soal #{{ $idx + 1 }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $ans->is_correct ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    {{ $ans->is_correct ? '✓ Benar (+' . $ans->points_earned . ' Poin)' : '✗ Salah (0 Poin)' }}
                                </span>
                            </div>

                            <p class="text-sm font-semibold text-white mb-3">{{ $ans->question->question_text ?? 'Pertanyaan' }}</p>

                            {{-- Options List Review --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs mb-3">
                                @foreach($ans->question->options as $opt)
                                    @php
                                        $isSelectedByStudent = ($ans->selected_option_id === $opt->id);
                                        $isCorrectOption = $opt->is_correct;
                                    @endphp
                                    <div class="p-2.5 rounded-xl border flex items-center justify-between gap-2 {{ $isCorrectOption ? 'bg-emerald-500/15 border-emerald-500/50 text-emerald-200 font-bold' : ($isSelectedByStudent ? 'bg-rose-500/15 border-rose-500/50 text-rose-300 line-through' : 'bg-[#090D0B] border-[#1A2820] text-gray-400') }}">
                                        <span>{{ $opt->option_text }}</span>
                                        @if($isCorrectOption)
                                            <span class="text-[10px] bg-emerald-500 text-white px-1.5 py-0.5 rounded font-bold">Kunci Benar</span>
                                        @elseif($isSelectedByStudent)
                                            <span class="text-[10px] bg-rose-500 text-white px-1.5 py-0.5 rounded font-bold">Jawaban Anda</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Explanation --}}
                            @if(!empty($ans->question->explanation))
                                <div class="p-3 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 text-xs">
                                    <strong>💡 Pembahasan:</strong> {{ $ans->question->explanation }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Bottom Action Buttons --}}
        <div class="flex items-center justify-center gap-3 pt-2">
            <a href="{{ route('home') }}"
               class="px-6 py-3 rounded-2xl bg-[#0F1713] border border-[#1A2820] hover:bg-[#1A2820] text-gray-200 text-sm font-semibold transition-colors">
                ← Kembali ke Portal Lab
            </a>
            @if($quiz->is_active)
                <a href="{{ route('quiz.lobby', $quiz->unique_token) }}"
                   class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-bold transition-all shadow-lg shadow-emerald-500/20">
                    Selesai
                </a>
            @endif
        </div>

    </main>

    <footer class="py-6 border-t border-[#1A2820] text-center text-xs text-gray-600 mt-auto">
        &copy; {{ date('Y') }} LabDrive &bull; Platform Distribusi & Evaluasi Laboratorium
    </footer>

    {{-- Trigger Confetti Celebration --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof confetti === 'function') {
                const score = {{ (float)$attempt->score }};
                if (score >= 60) {
                    confetti({
                        particleCount: 80,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                }
            }
        });
    </script>
</body>
</html>
