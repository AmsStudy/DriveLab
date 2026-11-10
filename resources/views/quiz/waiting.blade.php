<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Tunggu Ujian: {{ $quiz->title }} &mdash; IC-Labs</title>
    <x-theme-head-script />
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Orbitron:wght@700;800;900&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #090D0B;
            --card: #0F1713;
            --border: #1A2820;
            --emerald: #10B981;
            --emerald-glow: rgba(16, 185, 129, 0.2);
        }
        html:not(.dark) {
            --bg: #F4F7F5;
            --card: #FFFFFF;
            --border: #E2EAE5;
            --emerald: #059669;
            --emerald-glow: rgba(16, 185, 129, 0.15);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #f3f4f6;
            min-height: 100vh;
            overflow-x: hidden;
        }
        html:not(.dark) body {
            color: #0F1F17 !important;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.08), transparent 70%), rgba(15, 23, 19, 0.90) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 15px 45px -10px rgba(0, 0, 0, 0.75), 0 0 30px rgba(16, 185, 129, 0.08) !important;
        }
        html:not(.dark) .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.06), transparent 70%), rgba(255, 255, 255, 0.94) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 12px 35px -8px rgba(0, 0, 0, 0.06), 0 0 25px rgba(16, 185, 129, 0.05) !important;
        }

        /* ─── Cyber Quantum Reactor Spinner (Ultra "Wah" & Futuristik) ─── */
        @keyframes orbitCW {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes orbitCCW {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes pulseGlowWave {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 15px rgba(16, 185, 129, 0.35), inset 0 0 10px rgba(16, 185, 129, 0.2);
            }
            50% {
                transform: scale(1);
                box-shadow: 0 0 32px rgba(52, 211, 153, 0.7), 0 0 55px rgba(16, 185, 129, 0.4), inset 0 0 18px rgba(52, 211, 153, 0.4);
            }
        }
        @keyframes floatingOrb {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(180deg); }
        }

        .quantum-reactor {
            position: relative;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
        }

        /* Ring 1 - Outermost Multi-Segment Ring with Glowing Orbits */
        .reactor-ring-outer {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px dashed rgba(16, 185, 129, 0.25);
            animation: orbitCW 18s linear infinite;
        }
        .reactor-ring-outer::before {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #34D399;
            box-shadow: 0 0 12px #34D399, 0 0 20px #10B981;
        }
        .reactor-ring-outer::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #06B6D4;
            box-shadow: 0 0 12px #06B6D4;
        }

        /* Ring 2 - Tech Circuit Gradient Ring */
        .reactor-ring-mid {
            position: absolute;
            inset: 14px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top: 3px solid #10B981;
            border-right: 3px solid #34D399;
            border-bottom: 3px solid transparent;
            border-left: 3px solid rgba(16, 185, 129, 0.3);
            filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.6));
            animation: orbitCW 3.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Ring 3 - Counter Rotating Segment */
        .reactor-ring-inner {
            position: absolute;
            inset: 28px;
            border-radius: 50%;
            border: 2.5px solid transparent;
            border-bottom: 2.5px solid #06B6D4;
            border-left: 2.5px solid #10B981;
            filter: drop-shadow(0 0 8px rgba(6, 182, 212, 0.5));
            animation: orbitCCW 2.4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Ring 4 - Ambient Particle Track */
        .reactor-ring-dotted {
            position: absolute;
            inset: 40px;
            border-radius: 50%;
            border: 2px dotted rgba(52, 211, 153, 0.45);
            animation: orbitCW 7s linear infinite;
        }

        /* Center Glowing Core */
        .reactor-core {
            position: relative;
            width: 58px;
            height: 58px;
            border-radius: 20px;
            background: radial-gradient(circle at 35% 35%, #10B981 0%, #064E3B 100%);
            border: 2px solid rgba(52, 211, 153, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            animation: pulseGlowWave 2s ease-in-out infinite alternate;
        }
        html:not(.dark) .reactor-core {
            background: radial-gradient(circle at 35% 35%, #059669 0%, #047857 100%);
            border: 2px solid rgba(16, 185, 129, 0.6);
            box-shadow: 0 8px 24px rgba(5, 150, 105, 0.35);
        }

        /* Light Mode Specific Overrides for Reactor Rings */
        html:not(.dark) .reactor-ring-outer {
            border-color: rgba(5, 150, 105, 0.3);
        }
        html:not(.dark) .reactor-ring-outer::before {
            background: #059669;
            box-shadow: 0 0 10px rgba(5, 150, 105, 0.7);
        }
        html:not(.dark) .reactor-ring-outer::after {
            background: #0891B2;
            box-shadow: 0 0 10px rgba(8, 145, 178, 0.7);
        }
        html:not(.dark) .reactor-ring-mid {
            border-top-color: #059669;
            border-right-color: #10B981;
            border-left-color: rgba(5, 150, 105, 0.35);
            filter: drop-shadow(0 2px 8px rgba(5, 150, 105, 0.4));
        }
        html:not(.dark) .reactor-ring-inner {
            border-bottom-color: #0891B2;
            border-left-color: #059669;
            filter: drop-shadow(0 2px 6px rgba(8, 145, 178, 0.35));
        }
        html:not(.dark) .reactor-ring-dotted {
            border-color: rgba(5, 150, 105, 0.4);
        }

        /* Direct Styling for Instruction Cards (Immune to Tailwind cascade / cache bugs) */
        .waiting-rule-box {
            background-color: #F9FAFB !important;
            border: 1px solid #E5E7EB !important;
            transition: all 0.2s ease !important;
        }
        .waiting-rule-box:hover {
            border-color: rgba(16, 185, 129, 0.5) !important;
            background-color: #F0FDF4 !important;
        }
        .waiting-rule-title {
            color: #111827 !important;
        }
        .waiting-rule-desc {
            color: #4B5563 !important;
        }

        html.dark .waiting-rule-box,
        html[data-theme="dark"] .waiting-rule-box {
            background-color: #0D1612 !important;
            border: 1px solid #1E3327 !important;
        }
        html.dark .waiting-rule-box:hover,
        html[data-theme="dark"] .waiting-rule-box:hover {
            border-color: rgba(16, 185, 129, 0.45) !important;
            background-color: #13221A !important;
        }
        html.dark .waiting-rule-title,
        html[data-theme="dark"] .waiting-rule-title {
            color: #F9FAFB !important;
        }
        html.dark .waiting-rule-desc,
        html[data-theme="dark"] .waiting-rule-desc {
            color: #9CA3AF !important;
        }

        /* Dynamic Digital Equalizer / Activity Bars */
        .audio-wave-bar {
            width: 3px;
            border-radius: 9999px;
            background: #10B981;
            animation: audioWave 1.2s ease-in-out infinite alternate;
        }
        html:not(.dark) .audio-wave-bar {
            background: #059669;
        }
        @keyframes audioWave {
            0% { height: 6px; opacity: 0.4; }
            100% { height: 22px; opacity: 1; }
        }

        /* Background Moving Gradient Orbs */
        /* ─── ULTRA "WAH" AMBIENT CYBER BACKGROUND (SERENTAK DENGAN HALAMAN SOAL) ─── */
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
            top: 40%;
            left: -120px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.16) 0%, rgba(99, 102, 241, 0.08) 55%, transparent 75%);
            animation: auroraMorph3 22s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-aurora-blob.blob-3 {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(99, 102, 241, 0.06) 55%, transparent 75%);
        }

        .soal-aurora-blob.blob-4 {
            width: 720px;
            height: 720px;
            bottom: -160px;
            right: -140px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, rgba(6, 182, 212, 0.12) 50%, transparent 75%);
            animation: auroraMorph4 32s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-aurora-blob.blob-4 {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(6, 182, 212, 0.09) 50%, transparent 75%);
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

        /* Cinematic Cyber Light Shafts / Rays */
        .soal-light-shaft {
            position: absolute;
            top: -20vh;
            width: 440px;
            height: 130vh;
            pointer-events: none;
            filter: blur(55px);
            -webkit-filter: blur(55px);
            opacity: 0.55;
            will-change: opacity, transform;
        }
        .soal-light-shaft.shaft-left {
            left: -100px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.18) 0%, rgba(6, 182, 212, 0.08) 50%, transparent 80%);
            transform: rotate(-24deg);
            animation: shaftPulse1 12s ease-in-out infinite alternate;
        }
        .soal-light-shaft.shaft-right {
            right: -100px;
            background: linear-gradient(225deg, rgba(6, 182, 212, 0.18) 0%, rgba(16, 185, 129, 0.08) 50%, transparent 80%);
            transform: rotate(24deg);
            animation: shaftPulse2 14s ease-in-out infinite alternate;
        }
        html:not(.dark) .soal-light-shaft.shaft-left {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.14) 0%, rgba(6, 182, 212, 0.06) 50%, transparent 80%);
        }
        html:not(.dark) .soal-light-shaft.shaft-right {
            background: linear-gradient(225deg, rgba(6, 182, 212, 0.14) 0%, rgba(16, 185, 129, 0.06) 50%, transparent 80%);
        }

        @keyframes shaftPulse1 {
            0% { opacity: 0.35; transform: rotate(-24deg) translateY(0); }
            100% { opacity: 0.65; transform: rotate(-22deg) translateY(20px); }
        }
        @keyframes shaftPulse2 {
            0% { opacity: 0.35; transform: rotate(24deg) translateY(0); }
            100% { opacity: 0.65; transform: rotate(22deg) translateY(20px); }
        }

        /* Cyber Matrix Grid */
        .soal-ambient-grid {
            position: absolute;
            inset: 0;
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
    </style>
</head>
<body class="flex flex-col min-h-screen selection:bg-emerald-500 selection:text-white relative"
      x-data="studentWaitingRoom({{ $quiz->id }}, {{ $attempt->id }}, '{{ $quiz->unique_token }}')"
      x-init="initWaiting()">

    {{-- ─── AMBIENT CYBER BACKGROUND (Identik Serentak dengan Halaman Soal) ─── --}}
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

    {{-- Top Navbar --}}
    <header class="border-b border-gray-200 dark:border-[#1A2820] bg-white/80 dark:bg-[#090D0B]/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-wider leading-none block text-gray-900 dark:text-white" style="font-family:'Orbitron',sans-serif;">IC-LABS</span>
                    <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-semibold tracking-widest block mt-0.5" style="font-family:'Chakra Petch',sans-serif;">RUANG TUNGGU UJIAN</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#142019] border border-gray-200 dark:border-[#1E3327] text-gray-700 dark:text-gray-300 font-mono text-xs font-bold">
                    Kode: <strong class="text-emerald-600 dark:text-emerald-400">{{ $quiz->access_code }}</strong>
                </span>
                <x-theme-switcher :compact="true" />
            </div>
        </div>
    </header>

    {{-- Starting Overlay Animation (When admin clicks Start) --}}
    <div x-show="isStarting"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 bg-[#090D0B]/95 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center">
        <div class="w-20 h-20 rounded-3xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 mb-6 shadow-2xl shadow-emerald-500/30 animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </div>
        <h2 class="text-3xl font-extrabold text-white heading-font mb-3">UJIAN TELAH DIMULAI!</h2>
        <p class="text-emerald-400 text-sm font-semibold max-w-md mx-auto mb-6">
            Pengawas telah membuka akses soal. Mengalihkan Anda langsung ke lembar ujian, mohon bersiap...
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-mono text-xs animate-pulse">
            <svg class="w-4 h-4 animate-spin text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Menyiapkan Lembar Soal...</span>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 py-8 md:py-12 flex flex-col justify-center relative z-10">

        <div class="space-y-6 relative z-10">

            {{-- Main Hero Waiting Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 sm:p-10 glow-card relative overflow-hidden text-center shadow-xl">
                {{-- Decorative Ambient Glows inside card --}}
                <div class="absolute -right-24 -top-24 w-80 h-80 bg-emerald-500/15 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-24 -bottom-24 w-80 h-80 bg-teal-500/10 dark:bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Status Chip with Glowing Aura --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-8 shadow-xs relative">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Ruang Tunggu Ujian Aktif</span>
                </div>

                {{-- ─── QUANTUM REACTOR SPINNER ("WAH" & FUTURISTIC ANIMATION) ─── --}}
                <div class="quantum-reactor">
                    {{-- Outer dashed counter-spinning orbit ring with twin satellites --}}
                    <div class="reactor-ring-outer"></div>

                    {{-- Middle cyber glowing arc ring --}}
                    <div class="reactor-ring-mid"></div>

                    {{-- Inner high-speed counter-rotating cyan/emerald ring --}}
                    <div class="reactor-ring-inner"></div>

                    {{-- Particle dotted track --}}
                    <div class="reactor-ring-dotted"></div>

                    {{-- Center Pulsing Glowing Core with Tech Icon --}}
                    <div class="reactor-core shadow-2xl">
                        <svg class="w-7 h-7 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Interactive Live Radar Sweep Signal Bar --}}
                <div class="flex items-center justify-center gap-1.5 mb-5 opacity-80">
                    <span class="audio-wave-bar" style="animation-delay: 0.1s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.3s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.5s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.2s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.4s;"></span>
                    <span class="text-[11px] font-mono font-bold tracking-widest uppercase text-emerald-600 dark:text-emerald-400 mx-2">
                        STANDBY &bull; SYNCING
                    </span>
                    <span class="audio-wave-bar" style="animation-delay: 0.2s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.4s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.1s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.5s;"></span>
                    <span class="audio-wave-bar" style="animation-delay: 0.3s;"></span>
                </div>

                {{-- Quiz Title --}}
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white heading-font mb-3 tracking-tight">
                    {{ $quiz->title }}
                </h1>

                {{-- Status Descriptions --}}
                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto mb-6 leading-relaxed">
                    Data Anda telah terverifikasi dan siap mengikuti ujian laboratorium.
                    <span class="text-emerald-700 dark:text-emerald-400 font-semibold block mt-1.5 text-xs sm:text-sm">
                        Halaman ini akan otomatis beralih sendiri ke lembar soal saat pengawas menekan tombol <strong>Mulai</strong>. Anda tidak perlu me-refresh browser.
                    </span>
                </p>

                {{-- Waiting Room Live Participants Pill with Pulse --}}
                <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-emerald-50/90 dark:bg-[#090D0B] border border-emerald-300 dark:border-[#1E3327] shadow-xs mb-8">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600 dark:bg-emerald-500"></span>
                    </span>
                    <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">Peserta di Ruang Tunggu:</span>
                    <span class="font-mono font-bold text-sm text-emerald-800 dark:text-emerald-400" x-text="waitingCount + ' Mahasiswa'">{{ $waitingCount }} Mahasiswa</span>
                </div>

                {{-- Student Identity Info Card --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-xl mx-auto pt-6 border-t border-gray-200 dark:border-[#1A2820] text-left">
                    <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">Nama Mahasiswa</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white mt-0.5 block truncate">{{ $attempt->student_name }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">NIM</span>
                        <span class="text-xs font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-0.5 block">{{ $attempt->nim }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] hover:border-emerald-500/40 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">Kelas & Status</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-gray-200 mt-0.5 block">
                            {{ $attempt->kelas ? 'Kelas ' . $attempt->kelas : 'Reguler' }} &bull; <span class="text-emerald-700 dark:text-emerald-400 font-bold">Siap</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Exam Integrity & Instructions Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-gray-100 dark:border-[#1A2820]">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-sm text-amber-600 dark:text-amber-400">
                        💡
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Petunjuk Selama Menunggu</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 border border-emerald-300 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            ⏱️
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Hitungan Mundur Waktu</strong>
                            <span class="waiting-rule-desc">Waktu ujian ({{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' menit' : 'tanpa batas' }}) akan mulai berjalan serentak begitu pengawas menekan tombol Mulai.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/15 border border-rose-300 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            🛡️
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Sistem Anti-Cheat CBT</strong>
                            <span class="waiting-rule-desc">Dilarang berpindah tab, membuka jendela lain, atau keluar dari layar penuh guna mencegah sanksi pembekuan dan penguncian ujian otomatis.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-500/15 border border-teal-300 dark:border-teal-500/30 text-teal-700 dark:text-teal-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            💾
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Penyimpanan Otomatis</strong>
                            <span class="waiting-rule-desc">Setiap pilihan jawaban otomatis tersimpan di cache lokal browser dan disinkronkan ke server pengawas secara berkala.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl waiting-rule-box transition-all">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-500/15 border border-amber-300 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 flex items-center justify-center flex-shrink-0 text-xs font-bold shadow-xs">
                            🔔
                        </div>
                        <div class="leading-relaxed">
                            <strong class="waiting-rule-title block mb-0.5">Kontrol Penuh Pengawas</strong>
                            <span class="waiting-rule-desc">Pengawas dapat menyesuaikan waktu tambahan atau mengakhiri sesi kapan saja sesuai arahan instruktur laboratorium.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <footer class="py-6 border-t border-gray-200 dark:border-[#1A2820] text-center text-xs text-gray-500 dark:text-gray-600 mt-auto relative z-10">
        &copy; 2026-AAN MS
    </footer>

    <script>
    function studentWaitingRoom(quizId, attemptId, quizToken) {
        return {
            quizId: quizId,
            attemptId: attemptId,
            quizToken: quizToken,
            waitingCount: {{ (int)$waitingCount }},
            isStarting: false,
            pollTimer: null,

            initWaiting() {
                // Poll every ~2.5s with slight jitter to smoothly distribute server load across 300+ concurrent students
                const jitter = Math.floor(Math.random() * 600) - 300; // 2200ms - 2800ms
                this.pollTimer = setInterval(() => {
                    this.checkStatus();
                }, 2500 + jitter);
            },

            async checkStatus() {
                try {
                    const res = await fetch(`{{ url('quiz') }}/${this.quizToken}/waiting-status/${this.attemptId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.waiting_count !== undefined) {
                        this.waitingCount = data.waiting_count;
                    }

                    // If instructor started the quiz, redirect immediately without student refreshing!
                    if (data.session_status === 'started' && data.redirect) {
                        clearInterval(this.pollTimer);
                        this.isStarting = true;
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 900);
                    } else if (data.session_status === 'ended' && data.redirect) {
                        clearInterval(this.pollTimer);
                        window.location.href = data.redirect;
                    }
                } catch (err) {
                    console.warn('Waiting room status check error:', err);
                }
            }
        };
    }
    </script>

    <script>
    // ─── High-End Cyber Constellation & Ambient Cursor Engine (Identik Serentak dengan Halaman Soal) ───
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

