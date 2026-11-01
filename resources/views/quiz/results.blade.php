<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis: {{ $quiz->title }} &mdash; IC-Labs</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Orbitron:wght@700;800;900&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
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
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-white font-bold group">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-wider leading-none block" style="font-family:'Orbitron',sans-serif;">IC-LABS</span>
                    <span class="text-[9px] text-emerald-400 font-semibold tracking-widest block mt-0.5" style="font-family:'Chakra Petch',sans-serif;">CBT EVALUATION</span>
                </div>
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

                {{-- Podium Top 3 (if at least 3 participants) --}}
                @if($leaderboard->count() >= 3)
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
            <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-[#1A2820]">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-400 text-lg">
                        💡
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white heading-font">Pembahasan & Kunci Jawaban</h2>
                        <p class="text-xs text-gray-400">Tinjau evaluasi butir soal, kunci jawaban yang tepat, dan catatan pembahasan instruktur</p>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach($reviewAnswers as $idx => $ans)
                        <div class="bg-[#090D0B] border border-[#1A2820] rounded-2xl p-5 shadow-sm transition-all {{ $ans->is_correct ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-rose-500' }}">
                            {{-- Header Pertanyaan --}}
                            <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b border-[#16221B]">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-gray-300 bg-[#142019] border border-[#1E3327] px-2.5 py-1 rounded-lg">
                                        Soal #{{ $idx + 1 }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        {{ $ans->is_correct ? '+' . $ans->points_earned : '0' }} / {{ $ans->question->points ?? 0 }} Poin
                                    </span>
                                </div>

                                <div>
                                    @if($ans->is_correct)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Jawaban Benar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Jawaban Salah
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Question Image (if uploaded) --}}
                            @if(!empty($ans->question->image_path))
                                <div class="mb-3 rounded-2xl overflow-hidden border border-[#1A2820] bg-black/40 max-h-72 flex items-center justify-center p-2">
                                    <a href="{{ $ans->question->image_url }}" target="_blank">
                                        <img src="{{ $ans->question->image_url }}" alt="Gambar Soal" class="max-h-64 w-auto object-contain rounded-xl">
                                    </a>
                                </div>
                            @endif

                            {{-- Question Text --}}
                            <div class="text-sm sm:text-base font-bold text-white mb-4 leading-relaxed">{!! App\Helpers\RichTextHelper::format($ans->question->question_text ?? 'Pertanyaan') !!}</div>

                            {{-- Options Grid: Hanya opsi yang dipilih yang diwarnai (Hijau jika benar, Merah jika salah) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                                @php
                                    $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
                                    $studentSelection = $ans->selected_option_ids ?: ($ans->selected_option_id ? [$ans->selected_option_id] : []);
                                @endphp
                                @foreach($ans->question->options as $optIdx => $opt)
                                    @php
                                        $isSelectedByStudent = in_array((int)$opt->id, array_map('intval', $studentSelection), true);
                                        $isCorrectOption = (bool)$opt->is_correct;
                                    @endphp

                                    @if($isSelectedByStudent)
                                        @if($isCorrectOption)
                                            {{-- JAWABAN BENAR MAHASISWA (HIJAU) --}}
                                            <div class="p-3 rounded-xl border-2 border-emerald-500 bg-[#0E2018] text-white flex items-start justify-between gap-2 shadow-sm shadow-emerald-500/10">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        @if($opt->image_path)
                                                            <div class="mb-1.5 max-h-24 overflow-hidden rounded-lg border border-white/10 bg-black/40 p-1">
                                                                <a href="{{ $opt->image_url }}" target="_blank">
                                                                    <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain">
                                                                </a>
                                                            </div>
                                                        @endif
                                                        <div class="font-semibold text-emerald-100 block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] bg-emerald-500 text-white font-bold px-2 py-0.5 rounded-md flex-shrink-0 flex items-center gap-1 shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Pilihan Anda
                                                </span>
                                            </div>
                                        @else
                                            {{-- JAWABAN SALAH MAHASISWA (MERAH) --}}
                                            <div class="p-3 rounded-xl border-2 border-rose-500 bg-[#221013] text-white flex items-start justify-between gap-2 shadow-sm shadow-rose-500/10">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span class="w-6 h-6 rounded-lg bg-rose-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        @if($opt->image_path)
                                                            <div class="mb-1.5 max-h-24 overflow-hidden rounded-lg border border-white/10 bg-black/40 p-1">
                                                                <a href="{{ $opt->image_url }}" target="_blank">
                                                                    <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain">
                                                                </a>
                                                            </div>
                                                        @endif
                                                        <div class="font-semibold text-rose-200 block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] bg-rose-600 text-white font-bold px-2 py-0.5 rounded-md flex-shrink-0 flex items-center gap-1 shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Pilihan Anda (Salah)
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        {{-- OPSI LAIN (NETRAL) --}}
                                        <div class="p-3 rounded-xl border border-[#1A2820] bg-[#0E1612] text-gray-400 flex items-start gap-2.5 opacity-75">
                                            <span class="w-6 h-6 rounded-lg bg-gray-800 text-gray-400 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                            </span>
                                            <div class="min-w-0">
                                                @if($opt->image_path)
                                                    <div class="mb-1.5 max-h-24 overflow-hidden rounded-lg border border-white/10 bg-black/40 p-1">
                                                        <a href="{{ $opt->image_url }}" target="_blank">
                                                            <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain">
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="font-normal block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Catatan Pembahasan & Kunci Jawaban --}}
                            @php
                                $correctOpts = $ans->question->options->where('is_correct', true)->values();
                                $correctTextArray = [];
                                foreach ($correctOpts as $co) {
                                    $idx = $ans->question->options->search(fn($item) => $item->id === $co->id);
                                    $ltr = $labels[$idx] ?? ($idx + 1);
                                    $correctTextArray[] = "[{$ltr}] " . $co->option_text;
                                }
                                $correctAnswerDisplay = implode(', ', $correctTextArray);
                            @endphp

                            <div class="mt-4 p-4 rounded-xl border text-xs {{ $ans->is_correct ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-200' : 'bg-[#0A1410] border-emerald-500/40 text-emerald-100' }}">
                                <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                    @if($ans->is_correct)
                                        <div class="flex items-center gap-2 font-bold text-emerald-400 text-xs sm:text-sm">
                                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-300 text-xs">✓</span>
                                            <span>Jawaban Anda Benar:</span>
                                            <span class="text-emerald-300 font-semibold">{{ $correctAnswerDisplay }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 font-bold text-emerald-400 text-xs sm:text-sm">
                                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-300 text-xs">🔑</span>
                                            <span>Kunci Jawaban Benar:</span>
                                            <span class="text-emerald-200 font-semibold">{{ $correctAnswerDisplay }}</span>
                                        </div>
                                    @endif

                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md {{ $ans->is_correct ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' }}">
                                        {{ $ans->is_correct ? 'Tepat (+ ' . ($ans->points_earned ?: 0) . ' Poin)' : 'Kunci Tepat' }}
                                    </span>
                                </div>

                                <div class="pt-2 border-t border-emerald-500/20">
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold shrink-0 flex items-center gap-1 text-emerald-400">
                                            <span>💡</span> Pembahasan:
                                        </span>
                                        <div class="leading-relaxed text-gray-300 flex-1">
                                            @if(!empty($ans->question->explanation))
                                                {!! App\Helpers\RichTextHelper::format($ans->question->explanation) !!}
                                            @else
                                                <span class="italic text-gray-400">{{ $ans->is_correct ? 'Jawaban Anda sudah tepat sesuai materi praktikum.' : 'Tidak ada catatan pembahasan tambahan untuk soal ini.' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        &copy; {{ date('Y') }} IC-Labs &bull; Platform Distribusi & Evaluasi Laboratorium
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
