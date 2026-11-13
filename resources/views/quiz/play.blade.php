<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arena Kuis: {{ $quiz->title }} &mdash; IC-Labs</title>
    <x-theme-head-script />
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            user-select: none;
            -webkit-user-select: none;
        }
        html:not(.dark) body {
            color: #0F1F17 !important;
        }
        html:not(.dark) .option-card .text-gray-200,
        html:not(.dark) .option-card .text-white {
            color: #0F1F17 !important;
        }
        html:not(.dark) .option-card.border-emerald-400 {
            background-color: #ECFDF5 !important;
        }
        html:not(.dark) .option-card.border-emerald-400 .text-white {
            color: #065F46 !important;
        }

        /* Light mode support for question image box and lightbox modal */
        html:not(.dark) .question-img-container {
            background-color: #F8FAF9 !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .question-img-box {
            background-color: #FFFFFF !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 0 10px rgba(16, 185, 129, 0.1) !important;
        }
        html:not(.dark) .lightbox-card {
            background-color: #FFFFFF !important;
            border-color: #D1DCD6 !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25), 0 0 30px rgba(16, 185, 129, 0.15) !important;
        }
        html:not(.dark) .lightbox-close-btn {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: #D1DCD6 !important;
            color: #374151 !important;
        }
        html:not(.dark) .lightbox-close-btn:hover {
            background-color: #F3F4F6 !important;
            color: #0F1F17 !important;
        }
        html:not(.dark) .lightbox-caption {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: #D1DCD6 !important;
            color: #374151 !important;
        }

        /* Hover Zoom Tooltip Preview */
        .cyber-hover-zoom-wrap {
            position: relative;
        }
        .cyber-hover-preview-bubble {
            position: fixed;
            pointer-events: none;
            z-index: 99;
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
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .swal2-container {
            backdrop-filter: blur(14px) !important;
            -webkit-backdrop-filter: blur(14px) !important;
        }
        .option-card {
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .option-card:hover {
            transform: translateY(-2px);
        }
        .option-card:active {
            transform: translateY(0);
        }
        html:not(.dark) .option-card {
            background-color: rgba(255, 255, 255, 0.92) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
        }
        html:not(.dark) .option-card:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.12) !important;
        }

        /* Arena Question Card Glassmorphic Styling */
        .arena-question-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.06), transparent 70%), rgba(15, 23, 19, 0.88) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(26, 40, 32, 0.9) !important;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.75), 0 0 30px rgba(16, 185, 129, 0.08) !important;
        }
        html:not(.dark) .arena-question-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.05), transparent 70%), rgba(255, 255, 255, 0.94) !important;
            border: 1px solid rgba(226, 234, 229, 0.95) !important;
            box-shadow: 0 15px 40px -10px rgba(0, 0, 0, 0.06), 0 0 25px rgba(16, 185, 129, 0.05) !important;
        }

        /* ─── ULTRA "WAH" AMBIENT CYBER BACKGROUND (SERENTAK DENGAN HALAMAN SOAL & RUANG TUNGGU) ─── */
        .soal-ambient-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            will-change: transform;
        }

        /* Fluid Morphing Aurora Waves */
        .soal-aurora-blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(85px);
            -webkit-filter: blur(85px);
            will-change: transform, border-radius;
            opacity: 0.85;
        }

        .soal-aurora-blob.blob-1 {
            width: 680px;
            height: 680px;
            top: -120px;
            left: -140px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(6, 182, 212, 0.12) 50%, transparent 75%);
            animation: auroraMorph1 24s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-aurora-blob.blob-1 {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.20) 0%, rgba(6, 182, 212, 0.10) 50%, transparent 75%);
        }

        .soal-aurora-blob.blob-2 {
            width: 640px;
            height: 640px;
            top: -80px;
            right: -120px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.20) 0%, rgba(13, 148, 136, 0.12) 50%, transparent 75%);
            animation: auroraMorph2 28s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-aurora-blob.blob-2 {
            background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, rgba(2, 132, 199, 0.08) 50%, transparent 75%);
        }

        .soal-aurora-blob.blob-3 {
            width: 580px;
            height: 580px;
            bottom: -100px;
            left: 20%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, rgba(5, 150, 105, 0.10) 50%, transparent 75%);
            animation: auroraMorph3 26s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-aurora-blob.blob-3 {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.08) 50%, transparent 75%);
        }

        .soal-aurora-blob.blob-4 {
            width: 520px;
            height: 520px;
            bottom: -80px;
            right: 5%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.16) 0%, rgba(16, 185, 129, 0.10) 50%, transparent 75%);
            animation: auroraMorph4 32s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-aurora-blob.blob-4 {
            background: radial-gradient(circle, rgba(6, 182, 212, 0.14) 0%, rgba(16, 185, 129, 0.08) 50%, transparent 75%);
        }

        @keyframes auroraMorph1 {
            0% {
                border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
                transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
            }
            50% {
                border-radius: 58% 42% 40% 60% / 60% 50% 50% 40%;
                transform: translate3d(80px, 60px, 0) rotate(160deg) scale(1.12);
            }
            100% {
                border-radius: 48% 52% 58% 42% / 40% 60% 40% 60%;
                transform: translate3d(-30px, 90px, 0) rotate(320deg) scale(0.96);
            }
        }

        @keyframes auroraMorph2 {
            0% {
                border-radius: 50% 50% 40% 60% / 55% 45% 55% 45%;
                transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
            }
            50% {
                border-radius: 40% 60% 55% 45% / 45% 55% 45% 55%;
                transform: translate3d(-90px, 70px, 0) rotate(-140deg) scale(1.08);
            }
            100% {
                border-radius: 60% 40% 45% 55% / 55% 40% 60% 45%;
                transform: translate3d(50px, 100px, 0) rotate(-280deg) scale(0.94);
            }
        }

        @keyframes auroraMorph3 {
            0% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
                transform: translate3d(0, 0, 0) scale(0.95);
            }
            50% {
                border-radius: 45% 55% 65% 35% / 40% 65% 35% 60%;
                transform: translate3d(70px, -50px, 0) scale(1.14);
            }
            100% {
                border-radius: 55% 45% 40% 60% / 50% 45% 55% 50%;
                transform: translate3d(-30px, 40px, 0) scale(1);
            }
        }

        @keyframes auroraMorph4 {
            0% {
                border-radius: 45% 55% 60% 40% / 50% 50% 50% 50%;
                transform: translate3d(0, 0, 0) scale(1);
            }
            50% {
                border-radius: 65% 35% 45% 55% / 40% 60% 40% 60%;
                transform: translate3d(-80px, -60px, 0) scale(1.1);
            }
            100% {
                border-radius: 35% 65% 55% 45% / 60% 40% 60% 40%;
                transform: translate3d(40px, -40px, 0) scale(0.95);
            }
        }

        /* Diagonal Volumetric Light Shafts */
        .soal-light-shaft {
            position: absolute;
            top: -30%;
            width: 480px;
            height: 180%;
            pointer-events: none;
            opacity: 0.18;
            filter: blur(60px);
            -webkit-filter: blur(60px);
            will-change: opacity, transform;
        }
        .soal-light-shaft.shaft-left {
            left: -10%;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.35) 0%, transparent 60%);
            transform: rotate(-18deg);
            animation: lightShaftPulse 16s ease-in-out infinite alternate;
        }
        .soal-light-shaft.shaft-right {
            right: -10%;
            background: linear-gradient(225deg, rgba(6, 182, 212, 0.30) 0%, transparent 60%);
            transform: rotate(20deg);
            animation: lightShaftPulse 20s ease-in-out infinite alternate 4s;
        }
        html:not(.dark) .soal-light-shaft {
            opacity: 0.12;
        }

        @keyframes lightShaftPulse {
            0% { opacity: 0.12; transform: rotate(-18deg) scaleY(1); }
            50% { opacity: 0.24; transform: rotate(-15deg) scaleY(1.08); }
            100% { opacity: 0.14; transform: rotate(-20deg) scaleY(0.95); }
        }

        /* Subtle Matrix Grid with sweeping light */
        .soal-ambient-grid {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                radial-gradient(circle, rgba(16, 185, 129, 0.14) 1.2px, transparent 1.2px),
                linear-gradient(to right, rgba(16, 185, 129, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.035) 1px, transparent 1px);
            background-size: 36px 36px, 36px 36px, 36px 36px;
            mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 40%, transparent 95%);
            -webkit-mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 40%, transparent 95%);
        }
        html:not(.dark) .soal-ambient-grid {
            background-image:
                radial-gradient(circle, rgba(16, 185, 129, 0.16) 1.2px, transparent 1.2px),
                linear-gradient(to right, rgba(16, 185, 129, 0.045) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.045) 1px, transparent 1px);
            mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 45%, transparent 95%);
            -webkit-mask-image: radial-gradient(ellipse 90% 85% at 50% 35%, black 45%, transparent 95%);
        }

        .soal-grid-scanner {
            position: absolute;
            left: 0;
            right: 0;
            height: 140px;
            background: linear-gradient(180deg, transparent, rgba(16, 185, 129, 0.07), transparent);
            animation: gridScanner 14s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            pointer-events: none;
        }
        html:not(.dark) .soal-grid-scanner {
            background: linear-gradient(180deg, transparent, rgba(16, 185, 129, 0.09), transparent);
        }
        @keyframes gridScanner {
            0% { top: -150px; opacity: 0; }
            15% { opacity: 1; }
            85% { opacity: 1; }
            100% { top: 100vh; opacity: 0; }
        }

        /* Interactive Quantum Constellation Canvas */
        .soal-constellation-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
        }

        /* Subtle Interactive Cursor Glow */
        .soal-cursor-glow {
            position: absolute;
            top: var(--cursor-y, 50vh);
            left: var(--cursor-x, 50vw);
            width: 560px;
            height: 560px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            pointer-events: none;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.09) 0%, rgba(6, 182, 212, 0.04) 40%, transparent 70%);
            filter: blur(65px);
            will-change: top, left;
            opacity: 0.9;
        }
        html:not(.dark) .soal-cursor-glow {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.07) 0%, rgba(6, 182, 212, 0.03) 40%, transparent 70%);
        }

        /* Floating Micro-Particles */
        .soal-particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            background: #10B981;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.7);
            opacity: 0;
            animation: particleFloat linear infinite;
        }
        html:not(.dark) .soal-particle {
            background: #059669;
            box-shadow: 0 0 8px rgba(5, 150, 105, 0.45);
        }
        .soal-particle.p-cyan {
            background: #06B6D4;
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.7);
        }
        html:not(.dark) .soal-particle.p-cyan {
            background: #0284C7;
            box-shadow: 0 0 8px rgba(2, 132, 199, 0.45);
        }
        .soal-particle.p1 { width: 4px; height: 4px; left: 8%; animation-duration: 22s; animation-delay: 0s; }
        .soal-particle.p2 { width: 5px; height: 5px; left: 22%; animation-duration: 26s; animation-delay: 5s; }
        .soal-particle.p3 { width: 3px; height: 3px; left: 35%; animation-duration: 19s; animation-delay: 2s; }
        .soal-particle.p4 { width: 5px; height: 5px; left: 68%; animation-duration: 24s; animation-delay: 7s; }
        .soal-particle.p5 { width: 4px; height: 4px; left: 82%; animation-duration: 21s; animation-delay: 3s; }
        .soal-particle.p6 { width: 3px; height: 3px; left: 93%; animation-duration: 28s; animation-delay: 9s; }

        @keyframes particleFloat {
            0% { transform: translateY(105vh) translateX(0) scale(0.6); opacity: 0; }
            15% { opacity: 0.65; }
            85% { opacity: 0.65; }
            100% { transform: translateY(-5vh) translateX(25px) scale(1.1); opacity: 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            .soal-aurora-blob,
            .soal-light-shaft,
            .soal-grid-scanner,
            .soal-particle,
            .soal-constellation-canvas {
                animation: none !important;
            }
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="quizArena()" x-init="initArena()" x-cloak class="flex flex-col min-h-screen bg-[#F4F7F5] dark:bg-[#090D0B] relative text-gray-900 dark:text-gray-100 selection:bg-emerald-500 selection:text-white">

    {{-- ─── AMBIENT CYBER BACKGROUND (Identik Serentak dengan Halaman Soal & Ruang Tunggu) ─── --}}
    <div class="soal-ambient-canvas" aria-hidden="true">
        {{-- Fluid Cosmic Morphing Aurora Blobs --}}
        <div class="soal-aurora-blob blob-1"></div>
        <div class="soal-aurora-blob blob-2"></div>
        <div class="soal-aurora-blob blob-3"></div>
        <div class="soal-aurora-blob blob-4"></div>

        {{-- Diagonal Volumetric Light Beams --}}
        <div class="soal-light-shaft shaft-left"></div>
        <div class="soal-light-shaft shaft-right"></div>

        {{-- Cyber Grid Mesh & Sweeping Radar Scanner --}}
        <div class="soal-ambient-grid"></div>
        <div class="soal-grid-scanner"></div>

        {{-- Interactive Quantum Constellation Canvas (Plexus / Synaptic Nodes) --}}
        <canvas id="ambientConstellationCanvas" class="soal-constellation-canvas"></canvas>

        {{-- Subtle Floating Horizon Glow & Particles --}}
        <div class="soal-particle p1"></div>
        <div class="soal-particle p2 p-cyan"></div>
        <div class="soal-particle p3"></div>
        <div class="soal-particle p4 p-cyan"></div>
        <div class="soal-particle p5"></div>
        <div class="soal-particle p6"></div>

        {{-- Interactive Cursor Aura Halo --}}
        <div class="soal-cursor-glow"></div>
    </div>

    {{-- Top App Bar --}}
    <header class="border-b border-gray-200 dark:border-[#1A2820] bg-white/80 dark:bg-[#090D0B]/80 backdrop-blur-md sticky top-0 z-40 px-4 py-3">
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
            {{-- Quiz Title & Progress --}}
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20 flex-shrink-0">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white heading-font truncate max-w-[180px] sm:max-w-xs">{{ $quiz->title }}</h2>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold" x-text="'Soal ' + (currentIndex + 1) + ' / ' + questions.length"></span>
                        <span>&bull;</span>
                        <span class="truncate">{{ $attempt->student_name }}</span>
                    </div>
                </div>
            </div>

            {{-- Timer & Anti-Cheat Controls --}}
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Countdown Timer (Jam:Menit:Detik) --}}
                <template x-if="remainingSeconds !== null">
                    <div class="px-3.5 py-1.5 rounded-xl border flex items-center gap-2 font-mono text-xs sm:text-sm font-bold transition-all shadow-sm"
                         :class="remainingSeconds <= 30 ? 'bg-red-500/20 border-red-500/40 text-red-400 animate-pulse' : (remainingSeconds <= 90 ? 'bg-amber-500/20 border-amber-500/40 text-amber-300' : 'bg-[#142019] border-[#1E3327] text-emerald-400')"
                         title="Sisa Waktu Pengerjaan">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="formatTime(remainingSeconds)"></span>
                    </div>
                </template>

                {{-- Anti-Cheat / Open Book Mode Status Badge --}}
                @if($quiz->allow_open_book)
                    <div class="px-2.5 py-1.5 rounded-xl bg-sky-500/10 border border-sky-500/25 flex items-center gap-1.5 text-xs font-semibold text-sky-400"
                         title="Kuis ini mengizinkan mahasiswa membuka modul atau referensi materi eksternal">
                        <span>📖</span>
                        <span class="hidden sm:inline">Bisa Lihat Modul</span>
                    </div>
                @else
                    <div class="px-2.5 py-1.5 rounded-xl border flex items-center gap-1.5 text-xs font-medium"
                         :class="isLocked ? 'bg-rose-500/20 border-rose-500/40 text-rose-300 animate-pulse' : (tabSwitchCount > 0 ? 'bg-amber-500/15 border-amber-500/30 text-amber-300' : 'bg-[#142019] border-[#1E3327] text-emerald-400')"
                         :title="'Status Pengawas CBT: ' + tabSwitchCount + ' kali terdeteksi switch tab/aplikasi'">
                        <span x-text="isLocked ? '🔒' : (tabSwitchCount > 0 ? '⚠️' : '🛡️')"></span>
                        <span class="hidden sm:inline text-gray-400">Pengawas:</span>
                        <span class="font-bold" x-text="isLocked ? 'TERKUNCI' : tabSwitchCount + '/3 Pelanggaran'"></span>
                    </div>
                @endif

                {{-- Autosave Sync Status Indicator --}}
                <div class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] flex items-center gap-1.5 text-xs font-medium"
                     :title="syncStatus === 'saving' ? 'Menyimpan jawaban ke server...' : (syncStatus === 'synced' ? 'Semua jawaban tersimpan aman di server & memori lokal' : 'Jaringan terputus: jawaban tersimpan aman di perangkat')">
                    <template x-if="syncStatus === 'saving'">
                        <span class="text-sky-400 flex items-center gap-1">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span class="hidden lg:inline text-[11px]">Menyimpan...</span>
                        </span>
                    </template>
                    <template x-if="syncStatus === 'synced'">
                        <span class="text-emerald-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="hidden lg:inline text-[11px]">Tersimpan</span>
                        </span>
                    </template>
                    <template x-if="syncStatus === 'offline'">
                        <span class="text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="hidden lg:inline text-[11px]">Lokal (Offline)</span>
                        </span>
                    </template>
                </div>

                {{-- Live Announcements Drawer Button --}}
                <button type="button" @click="isAnnouncementDrawerOpen = true"
                        class="px-2.5 py-1.5 rounded-xl border flex items-center gap-1.5 text-xs font-medium transition-all relative cursor-pointer"
                        :class="announcements.length > 0 ? 'bg-amber-500/15 border-amber-500/40 text-amber-300 hover:bg-amber-500/25' : 'bg-[#142019] border-[#1E3327] text-gray-400 hover:text-white'"
                        title="Lihat riwayat pengumuman & ralat soal dari pengawas lab">
                    <span>📢</span>
                    <span class="hidden sm:inline">Pengumuman</span>
                    <template x-if="announcements.length > 0">
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-400 text-gray-950 font-extrabold" x-text="announcements.length"></span>
                    </template>
                </button>

                @if($quiz->allow_open_book)
                    {{-- Fullscreen Toggle allowed in Open Book mode --}}
                    <button type="button" @click="toggleFullscreen()"
                            :title="isFullscreen ? 'Keluar dari Mode Layar Penuh' : 'Mode Layar Penuh (Menghilangkan gangguan browser agar fokus ujian)'"
                            class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] hover:border-emerald-500/40 text-gray-400 hover:text-white transition-all flex items-center gap-1.5 text-xs font-medium">
                        <template x-if="!isFullscreen">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        </template>
                        <template x-if="isFullscreen">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </template>
                        <span class="hidden md:inline text-[11px]" x-text="isFullscreen ? 'Keluar Fullscreen' : 'Layar Penuh'"></span>
                    </button>
                @else
                    {{-- Kiosk Enforced Indicator (No manual exit button in strict mode) --}}
                    <div class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] flex items-center gap-1.5 text-xs font-medium text-emerald-400"
                         title="Ujian berjalan dalam Mode Kiosk Layar Penuh Terkunci">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="hidden md:inline text-[11px] font-mono text-emerald-300">Kiosk Terkunci</span>
                    </div>
                @endif
                <x-theme-switcher :compact="true" />
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="max-w-5xl mx-auto mt-2.5 h-1.5 bg-[#142019] rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300 rounded-full"
                 :style="'width: ' + ((currentIndex + 1) / questions.length * 100) + '%'"></div>
        </div>
    </header>

    {{-- Main Question Arena (Zero-Visibility Curtain if not in Fullscreen during strict exam) --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-6 sm:py-10 flex flex-col justify-between transition-all duration-300 relative z-10"
          :class="(!allowOpenBook && !isFullscreen && !isLocked && penaltySeconds === 0) ? 'filter blur-2xl pointer-events-none select-none opacity-5 scale-95' : ''">

        <template x-if="currentQuestion">
            <div class="space-y-6">

                {{-- Question Card --}}
                <div class="bg-white/90 dark:bg-[#0F1713]/90 border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 sm:p-8 shadow-xl relative arena-question-card">
                    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-mono"
                                  x-text="'Soal #' + currentQuestion.number"></span>
                            
                            <template x-if="currentQuestion.question_type === 'multi_select'">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-500/15 text-purple-700 dark:text-purple-300 border border-purple-500/30 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pilihan Jamak (Centang Semua Jawaban Benar)
                                </span>
                            </template>
                            <template x-if="currentQuestion.question_type === 'true_false'">
                                <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 dark:bg-[#142019] text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-[#1E3327]">
                                    Benar / Salah
                                </span>
                            </template>
                            <template x-if="currentQuestion.question_type === 'multiple_choice'">
                                <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 dark:bg-[#142019] text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-[#1E3327]">
                                    Pilihan Tunggal
                                </span>
                            </template>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Tombol Ragu-ragu (Mark for Review) --}}
                            <button type="button"
                                    @click="toggleFlagQuestion(currentQuestion.id)"
                                    class="px-3 py-1 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 border shadow-sm cursor-pointer"
                                    :class="isFlagged(currentQuestion.id) 
                                        ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-500/60 ring-2 ring-amber-500/25 shadow-amber-500/20 animate-pulse' 
                                        : 'bg-gray-100 dark:bg-[#142019] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-[#1E3327] hover:text-amber-600 dark:hover:text-amber-300 hover:border-amber-500/40 hover:bg-amber-500/10'"
                                    :title="isFlagged(currentQuestion.id) ? 'Klik untuk membatalkan tanda ragu-ragu' : 'Tandai soal ini sebagai ragu-ragu untuk ditinjau ulang'">
                                <span x-text="isFlagged(currentQuestion.id) ? '🚩' : '🏳️'"></span>
                                <span x-text="isFlagged(currentQuestion.id) ? 'Ragu-ragu' : 'Tandai Ragu'"></span>
                            </button>

                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20"
                                  x-text="'+' + currentQuestion.points + ' Poin'"></span>
                        </div>
                    </div>

                    {{-- Question Image (if uploaded) --}}
                    <template x-if="currentQuestion.image_url">
                        <div class="my-4 flex items-center justify-center question-img-container">
                            <button type="button"
                                    @mouseenter="showHoverPreview($event, currentQuestion.image_url)"
                                    @mousemove="updateHoverPreviewPos($event)"
                                    @mouseleave="hideHoverPreview()"
                                    @click="openImagePreview(currentQuestion.image_url, 'Gambar Soal #' + (currentIndex + 1))"
                                    class="inline-block group cursor-zoom-in focus:outline-none rounded-2xl overflow-hidden border border-gray-200 dark:border-[#1A2820] bg-gray-50/50 dark:bg-black/40 hover:border-emerald-500/40 p-2 transition-all shadow-sm">
                                <img :src="currentQuestion.image_url" alt="Gambar Soal"
                                     class="max-h-72 w-auto object-contain rounded-xl group-hover:scale-[1.01] transition-transform">
                            </button>
                        </div>
                    </template>

                    {{-- Question Text with Syntax Highlighting Support --}}
                    <div class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white heading-font leading-relaxed"
                         x-show="currentQuestion.question_type !== 'fill_blank' || getTopQuestionDisplay(currentQuestion)"
                         x-html="renderRichText(getTopQuestionDisplay(currentQuestion))"></div>
                </div>

                {{-- Opsi Jawaban Khusus Pilihan Ganda & Benar/Salah --}}
                <template x-if="currentQuestion.question_type !== 'fill_blank'">
                    <div>
                        {{-- Options Grid (Quizizz Colorful Theme with Image & Rich Text Support) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                            <template x-for="(opt, optIdx) in currentQuestion.options" :key="opt.id">
                                <button type="button"
                                        @click="selectOption(currentQuestion.id, opt.id)"
                                        class="option-card p-4 sm:p-5 rounded-2xl border text-left flex items-start gap-3.5 relative overflow-hidden group cursor-pointer transition-all"
                                        :class="getOptionClasses(optIdx, isSelected(currentQuestion.id, opt.id))">
                                    
                                    {{-- Option Badge (A, B, C, D) / Checkbox indicator --}}
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 transition-transform group-hover:scale-105 mt-0.5"
                                         :class="getOptionBadgeClasses(optIdx, isSelected(currentQuestion.id, opt.id))">
                                        <span x-text="getOptionLabel(optIdx)"></span>
                                    </div>

                                    {{-- Option Content (Text + Image) --}}
                                    <div class="flex-1 min-w-0">
                                        <template x-if="opt.image_url">
                                            <div class="mb-2 inline-block">
                                                <button type="button"
                                                        @mouseenter="showHoverPreview($event, opt.image_url)"
                                                        @mousemove="updateHoverPreviewPos($event)"
                                                        @mouseleave="hideHoverPreview()"
                                                        @click.stop="openImagePreview(opt.image_url, 'Gambar Opsi ' + getOptionLabel(optIdx))"
                                                        class="block group cursor-zoom-in focus:outline-none rounded-xl overflow-hidden border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-black/40 hover:border-emerald-500/40 p-1 transition-all">
                                                    <img :src="opt.image_url" alt="Gambar Opsi" class="max-h-32 w-auto object-contain rounded-lg group-hover:scale-105 transition-transform">
                                                </button>
                                            </div>
                                        </template>
                                        <div class="text-sm sm:text-base font-semibold leading-snug"
                                             :class="isSelected(currentQuestion.id, opt.id) ? 'text-gray-900 dark:text-white' : 'text-gray-800 dark:text-gray-200'"
                                             x-html="renderRichText(opt.text)">
                                        </div>
                                    </div>

                                    {{-- Active Selected Indicator --}}
                                    <div x-show="isSelected(currentQuestion.id, opt.id)"
                                         class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-md mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </button>
                            </template>
                        </div>

                        {{-- Keyboard hint --}}
                        <div class="text-center mt-4">
                            <span class="text-[11px] text-gray-500">
                                💡 Tips: Tekan angka <strong class="text-gray-600 dark:text-gray-400">1 - 4</strong> atau huruf <strong class="text-gray-600 dark:text-gray-400">A - D</strong> di keyboard untuk memilih jawaban cepat.
                            </span>
                        </div>
                    </div>
                </template>

                {{-- Area Isian Rumpang (Fill in the Blank) --}}
                <template x-if="currentQuestion.question_type === 'fill_blank'">
                    <div class="space-y-4">
                        <div class="p-5 sm:p-7 rounded-2xl border border-gray-200 dark:border-[#1A2820] bg-white/95 dark:bg-[#0F1713] shadow-md">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-[#1A2820]">
                                <span class="text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 uppercase tracking-wider">
                                    <span>✏️</span>
                                    <span>Lengkapi Bagian Titik-Titik di Bawah Ini:</span>
                                </span>
                                <span class="text-xs font-mono text-gray-500" x-text="(currentQuestion.blanks_count || 1) + ' Kolom Isian'"></span>
                            </div>

                            {{-- Kalimat dengan Kotak Input Tersemat Langsung (Inline Blank Inputs) --}}
                            <div class="text-base sm:text-xl font-medium leading-loose text-gray-900 dark:text-gray-100">
                                <template x-for="(seg, segIdx) in getBlankSegments(getFillBlankParts(currentQuestion).sentence)" :key="segIdx">
                                    <span class="inline">
                                        <span x-html="renderRichTextInline(seg)"></span>
                                        <template x-if="segIdx < getBlankSegments(getFillBlankParts(currentQuestion).sentence).length - 1">
                                            <span class="inline-block mx-1.5 my-1 align-baseline">
                                                <input type="text"
                                                       :value="getBlankAnswer(currentQuestion.id, segIdx)"
                                                       @input="setBlankAnswer(currentQuestion.id, segIdx, $event.target.value)"
                                                       @keydown.enter.prevent="focusNextBlank($event, segIdx)"
                                                       :placeholder="'Titik-titik #' + (segIdx + 1)"
                                                       autocomplete="off"
                                                       spellcheck="false"
                                                       class="inline-blank-input text-center font-mono font-bold text-sm sm:text-base px-3.5 py-1.5 rounded-xl border-2 border-emerald-500/50 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 bg-gray-50 dark:bg-[#070B09] text-gray-900 dark:text-emerald-300 placeholder-gray-400 dark:placeholder-gray-600 outline-none transition-all shadow-inner"
                                                       style="min-width: 140px; max-width: 250px;" />
                                            </span>
                                        </template>
                                    </span>
                                </template>
                            </div>

                            {{-- Navigasi Cepat per Kolom Isian (Mobile / Multi-blank summary) --}}
                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-[#1A2820] grid grid-cols-1 sm:grid-cols-2 gap-3"
                                 x-show="(currentQuestion.blanks_count || 1) > 1">
                                <template x-for="bNum in (currentQuestion.blanks_count || 1)" :key="'blank-num-' + bNum">
                                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-gray-50 dark:bg-[#070B09] border border-gray-200 dark:border-[#1A2820]">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs flex items-center justify-center flex-shrink-0"
                                              x-text="'#' + bNum"></span>
                                        <input type="text"
                                               :value="getBlankAnswer(currentQuestion.id, bNum - 1)"
                                               @input="setBlankAnswer(currentQuestion.id, bNum - 1, $event.target.value)"
                                               :placeholder="'Ketik jawaban titik-titik #' + bNum + '...'"
                                               class="flex-1 bg-transparent border-0 border-b border-gray-300 dark:border-gray-700 focus:border-emerald-500 focus:ring-0 text-sm font-mono text-gray-900 dark:text-emerald-300 placeholder-gray-400 dark:placeholder-gray-600 py-1 px-2 outline-none transition-colors">
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Keyboard hint khusus Isian Rumpang --}}
                        <div class="text-center">
                            <span class="text-[11px] text-gray-500">
                                💡 Tips: Tekan <strong class="text-gray-600 dark:text-gray-400">Tab</strong> atau <strong class="text-gray-600 dark:text-gray-400">Enter</strong> untuk berpindah ke kotak isian titik-titik berikutnya secara otomatis.
                            </span>
                        </div>
                    </div>
                </template>

            </div>
        </template>

        {{-- Bottom Navigation Controls --}}
        <div class="pt-6 sm:pt-8 flex items-center justify-between gap-3 border-t border-gray-200 dark:border-[#1A2820] mt-6 relative z-10">
            {{-- Prev Question --}}
            <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#1A2820] bg-white/80 dark:bg-[#0F1713] text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#142019] text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 disabled:opacity-30 disabled:pointer-events-none shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Sebelumnya</span>
            </button>

            {{-- Question Drawer / Grid Button --}}
            <button type="button" @click="showDrawer = true"
                    class="px-3.5 py-2.5 rounded-xl bg-white/80 dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-semibold transition-colors flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                <span class="hidden sm:inline">Daftar Soal</span>
                <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-xs font-mono font-bold"
                      x-text="answeredCount() + '/' + questions.length"></span>
                <template x-if="flaggedCount() > 0">
                    <span class="px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-xs font-mono font-bold flex items-center gap-1"
                          title="Jumlah Soal Ragu-ragu">
                        <span>🚩</span>
                        <span x-text="flaggedCount()"></span>
                    </span>
                </template>
            </button>

            {{-- Next or Submit Button --}}
            <div>
                <template x-if="currentIndex < questions.length - 1">
                    <button type="button" @click="nextQuestion()"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                        <span>Selanjutnya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </template>

                <template x-if="currentIndex === questions.length - 1">
                    <button type="button" @click="confirmSubmit()"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs sm:text-sm font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/30 animate-bounce">
                        <span>Selesai & Kumpulkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </template>
            </div>
        </div>

    </main>

    {{-- Question Navigation Drawer / Grid Modal --}}
    <div x-show="showDrawer"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="z-index: 100;">

        <div @click.away="showDrawer = false"
             class="bg-[#0F1713] border border-[#1A2820] rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#1A2820]">
                <div>
                    <h3 class="text-base font-bold text-white heading-font">Navigasi Butir Soal</h3>
                    <p class="text-xs text-gray-400">Pilih nomor soal untuk melompat langsung</p>
                </div>
                <button type="button" @click="showDrawer = false" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Grid of Questions --}}
            <div class="grid grid-cols-5 sm:grid-cols-6 gap-2.5 max-h-60 overflow-y-auto p-1">
                <template x-for="(q, idx) in questions" :key="q.id">
                    <button type="button"
                            @click="jumpToQuestion(idx)"
                            class="h-12 rounded-xl text-xs font-bold font-mono flex flex-col items-center justify-center border transition-all relative"
                            :class="idx === currentIndex ? 'border-emerald-400 bg-emerald-500/25 text-emerald-300 ring-2 ring-emerald-500/30 font-extrabold' : (isFlagged(q.id) ? 'bg-amber-500/20 border-amber-500/60 text-amber-300' : (isQuestionAnswered(q.id) ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-400' : 'bg-[#090D0B] border-[#1A2820] text-gray-400 hover:border-gray-600'))">
                        <span x-text="idx + 1"></span>
                        <div class="flex items-center gap-0.5 text-[9px] mt-0.5 leading-none">
                            <template x-if="isFlagged(q.id)">
                                <span>🚩</span>
                            </template>
                            <template x-if="isQuestionAnswered(q.id)">
                                <span class="text-emerald-400 font-bold">✓</span>
                            </template>
                        </div>
                    </button>
                </template>
            </div>

            <div class="mt-4 pt-3 border-t border-[#1A2820] flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-3 text-gray-400 flex-wrap">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Dijawab (<span x-text="answeredCount()"></span>)</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Ragu-ragu (<span x-text="flaggedCount()"></span>)</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-gray-700"></span> Belum</span>
                </div>
                <button type="button" @click="showDrawer = false"
                        class="px-4 py-1.5 rounded-xl bg-gray-800 text-gray-200 font-semibold hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 1: Freeze Cooldown Screen (Strike 1: 60s, Strike 2: 90s)        --}}
    {{-- ========================================================================= --}}
    <div x-show="!isLocked && penaltySeconds > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
         style="z-index: 100;">
        
        <div class="bg-[#0F1713] border-2 border-amber-500/40 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            {{-- Glowing Ambient --}}
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-16 h-16 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                ⚠️
            </div>

            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30 mb-2">
                    Peringatan 2 dari 3: Sanksi Pembekuan 1 Menit
                </span>
                <h3 class="text-xl font-bold text-white heading-font">Pelanggaran Kedua Terdeteksi!</h3>
                <p class="text-xs text-gray-400 mt-2">
                    Anda telah melewati batas peringatan pertama. Sebagai sanksi, layar kuis dibekukan selama <strong class="text-rose-400">1 Menit (60 detik)</strong>.
                    <br><span class="text-rose-300 font-semibold mt-1 inline-block">PERINGATAN TERAKHIR: 1x lagi berpindah, kuis Anda akan TERKUNCI TOTAL!</span>
                </p>
            </div>

            {{-- Big Timer Countdown --}}
            <div class="my-6 p-4 rounded-2xl bg-[#090D0B] border transition-colors duration-200"
                 :class="isPenaltyPaused ? 'border-rose-500/60 bg-rose-950/20' : 'border-amber-500/30'">
                <div class="text-[11px] font-semibold uppercase tracking-widest mb-1"
                     :class="isPenaltyPaused ? 'text-rose-300' : 'text-gray-400'">
                    <span x-show="!isPenaltyPaused">Layar Terbuka Otomatis Dalam:</span>
                    <span x-show="isPenaltyPaused" class="flex items-center justify-center gap-1.5 text-rose-300 font-bold">
                        <span>⏸️</span> HITUNGAN SANKSI DIJEDA
                    </span>
                </div>
                <div class="text-4xl sm:text-5xl font-mono font-extrabold tracking-wider"
                     :class="isPenaltyPaused ? 'text-rose-400' : 'text-amber-400 animate-pulse'"
                     x-text="formatTime(penaltySeconds)"></div>

                {{-- Status Paused Indicator --}}
                <div x-show="isPenaltyPaused" class="mt-3 px-3 py-1.5 rounded-lg bg-rose-500/20 border border-rose-500/40 text-xs font-semibold text-rose-300 flex items-center justify-center gap-2 animate-bounce">
                    <span>⚠️ Layar dijeda karena Anda tidak fokus. Tetaplah di jendela ini!</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-[#142019] border border-[#1E3327] text-[11px] text-gray-400 text-left space-y-1">
                <div>💡 <strong>Aturan Ketat Masa Sanksi:</strong></div>
                <div class="text-gray-300">1. Waktu sanksi <em>hanya berkurang</em> jika jendela ini tetap aktif & berfokus di layar Anda.</div>
                <div class="text-rose-400 font-medium">2. Dilarang berpindah aplikasi (Alt+Tab)! Berpindah saat sanksi akan langsung menaikkan hukuman atau mengunci kuis permanen.</div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 2: Full Lockout Screen (Strike 3 - Butuh Password Admin)           --}}
    {{-- ========================================================================= --}}
    <div x-show="isLocked"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-lg flex items-center justify-center p-4"
         style="z-index: 100;">
        
        <div class="bg-[#0F1713] border-2 border-rose-500/60 rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            {{-- Glowing Ambient --}}
            <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-64 h-64 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                ⛔
            </div>

            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/25 text-rose-300 border border-rose-500/40 mb-2">
                Strike 3: Kuis Terkunci Total
            </span>
            <h3 class="text-2xl font-extrabold text-white heading-font">Ujian Dibekukan Secara Permanen</h3>
            
            <p class="text-xs sm:text-sm text-gray-300 mt-2 max-w-sm mx-auto leading-relaxed">
                Anda telah terdeteksi melakukan <strong class="text-rose-400">3 kali perpindahan layar/tab</strong>. Hak akses pengerjaan kuis Anda telah dikunci demi integritas ujian.
            </p>

            <div class="my-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-left text-xs text-rose-200">
                <div class="font-bold flex items-center gap-1.5 mb-1 text-rose-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Instruksi Pembukaan Kunci:
                </div>
                Silakan panggil <strong>Pengawas Ujian / Dosen / Asisten Lab</strong> yang bertugas. Pengawas akan memasukkan password otorisasi untuk membuka kembali sesi kuis Anda.
            </div>

            {{-- Admin Password Unlock Form --}}
            <form @submit.prevent="submitUnlockPassword()" class="space-y-3 pt-2 text-left">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">
                        Password Otorisasi Pengawas:
                    </label>
                    <div class="relative">
                        <input type="password" x-model="adminPassword" placeholder="Masukkan password pengawas..."
                               class="w-full px-4 py-3 bg-[#090D0B] border border-rose-500/40 focus:border-rose-400 focus:ring-1 focus:ring-rose-400 rounded-xl text-sm text-white placeholder-gray-500 font-mono tracking-wider"
                               required :disabled="isUnlocking">
                    </div>
                </div>

                {{-- Error Message Alert --}}
                <template x-if="unlockError">
                    <div class="p-2.5 rounded-xl bg-red-950/50 border border-red-500/50 text-red-300 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span x-text="unlockError"></span>
                    </div>
                </template>

                <button type="submit" :disabled="isUnlocking"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 disabled:opacity-50 text-white font-bold text-sm shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
                    <span x-show="!isUnlocking">Buka Kunci Ujian Sekarang</span>
                    <span x-show="isUnlocking" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Memverifikasi Password...</span>
                    </span>
                </button>
            </form>

            <div class="mt-4 text-[11px] text-gray-500">
                Sistem Pengawasan CBT &bull; IC-Labs Security Core
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 3: Initial Fullscreen Enforced Prompt (CBT Kiosk Entrance)        --}}
    {{-- ========================================================================= --}}
    <div x-show="!isFullscreen && !showFullscreenWarning && !allowOpenBook && !isLocked && penaltySeconds === 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-md flex items-center justify-center p-4"
         style="z-index: 100;">
        <div class="bg-[#0F1713] border-2 border-emerald-500/40 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                🖥️
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 mb-2">
                CBT Kiosk Mode
            </span>
            <h3 class="text-xl font-bold text-white heading-font">Mode Layar Penuh Diwajibkan</h3>
            <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                Untuk menjaga ketertiban dan integritas praktikum, ujian ini mewajibkan Anda mengerjakan dalam mode layar penuh (Kiosk Experience).
            </p>
            <div class="mt-6">
                <button type="button" @click="enterFullscreen()"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <span>Aktifkan Layar Penuh & Mulai Ujian</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 4: Fullscreen Exit 5-Second Warning Modal (Kiosk Enforcement)     --}}
    {{-- ========================================================================= --}}
    <div x-show="showFullscreenWarning && !isFullscreen && !allowOpenBook && !isLocked && penaltySeconds === 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-md flex items-center justify-center p-4"
         style="z-index: 100;">
        <div class="bg-[#0F1713] border-2 border-rose-500/60 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                ⚠️
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/25 text-rose-300 border border-rose-500/40 mb-2">
                Peringatan Kiosk CBT
            </span>
            <h3 class="text-xl font-bold text-white heading-font">Anda Keluar Dari Layar Penuh!</h3>
            <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                Ujian mewajibkan pengerjaan dalam mode layar penuh. Segera klik tombol di bawah sebelum waktu toleransi habis:
            </p>
            
            <div class="my-5 p-4 rounded-2xl bg-[#090D0B] border border-rose-500/30">
                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-1">Toleransi Waktu Kembali:</div>
                <div class="text-5xl font-mono font-extrabold text-rose-400 tracking-wider animate-pulse"
                     x-text="fullscreenToleranceSeconds + 's'"></div>
            </div>

            <button type="button" @click="enterFullscreen()"
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span>Kembali ke Layar Penuh Sekarang</span>
            </button>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- BROADCAST PENGUMUMAN POPUP MODAL (REAL-TIME DARI PENGAWAS)                 --}}
    {{-- ========================================================================= --}}
    <div x-show="activeAnnouncementModal !== null"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/85 backdrop-blur-md"
         style="display: none;">
        
        <div @click.away="closeAnnouncementModal()"
             class="w-full max-w-lg bg-[#0F1713] border rounded-3xl p-6 sm:p-7 shadow-2xl relative overflow-hidden"
             :class="{
                 'border-sky-500/40 shadow-sky-500/10': activeAnnouncementModal && activeAnnouncementModal.type === 'info',
                 'border-amber-500/50 shadow-amber-500/15': activeAnnouncementModal && activeAnnouncementModal.type === 'warning',
                 'border-rose-500/60 shadow-rose-500/20': activeAnnouncementModal && activeAnnouncementModal.type === 'urgent'
             }">
            
            {{-- Glowing ambient background light --}}
            <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full blur-3xl opacity-20 pointer-events-none"
                 :class="{
                     'bg-sky-500': activeAnnouncementModal && activeAnnouncementModal.type === 'info',
                     'bg-amber-500': activeAnnouncementModal && activeAnnouncementModal.type === 'warning',
                     'bg-rose-500': activeAnnouncementModal && activeAnnouncementModal.type === 'urgent'
                 }"></div>

            {{-- Header Badge & Time --}}
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shadow-sm"
                          :class="{
                              'bg-sky-500/20 text-sky-400 border border-sky-500/30': activeAnnouncementModal && activeAnnouncementModal.type === 'info',
                              'bg-amber-500/20 text-amber-400 border border-amber-500/30': activeAnnouncementModal && activeAnnouncementModal.type === 'warning',
                              'bg-rose-500/20 text-rose-400 border border-rose-500/30 animate-pulse': activeAnnouncementModal && activeAnnouncementModal.type === 'urgent'
                          }">
                        <span x-text="activeAnnouncementModal && activeAnnouncementModal.type === 'urgent' ? '🚨' : (activeAnnouncementModal && activeAnnouncementModal.type === 'warning' ? '⚠️' : '📢')"></span>
                    </span>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                              :class="{
                                  'bg-sky-500/15 text-sky-400 border border-sky-500/30': activeAnnouncementModal && activeAnnouncementModal.type === 'info',
                                  'bg-amber-500/15 text-amber-400 border border-amber-500/30': activeAnnouncementModal && activeAnnouncementModal.type === 'warning',
                                  'bg-rose-500/15 text-rose-400 border border-rose-500/30': activeAnnouncementModal && activeAnnouncementModal.type === 'urgent'
                              }"
                              x-text="activeAnnouncementModal && activeAnnouncementModal.type === 'urgent' ? 'Perhatian Darurat' : (activeAnnouncementModal && activeAnnouncementModal.type === 'warning' ? 'Ralat / Klarifikasi Soal' : 'Pengumuman Pengawas')">
                        </span>
                    </div>
                </div>

                <span class="text-xs font-mono text-gray-400" x-text="activeAnnouncementModal ? (activeAnnouncementModal.time || 'Baru Saja') : ''"></span>
            </div>

            <h3 class="text-lg font-bold text-white mb-2">
                <span x-show="activeAnnouncementModal && activeAnnouncementModal.type === 'warning'">Ada Ralat / Catatan Khusus Soal!</span>
                <span x-show="activeAnnouncementModal && activeAnnouncementModal.type === 'urgent'">Peringatan Penting Pengawas Lab!</span>
                <span x-show="activeAnnouncementModal && activeAnnouncementModal.type === 'info'">Pengumuman dari Pengawas Ujian</span>
            </h3>

            {{-- Message body --}}
            <div class="p-4 rounded-2xl bg-black/40 border border-white/5 my-4">
                <p class="text-sm sm:text-base text-gray-100 leading-relaxed whitespace-pre-line font-medium"
                   x-text="activeAnnouncementModal ? activeAnnouncementModal.message : ''"></p>
            </div>

            <p class="text-[11px] text-gray-400 mb-5 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Anda dapat melihat kembali pengumuman ini kapan saja lewat tombol <strong class="text-gray-300">"📢 Pengumuman"</strong> di menu atas.</span>
            </p>

            <button type="button" @click="closeAnnouncementModal()"
                    class="w-full py-3.5 rounded-xl text-white font-bold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg hover:scale-[1.01] active:scale-98"
                    :class="{
                        'bg-gradient-to-r from-sky-600 to-teal-600 hover:from-sky-500 hover:to-teal-500 shadow-sky-500/20': activeAnnouncementModal && activeAnnouncementModal.type === 'info',
                        'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 shadow-amber-500/20': activeAnnouncementModal && activeAnnouncementModal.type === 'warning',
                        'bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 shadow-rose-500/20': activeAnnouncementModal && activeAnnouncementModal.type === 'urgent'
                    }">
                <span>Saya Mengerti, Lanjutkan Pengerjaan</span>
            </button>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- ANNOUNCEMENTS HISTORY DRAWER (RIWAYAT PENGUMUMAN & RALAT SOAL)             --}}
    {{-- ========================================================================= --}}
    <div x-show="isAnnouncementDrawerOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[115] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="isAnnouncementDrawerOpen = false"
             class="w-full max-w-lg bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 shadow-2xl relative max-h-[85vh] flex flex-col">
            
            <div class="flex items-center justify-between pb-4 border-b border-[#1A2820]">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">📢</span>
                    <h3 class="text-base font-bold text-white">Riwayat Pengumuman Pengawas</h3>
                </div>
                <button type="button" @click="isAnnouncementDrawerOpen = false"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-4 space-y-3 pr-1">
                <template x-if="announcements.length === 0">
                    <div class="py-8 text-center text-xs text-gray-400">
                        Belum ada pengumuman atau ralat soal dari pengawas lab.
                    </div>
                </template>

                <template x-for="item in announcements" :key="item.id">
                    <div class="p-4 rounded-2xl border"
                         :class="{
                             'bg-sky-500/10 border-sky-500/30': item.type === 'info',
                             'bg-amber-500/10 border-amber-500/30': item.type === 'warning',
                             'bg-rose-500/10 border-rose-500/30': item.type === 'urgent'
                         }">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                  :class="{
                                      'bg-sky-500/20 text-sky-300': item.type === 'info',
                                      'bg-amber-500/20 text-amber-300': item.type === 'warning',
                                      'bg-rose-500/20 text-rose-300': item.type === 'urgent'
                                  }"
                                  x-text="item.type === 'urgent' ? '🚨 Darurat' : (item.type === 'warning' ? '⚠️ Ralat Soal' : 'ℹ️ Info')"></span>
                            <span class="text-[11px] font-mono text-gray-400" x-text="item.time"></span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-200 leading-relaxed font-medium" x-text="item.message"></p>
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-[#1A2820]">
                <button type="button" @click="isAnnouncementDrawerOpen = false"
                        class="w-full py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-xs transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- EMERGENCY GLOBAL PAUSE OVERLAY (UJIAN DIJEDA OLEH PENGAWAS LAB)           --}}
    {{-- ========================================================================= --}}
    <div x-show="isExamPaused"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md select-none"
         style="display: none;">
        
        <div class="max-w-md w-full bg-[#0F1713] border-2 border-amber-500/50 rounded-3xl p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden ring-4 ring-amber-500/10">
            {{-- Ambient Glow --}}
            <div class="absolute -top-20 -left-20 w-44 h-44 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-44 h-44 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Pause Icon --}}
            <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-amber-500/20 border-2 border-amber-500/40 flex items-center justify-center text-amber-400 shadow-lg shadow-amber-500/20">
                <svg class="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>

            <span class="px-3 py-1 rounded-full text-xs font-black tracking-widest uppercase bg-amber-500 text-gray-950 font-mono inline-block mb-3 animate-pulse">
                ⏸️ Ujian Dijeda Pengawas
            </span>

            <h3 class="text-xl sm:text-2xl font-black text-white mb-2">Hitungan Waktu Dibekukan</h3>
            
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed font-medium mb-5">
                Pengawas lab sedang menjeda jalannya ujian. <strong class="text-amber-400 font-bold">Waktu pengerjaan Anda otomatis dibekukan</strong> dan tidak akan berkurang sama sekali selama masa jeda ini.
            </p>

            {{-- Frozen Timer Status --}}
            <div class="p-3.5 rounded-2xl bg-black/40 border border-[#1A2820] flex items-center justify-center gap-3">
                <span class="text-xs text-gray-400 font-medium">Sisa Waktu Anda:</span>
                <span class="font-mono text-base font-bold text-amber-400" x-text="formatTime(remainingSeconds) + ' (Frozen)'"></span>
            </div>

            <div class="mt-5 flex items-center justify-center gap-2 text-[11px] text-gray-400">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                <span>Layar akan otomatis terbuka saat pengawas melanjutkan ujian</span>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- HOVER PREVIEW POPUP (INSTANT PREVIEW SAAT KURSOR MENDEKAT)                --}}
    {{-- ========================================================================= --}}
    <div class="cyber-hover-preview-bubble"
         :class="{ 'is-active': hoverPreview.active }"
         :style="'left: ' + hoverPreview.x + 'px; top: ' + hoverPreview.y + 'px;'">
        <template x-if="hoverPreview.src">
            <img :src="hoverPreview.src" alt="Pratinjau Kursor">
        </template>
    </div>

    {{-- ========================================================================= --}}
    {{-- LIGHTBOX PREVIEW MODAL (POP-UP PEMBESAR GAMBAR SOAL / OPSI DENGAN ZOOM)    --}}
    {{-- ========================================================================= --}}
    <div x-show="previewImageModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[120] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
         style="z-index: 120;"
         @click="closeImagePreview()"
         @keydown.escape.window="closeImagePreview()">
        <div class="relative max-w-5xl max-h-[92vh] flex flex-col items-center justify-center select-none"
             @click.stop>
            
            {{-- Top Controls Bar: Zoom In, Zoom Out, Reset, & Close --}}
            <div class="absolute -top-14 right-0 sm:right-0 flex items-center gap-2">
                <div class="flex items-center bg-[#0F1713]/90 hover:bg-[#142019] border border-[#1E3327] rounded-xl px-1.5 py-1 shadow-lg text-xs font-semibold gap-1 lightbox-card">
                    <button type="button"
                            @click="zoomPreview(-0.25)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            title="Zoom Out (−)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <span class="px-2 font-mono text-emerald-400 font-bold min-w-[50px] text-center"
                          x-text="Math.round(previewImageModal.scale * 100) + '%'"></span>
                    <button type="button"
                            @click="zoomPreview(0.25)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                            title="Zoom In (+)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button type="button"
                            @click="resetPreviewZoom()"
                            class="px-2 py-1 rounded-md text-[11px] text-gray-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer font-mono"
                            title="Reset Skala 100%">
                        1:1
                    </button>
                </div>

                <button type="button"
                        @click="closeImagePreview()"
                        class="p-2 text-gray-400 hover:text-white bg-[#0F1713]/80 hover:bg-[#1A2820] border border-[#1E3327] rounded-xl transition-all shadow-lg flex items-center gap-1.5 text-xs font-semibold cursor-pointer lightbox-close-btn"
                        title="Tutup (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="hidden sm:inline">Tutup</span>
                </button>
            </div>

            {{-- Image Card with Zoom and Pan/Drag Support --}}
            <div class="w-[92vw] max-w-5xl h-[78vh] sm:h-[82vh] rounded-2xl overflow-hidden border border-emerald-500/30 bg-[#0A0F0D] lightbox-card shadow-[0_0_50px_rgba(0,0,0,0.9),0_0_25px_rgba(16,185,129,0.2)] p-2 relative flex items-center justify-center select-none"
                 @wheel.prevent="zoomPreview($event.deltaY < 0 ? 0.2 : -0.2)">
                <div class="w-full h-full flex items-center justify-center overflow-hidden relative"
                     style="touch-action: none;"
                     @mousedown="startDrag($event)"
                     @mousemove="onDrag($event)"
                     @mouseup="stopDrag()"
                     @mouseleave="stopDrag()"
                     @touchstart="onTouchStart($event)"
                     @touchmove="onTouchMove($event)"
                     @touchend="stopDrag()"
                     @dblclick="toggleZoom()">
                    <img :src="previewImageModal.src" alt="Pratinjau Gambar Soal"
                         class="max-w-full max-h-full object-contain rounded-xl transition-transform duration-75 ease-out select-none"
                         :class="previewImageModal.scale > 1 ? (previewImageModal.isDragging ? 'cursor-grabbing' : 'cursor-grab') : 'cursor-zoom-in'"
                         :style="'transform: translate(' + previewImageModal.translateX + 'px, ' + previewImageModal.translateY + 'px) scale(' + previewImageModal.scale + ');'"
                         draggable="false">
                </div>
            </div>

            {{-- Caption --}}
            <p x-show="previewImageModal.caption"
               x-text="previewImageModal.caption"
               class="text-xs text-gray-300 font-medium mt-3 text-center px-4 py-1.5 rounded-full bg-[#0F1713]/90 border border-[#1E3327] max-w-xl truncate lightbox-caption"></p>
        </div>
    </div>

    {{-- Script Quiz Arena Logic --}}
    <script>
        function quizArena() {
            return {
                questions: @json($clientQuestions),
                remainingSeconds: {{ $timeRemaining !== null ? (int)$timeRemaining : 'null' }},
                currentIndex: 0,
                answers: {},
                flaggedQuestions: [],
                syncStatus: 'synced', // 'synced', 'saving', 'offline'
                syncDebounceTimer: null,
                heartbeatInterval: null,
                tabSwitchCount: {{ (int)$attempt->tab_switch_count }},
                allowOpenBook: {{ $quiz->allow_open_book ? 'true' : 'false' }},
                isLocked: {{ $attempt->is_locked ? 'true' : 'false' }},
                penaltySeconds: {{ (int)$penaltyRemainingSeconds }},
                strike: {{ min(3, (int)$attempt->tab_switch_count) }},
                adminPassword: '',
                isUnlocking: false,
                unlockError: '',
                showDrawer: false,
                isSubmitting: false,

                // Announcements & Real-Time Broadcasts
                announcements: @json($initialAnnouncements ?? []),
                activeAnnouncementModal: null,
                isAnnouncementDrawerOpen: false,
                lastSeenAnnouncementId: parseInt(localStorage.getItem('quiz_seen_announcement_{{ $attempt->id }}') || '0'),

                // Emergency Global Pause
                isExamPaused: {{ $quiz->isSessionPaused() ? 'true' : 'false' }},
                timerInterval: null,
                penaltyTimerInterval: null,
                isFullscreen: false,
                isEnteringFullscreen: false,
                hasEnteredFullscreenOnce: false,
                showFullscreenWarning: false,
                fullscreenToleranceSeconds: 5,
                fullscreenToleranceInterval: null,
                focusLossTimer: null,
                visibilityLossTimer: null,
                penaltyEvasionTimer: null,
                isPenaltyPaused: false,
                unlockImmunityUntil: Date.now() + 10000, // 10 detik proteksi awal saat kuis dimuat
                previewImageModal: {
                    open: false,
                    src: '',
                    caption: '',
                    scale: 1.0,
                    isDragging: false,
                    startX: 0,
                    startY: 0,
                    translateX: 0,
                    translateY: 0
                },
                hoverPreview: {
                    active: false,
                    src: '',
                    x: 0,
                    y: 0
                },

                get currentQuestion() {
                    return this.questions[this.currentIndex] || null;
                },

                checkFullscreenState() {
                    return Boolean(
                        document.fullscreenElement ||
                        document.webkitFullscreenElement ||
                        document.mozFullScreenElement ||
                        document.msFullscreenElement
                    );
                },

                initArena() {
                    // Initialize answers: merge server-saved draft answers with LocalStorage cache
                    const serverDraft = @json($savedAnswers);
                    const localDraft = this.loadLocalCache();
                    this.answers = Object.assign({}, serverDraft, localDraft);

                    // Initialize flagged questions
                    if (Array.isArray(this.answers['__flagged'])) {
                        this.flaggedQuestions = this.answers['__flagged'].map(String);
                    }

                    // Check initial fullscreen state
                    this.isFullscreen = this.checkFullscreenState();
                    if (this.isFullscreen) {
                        this.hasEnteredFullscreenOnce = true;
                    }

                    // Start main quiz timer if applicable
                    if (this.remainingSeconds !== null && this.remainingSeconds > 0) {
                        this.remainingSeconds = Math.max(0, Math.floor(Number(this.remainingSeconds)));
                        this.timerInterval = setInterval(() => {
                            if (this.isExamPaused) return; // Do not decrement timer while exam is paused!
                            if (this.remainingSeconds > 0) {
                                this.remainingSeconds--;
                            } else {
                                clearInterval(this.timerInterval);
                                this.autoSubmitExpired();
                            }
                        }, 1000);
                    }

                    // Start penalty countdown if student refreshed with an active penalty
                    if (this.penaltySeconds > 0 && !this.isLocked) {
                        this.initPenaltyCountdown();
                    }

                    // Setup realtime session heartbeat (deteksi otomatis jika admin mengakhiri sesi kuis)
                    this.startSessionHeartbeat();

                    // Initialize seen announcement tracking
                    if (this.lastSeenAnnouncementId === 0 && this.announcements.length > 0) {
                        this.lastSeenAnnouncementId = Number(this.announcements[0].id);
                        try {
                            localStorage.setItem('quiz_seen_announcement_{{ $attempt->id }}', String(this.lastSeenAnnouncementId));
                        } catch (e) {}
                    }

                    // Setup background heartbeat sync every 15 seconds so supervisor Live Monitor stays online
                    this.heartbeatInterval = setInterval(() => {
                        if (!this.isSubmitting && !this.isLocked) {
                            this.triggerBackgroundSync();
                        }
                    }, 15000);

                    // Proactive CSRF token refresh every 10 minutes to prevent session expiration during long exams
                    setInterval(() => {
                        fetch('{{ route('csrf.refresh') }}')
                            .then(r => r.json())
                            .then(d => {
                                if (d && d.csrf_token) {
                                    const meta = document.querySelector('meta[name="csrf-token"]');
                                    if (meta) meta.content = d.csrf_token;
                                }
                            }).catch(() => {});
                    }, 600000);

                    // Initial code syntax highlighting pass
                    this.$nextTick(() => {
                        if (window.Prism) Prism.highlightAll();
                    });

                    // Comprehensive vendor-prefixed fullscreen listeners
                    const onFsChange = () => {
                        this.isFullscreen = this.checkFullscreenState();
                        if (this.isFullscreen) {
                            this.hasEnteredFullscreenOnce = true;
                            this.cancelFullscreenTolerance();
                        } else {
                            if (!this.allowOpenBook && this.hasEnteredFullscreenOnce && !this.isLocked && !this.isSubmitting && this.penaltySeconds === 0) {
                                this.startFullscreenTolerance();
                            }
                        }
                    };

                    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
                        document.addEventListener(evt, onFsChange);
                    });

                    // Setup Anti-Cheat Listeners
                    this.setupAntiCheat();

                    // Setup Keyboard shortcuts
                    this.setupKeyboardShortcuts();

                    // Setup Network & Page Unload Listeners (Prioritas 1 & Prioritas 3)
                    this.setupNetworkAndUnloadGuards();
                },

                enterFullscreen() {
                    this.isEnteringFullscreen = true;
                    setTimeout(() => { this.isEnteringFullscreen = false; }, 1200);

                    const el = document.documentElement;
                    const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                    if (req) {
                        req.call(el).then(() => {
                            this.isFullscreen = true;
                            this.hasEnteredFullscreenOnce = true;
                            this.cancelFullscreenTolerance();
                        }).catch((err) => {
                            console.warn('Fullscreen request blocked or failed:', err);
                            this.isFullscreen = this.checkFullscreenState();
                        });
                    }
                },

                startFullscreenTolerance() {
                    this.showFullscreenWarning = true;
                    this.fullscreenToleranceSeconds = 5;
                    if (this.fullscreenToleranceInterval) clearInterval(this.fullscreenToleranceInterval);

                    this.fullscreenToleranceInterval = setInterval(() => {
                        if (this.fullscreenToleranceSeconds > 1) {
                            this.fullscreenToleranceSeconds--;
                        } else {
                            this.cancelFullscreenTolerance();
                            this.reportViolation('fullscreen_exit');
                        }
                    }, 1000);
                },

                cancelFullscreenTolerance() {
                    this.showFullscreenWarning = false;
                    if (this.fullscreenToleranceInterval) {
                        clearInterval(this.fullscreenToleranceInterval);
                        this.fullscreenToleranceInterval = null;
                    }
                },

                loadLocalCache() {
                    try {
                        const raw = localStorage.getItem('cbt_quiz_{{ $attempt->id }}_answers');
                        return raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        return {};
                    }
                },

                saveLocalCache() {
                    try {
                        localStorage.setItem('cbt_quiz_{{ $attempt->id }}_answers', JSON.stringify(this.answers));
                        localStorage.setItem('cbt_quiz_{{ $attempt->id }}_last_save', Date.now());
                    } catch (e) {
                        console.warn('LocalStorage save failed:', e);
                    }
                },

                openImagePreview(src, caption = '') {
                    this.previewImageModal.src = src;
                    this.previewImageModal.caption = caption;
                    this.previewImageModal.scale = 1.0;
                    this.previewImageModal.translateX = 0;
                    this.previewImageModal.translateY = 0;
                    this.previewImageModal.open = true;
                    this.hideHoverPreview();
                },

                closeImagePreview() {
                    this.previewImageModal.open = false;
                    this.previewImageModal.src = '';
                    this.previewImageModal.caption = '';
                    this.previewImageModal.scale = 1.0;
                    this.previewImageModal.translateX = 0;
                    this.previewImageModal.translateY = 0;
                    this.previewImageModal.isDragging = false;
                },

                zoomPreview(delta) {
                    let nextScale = Math.round((this.previewImageModal.scale + delta) * 10) / 10;
                    if (nextScale < 0.5) nextScale = 0.5;
                    if (nextScale > 5.0) nextScale = 5.0;
                    this.previewImageModal.scale = nextScale;
                    if (nextScale <= 1.0) {
                        this.previewImageModal.translateX = 0;
                        this.previewImageModal.translateY = 0;
                    }
                },

                resetPreviewZoom() {
                    this.previewImageModal.scale = 1.0;
                    this.previewImageModal.translateX = 0;
                    this.previewImageModal.translateY = 0;
                    this.previewImageModal.isDragging = false;
                },

                toggleZoom() {
                    if (this.previewImageModal.scale > 1.0) {
                        this.resetPreviewZoom();
                    } else {
                        this.zoomPreview(1.0); // Jump to 200%
                    }
                },

                startDrag(e) {
                    if (this.previewImageModal.scale <= 1.0) return;
                    this.previewImageModal.isDragging = true;
                    this.previewImageModal.startX = e.clientX - this.previewImageModal.translateX;
                    this.previewImageModal.startY = e.clientY - this.previewImageModal.translateY;
                    e.preventDefault();
                },

                onDrag(e) {
                    if (!this.previewImageModal.isDragging) return;
                    this.previewImageModal.translateX = e.clientX - this.previewImageModal.startX;
                    this.previewImageModal.translateY = e.clientY - this.previewImageModal.startY;
                },

                stopDrag() {
                    this.previewImageModal.isDragging = false;
                },

                onTouchStart(e) {
                    if (e.touches.length === 1 && this.previewImageModal.scale > 1.0) {
                        this.previewImageModal.isDragging = true;
                        this.previewImageModal.startX = e.touches[0].clientX - this.previewImageModal.translateX;
                        this.previewImageModal.startY = e.touches[0].clientY - this.previewImageModal.translateY;
                    }
                },

                onTouchMove(e) {
                    if (this.previewImageModal.isDragging && e.touches.length === 1) {
                        this.previewImageModal.translateX = e.touches[0].clientX - this.previewImageModal.startX;
                        this.previewImageModal.translateY = e.touches[0].clientY - this.previewImageModal.startY;
                    }
                },

                showHoverPreview(e, src) {
                    if (this.previewImageModal.open) return;
                    this.hoverPreview.src = src;
                    this.hoverPreview.active = true;
                    this.updateHoverPreviewPos(e);
                },

                updateHoverPreviewPos(e) {
                    if (!this.hoverPreview.active) return;
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

                    this.hoverPreview.x = posX;
                    this.hoverPreview.y = posY;
                },

                hideHoverPreview() {
                    this.hoverPreview.active = false;
                },

                triggerBackgroundSync(isImmediate = false) {
                    this.syncStatus = 'saving';
                    if (this.syncDebounceTimer) clearTimeout(this.syncDebounceTimer);

                    const doSync = () => {
                        fetch('{{ route('quiz.autosave', [$quiz->unique_token, $attempt->id]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ answers: this.answers })
                        })
                        .then(res => {
                            if (res.status === 419) {
                                return res.json().then(data => {
                                    if (data && data.csrf_token) {
                                        const meta = document.querySelector('meta[name="csrf-token"]');
                                        if (meta) meta.content = data.csrf_token;
                                    }
                                    return { success: false, expired: true };
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            // Cek jika sesi diakhiri oleh pengawas atau attempt di-force submit terlepas dari flag success
                            if (data.is_ended || data.is_completed || data.is_force_submitted || data.redirect) {
                                this.handleSessionEnded(data.redirect, data.message);
                                return;
                            }

                            if (data.success) {
                                this.syncStatus = 'synced';

                                // Check if supervisor adjusted/added extra time
                                if (data.time_remaining !== undefined && data.time_remaining !== null) {
                                    const newTime = Math.max(0, Math.floor(Number(data.time_remaining)));
                                    if (this.remainingSeconds !== null && Math.abs(this.remainingSeconds - newTime) > 60) {
                                        this.remainingSeconds = newTime;
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'Waktu ujian telah disesuaikan oleh pengawas.',
                                            showConfirmButton: false,
                                            timer: 3500,
                                            background: '#0F1713',
                                            color: '#34D399',
                                            customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                                        });
                                    }
                                }
                            } else {
                                this.syncStatus = 'offline';
                            }
                        })
                        .catch(() => {
                            this.syncStatus = 'offline';
                        });
                    };

                    if (isImmediate) {
                        doSync();
                    } else {
                        this.syncDebounceTimer = setTimeout(doSync, 400);
                    }
                },

                startSessionHeartbeat() {
                    if (this.sessionHeartbeatInterval) clearInterval(this.sessionHeartbeatInterval);

                    const poll = () => {
                        if (this.isSubmitting) return;

                        fetch('{{ route('quiz.session.check', [$quiz->unique_token, $attempt->id]) }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.is_ended || data.status === 'ended' || data.redirect) {
                                this.handleSessionEnded(data.redirect, data.message);
                                return;
                            }

                            // Sync adjusted extra time
                            if (data.time_remaining !== undefined && data.time_remaining !== null) {
                                const newTime = Math.max(0, Math.floor(Number(data.time_remaining)));
                                if (this.remainingSeconds !== null && (newTime - this.remainingSeconds) > 30) {
                                    this.remainingSeconds = newTime;
                                    if (window.Swal) {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'Waktu ujian telah disesuaikan oleh pengawas.',
                                            showConfirmButton: false,
                                            timer: 3500,
                                            background: '#0F1713',
                                            color: '#34D399',
                                            customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                                        });
                                    }
                                } else if (this.remainingSeconds !== null && Math.abs(this.remainingSeconds - newTime) > 60) {
                                    this.remainingSeconds = newTime;
                                }
                            }

                            // Sync remote lock state
                            if (data.is_locked !== undefined && data.is_locked !== this.isLocked) {
                                this.isLocked = data.is_locked;
                                if (!this.isLocked) {
                                    // Kunci dibuka dari dashboard pengawas (remote unlock)
                                    this.unlockImmunityUntil = Date.now() + 15000; // Imunitas 15 detik dari false-positive
                                    this.penaltySeconds = 0;
                                    if (data.tab_switch_count !== undefined) {
                                        this.tabSwitchCount = data.tab_switch_count;
                                        this.strike = Math.min(3, this.tabSwitchCount);
                                    }
                                    if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                                    if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                                    if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                                    if (!this.isFullscreen && !this.allowOpenBook) {
                                        this.enterFullscreen();
                                    }
                                    window.focus();
                                }
                            }

                            // Sync emergency global pause state
                            if (data.is_paused !== undefined) {
                                if (data.is_paused && !this.isExamPaused) {
                                    this.isExamPaused = true;
                                    if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                                    if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                                    if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                                } else if (!data.is_paused && this.isExamPaused) {
                                    this.isExamPaused = false;
                                    this.unlockImmunityUntil = Date.now() + 10000; // 10s immunity upon resume
                                    if (window.Swal) {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'success',
                                            title: 'Ujian dilanjutkan oleh pengawas!',
                                            showConfirmButton: false,
                                            timer: 3000,
                                            background: '#0F1713',
                                            color: '#34D399',
                                            customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                                        });
                                    }
                                }
                            }

                            // Sync announcements from exam supervisor
                            if (data.announcements && Array.isArray(data.announcements)) {
                                this.announcements = data.announcements;
                            }

                            if (data.latest_announcement && Number(data.latest_announcement.id) > this.lastSeenAnnouncementId) {
                                this.lastSeenAnnouncementId = Number(data.latest_announcement.id);
                                try {
                                    localStorage.setItem('quiz_seen_announcement_{{ $attempt->id }}', String(this.lastSeenAnnouncementId));
                                } catch (e) {}
                                this.activeAnnouncementModal = data.latest_announcement;
                                this.playAnnouncementChime(data.latest_announcement.type);
                            }
                        })
                        .catch(() => {});
                    };

                    // Polling status kuis tiap 3 detik (dengan variasi acak agar tidak bentrok)
                    this.sessionHeartbeatInterval = setInterval(poll, 3000 + Math.floor(Math.random() * 800));
                },

                playAnnouncementChime(type = 'info') {
                    try {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (!AudioCtx) return;
                        const ctx = new AudioCtx();
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }
                        const now = ctx.currentTime;
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();

                        if (type === 'urgent') {
                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(880, now);
                            osc.frequency.setValueAtTime(1046, now + 0.15);
                            osc.frequency.setValueAtTime(1318, now + 0.3);
                        } else if (type === 'warning') {
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(587.33, now); // D5
                            osc.frequency.setValueAtTime(880, now + 0.18); // A5
                        } else {
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(523.25, now); // C5
                            osc.frequency.setValueAtTime(659.25, now + 0.15); // E5
                        }

                        gain.gain.setValueAtTime(0.18, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.55);

                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(now);
                        osc.stop(now + 0.55);
                    } catch (e) {
                        // Audio context fallback
                    }
                },

                closeAnnouncementModal() {
                    this.activeAnnouncementModal = null;
                },

                handleSessionEnded(redirectUrl, message) {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    // Langsung hentikan seluruh timer dan scanner di browser
                    if (this.timerInterval) clearInterval(this.timerInterval);
                    if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                    if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                    if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                    if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
                    if (this.sessionHeartbeatInterval) clearInterval(this.sessionHeartbeatInterval);

                    const targetUrl = redirectUrl || '{{ route('quiz.results', [$quiz->unique_token, $attempt->id]) }}';
                    const noticeText = message || 'Sesi kuis telah diakhiri oleh pengawas lab. Jawaban Anda telah dikumpulkan dan dinilai secara otomatis.';

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Ujian Telah Selesai',
                            text: noticeText,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            timer: 2200,
                            background: '#0F1713',
                            color: '#F3F4F6',
                            customClass: { popup: 'border border-amber-500/40 rounded-2xl' }
                        }).then(() => {
                            window.location.href = targetUrl;
                        });
                    } else {
                        alert(noticeText);
                        window.location.href = targetUrl;
                    }
                },

                initPenaltyCountdown() {
                    if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                    this.isPenaltyPaused = false;
                    this.penaltyTimerInterval = setInterval(() => {
                        // Jeda sanksi jika tab di-minimize atau mahasiswa keluar dari jendela
                        if (document.hidden || !document.hasFocus()) {
                            this.isPenaltyPaused = true;
                            return; // Freeze! Jangan kurangi detik jika tab ditinggalkan
                        }
                        this.isPenaltyPaused = false;

                        if (this.penaltySeconds > 0) {
                            this.penaltySeconds--;
                        } else {
                            clearInterval(this.penaltyTimerInterval);
                            this.penaltyTimerInterval = null;
                            this.isPenaltyPaused = false;
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Waktu sanksi pembekuan berakhir. Silakan lanjutkan ujian.',
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#0F1713',
                                color: '#34D399',
                                customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                            });
                        }
                    }, 1000);
                },

                setupAntiCheat() {
                    // If exam is open book ("Bisa lihat modul"), do not enforce tab switch penalty!
                    if (this.allowOpenBook) {
                        return;
                    }

                    let lastReportedTime = 0;
                    this.reportViolation = (reason = 'tab_switch') => {
                        if (this.isLocked || this.isSubmitting || this.isEnteringFullscreen) return;

                        // Imunitas pasca-unlock atau saat kuis baru dimuat (cegah langsung terkunci berulang)
                        if (Date.now() < this.unlockImmunityUntil) {
                            return;
                        }

                        // Throttle triggers: untuk penalty_evasion atau alt_tab beri toleransi 1.5s, lainnya 2.5s
                        const now = Date.now();
                        const throttleMs = (reason === 'penalty_evasion' || reason === 'alt_tab_switch') ? 1500 : 2500;
                        if (now - lastReportedTime < throttleMs) return;
                        lastReportedTime = now;

                        // Bersihkan seluruh timer pending agar tidak memicu duplikat
                        if (this.focusLossTimer) { clearTimeout(this.focusLossTimer); this.focusLossTimer = null; }
                        if (this.visibilityLossTimer) { clearTimeout(this.visibilityLossTimer); this.visibilityLossTimer = null; }
                        if (this.penaltyEvasionTimer) { clearTimeout(this.penaltyEvasionTimer); this.penaltyEvasionTimer = null; }

                        fetch('{{ route('quiz.tab-switch', [$quiz->unique_token, $attempt->id]) }}', {
                            method: 'POST',
                            keepalive: true,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ reason: reason })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success || data.allow_open_book) return;

                            this.tabSwitchCount = data.tab_switch_count;
                            this.strike = data.strike;

                            if (data.is_locked) {
                                this.isLocked = true;
                                this.penaltySeconds = 0;
                                this.isPenaltyPaused = false;
                                if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                                if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                                if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                                if (this.penaltyEvasionTimer) clearTimeout(this.penaltyEvasionTimer);
                            } else if (data.penalty_seconds > 0) {
                                this.penaltySeconds = data.penalty_seconds;
                                this.initPenaltyCountdown();
                            } else if (data.tab_switch_count === 1) {
                                // Peringatan Pertama: Kartu Kuning (Layar TIDAK dibekukan)
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Terdeteksi Berpindah Layar!',
                                        text: data.message || 'Ini adalah kesempatan pertama Anda. Jika Anda berpindah sekali lagi, layar kuis akan DIBEKUKAN!',
                                        confirmButtonColor: '#F59E0B',
                                        confirmButtonText: 'Saya Mengerti, Lanjutkan Ujian',
                                        background: '#0F1713',
                                        color: '#F3F4F6',
                                        customClass: { popup: 'border border-amber-500/50 rounded-2xl' }
                                    });
                                } else {
                                    alert(data.message);
                                }
                            }
                        })
                        .catch(err => console.error('Violation record error:', err));
                    };

                    // Deteksi penekanan tombol Alt untuk menangkap jalan pintas Alt+Tab secara spesifik.
                    // Tombol Windows dan Ctrl sengaja tidak dideteksi agar tidak menghukum ketidaksengajaan.
                    let lastAltKeyTime = 0;
                    let blurStartTime = 0;
                    let recentQuickSwitches = [];

                    window.addEventListener('keydown', (e) => {
                        if (
                            e.key === 'Alt' ||
                            e.altKey ||
                            e.code === 'AltLeft' ||
                            e.code === 'AltRight' ||
                            (e.key === 'Tab' && e.altKey)
                        ) {
                            lastAltKeyTime = Date.now();
                        }
                    }, true);

                    // 1. Tab switch or window minimize (Visibility API)
                    document.addEventListener('visibilitychange', () => {
                        if (this.allowOpenBook || this.isLocked || this.isSubmitting || this.isEnteringFullscreen || Date.now() < this.unlockImmunityUntil) {
                            return;
                        }

                        if (document.hidden) {
                            const isAltTab = (Date.now() - lastAltKeyTime) < 1500;

                            // KASUS A: Mahasiswa sedang dalam masa sanksi pembekuan (penaltySeconds > 0)
                            if (this.penaltySeconds > 0) {
                                this.isPenaltyPaused = true;
                                if (isAltTab) {
                                    // Alt+Tab saat sedang dihukum -> Langsung eskalasi sanksi!
                                    this.reportViolation('penalty_evasion');
                                } else {
                                    // Hilang visibilitas pasif saat sanksi -> beri toleransi 2 detik
                                    if (this.penaltyEvasionTimer) clearTimeout(this.penaltyEvasionTimer);
                                    this.penaltyEvasionTimer = setTimeout(() => {
                                        if (document.hidden && this.penaltySeconds > 0 && !this.isLocked) {
                                            this.reportViolation('penalty_evasion');
                                        }
                                    }, 2000);
                                }
                                return;
                            }

                            // KASUS B: Ujian berjalan normal (penaltySeconds === 0)
                            if (isAltTab) {
                                // Tombol Alt ditekan sebelum tab hilang -> 100% Alt+Tab sengaja!
                                // Toleransi 0 detik (eksekusi seketika, celah Alt+Tab cepat ditutup)!
                                this.reportViolation('alt_tab_switch');
                                return;
                            }

                            // Hilang visibilitas pasif (tanpa tombol Alt) -> Toleransi 2.5 detik untuk cegah false-positive
                            if (blurStartTime === 0) blurStartTime = Date.now();
                            if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                            this.visibilityLossTimer = setTimeout(() => {
                                if (document.hidden && !this.allowOpenBook && !this.isLocked && this.penaltySeconds === 0 && !this.isSubmitting && !this.isEnteringFullscreen && Date.now() >= this.unlockImmunityUntil) {
                                    this.reportViolation('tab_switch');
                                }
                            }, 2500);
                        } else {
                            // Tab kembali terlihat aktif
                            if (this.visibilityLossTimer) {
                                clearTimeout(this.visibilityLossTimer);
                                this.visibilityLossTimer = null;
                            }
                            if (this.penaltyEvasionTimer) {
                                clearTimeout(this.penaltyEvasionTimer);
                                this.penaltyEvasionTimer = null;
                            }
                            if (this.penaltySeconds > 0 && document.hasFocus()) {
                                this.isPenaltyPaused = false;
                            }

                            // Deteksi micro-switch cepat berulang (akumulasi perpindahan cepat < 2.5s)
                            if (blurStartTime > 0 && this.penaltySeconds === 0) {
                                const awayDuration = Date.now() - blurStartTime;
                                blurStartTime = 0;
                                if (awayDuration >= 350 && awayDuration < 2500) {
                                    recentQuickSwitches = recentQuickSwitches.filter(t => (Date.now() - t) < 60000);
                                    recentQuickSwitches.push(Date.now());
                                    if (recentQuickSwitches.length >= 2) {
                                        this.reportViolation('rapid_window_switch');
                                        recentQuickSwitches = [];
                                    }
                                }
                            }
                        }
                    });

                    // 2. Window loss of focus (Blur event)
                    window.addEventListener('blur', () => {
                        if (this.allowOpenBook || this.isLocked || this.isSubmitting || this.isEnteringFullscreen || Date.now() < this.unlockImmunityUntil) {
                            return;
                        }

                        const isAltTab = (Date.now() - lastAltKeyTime) < 1500;

                        // KASUS A: Sedang dalam masa sanksi beku
                        if (this.penaltySeconds > 0) {
                            this.isPenaltyPaused = true;
                            if (isAltTab) {
                                this.reportViolation('penalty_evasion');
                            } else {
                                if (this.penaltyEvasionTimer) clearTimeout(this.penaltyEvasionTimer);
                                this.penaltyEvasionTimer = setTimeout(() => {
                                    if (!document.hasFocus() && this.penaltySeconds > 0 && !this.isLocked) {
                                        this.reportViolation('penalty_evasion');
                                    }
                                }, 2000);
                            }
                            return;
                        }

                        // KASUS B: Ujian normal
                        if (isAltTab) {
                            // Alt+Tab terdeteksi -> Langsung rekam tanpa ampun (0s grace period)
                            this.reportViolation('alt_tab_switch');
                            return;
                        }

                        // Kehilangan fokus pasif (OS / background process) -> Toleransi 2.5 detik
                        if (blurStartTime === 0) blurStartTime = Date.now();
                        if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                        this.focusLossTimer = setTimeout(() => {
                            if (!document.hasFocus() && !this.allowOpenBook && !this.isLocked && this.penaltySeconds === 0 && !this.isSubmitting && !this.isEnteringFullscreen && Date.now() >= this.unlockImmunityUntil) {
                                this.reportViolation('tab_switch');
                            }
                        }, 2500);
                    });

                    // 3. Focus recovery listener: Batalkan timer pelanggaran seketika saat kursor / jendela kembali fokus
                    window.addEventListener('focus', () => {
                        if (this.focusLossTimer) {
                            clearTimeout(this.focusLossTimer);
                            this.focusLossTimer = null;
                        }
                        if (this.visibilityLossTimer && !document.hidden) {
                            clearTimeout(this.visibilityLossTimer);
                            this.visibilityLossTimer = null;
                        }
                        if (this.penaltyEvasionTimer) {
                            clearTimeout(this.penaltyEvasionTimer);
                            this.penaltyEvasionTimer = null;
                        }
                        if (this.penaltySeconds > 0 && !document.hidden) {
                            this.isPenaltyPaused = false;
                        }

                        // Deteksi micro-switch cepat berulang lewat blur
                        if (blurStartTime > 0 && this.penaltySeconds === 0) {
                            const awayDuration = Date.now() - blurStartTime;
                            blurStartTime = 0;
                            if (awayDuration >= 350 && awayDuration < 2500) {
                                recentQuickSwitches = recentQuickSwitches.filter(t => (Date.now() - t) < 60000);
                                recentQuickSwitches.push(Date.now());
                                if (recentQuickSwitches.length >= 2) {
                                    this.reportViolation('rapid_window_switch');
                                    recentQuickSwitches = [];
                                }
                            }
                        }
                    });

                    // Anti-Cheat: Disable right click, copy, and devtools shortcuts in strict exam mode
                    document.addEventListener('contextmenu', (e) => {
                        if (!this.allowOpenBook) e.preventDefault();
                    });
                    document.addEventListener('copy', (e) => {
                        if (!this.allowOpenBook) e.preventDefault();
                    });
                    document.addEventListener('cut', (e) => {
                        if (!this.allowOpenBook) e.preventDefault();
                    });
                },

                submitUnlockPassword() {
                    if (!this.adminPassword.trim()) {
                        this.unlockError = 'Silakan masukkan password pengawas!';
                        return;
                    }
                    this.isUnlocking = true;
                    this.unlockError = '';

                    fetch('{{ route('quiz.unlock-password', [$quiz->unique_token, $attempt->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ admin_password: this.adminPassword })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        this.isUnlocking = false;
                        if (status === 200 && body.success) {
                            this.isLocked = false;
                            this.penaltySeconds = 0;
                            this.adminPassword = '';
                            this.tabSwitchCount = body.tab_switch_count || 2;
                            this.strike = 2;
                            this.unlockImmunityUntil = Date.now() + 15000; // Imunitas 15 detik dari false-positive setelah dibuka
                            this.isPenaltyPaused = false;
                            if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                            if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                            if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                            if (this.penaltyEvasionTimer) clearTimeout(this.penaltyEvasionTimer);
                            if (!this.isFullscreen && !this.allowOpenBook) {
                                this.enterFullscreen();
                            }
                            window.focus();
                            Swal.fire({
                                icon: 'success',
                                title: 'Kuis Berhasil Dibuka!',
                                text: body.message || 'Sesi ujian Anda telah diizinkan kembali oleh pengawas.',
                                background: '#0F1713',
                                color: '#f3f4f6',
                                confirmButtonColor: '#10B981',
                                confirmButtonText: 'Lanjutkan Ujian',
                                customClass: { popup: 'border border-emerald-500/40 rounded-2xl' }
                            });
                        } else {
                            this.unlockError = body.message || 'Password pengawas salah! Silakan coba lagi.';
                        }
                    })
                    .catch(() => {
                        this.isUnlocking = false;
                        this.unlockError = 'Terjadi gangguan jaringan saat memverifikasi password.';
                    });
                },

                setupKeyboardShortcuts() {
                    window.addEventListener('keydown', (e) => {
                        if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;

                        const key = e.key.toUpperCase();
                        if (['1', '2', '3', '4'].includes(key)) {
                            const optIdx = parseInt(key) - 1;
                            if (this.currentQuestion && this.currentQuestion.options[optIdx]) {
                                this.selectOption(this.currentQuestion.id, this.currentQuestion.options[optIdx].id);
                            }
                        } else if (['A', 'B', 'C', 'D'].includes(key)) {
                            const optIdx = key.charCodeAt(0) - 65;
                            if (this.currentQuestion && this.currentQuestion.options[optIdx]) {
                                this.selectOption(this.currentQuestion.id, this.currentQuestion.options[optIdx].id);
                            }
                        } else if (e.key === 'ArrowRight') {
                            this.nextQuestion();
                        } else if (e.key === 'ArrowLeft') {
                            this.prevQuestion();
                        }
                    });
                },

                setupNetworkAndUnloadGuards() {
                    // 1. Deteksi saat koneksi internet / WiFi lab kembali pulih (Prioritas 3)
                    window.addEventListener('online', () => {
                        this.handleNetworkRestored();
                    });

                    // 2. Deteksi saat koneksi internet / WiFi lab terputus
                    window.addEventListener('offline', () => {
                        this.syncStatus = 'offline';
                        if (window.Swal) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: 'Koneksi jaringan terputus. Jawaban tetap tersimpan di draf lokal perangkat Anda.',
                                showConfirmButton: false,
                                timer: 4000,
                                background: '#0F1713',
                                color: '#FBBF24',
                                customClass: { popup: 'border border-amber-500/40 rounded-xl' }
                            });
                        }
                    });

                    // 3. Proteksi Anti Tutup Tab / Back / Reload Tidak Sengaja (Prioritas 1)
                    // Push state ke history agar tombol Back browser tertahan
                    try {
                        history.pushState(null, document.title, location.href);
                        window.addEventListener('popstate', (e) => {
                            if (!this.isSubmitting && !this.isCompleted && !this.isLocked) {
                                history.pushState(null, document.title, location.href);
                                if (window.Swal) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'warning',
                                        title: 'Navigasi Back dinonaktifkan selama ujian berlangsung!',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        background: '#0F1713',
                                        color: '#FBBF24',
                                        customClass: { popup: 'border border-amber-500/40 rounded-xl' }
                                    });
                                }
                            }
                        });
                    } catch (e) {}

                    // beforeunload untuk tombol Close [X] browser, Ctrl+W, F5, atau Alt+F4
                    window.addEventListener('beforeunload', (e) => {
                        if (!this.isSubmitting && !this.isCompleted && !this.isLocked) {
                            e.preventDefault();
                            // Mengatur returnValue memunculkan dialog konfirmasi native browser
                            e.returnValue = 'Ujian sedang berlangsung! Apakah Anda yakin ingin meninggalkan atau memuat ulang halaman?';
                            return e.returnValue;
                        }
                    });
                },

                handleNetworkRestored() {
                    // Segera sinkronkan draf jawaban lokal ke server
                    this.triggerBackgroundSync(true);

                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Koneksi internet pulih. Draf jawaban telah berhasil disinkronisasi ke server!',
                            showConfirmButton: false,
                            timer: 3500,
                            background: '#0F1713',
                            color: '#34D399',
                            customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                        });
                    }
                },

                selectOption(questionId, optionId) {
                    if (this.currentQuestion && this.currentQuestion.question_type === 'multi_select') {
                        let current = this.answers[questionId];
                        if (!Array.isArray(current)) {
                            current = current ? [current] : [];
                        }
                        const idx = current.indexOf(optionId);
                        if (idx > -1) {
                            current.splice(idx, 1);
                        } else {
                            current.push(optionId);
                        }
                        this.answers[questionId] = current;
                    } else {
                        this.answers[questionId] = optionId;
                    }
                    this.saveLocalCache();
                    this.triggerBackgroundSync();
                },

                isSelected(questionId, optionId) {
                    const val = this.answers[questionId];
                    if (Array.isArray(val)) {
                        return val.includes(optionId);
                    }
                    return val === optionId;
                },

                toggleFlagQuestion(questionId) {
                    const qIdStr = String(questionId);
                    const idx = this.flaggedQuestions.indexOf(qIdStr);
                    if (idx > -1) {
                        this.flaggedQuestions.splice(idx, 1);
                    } else {
                        this.flaggedQuestions.push(qIdStr);
                    }
                    this.answers['__flagged'] = [...this.flaggedQuestions];
                    this.saveLocalCache();
                    this.triggerBackgroundSync();
                },

                isFlagged(questionId) {
                    return this.flaggedQuestions.includes(String(questionId));
                },

                flaggedCount() {
                    return this.flaggedQuestions.length;
                },

                isQuestionAnswered(questionId) {
                    if (questionId === '__flagged') return false;
                    const val = this.answers[questionId];
                    if (Array.isArray(val)) {
                        return val.length > 0 && val.some(item => item !== undefined && item !== null && String(item).trim() !== '');
                    }
                    return val !== undefined && val !== null && val !== '';
                },

                getFillBlankParts(question) {
                    if (!question || question.question_type !== 'fill_blank') {
                        return { preamble: '', sentence: question ? question.question_text : '' };
                    }
                    const text = question.question_text || '';
                    const codeBlockMatch = text.match(/^([\s\S]*?```[\s\S]*?```)\s*([\s\S]*)$/);
                    if (codeBlockMatch) {
                        return {
                            preamble: codeBlockMatch[1].trim(),
                            sentence: codeBlockMatch[2].trim()
                        };
                    }
                    const lines = text.split('\n');
                    const blankLineIndex = lines.findIndex(l => l.includes('[___]'));
                    if (blankLineIndex > 0) {
                        return {
                            preamble: lines.slice(0, blankLineIndex).join('\n').trim(),
                            sentence: lines.slice(blankLineIndex).join('\n').trim()
                        };
                    }
                    return { preamble: '', sentence: text };
                },

                getTopQuestionDisplay(question) {
                    if (!question) return '';
                    if (question.question_type !== 'fill_blank') {
                        return question.question_text;
                    }
                    const parts = this.getFillBlankParts(question);
                    return parts.preamble || '';
                },

                getBlankSegments(sentence) {
                    if (!sentence) return [''];
                    return sentence.split('[___]');
                },

                getBlankAnswer(questionId, blankIdx) {
                    const list = this.answers[questionId];
                    if (Array.isArray(list) && list[blankIdx] !== undefined) {
                        return list[blankIdx];
                    }
                    return '';
                },

                setBlankAnswer(questionId, blankIdx, val) {
                    if (!Array.isArray(this.answers[questionId])) {
                        const q = this.questions.find(item => item.id === questionId);
                        const count = q ? (q.blanks_count || 1) : (blankIdx + 1);
                        this.answers[questionId] = new Array(count).fill('');
                    }
                    this.answers[questionId][blankIdx] = val;
                    this.saveLocalCache();
                    this.triggerBackgroundSync();
                },

                focusNextBlank(event, currentIdx) {
                    const parentContainer = event.target.closest('.space-y-4');
                    if (!parentContainer) return;
                    const inputs = Array.from(parentContainer.querySelectorAll('.inline-blank-input'));
                    const nextInput = inputs[currentIdx + 1];
                    if (nextInput) {
                        nextInput.focus();
                    } else {
                        this.nextQuestion();
                    }
                },

                renderRichTextInline(raw) {
                    if (!raw) return '';
                    return this.renderRichText(raw);
                },

                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                        this.$nextTick(() => { if (window.Prism) Prism.highlightAll(); });
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                        this.$nextTick(() => { if (window.Prism) Prism.highlightAll(); });
                    }
                },

                jumpToQuestion(index) {
                    this.currentIndex = index;
                    this.showDrawer = false;
                    this.$nextTick(() => { if (window.Prism) Prism.highlightAll(); });
                },

                answeredCount() {
                    return this.questions.filter(q => this.isQuestionAnswered(q.id)).length;
                },

                renderRichText(raw) {
                    if (!raw) return '';
                    const codeBlocks = [];
                    let text = String(raw);

                    // 1. Extract and protect code blocks ```lang ... ```
                    text = text.replace(/```([a-zA-Z0-9_\-\+#]*)\r?\n?([\s\S]*?)```/g, (match, lang, code) => {
                        const cleanLang = (lang || 'markup').trim().toLowerCase();
                        const placeholder = `___CODE_BLOCK_${codeBlocks.length}___`;
                        
                        const escEl = document.createElement('div');
                        escEl.textContent = code.trim();
                        const escCode = escEl.innerHTML;

                        codeBlocks.push(
                            `<div class="my-3 rounded-xl overflow-hidden border border-[#1A2820] bg-[#070b09] text-left">` +
                            `<div class="px-3 py-1 bg-[#101713] border-b border-[#1A2820] text-[10px] font-mono text-gray-400 uppercase tracking-wider flex items-center justify-between">` +
                            `<span class="text-emerald-400 font-bold">${cleanLang || 'code'}</span>` +
                            `<span class="text-gray-500 text-[9px]">Syntax Highlighted</span>` +
                            `</div>` +
                            `<pre class="language-${cleanLang} !bg-transparent !p-3 !m-0 text-xs sm:text-sm font-mono overflow-x-auto"><code class="language-${cleanLang}">${escCode}</code></pre>` +
                            `</div>`
                        );
                        return placeholder;
                    });

                    // 2. Extract and protect inline code `...`
                    const inlineBlocks = [];
                    text = text.replace(/`([^`\n\r]+)`/g, (match, code) => {
                        const placeholder = `___INLINE_CODE_${inlineBlocks.length}___`;
                        const escEl = document.createElement('div');
                        escEl.textContent = code;
                        inlineBlocks.push(`<code class="px-1.5 py-0.5 rounded bg-[#16221B] border border-[#23382C] text-emerald-300 font-mono text-xs sm:text-sm">${escEl.innerHTML}</code>`);
                        return placeholder;
                    });

                    // 3. Escape normal text
                    const escapeEl = document.createElement('div');
                    escapeEl.textContent = text;
                    let safe = escapeEl.innerHTML;

                    // 4. Convert single newlines to <br>
                    safe = safe.replace(/\n/g, '<br>');

                    // 5. Restore inline code
                    inlineBlocks.forEach((html, i) => {
                        safe = safe.replace(`___INLINE_CODE_${i}___`, html);
                    });

                    // 6. Restore code blocks
                    codeBlocks.forEach((html, i) => {
                        safe = safe.replace(`___CODE_BLOCK_${i}___`, html);
                    });

                    return safe;
                },

                confirmSubmit() {
                    const answered = this.answeredCount();
                    const total = this.questions.length;
                    const unanswered = total - answered;
                    const flagged = this.flaggedCount();

                    let message = `Anda telah menjawab ${answered} dari ${total} pertanyaan.`;
                    if (flagged > 0) {
                        message += `\n⚠️ Anda masih menandai ${flagged} soal sebagai RAGU-RAGU.`;
                    }
                    if (unanswered > 0) {
                        message += `\n⚠️ Terdapat ${unanswered} pertanyaan yang belum dijawab.`;
                    } else if (flagged === 0) {
                        message += `\nSemua pertanyaan telah terisi.`;
                    }
                    message += `\n\nYakin ingin mengumpulkan ujian sekarang?`;

                    Swal.fire({
                        title: 'Kumpulkan Jawaban?',
                        text: message,
                        icon: (unanswered > 0 || flagged > 0) ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#1A2820',
                        confirmButtonText: 'Ya, Kumpulkan Sekarang',
                        cancelButtonText: 'Periksa Kembali',
                        background: '#0F1713',
                        color: '#f3f4f6',
                        customClass: {
                            popup: 'border border-[#1A2820] rounded-2xl',
                            confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold',
                            cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submitQuiz();
                        }
                    });
                },

                submitQuiz() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;
                    if (this.focusLossTimer) clearTimeout(this.focusLossTimer);
                    if (this.visibilityLossTimer) clearTimeout(this.visibilityLossTimer);
                    if (this.penaltyEvasionTimer) clearTimeout(this.penaltyEvasionTimer);
                    if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                    if (this.fullscreenToleranceInterval) clearInterval(this.fullscreenToleranceInterval);
                    if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);

                    if (window.showLoading) {
                        window.showLoading('MENGIRIM JAWABAN...', 'Sistem sedang menghitung akurasi dan skor evaluasi...');
                    }

                    fetch('{{ route('quiz.submit', [$quiz->unique_token, $attempt->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ answers: this.answers })
                    })
                    .then(res => {
                        if (res.status === 419) {
                            return res.json().then(data => {
                                if (data && data.csrf_token) {
                                    const meta = document.querySelector('meta[name="csrf-token"]');
                                    if (meta) meta.content = data.csrf_token;
                                }
                                throw new Error('Sesi keamanan sempat terputus karena padatnya koneksi server. Token telah diperbarui otomatis, silakan klik tombol Kirim Jawaban sekali lagi.');
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        try {
                            localStorage.removeItem('cbt_quiz_{{ $attempt->id }}_answers');
                            localStorage.removeItem('cbt_quiz_{{ $attempt->id }}_last_save');
                        } catch (e) {}

                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch((err) => {
                        this.isSubmitting = false;
                        if (window.hideLoading) window.hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Pemberitahuan Sistem',
                            text: (err && err.message) ? err.message : 'Terjadi gangguan koneksi internet. Silakan coba lagi.',
                            background: '#0F1713',
                            color: '#f3f4f6'
                        });
                    });
                },

                autoSubmitExpired() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Waktu Pengerjaan Habis!',
                        text: 'Sistem mengumpulkan jawaban Anda secara otomatis.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 2000,
                        background: '#0F1713',
                        color: '#f3f4f6'
                    }).then(() => {
                        this.submitQuiz();
                    });
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        this.enterFullscreen();
                    } else {
                        if (!this.allowOpenBook) {
                            Swal.fire({
                                title: 'Keluar Layar Penuh?',
                                text: 'Ujian ini mewajibkan mode layar penuh. Keluar dari layar penuh akan memicu peringatan toleransi 5 detik.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Tetap di Layar Penuh',
                                cancelButtonText: 'Keluar (Beresiko)',
                                confirmButtonColor: '#10B981',
                                cancelButtonColor: '#1A2820',
                                background: '#0F1713',
                                color: '#f3f4f6'
                            }).then((res) => {
                                if (res.dismiss === Swal.DismissReason.cancel) {
                                    if (document.exitFullscreen) document.exitFullscreen();
                                }
                            });
                        } else {
                            if (document.exitFullscreen) document.exitFullscreen();
                        }
                    }
                },

                formatTime(totalSec) {
                    if (totalSec === null || totalSec === undefined || isNaN(totalSec)) return '00:00:00';
                    const secInt = Math.max(0, Math.floor(Number(totalSec)));
                    const hours = Math.floor(secInt / 3600);
                    const minutes = Math.floor((secInt % 3600) / 60);
                    const seconds = secInt % 60;

                    const pad = (n) => String(n).padStart(2, '0');
                    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                },

                getOptionLabel(idx) {
                    return ['A', 'B', 'C', 'D', 'E', 'F'][idx] || (idx + 1);
                },

                getOptionClasses(idx, selected) {
                    if (selected) {
                        return 'border-emerald-400 bg-emerald-500/20 shadow-lg shadow-emerald-500/10 ring-2 ring-emerald-500/40';
                    }
                    const palettes = [
                        'bg-[#0F1713] border-emerald-500/30 hover:border-emerald-500/70',
                        'bg-[#0F1713] border-sky-500/30 hover:border-sky-500/70',
                        'bg-[#0F1713] border-amber-500/30 hover:border-amber-500/70',
                        'bg-[#0F1713] border-purple-500/30 hover:border-purple-500/70'
                    ];
                    return palettes[idx % 4] || 'bg-[#0F1713] border-[#1A2820]';
                },

                getOptionBadgeClasses(idx, selected) {
                    if (selected) {
                        return 'bg-emerald-500 text-white';
                    }
                    const badgePalettes = [
                        'bg-emerald-500/15 text-emerald-400',
                        'bg-sky-500/15 text-sky-400',
                        'bg-amber-500/15 text-amber-400',
                        'bg-purple-500/15 text-purple-400'
                    ];
                    return badgePalettes[idx % 4] || 'bg-gray-800 text-gray-300';
                }
            };
        }
        window.quizArena = quizArena;
        document.addEventListener('alpine:init', () => {
            if (window.Alpine && typeof Alpine.data === 'function') {
                Alpine.data('quizArena', quizArena);
            }
        });
    </script>

    {{-- ─── UNIFIED CYBER LOADING POPUP COMPONENT ─── --}}
    @include('components.cyber-loading')

    <script>
    // ─── High-End Cyber Constellation & Ambient Cursor Engine (Identik Serentak dengan Halaman Soal & Ruang Tunggu) ───
    (function() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const ambientCanvas = document.querySelector('.soal-ambient-canvas');
        const constCanvas = document.getElementById('ambientConstellationCanvas');
        if (!constCanvas) return;

        const ctx = constCanvas.getContext('2d');
        if (!ctx) return;

        let width = 0;
        let height = 0;
        let dpr = 1;
        let particles = [];
        const particleCount = 42;
        const maxDistance = 120;
        let mouse = { x: null, y: null, targetX: null, targetY: null, maxDist: 140 };
        let animationId = null;

        function isDark() {
            return document.documentElement.classList.contains('dark') ||
                   document.documentElement.getAttribute('data-theme') === 'dark';
        }

        function resize() {
            dpr = Math.min(window.devicePixelRatio || 1, 2);
            width = window.innerWidth;
            height = window.innerHeight;
            constCanvas.width = width * dpr;
            constCanvas.height = height * dpr;
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(dpr, dpr);
        }

        function initParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.4,
                    vy: (Math.random() - 0.5) * 0.4,
                    radius: Math.random() * 2 + 1.2,
                    colorType: Math.random() > 0.4 ? 'emerald' : 'cyan',
                    pulse: Math.random() * Math.PI * 2
                });
            }
        }

        function render() {
            ctx.clearRect(0, 0, width, height);
            const dark = isDark();

            const colorEmerald = dark ? 'rgba(52, 211, 153,' : 'rgba(5, 150, 105,';
            const colorCyan = dark ? 'rgba(56, 189, 248,' : 'rgba(2, 132, 199,';
            const lineBase = dark ? '52, 211, 153' : '16, 185, 129';

            // Update mouse position smoothly
            if (mouse.targetX !== null && mouse.targetY !== null) {
                if (mouse.x === null) {
                    mouse.x = mouse.targetX;
                    mouse.y = mouse.targetY;
                } else {
                    mouse.x += (mouse.targetX - mouse.x) * 0.12;
                    mouse.y += (mouse.targetY - mouse.y) * 0.12;
                }

                if (ambientCanvas) {
                    ambientCanvas.style.setProperty('--cursor-x', mouse.x.toFixed(1) + 'px');
                    ambientCanvas.style.setProperty('--cursor-y', mouse.y.toFixed(1) + 'px');
                }
            }

            // Render particles and connections
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.x += p.vx;
                p.y += p.vy;
                p.pulse += 0.025;

                // Screen wrap
                if (p.x < -10) p.x = width + 10;
                else if (p.x > width + 10) p.x = -10;
                if (p.y < -10) p.y = height + 10;
                else if (p.y > height + 10) p.y = -10;

                const baseAlpha = dark ? 0.65 : 0.45;
                const dynamicAlpha = Math.max(0.2, baseAlpha + Math.sin(p.pulse) * 0.25);
                const pColor = (p.colorType === 'emerald' ? colorEmerald : colorCyan) + dynamicAlpha + ')';

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = pColor;
                if (dark) {
                    ctx.shadowColor = p.colorType === 'emerald' ? '#10B981' : '#06B6D4';
                    ctx.shadowBlur = 8;
                } else {
                    ctx.shadowBlur = 0;
                }
                ctx.fill();

                // Connect lines between nearby particles
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x;
                    const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < maxDistance) {
                        const lineAlpha = (1 - dist / maxDistance) * (dark ? 0.22 : 0.12);
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(${lineBase}, ${lineAlpha})`;
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }

                // Connect to mouse cursor
                if (mouse.x !== null && mouse.y !== null) {
                    const mdx = p.x - mouse.x;
                    const mdy = p.y - mouse.y;
                    const mdist = Math.sqrt(mdx * mdx + mdy * mdy);

                    if (mdist < mouse.maxDist) {
                        const mLineAlpha = (1 - mdist / mouse.maxDist) * (dark ? 0.35 : 0.2);
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.strokeStyle = `rgba(${lineBase}, ${mLineAlpha})`;
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }

            animationId = requestAnimationFrame(render);
        }

        window.addEventListener('resize', () => {
            resize();
            initParticles();
        });

        window.addEventListener('mousemove', (e) => {
            mouse.targetX = e.clientX;
            mouse.targetY = e.clientY;
        }, { passive: true });

        window.addEventListener('mouseleave', () => {
            mouse.targetX = null;
            mouse.targetY = null;
            mouse.x = null;
            mouse.y = null;
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (animationId) cancelAnimationFrame(animationId);
            } else {
                animationId = requestAnimationFrame(render);
            }
        });

        resize();
        initParticles();
        animationId = requestAnimationFrame(render);
    })();
    </script>
</body>
</html>
