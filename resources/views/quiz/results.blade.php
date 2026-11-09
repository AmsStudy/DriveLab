<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis: {{ $quiz->title }} &mdash; IC-Labs</title>
    <x-theme-head-script />
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
        html:not(.dark) {
            --bg: #F4F7F5;
            --card: #FFFFFF;
            --border: #E2EAE5;
            --emerald: #059669;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #f3f4f6;
            min-height: 100vh;
        }
        html:not(.dark) body {
            color: #0F1F17 !important;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Score Circle Badge (Dark & Light Mode) */
        .score-circle-ring {
            background: radial-gradient(circle at 30% 30%, #1A2D23 0%, #0D1611 100%);
            border: 4px solid rgba(16, 185, 129, 0.55);
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.28), inset 0 0 20px rgba(16, 185, 129, 0.15);
        }
        .score-circle-ring .score-value {
            color: #FFFFFF !important;
        }
        .score-circle-ring .score-label {
            color: #34D399 !important;
        }

        html:not(.dark) .score-circle-ring {
            background: radial-gradient(circle at 30% 30%, #FFFFFF 0%, #ECFDF5 50%, #D1FAE5 100%) !important;
            border: 4px solid #10B981 !important;
            box-shadow: 0 16px 36px -4px rgba(16, 185, 129, 0.35), 0 0 0 6px rgba(16, 185, 129, 0.15), inset 0 2px 5px rgba(255, 255, 255, 0.95) !important;
        }
        html:not(.dark) .score-circle-ring .score-value {
            color: #064E3B !important;
        }
        html:not(.dark) .score-circle-ring .score-label {
            color: #047857 !important;
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

        /* Podium Light Mode Enhancements */
        html:not(.dark) .podium-1 {
            background: linear-gradient(180deg, #FEF3C7 0%, #FFFFFF 100%) !important;
            border-top: 4px solid #F59E0B !important;
            border-inline: 1px solid #FDE68A !important;
            border-bottom: 1px solid #FDE68A !important;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.15) !important;
        }
        html:not(.dark) .podium-2 {
            background: linear-gradient(180deg, #F1F5F9 0%, #FFFFFF 100%) !important;
            border-top: 4px solid #94A3B8 !important;
            border-inline: 1px solid #E2E8F0 !important;
            border-bottom: 1px solid #E2E8F0 !important;
            box-shadow: 0 8px 25px rgba(148, 163, 184, 0.15) !important;
        }
        html:not(.dark) .podium-3 {
            background: linear-gradient(180deg, #FFEDD5 0%, #FFFFFF 100%) !important;
            border-top: 4px solid #EA580C !important;
            border-inline: 1px solid #FED7AA !important;
            border-bottom: 1px solid #FED7AA !important;
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.15) !important;
        }

        /* Results Card Surface in Light Mode */
        html:not(.dark) .results-card {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            box-shadow: 0 10px 30px -4px rgba(0, 0, 0, 0.06), 0 0 1px rgba(0, 0, 0, 0.08) !important;
        }

        /* Leaderboard & Review Surfaces (Dark Mode Cyber Default) */
        .leaderboard-list-box {
            background-color: #090D0B !important;
            border-color: #1A2820 !important;
        }
        .leaderboard-row {
            color: #F3F4F6 !important;
        }
        .leaderboard-row .student-name {
            color: #FFFFFF !important;
        }
        .leaderboard-row.is-self {
            background-color: rgba(16, 185, 129, 0.12) !important;
            border-left: 4px solid #10B981 !important;
        }
        .review-question-card {
            background-color: #090D0B !important;
            border-color: #1A2820 !important;
            color: #F3F4F6 !important;
        }
        .review-question-text {
            color: #FFFFFF !important;
        }
        .review-badge-soal {
            background-color: #142019 !important;
            border-color: #1E3327 !important;
            color: #D1D5DB !important;
        }
        .review-option-neutral {
            background-color: #0E1612 !important;
            border-color: #1A2820 !important;
            color: #D1D5DB !important;
        }
        .review-option-correct {
            background-color: #0E2018 !important;
            border-color: #10B981 !important;
            color: #ECFDF5 !important;
        }
        .review-option-wrong {
            background-color: #221013 !important;
            border-color: #F43F5E !important;
            color: #FFF1F2 !important;
        }
        .review-opt-badge {
            background-color: #1F2937 !important;
            color: #9CA3AF !important;
        }
        .review-explanation-box {
            background-color: rgba(16, 185, 129, 0.08) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #D1FAE5 !important;
        }
        .review-explanation-box .exp-title {
            color: #34D399 !important;
        }
        .review-explanation-box .exp-text {
            color: #D1D5DB !important;
        }
        .btn-back-portal {
            background-color: #0F1713 !important;
            border-color: #1A2820 !important;
            color: #E5E7EB !important;
        }
        .btn-back-portal:hover {
            background-color: #1A2820 !important;
            color: #FFFFFF !important;
        }
        .btn-nav-home {
            background-color: transparent !important;
            border-color: #1A2820 !important;
            color: #D1D5DB !important;
        }
        .btn-nav-home:hover {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
        }
        .metric-stat-card {
            background-color: #090D0B !important;
            border-color: #1A2820 !important;
        }
        .review-opt-img-box {
            background-color: rgba(0, 0, 0, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Light Mode Overrides for Leaderboard & Review */
        html:not(.dark) .leaderboard-list-box {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }
        html:not(.dark) .leaderboard-row {
            color: #0F1F17 !important;
        }
        html:not(.dark) .leaderboard-row .student-name {
            color: #111827 !important;
        }
        html:not(.dark) .leaderboard-row.is-self {
            background-color: #ECFDF5 !important;
            border-left: 4px solid #10B981 !important;
        }
        html:not(.dark) .review-question-card {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            color: #0F1F17 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }
        html:not(.dark) .review-question-text {
            color: #111827 !important;
        }
        html:not(.dark) .review-badge-soal {
            background-color: #F3F4F6 !important;
            border-color: #E5E7EB !important;
            color: #374151 !important;
        }
        html:not(.dark) .review-option-neutral {
            background-color: rgba(249, 250, 251, 0.8) !important;
            border-color: #E5E7EB !important;
            color: #374151 !important;
        }
        html:not(.dark) .review-option-correct {
            background-color: #ECFDF5 !important;
            border-color: #10B981 !important;
            color: #064E3B !important;
        }
        html:not(.dark) .review-option-wrong {
            background-color: #FFF1F2 !important;
            border-color: #F43F5E !important;
            color: #881337 !important;
        }
        html:not(.dark) .review-opt-badge {
            background-color: #E5E7EB !important;
            color: #4B5563 !important;
        }
        html:not(.dark) .review-explanation-box {
            background-color: rgba(236, 253, 245, 0.85) !important;
            border-color: #A7F3D0 !important;
            color: #064E3B !important;
        }
        html:not(.dark) .review-explanation-box .exp-title {
            color: #065F46 !important;
        }
        html:not(.dark) .review-explanation-box .exp-text {
            color: #1F2937 !important;
        }
        html:not(.dark) .btn-back-portal {
            background-color: #FFFFFF !important;
            border-color: #D1DCD6 !important;
            color: #374151 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
        }
        html:not(.dark) .btn-back-portal:hover {
            background-color: #F8FAF9 !important;
            color: #111827 !important;
            border-color: #10B981 !important;
        }
        html:not(.dark) .btn-nav-home {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            color: #374151 !important;
        }
        html:not(.dark) .btn-nav-home:hover {
            background-color: #F8FAF9 !important;
            color: #111827 !important;
        }
        html:not(.dark) .metric-stat-card.metric-emerald {
            background-color: rgba(236, 253, 245, 0.7) !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
        }
        html:not(.dark) .metric-stat-card.metric-rose {
            background-color: rgba(255, 241, 242, 0.7) !important;
            border-color: rgba(244, 63, 94, 0.2) !important;
        }
        html:not(.dark) .metric-stat-card.metric-sky {
            background-color: rgba(240, 249, 255, 0.7) !important;
            border-color: rgba(14, 165, 233, 0.2) !important;
        }
        html:not(.dark) .metric-stat-card.metric-amber {
            background-color: rgba(254, 243, 199, 0.7) !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
        }
        html:not(.dark) .review-opt-img-box {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
        }

        /* Hover Zoom Tooltip Preview & Lightbox in Results View */
        .cyber-hover-preview-bubble {
            position: fixed;
            pointer-events: none;
            z-index: 110;
            opacity: 0;
            transform: scale(0.92) translateY(8px);
            transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: min(440px, 90vw);
            max-height: min(340px, 60vh);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(16, 185, 129, 0.4);
            background: #0A0F0D;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.85), 0 0 25px rgba(16, 185, 129, 0.25);
            padding: 6px;
        }
        html:not(.dark) .cyber-hover-preview-bubble {
            background: #FFFFFF !important;
            border-color: #A7F3D0 !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18), 0 0 20px rgba(16, 185, 129, 0.15) !important;
        }
        .cyber-hover-preview-bubble.is-active {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        .cyber-hover-preview-bubble img {
            width: 100%;
            height: auto;
            max-height: calc(min(340px, 60vh) - 12px);
            object-fit: contain;
            border-radius: 12px;
            display: block;
        }
        html:not(.dark) #cyber-image-lightbox .lightbox-card {
            background-color: #FFFFFF !important;
            border-color: #D1DCD6 !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25), 0 0 30px rgba(16, 185, 129, 0.15) !important;
        }
        html:not(.dark) #cyber-image-lightbox .lightbox-close-btn {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: #D1DCD6 !important;
            color: #374151 !important;
        }
        html:not(.dark) #cyber-image-lightbox .lightbox-close-btn:hover {
            background-color: #F3F4F6 !important;
            color: #0F1F17 !important;
        }
        html:not(.dark) #cyber-image-lightbox #cyber-lightbox-caption {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: #D1DCD6 !important;
            color: #374151 !important;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    {{-- Top App Bar --}}
    <header class="border-b border-[#1A2820] bg-[#090D0B]/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-gray-900 dark:text-white font-bold group">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-wider leading-none block" style="font-family:'Orbitron',sans-serif;">IC-LABS</span>
                    <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold tracking-widest block mt-0.5" style="font-family:'Chakra Petch',sans-serif;">CBT EVALUATION</span>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <x-theme-switcher :compact="true" />
                <a href="{{ route('home') }}"
                   class="btn-nav-home px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition-all shadow-sm">
                    Beranda
                </a>
            </div>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-8 space-y-8">

        {{-- Hero Score Celebration Card --}}
        <div class="results-card bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-10 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute -right-20 -top-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 border border-emerald-500/30 mb-3 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Pengerjaan Kuis Selesai
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white heading-font mb-1">
                {{ $quiz->title }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Mahasiswa: <strong class="text-gray-900 dark:text-white">{{ $attempt->student_name }}</strong> ({{ $attempt->nim }})</p>

            {{-- Score Big Ring --}}
            <div class="score-circle-ring inline-flex flex-col items-center justify-center w-36 h-36 sm:w-44 sm:h-44 rounded-full mb-5 transition-all">
                <span class="score-value text-3xl sm:text-5xl font-extrabold heading-font tracking-tight font-mono">
                    {{ number_format($attempt->score, 1) }}
                </span>
                <span class="score-label text-xs font-bold uppercase tracking-widest mt-1">Skor Akhir</span>
            </div>

            {{-- Grade Feedback --}}
            <div class="max-w-md mx-auto mb-6">
                @if($attempt->score >= 85)
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm font-semibold shadow-sm">
                        <span>🏆</span> <span>Luar Biasa! Anda menunjukkan penguasaan materi yang sangat baik.</span>
                    </div>
                @elseif($attempt->score >= 70)
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-teal-500/10 border border-teal-500/25 text-teal-800 dark:text-teal-300 text-xs sm:text-sm font-semibold shadow-sm">
                        <span>🎉</span> <span>Bagus Sekali! Nilai Anda di atas standar kompetensi praktikum.</span>
                    </div>
                @elseif($attempt->score >= 50)
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-amber-500/10 border border-amber-500/25 text-amber-800 dark:text-amber-300 text-xs sm:text-sm font-semibold shadow-sm">
                        <span>👍</span> <span>Cukup Bagus! Anda telah memahami sebagian besar konsep.</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-500/10 border border-rose-500/25 text-rose-700 dark:text-rose-300 text-xs sm:text-sm font-semibold shadow-sm">
                        <span>📚</span> <span>Tetap Semangat! Pelajari kembali modul sebelum praktikum dimulai.</span>
                    </div>
                @endif
            </div>

            {{-- Metrics Breakdown Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto pt-5 border-t border-gray-200 dark:border-[#1A2820]">
                <div class="metric-stat-card metric-emerald p-3.5 rounded-2xl border shadow-sm">
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-semibold block tracking-wider">Jawaban Benar</span>
                    <span class="text-base font-bold text-emerald-700 dark:text-emerald-400 mt-0.5 block">{{ $attempt->correct_answers_count }} Soal</span>
                </div>
                <div class="metric-stat-card metric-rose p-3.5 rounded-2xl border shadow-sm">
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-semibold block tracking-wider">Jawaban Salah</span>
                    <span class="text-base font-bold text-rose-700 dark:text-rose-400 mt-0.5 block">{{ $attempt->wrong_answers_count }} Soal</span>
                </div>
                <div class="metric-stat-card metric-sky p-3.5 rounded-2xl border shadow-sm">
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-semibold block tracking-wider">Waktu Selesai</span>
                    <span class="text-base font-bold text-sky-700 dark:text-sky-400 mt-0.5 block">{{ $attempt->formattedDuration() }}</span>
                </div>
                <div class="metric-stat-card metric-amber p-3.5 rounded-2xl border shadow-sm">
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-semibold block tracking-wider">Pengawas Anti-Cheat</span>
                    <span class="text-base font-bold mt-0.5 block {{ $attempt->tab_switch_count === 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                        {{ $attempt->tab_switch_count === 0 ? '0 Switch 🛡️' : $attempt->tab_switch_count . 'x Switch' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Review Answers & Explanations (if quiz allows show_review) --}}
        @if($quiz->show_review && $reviewAnswers)
            @php
                $canRevealCorrect = (bool)($quiz->show_correct_answer ?? false) || (auth()->check() && (auth()->user()->isSuperAdmin() || $quiz->user_id === auth()->id()));
            @endphp
            <div class="results-card bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-[#1A2820]">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-500 text-lg shadow-sm">
                        💡
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white heading-font">{{ $canRevealCorrect ? 'Pembahasan & Kunci Jawaban' : 'Evaluasi Lembar Jawaban' }}</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $canRevealCorrect ? 'Tinjau evaluasi butir soal, kunci jawaban yang tepat, dan catatan pembahasan instruktur' : 'Tinjau evaluasi hasil pengerjaan soal Anda (kunci jawaban disembunyikan oleh instruktur)' }}</p>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach($reviewAnswers as $idx => $ans)
                        <div class="review-question-card bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] rounded-2xl p-5 shadow-sm transition-all {{ $ans->is_correct ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-rose-500' }}">
                            {{-- Header Pertanyaan --}}
                            <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b border-gray-100 dark:border-[#16221B]">
                                <div class="flex items-center gap-2">
                                    <span class="review-badge-soal font-mono text-xs font-bold px-2.5 py-1 rounded-lg">
                                        Soal #{{ $idx + 1 }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $ans->is_correct ? '+' . $ans->points_earned : '0' }} / {{ $ans->question->points ?? 0 }} Poin
                                    </span>
                                </div>

                                <div>
                                    @if($ans->is_correct)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 border border-emerald-500/30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Jawaban Benar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-500/30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Jawaban Salah
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Question Image (if uploaded) --}}
                            @if(!empty($ans->question->image_path))
                                <div class="mb-3 flex items-center justify-center">
                                    <button type="button"
                                            onmouseenter="showCyberHoverBubble(event, '{{ $ans->question->image_url }}')"
                                            onmousemove="updateCyberHoverBubblePos(event)"
                                            onmouseleave="hideCyberHoverBubble()"
                                            onclick="openCyberLightbox('{{ $ans->question->image_url }}', 'Gambar Soal #{{ $idx + 1 }}')"
                                            class="inline-block group cursor-zoom-in focus:outline-none rounded-2xl overflow-hidden border border-gray-200 dark:border-[#1A2820] bg-gray-50 dark:bg-black/40 hover:border-emerald-500/40 p-2 transition-all shadow-sm">
                                        <img src="{{ $ans->question->image_url }}" alt="Gambar Soal" class="max-h-64 w-auto object-contain rounded-xl group-hover:scale-[1.01] transition-transform" loading="lazy">
                                    </button>
                                </div>
                            @endif

                            {{-- Question Text --}}
                            <div class="review-question-text text-sm sm:text-base font-bold text-gray-900 dark:text-white mb-4 leading-relaxed">{!! App\Helpers\RichTextHelper::format($ans->question->question_text ?? 'Pertanyaan') !!}</div>

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
                                            <div class="review-option-correct p-3 rounded-xl border-2 border-emerald-500 bg-[#0E2018] flex items-start justify-between gap-2 shadow-sm shadow-emerald-500/10">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        @if($opt->image_path)
                                                            <div class="mb-1.5 inline-block">
                                                                <button type="button"
                                                                        onmouseenter="showCyberHoverBubble(event, '{{ $opt->image_url }}')"
                                                                        onmousemove="updateCyberHoverBubblePos(event)"
                                                                        onmouseleave="hideCyberHoverBubble()"
                                                                        onclick="openCyberLightbox('{{ $opt->image_url }}', 'Gambar Opsi {{ $labels[$optIdx] ?? '' }}')"
                                                                        class="review-opt-img-box block group cursor-zoom-in focus:outline-none rounded-lg border hover:border-emerald-500/40 p-1 transition-all">
                                                                    <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain group-hover:scale-105 transition-transform" loading="lazy">
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <div class="font-semibold block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] bg-emerald-600 text-white font-bold px-2 py-0.5 rounded-md flex-shrink-0 flex items-center gap-1 shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Pilihan Anda
                                                </span>
                                            </div>
                                        @else
                                            {{-- JAWABAN SALAH MAHASISWA (MERAH) --}}
                                            <div class="review-option-wrong p-3 rounded-xl border-2 border-rose-500 bg-[#221013] flex items-start justify-between gap-2 shadow-sm shadow-rose-500/10">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span class="w-6 h-6 rounded-lg bg-rose-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        @if($opt->image_path)
                                                            <div class="mb-1.5 inline-block">
                                                                <button type="button"
                                                                        onmouseenter="showCyberHoverBubble(event, '{{ $opt->image_url }}')"
                                                                        onmousemove="updateCyberHoverBubblePos(event)"
                                                                        onmouseleave="hideCyberHoverBubble()"
                                                                        onclick="openCyberLightbox('{{ $opt->image_url }}', 'Gambar Opsi {{ $labels[$optIdx] ?? '' }}')"
                                                                        class="review-opt-img-box block group cursor-zoom-in focus:outline-none rounded-lg border hover:border-emerald-500/40 p-1 transition-all">
                                                                    <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain group-hover:scale-105 transition-transform" loading="lazy">
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <div class="font-semibold block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] bg-rose-600 text-white font-bold px-2 py-0.5 rounded-md flex-shrink-0 flex items-center gap-1 shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Pilihan Anda (Salah)
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        {{-- BUKAN PILIHAN MAHASISWA --}}
                                        @if(!$ans->is_correct && $isCorrectOption && $canRevealCorrect)
                                            {{-- KUNCI JAWABAN BENAR (HANYA MUNCUL JIKA JAWABAN MAHASISWA SALAH & ADMIN MENGIJINKAN) --}}
                                            <div class="review-option-correct p-3 rounded-xl border-2 border-emerald-500 bg-[#0E2018] flex items-start justify-between gap-2 shadow-sm shadow-emerald-500/10">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        @if($opt->image_path)
                                                            <div class="mb-1.5 inline-block">
                                                                <button type="button"
                                                                        onmouseenter="showCyberHoverBubble(event, '{{ $opt->image_url }}')"
                                                                        onmousemove="updateCyberHoverBubblePos(event)"
                                                                        onmouseleave="hideCyberHoverBubble()"
                                                                        onclick="openCyberLightbox('{{ $opt->image_url }}', 'Gambar Opsi {{ $labels[$optIdx] ?? '' }}')"
                                                                        class="review-opt-img-box block group cursor-zoom-in focus:outline-none rounded-lg border hover:border-emerald-500/40 p-1 transition-all">
                                                                    <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain group-hover:scale-105 transition-transform" loading="lazy">
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <div class="font-semibold block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] bg-emerald-600 text-white font-bold px-2 py-0.5 rounded-md flex-shrink-0 flex items-center gap-1 shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Kunci Benar
                                                </span>
                                            </div>
                                        @else
                                            {{-- OPSI LAIN (NETRAL) --}}
                                            <div class="review-option-neutral p-3 rounded-xl border border-gray-200 dark:border-[#1A2820] flex items-start gap-2.5">
                                                <span class="review-opt-badge w-6 h-6 rounded-lg font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    {{ $labels[$optIdx] ?? ($optIdx + 1) }}
                                                </span>
                                                <div class="min-w-0">
                                                    @if($opt->image_path)
                                                        <div class="review-opt-img-box mb-1.5 max-h-24 overflow-hidden rounded-lg border p-1">
                                                            <button type="button"
                                                                    onmouseenter="showCyberHoverBubble(event, '{{ $opt->image_url }}')"
                                                                    onmousemove="updateCyberHoverBubblePos(event)"
                                                                    onmouseleave="hideCyberHoverBubble()"
                                                                    onclick="openCyberLightbox('{{ $opt->image_url }}', 'Gambar Opsi {{ $labels[$optIdx] ?? '' }}')"
                                                                    class="block group cursor-zoom-in focus:outline-none">
                                                                <img src="{{ $opt->image_url }}" alt="Gambar Opsi" class="max-h-20 w-auto object-contain group-hover:scale-105 transition-transform" loading="lazy">
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <div class="font-normal block leading-snug">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                                                </div>
                                            </div>
                                        @endif
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

                            @if($ans->is_correct)
                                {{-- MAHASISWA MENJAWAB BENAR --}}
                                @if($canRevealCorrect)
                                    {{-- ADMIN MENGIJINKAN LIHAT KUNCI BENAR: Tampilkan Kunci & Pembahasan --}}
                                    <div class="review-explanation-box mt-4 p-4 rounded-xl border text-xs">
                                        <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                            <div class="flex items-center gap-2 font-bold exp-title text-xs sm:text-sm">
                                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-700 dark:text-emerald-300 text-xs">✓</span>
                                                <span>Jawaban Anda Benar:</span>
                                                <span class="font-semibold">{{ $correctAnswerDisplay }}</span>
                                            </div>

                                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-500/30">
                                                Tepat (+ {{ $ans->points_earned ?: 0 }} Poin)
                                            </span>
                                        </div>

                                        @if(!empty($ans->question->explanation))
                                            <div class="pt-2 border-t border-emerald-500/20">
                                                <div class="flex items-start gap-2">
                                                    <span class="font-bold shrink-0 flex items-center gap-1 exp-title">
                                                        <span>💡</span> Pembahasan:
                                                    </span>
                                                    <div class="leading-relaxed exp-text flex-1">
                                                        {!! App\Helpers\RichTextHelper::format($ans->question->explanation) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    {{-- ADMIN TIDAK MENGIJINKAN LIHAT KUNCI: HANYA TAMPILKAN STATUS BENAR TANPA PEMBAHASAN / KUNCI --}}
                                    <div class="mt-4 p-4 rounded-xl border border-emerald-500/30 bg-emerald-50/70 dark:bg-[#07130C] text-xs">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-2 font-medium text-gray-800 dark:text-gray-200 text-xs sm:text-sm">
                                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-xs font-bold">✓</span>
                                                <span>Evaluasi:</span>
                                                <span class="text-emerald-700 dark:text-emerald-400 font-bold">Jawaban Anda Benar</span>
                                            </div>

                                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-500/30">
                                                + {{ $ans->points_earned ?: 0 }} Poin
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @elseif($canRevealCorrect)
                                {{-- MAHASISWA SALAH, TAPI ADMIN MENGIJINKAN LIHAT KUNCI BENAR --}}
                                <div class="review-explanation-box mt-4 p-4 rounded-xl border text-xs">
                                    <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                        <div class="flex items-center gap-2 font-bold exp-title text-xs sm:text-sm">
                                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-700 dark:text-emerald-300 text-xs">🔑</span>
                                            <span>Kunci Jawaban Benar:</span>
                                            <span class="font-semibold">{{ $correctAnswerDisplay }}</span>
                                        </div>

                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-500/30">
                                            Kunci Tepat
                                        </span>
                                    </div>

                                    <div class="pt-2 border-t border-emerald-500/20">
                                        <div class="flex items-start gap-2">
                                            <span class="font-bold shrink-0 flex items-center gap-1 exp-title">
                                                <span>💡</span> Pembahasan:
                                            </span>
                                            <div class="leading-relaxed exp-text flex-1">
                                                @if(!empty($ans->question->explanation))
                                                    {!! App\Helpers\RichTextHelper::format($ans->question->explanation) !!}
                                                @else
                                                    <span class="italic text-gray-500 dark:text-gray-400">Tidak ada catatan pembahasan tambahan untuk soal ini.</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- MAHASISWA SALAH & ADMIN TIDAK MENGIJINKAN LIHAT KUNCI BENAR --}}
                                <div class="mt-4 p-4 rounded-xl border border-gray-200 dark:border-[#1A2820] bg-gray-50/80 dark:bg-[#070B09] text-xs">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2 font-medium text-gray-800 dark:text-gray-200 text-xs sm:text-sm">
                                            <span class="w-5 h-5 rounded-full bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-500 text-xs font-bold">✕</span>
                                            <span>Evaluasi:</span>
                                            <span class="text-rose-600 dark:text-rose-400 font-semibold">Jawaban Anda belum tepat</span>
                                        </div>

                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            0 Poin
                                        </span>
                                    </div>

                                    <div class="pt-2 mt-2 border-t border-gray-200 dark:border-[#16221B] text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Kunci jawaban yang benar disembunyikan oleh instruktur sesuai kebijakan kuis ini.</span>
                                    </div>
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
               class="btn-back-portal px-6 py-3 rounded-2xl bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] text-sm font-semibold transition-all shadow-sm">
                ← Kembali ke Portal Lab
            </a>
            @if($quiz->is_active)
                <a href="{{ route('quiz.lobby', $quiz->slug ?: $quiz->unique_token) }}"
                   class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-bold transition-all shadow-lg shadow-emerald-500/20">
                    Selesai
                </a>
            @endif
        </div>

    </main>

    <footer class="py-6 border-t border-gray-200 dark:border-[#1A2820] text-center text-xs text-gray-500 dark:text-gray-600 mt-auto">
        &copy; 2026-AAN MS
    </footer>

    {{-- ─── HOVER PREVIEW POPUP (INSTANT BUBBLE SAAT KURSOR MENDEKAT) ─── --}}
    <div id="cyber-hover-preview-bubble" class="cyber-hover-preview-bubble">
        <img id="cyber-hover-preview-img" src="" alt="Pratinjau Kursor">
    </div>

    {{-- ─── UNIFIED IMAGE LIGHTBOX PREVIEW POPUP DENGAN ZOOM IN / ZOOM OUT ─── --}}
    <div id="cyber-image-lightbox"
         class="fixed inset-0 z-[120] hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300"
         style="z-index: 120;"
         onclick="closeCyberLightbox(event)">
        <div class="relative max-w-5xl max-h-[92vh] flex flex-col items-center justify-center select-none" onclick="event.stopPropagation()">
            
            {{-- Top Controls Bar: Zoom In, Zoom Out, Reset, & Close --}}
            <div class="absolute -top-14 right-0 sm:right-0 flex items-center gap-2">
                <div class="flex items-center bg-[#0F1713]/90 hover:bg-[#142019] border border-[#1E3327] rounded-xl px-1.5 py-1 shadow-lg text-xs font-semibold gap-1 lightbox-card">
                    <button type="button"
                            onclick="adjustCyberLightboxZoom(-0.25)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            title="Zoom Out (−)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <span id="cyber-lightbox-zoom-label" class="px-2 font-mono text-emerald-400 font-bold min-w-[50px] text-center">100%</span>
                    <button type="button"
                            onclick="adjustCyberLightboxZoom(0.25)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            title="Zoom In (+)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button type="button"
                            onclick="resetCyberLightboxZoom()"
                            class="px-2 py-1 rounded-md text-[11px] text-gray-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer font-mono"
                            title="Reset Skala 100%">
                        1:1
                    </button>
                </div>

                <button type="button"
                        onclick="closeCyberLightbox()"
                        class="p-2 text-gray-400 hover:text-white bg-[#0F1713]/80 hover:bg-[#1A2820] border border-[#1E3327] rounded-xl transition-all shadow-lg flex items-center gap-1.5 text-xs font-semibold lightbox-close-btn"
                        title="Tutup (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="hidden sm:inline">Tutup</span>
                </button>
            </div>

            {{-- Image Box with Zoom and Pan --}}
            <div id="cyber-lightbox-stage"
                 class="w-[92vw] max-w-5xl h-[78vh] sm:h-[82vh] rounded-2xl overflow-hidden border border-emerald-500/30 bg-[#0A0F0D] lightbox-card shadow-[0_0_50px_rgba(0,0,0,0.9),0_0_25px_rgba(16,185,129,0.2)] p-2 relative flex items-center justify-center select-none"
                 onwheel="handleCyberLightboxWheel(event)">
                <div id="cyber-lightbox-canvas" class="w-full h-full flex items-center justify-center overflow-hidden relative">
                    <img id="cyber-lightbox-img" src="" alt="Pratinjau Gambar"
                         class="max-w-full max-h-full object-contain select-none transition-transform duration-75 ease-out rounded-lg cursor-zoom-in"
                         style="transform: translate(0px, 0px) scale(1);"
                         draggable="false">
                </div>
            </div>

            {{-- Caption / Subtitle --}}
            <p id="cyber-lightbox-caption" class="text-xs text-gray-300 font-medium mt-3 text-center px-4 py-1.5 rounded-full bg-[#0F1713]/90 border border-[#1E3327] max-w-xl truncate lightbox-caption"></p>
        </div>
    </div>

    {{-- Trigger Confetti Celebration & Lightbox JS --}}
    <script>
        let currentCyberLightboxScale = 1.0;
        let currentCyberLightboxTranslateX = 0;
        let currentCyberLightboxTranslateY = 0;
        let isCyberLightboxDragging = false;
        let cyberLightboxDragStartX = 0;
        let cyberLightboxDragStartY = 0;

        function updateCyberLightboxTransform() {
            const img = document.getElementById('cyber-lightbox-img');
            const zoomLbl = document.getElementById('cyber-lightbox-zoom-label');
            if (!img) return;

            if (currentCyberLightboxScale <= 1.0) {
                currentCyberLightboxTranslateX = 0;
                currentCyberLightboxTranslateY = 0;
                img.style.cursor = 'zoom-in';
            } else {
                img.style.cursor = isCyberLightboxDragging ? 'grabbing' : 'grab';
            }

            img.style.transform = `translate(${currentCyberLightboxTranslateX}px, ${currentCyberLightboxTranslateY}px) scale(${currentCyberLightboxScale})`;
            if (zoomLbl) zoomLbl.textContent = `${Math.round(currentCyberLightboxScale * 100)}%`;
        }

        window.openCyberLightbox = function(src, caption = '') {
            const lb = document.getElementById('cyber-image-lightbox');
            const img = document.getElementById('cyber-lightbox-img');
            const cap = document.getElementById('cyber-lightbox-caption');
            if (!lb || !img) return;

            window.hideCyberHoverBubble();
            currentCyberLightboxScale = 1.0;
            currentCyberLightboxTranslateX = 0;
            currentCyberLightboxTranslateY = 0;
            isCyberLightboxDragging = false;

            img.onerror = function() {
                if (cap) {
                    cap.textContent = (caption ? caption + ' — ' : '') + 'Gambar belum tersedia di server (pastikan `php artisan storage:link` aktif)';
                    cap.style.color = '#F59E0B';
                }
            };
            img.onload = function() {
                if (cap) {
                    cap.style.color = '';
                }
            };
            img.src = src;
            updateCyberLightboxTransform();

            if (cap) {
                cap.textContent = caption || 'Pratinjau Gambar';
                cap.style.display = caption ? 'inline-block' : 'none';
            }
            lb.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        window.closeCyberLightbox = function() {
            const lb = document.getElementById('cyber-image-lightbox');
            const img = document.getElementById('cyber-lightbox-img');
            if (!lb) return;
            lb.classList.add('hidden');
            if (img) {
                img.src = '';
                img.style.transform = 'translate(0px, 0px) scale(1)';
            }
            currentCyberLightboxScale = 1.0;
            currentCyberLightboxTranslateX = 0;
            currentCyberLightboxTranslateY = 0;
            isCyberLightboxDragging = false;
            document.body.style.overflow = '';
        };

        window.adjustCyberLightboxZoom = function(delta) {
            let nextScale = Math.round((currentCyberLightboxScale + delta) * 10) / 10;
            if (nextScale < 0.5) nextScale = 0.5;
            if (nextScale > 5.0) nextScale = 5.0;
            currentCyberLightboxScale = nextScale;
            if (currentCyberLightboxScale <= 1.0) {
                currentCyberLightboxTranslateX = 0;
                currentCyberLightboxTranslateY = 0;
            }
            updateCyberLightboxTransform();
        };

        window.resetCyberLightboxZoom = function() {
            currentCyberLightboxScale = 1.0;
            currentCyberLightboxTranslateX = 0;
            currentCyberLightboxTranslateY = 0;
            isCyberLightboxDragging = false;
            updateCyberLightboxTransform();
        };

        window.handleCyberLightboxWheel = function(e) {
            e.preventDefault();
            window.adjustCyberLightboxZoom(e.deltaY < 0 ? 0.2 : -0.2);
        };

        // Pan & Drag event bindings for Results Lightbox
        document.addEventListener('DOMContentLoaded', function() {
            const stage = document.getElementById('cyber-lightbox-stage');
            const img = document.getElementById('cyber-lightbox-img');
            if (!stage || !img) return;

            stage.addEventListener('mousedown', function(e) {
                if (currentCyberLightboxScale <= 1.0) return;
                isCyberLightboxDragging = true;
                cyberLightboxDragStartX = e.clientX - currentCyberLightboxTranslateX;
                cyberLightboxDragStartY = e.clientY - currentCyberLightboxTranslateY;
                updateCyberLightboxTransform();
                e.preventDefault();
            });

            window.addEventListener('mousemove', function(e) {
                if (!isCyberLightboxDragging) return;
                currentCyberLightboxTranslateX = e.clientX - cyberLightboxDragStartX;
                currentCyberLightboxTranslateY = e.clientY - cyberLightboxDragStartY;
                updateCyberLightboxTransform();
            });

            window.addEventListener('mouseup', function() {
                if (isCyberLightboxDragging) {
                    isCyberLightboxDragging = false;
                    updateCyberLightboxTransform();
                }
            });

            stage.addEventListener('touchstart', function(e) {
                if (e.touches.length === 1 && currentCyberLightboxScale > 1.0) {
                    isCyberLightboxDragging = true;
                    cyberLightboxDragStartX = e.touches[0].clientX - currentCyberLightboxTranslateX;
                    cyberLightboxDragStartY = e.touches[0].clientY - currentCyberLightboxTranslateY;
                }
            }, { passive: true });

            stage.addEventListener('touchmove', function(e) {
                if (isCyberLightboxDragging && e.touches.length === 1) {
                    currentCyberLightboxTranslateX = e.touches[0].clientX - cyberLightboxDragStartX;
                    currentCyberLightboxTranslateY = e.touches[0].clientY - cyberLightboxDragStartY;
                    updateCyberLightboxTransform();
                }
            }, { passive: true });

            stage.addEventListener('touchend', function() {
                isCyberLightboxDragging = false;
                updateCyberLightboxTransform();
            });

            img.addEventListener('dblclick', function(e) {
                e.stopPropagation();
                if (currentCyberLightboxScale > 1.0) {
                    window.resetCyberLightboxZoom();
                } else {
                    window.adjustCyberLightboxZoom(1.0); // jump directly to 200%
                }
            });
        });

        // ── Instant Hover Preview Bubble ──
        window.showCyberHoverBubble = function(e, src) {
            const lb = document.getElementById('cyber-image-lightbox');
            if (lb && !lb.classList.contains('hidden')) return;

            const bubble = document.getElementById('cyber-hover-preview-bubble');
            const bImg = document.getElementById('cyber-hover-preview-img');
            if (!bubble || !bImg) return;

            bImg.src = src;
            bubble.classList.add('is-active');
            window.updateCyberHoverBubblePos(e);
        };

        window.updateCyberHoverBubblePos = function(e) {
            const bubble = document.getElementById('cyber-hover-preview-bubble');
            if (!bubble || !bubble.classList.contains('is-active')) return;

            const pad = 16;
            const previewW = Math.min(420, window.innerWidth * 0.85);
            const previewH = Math.min(320, window.innerHeight * 0.55);

            let posX = e.clientX + pad;
            let posY = e.clientY - (previewH / 2);

            if (posX + previewW > window.innerWidth - pad) {
                posX = e.clientX - previewW - pad;
            }
            if (posY < pad) posY = pad;
            if (posY + previewH > window.innerHeight - pad) {
                posY = window.innerHeight - previewH - pad;
            }

            bubble.style.left = `${posX}px`;
            bubble.style.top = `${posY}px`;
        };

        window.hideCyberHoverBubble = function() {
            const bubble = document.getElementById('cyber-hover-preview-bubble');
            if (bubble) bubble.classList.remove('is-active');
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeCyberLightbox();
            }
        });

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
