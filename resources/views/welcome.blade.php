<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IC-Labs Repository</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@700;800;900&family=Rajdhani:wght@600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #050907;
            --emerald:  #10B981;
            --emerald2: #059669;
            --mint:     #34D399;
            --teal:     #0D9488;
            --sky:      #38BDF8;
            --muted:    #4B6358;
            --muted2:   #7A9E8E;
            --text:     #D4E8DC;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        /* ─────────────────── BACKGROUND ─────────────────── */
        .bg-scene {
            position: fixed; inset: 0; z-index: 0;
            overflow: hidden;
        }

        /* Layered gradient mesh */
        .bg-scene::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 15% 20%,  rgba(16,185,129,.13) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 75%,  rgba(13,148,136,.10) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 60% 10%,  rgba(52,211,153,.06) 0%, transparent 50%),
                radial-gradient(ellipse 100% 100% at 50% 50%, rgba(5,9,7,1) 55%, transparent 100%);
        }

        /* Subtle grid */
        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(16,185,129,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16,185,129,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 45%, black 20%, transparent 100%);
        }

        /* Floating orbs */
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); pointer-events: none;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,.14) 0%, transparent 70%);
            top: -120px; left: -80px;
            animation: float1 20s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(13,148,136,.11) 0%, transparent 70%);
            bottom: -80px; right: -60px;
            animation: float2 25s ease-in-out infinite alternate;
        }
        .orb-3 {
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(52,211,153,.08) 0%, transparent 70%);
            top: 40%; left: 60%;
            animation: float3 15s ease-in-out infinite alternate;
        }
        @keyframes float1 { to { transform: translate(50px, 60px) scale(1.08); } }
        @keyframes float2 { to { transform: translate(-40px, -50px) scale(.94); } }
        @keyframes float3 { to { transform: translate(-30px, 30px); } }

        /* Fine noise texture */
        .bg-noise {
            position: absolute; inset: 0;
            opacity: .3;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.05'/%3E%3C/svg%3E");
        }

        /* ─────────────────── LAYOUT ─────────────────── */
        .stage {
            position: relative; z-index: 10;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ─────────────────── CORNER ADMIN ─────────────────── */
        .admin-link {
            position: fixed; top: 22px; right: 26px; z-index: 30;
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 500; letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(16,185,129,.1);
            background: rgba(5,9,7,.6);
            backdrop-filter: blur(8px);
            transition: color .2s, border-color .2s, background .2s;
        }
        .admin-link:hover {
            color: var(--mint);
            border-color: rgba(16,185,129,.3);
            background: rgba(16,185,129,.07);
        }

        /* ─────────────────── MODAL POPUP (ADMIN LOGIN) ─────────────────── */
        [x-cloak] { display: none !important; }

        .modal-backdrop {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(3, 7, 5, 0.78);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-panel {
            width: 100%;
            max-width: 410px;
            background: rgba(10, 22, 16, 0.88);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgba(52, 211, 153, 0.22);
            border-radius: 24px;
            padding: 32px 28px 28px;
            box-shadow:
                0 0 0 1px rgba(0,0,0,0.5) inset,
                0 32px 70px rgba(0, 0, 0, 0.75),
                0 0 60px rgba(16, 185, 129, 0.12);
            position: relative;
        }

        .modal-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 24px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(52,211,153,.35) 0%, transparent 50%, rgba(13,148,136,.25) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .modal-close-btn {
            position: absolute;
            top: 20px; right: 20px;
            width: 32px; height: 32px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted2);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .modal-close-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(52, 211, 153, 0.3);
            transform: scale(1.05);
        }

        .modal-badge {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 10px; font-weight: 700;
            font-family: 'Chakra Petch', sans-serif;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--mint);
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 14px;
        }

        .modal-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #fff;
            margin-bottom: 4px;
        }

        .modal-desc {
            font-size: 12px;
            color: var(--muted2);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .modal-field {
            margin-bottom: 16px;
        }

        .modal-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted2);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .modal-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            background: #060c09;
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 12px;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .modal-input-wrap:focus-within {
            border-color: rgba(16, 185, 129, 0.55);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12), 0 8px 20px rgba(0,0,0,0.4);
        }

        .modal-input-icon {
            padding: 0 14px;
            color: var(--muted);
            display: flex; align-items: center;
            flex-shrink: 0;
            background: #060c09;
        }

        .modal-input {
            flex: 1;
            background: #060c09 !important;
            border: none;
            outline: none;
            color: #ffffff !important;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            padding: 12px 0;
        }
        .modal-input::placeholder {
            color: #2e4336;
            font-size: 13px;
        }

        /* ── Sembunyikan Simbol Mata Bawaan Browser (Edge / Windows Chromium) ── */
        .modal-input::-ms-reveal,
        .modal-input::-ms-clear,
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
        }

        /* ── Hilangkan Warna Biru/Abu-abu Autofill Browser (Chrome / Edge / Brave / Safari / Firefox) ── */
        .modal-input:-webkit-autofill,
        .modal-input:-webkit-autofill:hover,
        .modal-input:-webkit-autofill:focus,
        .modal-input:-webkit-autofill:active,
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #060c09 inset !important;
            box-shadow: 0 0 0 1000px #060c09 inset !important;
            -webkit-text-fill-color: #ffffff !important;
            color: #ffffff !important;
            caret-color: #ffffff !important;
            transition: background-color 999999s ease-in-out 0s !important;
        }

        .modal-input:autofill,
        input:autofill {
            background-color: #060c09 !important;
            filter: none !important;
            color: #ffffff !important;
        }

        .modal-pwd-toggle {
            background: #060c09;
            border: none;
            padding: 0 14px;
            color: var(--muted);
            cursor: pointer;
            display: flex; align-items: center;
            transition: color 0.2s;
            height: 100%;
        }
        .modal-pwd-toggle:hover { color: var(--text); }

        .modal-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .modal-checkbox {
            display: inline-flex; align-items: center; gap: 7px;
            color: var(--muted2);
            cursor: pointer;
            user-select: none;
        }
        .modal-checkbox input[type="checkbox"] {
            accent-color: var(--emerald);
            cursor: pointer;
            width: 14px; height: 14px;
        }

        .modal-btn-submit {
            width: 100%;
            padding: 13px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: linear-gradient(135deg, var(--emerald) 0%, var(--teal) 100%);
            color: #fff;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.28);
            transition: filter 0.15s, transform 0.15s, opacity 0.15s;
        }
        .modal-btn-submit:hover:not(:disabled) {
            filter: brightness(1.12);
            transform: translateY(-1px);
        }
        .modal-btn-submit:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .modal-err-box {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: #fca5a5;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 16px;
        }

        /* Pulse glow animation on tap */
        .logo-glow.glow-pulse {
            transform: scale(1.4) !important;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.65) 0%, transparent 70%) !important;
            filter: blur(8px) !important;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* ─────────────────── GLASS CARD ─────────────────── */
        .glass-card {
            width: 100%;
            max-width: 440px;

            background: rgba(10, 20, 14, 0.55);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);

            border: 1px solid rgba(52, 211, 153, 0.12);
            border-radius: 24px;
            padding: 36px 32px 30px;

            box-shadow:
                0 0 0 1px rgba(0,0,0,.4) inset,
                0 32px 64px rgba(0,0,0,.5),
                0 0 80px rgba(16,185,129,.06);

            /* Entry animation */
            animation: cardIn .6s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Shimmer border top */
        .glass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 24px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(52,211,153,.25) 0%, transparent 50%, rgba(13,148,136,.15) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* ─────────────────── BRAND HEADER ─────────────────── */
        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 22px;
        }
        .brand-logo-wrap {
            position: relative;
            width: 76px;
            height: 76px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .brand-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 16px rgba(52, 211, 153, 0.45));
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.3s ease;
            position: relative;
            z-index: 2;
        }
        .brand-logo-wrap:hover .brand-logo {
            transform: scale(1.08);
            filter: drop-shadow(0 0 25px rgba(52, 211, 153, 0.75));
        }
        .logo-glow {
            position: absolute;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            filter: blur(12px);
            z-index: 1;
        }
        .brand-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .brand-name {
            font-family: 'Orbitron', 'Space Grotesk', sans-serif;
            font-weight: 800;
            font-size: 21px;
            letter-spacing: 0.14em;
            background: linear-gradient(135deg, #ffffff 40%, #6ee7b7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .brand-sub {
            font-family: 'Chakra Petch', 'Space Grotesk', sans-serif;
            font-size: 10.5px;
            font-weight: 600;
            color: #38bdf8;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            opacity: 0.88;
        }

        /* ─────────────────── DIVIDER ─────────────────── */
        .hline {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(52,211,153,.15) 30%, rgba(52,211,153,.15) 70%, transparent);
            margin-bottom: 24px;
        }

        /* ─────────────────── TABS ─────────────────── */
        .tabs {
            display: flex; gap: 2px;
            margin-bottom: 20px;
            background: rgba(0,0,0,.3);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 12px;
            padding: 3px;
        }
        .tab-btn {
            flex: 1;
            background: none; border: none;
            padding: 8px 10px;
            border-radius: 9px;
            font-size: 11px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: var(--muted);
            cursor: pointer;
            transition: all .18s;
            letter-spacing: .05em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .tab-btn:hover { color: var(--muted2); }
        .tab-doc  { background: rgba(16,185,129,.15); color: var(--mint) !important; box-shadow: 0 1px 4px rgba(0,0,0,.3); }
        .tab-quiz { background: rgba(56,189,248,.12); color: var(--sky)  !important; box-shadow: 0 1px 4px rgba(0,0,0,.3); }
        .tab-lacak{ background: rgba(52,211,153,.1);  color: var(--mint) !important; box-shadow: 0 1px 4px rgba(0,0,0,.3); }

        /* ─────────────────── INPUT GROUP ─────────────────── */
        .input-wrap {
            position: relative;
            display: flex;
            background: rgba(0,0,0,.35);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: 14px;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }
        .input-wrap:focus-within {
            border-color: rgba(16,185,129,.5);
            box-shadow: 0 0 0 3px rgba(16,185,129,.08), 0 8px 24px rgba(0,0,0,.3);
        }
        .input-wrap.sky-focus:focus-within {
            border-color: rgba(56,189,248,.45);
            box-shadow: 0 0 0 3px rgba(56,189,248,.08), 0 8px 24px rgba(0,0,0,.3);
        }
        .inp-icon {
            display: flex; align-items: center;
            padding: 0 14px;
            color: var(--muted);
            flex-shrink: 0;
        }
        .main-inp {
            flex: 1;
            background: transparent; border: none; outline: none;
            color: #e8f5ee;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            padding: 14px 0;
            letter-spacing: .01em;
        }
        .main-inp::placeholder { color: #263e2e; font-size: 13px; }
        .go-btn {
            border: none; cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 12px; font-weight: 700;
            padding: 0 20px;
            flex-shrink: 0;
            letter-spacing: .08em;
            text-transform: uppercase;
            transition: filter .15s, opacity .15s;
        }
        .go-btn:hover:not(:disabled) { filter: brightness(1.12); }
        .go-btn:disabled { opacity: .4; cursor: not-allowed; }
        .go-green { background: linear-gradient(135deg, var(--emerald) 0%, var(--teal) 100%); color: #fff; }
        .go-blue  { background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%); color: #fff; }

        /* ─────────────────── FEEDBACK ─────────────────── */
        .feedback {
            font-size: 12px;
            margin-top: 9px;
            padding-left: 4px;
            min-height: 18px;
            display: flex; align-items: center; gap: 5px;
        }
        .feedback.err  { color: #FC8181; }
        .feedback.ok   { color: var(--mint); }

        /* ─────────────────── TRACK RESULT ─────────────────── */
        .track-box {
            margin-top: 14px;
            background: rgba(0,0,0,.3);
            border: 1px solid rgba(52,211,153,.1);
            border-radius: 12px;
            padding: 14px;
            max-height: 200px;
            overflow-y: auto;
        }
        .track-box::-webkit-scrollbar { width: 3px; }
        .track-box::-webkit-scrollbar-thumb { background: rgba(52,211,153,.2); border-radius: 3px; }
        .track-head { font-size: 11px; color: var(--muted2); font-weight: 500; margin-bottom: 10px; }
        .task-row {
            background: rgba(255,255,255,.02);
            border: 1px solid rgba(255,255,255,.04);
            border-radius: 8px;
            padding: 9px 11px;
            margin-bottom: 5px;
        }
        .task-row:last-child { margin-bottom: 0; }
        .task-title { font-size: 12px; font-weight: 600; color: var(--mint); margin-bottom: 3px; }
        .task-meta  { font-size: 11px; color: var(--muted2); display: flex; align-items: center; gap: 5px; }
        .task-date  { font-size: 10px; color: var(--muted); margin-top: 2px; }
        .badge-pill {
            font-size: 9px; font-weight: 700;
            font-family: 'Space Grotesk', 'Inter', sans-serif;
            padding: 1px 6px; border-radius: 4px;
            text-transform: uppercase; letter-spacing: .05em;
            background: rgba(16,185,129,.12);
            border: 1px solid rgba(16,185,129,.2);
            color: var(--mint);
        }
        .badge-pill.lnk { background: rgba(13,148,136,.1); border-color: rgba(13,148,136,.25); color: #5eead4; }
        .badge-pill.txt { background: rgba(251,191,36,.08); border-color: rgba(251,191,36,.2); color: #fbbf24; }

        /* ─────────────────── FOOTER AREA ─────────────────── */
        .card-footer {
            margin-top: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .cf-line { flex: 1; height: 1px; background: rgba(255,255,255,.05); }
        .cf-label {
            font-size: 10px; font-weight: 500;
            color: #1E3028;
            text-transform: uppercase; letter-spacing: .1em;
            white-space: nowrap;
        }

        /* ─────────────────── TAGLINE ─────────────────── */
        .tagline {
            margin-top: 20px;
            font-size: 10.5px;
            color: #1A2C22;
            letter-spacing: .09em;
            text-align: center;
            text-transform: uppercase;
        }

        /* ─────────────────── RESPONSIVE ─────────────────── */
        @media (max-width: 500px) {
            html, body { overflow: auto; }
            .stage { justify-content: flex-start; padding-top: 60px; }
            .glass-card { padding: 28px 20px 24px; border-radius: 20px; }
            .tab-btn { font-size: 10px; padding: 7px 6px; }
        }
    </style>
</head>
<body x-data="gateway()">

    {{-- Background scene --}}
    <div class="bg-scene" aria-hidden="true">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="bg-grid"></div>
        <div class="bg-noise"></div>
    </div>

    {{-- Admin corner: only rendered when user is authenticated --}}
    @auth
        <a href="{{ route('admin.dashboard') }}" class="admin-link">
            Dashboard
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @endauth

    {{-- Gateway stage --}}
    <main class="stage">
        <div class="glass-card" style="position:relative;">

            {{-- Brand Header (Centered with Chip Logo) --}}
            <div class="brand-header">
                <div class="brand-logo-wrap"
                     @click="handleLogoClick"
                     @dblclick="openLoginModal"
                     role="button"
                     tabindex="0"
                     @keydown.enter="openLoginModal"
                     title="IC-Labs">
                    <div class="logo-glow" :class="{ 'glow-pulse': isSecretTapped }"></div>
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs Logo" class="brand-logo" draggable="false">
                </div>
                <div class="brand-text">
                    <span class="brand-name">IC-LABS</span>
                    <span class="brand-sub">REPOSITORY</span>
                </div>
            </div>

            {{-- Thin line --}}
            <div class="hline"></div>

            {{-- Tabs --}}
            <div class="tabs" role="tablist">
                <button
                    class="tab-btn"
                    :class="tab === 'akses' ? 'tab-doc' : ''"
                    @click="tab = 'akses'"
                    role="tab" :aria-selected="tab === 'akses'"
                >Dokumen</button>
                <button
                    class="tab-btn"
                    :class="tab === 'kuis' ? 'tab-quiz' : ''"
                    @click="tab = 'kuis'"
                    role="tab" :aria-selected="tab === 'kuis'"
                >Kuis</button>
                <button
                    class="tab-btn"
                    :class="tab === 'lacak' ? 'tab-lacak' : ''"
                    @click="tab = 'lacak'"
                    role="tab" :aria-selected="tab === 'lacak'"
                >Lacak Tugas</button>
            </div>

            {{-- ── Panel: Dokumen ── --}}
            <div x-show="tab === 'akses'" x-transition.opacity role="tabpanel">
                <form action="{{ route('soal.accessByCode') }}" method="POST" @submit="busy = true">
                    @csrf
                    <div class="input-wrap">
                        <div class="inp-icon">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="kode_unik"
                            class="main-inp"
                            placeholder="Masukkan kode akses soal…"
                            required autocomplete="off" autofocus
                        >
                        <button type="submit" class="go-btn go-green" :disabled="busy">
                            <span x-show="!busy">Akses →</span>
                            <span x-show="busy" style="opacity:.7">…</span>
                        </button>
                    </div>
                </form>
                @if(session('error'))
                    <p class="feedback err">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('error') }}
                    </p>
                @elseif(session('success'))
                    <p class="feedback ok">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </p>
                @else
                    <div style="min-height:28px;"></div>
                @endif
            </div>

            {{-- ── Panel: Kuis ── --}}
            <div x-show="tab === 'kuis'" x-transition.opacity role="tabpanel" style="display:none">
                <form action="{{ route('quiz.join') }}" method="POST" @submit="busy = true">
                    @csrf
                    <div class="input-wrap sky-focus">
                        <div class="inp-icon" style="color:var(--sky)">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="access_code"
                            class="main-inp"
                            placeholder="Masukkan kode kuis…"
                            required autocomplete="off"
                            style="text-transform:uppercase"
                        >
                        <button type="submit" class="go-btn go-blue" :disabled="busy">
                            <span x-show="!busy">Masuk →</span>
                            <span x-show="busy" style="opacity:.7">…</span>
                        </button>
                    </div>
                </form>
                @if(session('quiz_error'))
                    <p class="feedback err">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('quiz_error') }}
                    </p>
                @else
                    <div style="min-height:28px;"></div>
                @endif
            </div>

            {{-- ── Panel: Lacak Tugas ── --}}
            <div x-show="tab === 'lacak'" x-transition.opacity role="tabpanel" style="display:none" x-data="lacakTugas()">
                <form @submit.prevent="cari">
                    <div class="input-wrap">
                        <div class="inp-icon">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            x-model="nim"
                            class="main-inp"
                            placeholder="Masukkan NIM…"
                            autocomplete="off"
                        >
                        <button type="submit" class="go-btn go-green" :disabled="loading">
                            <span x-show="!loading">Cari →</span>
                            <span x-show="loading" style="opacity:.7">…</span>
                        </button>
                    </div>
                </form>

                <p class="feedback err" x-show="errMsg" x-text="errMsg" style="display:none">
                </p>

                <div class="track-box" x-show="result" x-transition.opacity style="display:none">
                    <p class="track-head" x-text="head"></p>
                    <template x-for="t in tasks" :key="t.id">
                        <div class="task-row">
                            <p class="task-title" x-text="t.soal_title"></p>
                            <p class="task-meta">
                                <span class="badge-pill"
                                      :class="t.submission_type === 'link' ? 'lnk' : (t.submission_type === 'text' ? 'txt' : '')"
                                      x-text="t.submission_type || 'file'"></span>
                                <template x-if="t.submission_type === 'link'">
                                    <a :href="t.link_url" target="_blank" rel="noopener"
                                       style="color:var(--sky);text-decoration:none;font-size:11px;"
                                       x-text="t.file_name"></a>
                                </template>
                                <template x-if="t.submission_type !== 'link'">
                                    <span style="font-size:11px;" x-text="t.file_name"></span>
                                </template>
                            </p>
                            <p class="task-date" x-text="'Dikumpulkan ' + t.created_at"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer rule --}}
            <div class="card-footer">
                <div class="cf-line"></div>
                <span class="cf-label">IC-Labs · Internal Platform</span>
                <div class="cf-line"></div>
            </div>

        </div>

        <p class="tagline">Sistem Distribusi Soal &amp; Modul Laboratorium</p>
    </main>

    {{-- ─────────────────── ADMIN LOGIN POPUP MODAL ─────────────────── --}}
    <div
        x-show="showLogin"
        x-cloak
        class="modal-backdrop"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="closeLoginModal"
        @keydown.escape.window="closeLoginModal"
        style="display: none;"
    >
        <div
            x-show="showLogin"
            class="modal-panel"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        >
            {{-- Close Button --}}
            <button type="button" class="modal-close-btn" @click="closeLoginModal" aria-label="Tutup">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Header --}}
            <div class="modal-badge">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Otorisasi Akses
            </div>
            <h2 class="modal-title">PORTAL ADMIN</h2>
            <p class="modal-desc">Masuk untuk mengelola arsip soal, modul, kuis, dan data laboratorium.</p>

            {{-- Error Flash --}}
            @if ($errors->any())
                <div class="modal-err-box">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $errors->first('email') ?: ($errors->first('password') ?: 'Kredensial yang dimasukkan tidak cocok.') }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" @submit="loginBusy = true">
                @csrf

                {{-- Email --}}
                <div class="modal-field">
                    <label class="modal-label" for="login-email">Email Admin</label>
                    <div class="modal-input-wrap">
                        <div class="modal-input-icon">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="login-email"
                            class="modal-input"
                            value="{{ old('email') }}"
                            placeholder="admin@iclabs.ac.id"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="modal-field">
                    <label class="modal-label" for="login-password">Password</label>
                    <div class="modal-input-wrap">
                        <div class="modal-input-icon">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="login-password"
                            class="modal-input"
                            placeholder="••••••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="modal-pwd-toggle" @click="showPassword = !showPassword" aria-label="Lihat password" tabindex="-1">
                            <svg x-show="!showPassword" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Row: Remember Me & Reset Password --}}
                <div class="modal-row">
                    <label class="modal-checkbox">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color:var(--muted2);text-decoration:none;font-size:11px;" onmouseover="this.style.color='#34d399'" onmouseout="this.style.color='var(--muted2)'">
                            Lupa password?
                        </a>
                    @endif
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="modal-btn-submit" :disabled="loginBusy">
                    <span x-show="!loginBusy">Masuk Portal →</span>
                    <span x-show="loginBusy" style="display:none">Memverifikasi…</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('gateway', () => ({
                tab: 'akses',
                busy: false,
                showLogin: {{ ($errors->any() || request()->has('admin_login')) ? 'true' : 'false' }},
                loginBusy: false,
                showPassword: false,
                lastTap: 0,
                isSecretTapped: false,

                handleLogoClick() {
                    const now = Date.now();
                    if (now - this.lastTap < 380) {
                        this.openLoginModal();
                        this.lastTap = 0;
                    } else {
                        this.lastTap = now;
                        this.isSecretTapped = true;
                        setTimeout(() => this.isSecretTapped = false, 400);
                    }
                },

                openLoginModal() {
                    @auth
                        window.location.href = "{{ route('admin.dashboard') }}";
                    @else
                        this.showLogin = true;
                        this.$nextTick(() => {
                            const input = document.getElementById('login-email');
                            if (input) input.focus();
                        });
                    @endauth
                },

                closeLoginModal() {
                    this.showLogin = false;
                }
            }));

            Alpine.data('lacakTugas', () => ({
                nim: '',
                loading: false,
                result: false,
                head: '',
                errMsg: '',
                tasks: [],

                async cari() {
                    if (!this.nim.trim()) return;
                    this.loading = true;
                    this.result  = false;
                    this.errMsg  = '';

                    try {
                        const res  = await fetch(`{{ route('soal.trackTask') }}?nim=${encodeURIComponent(this.nim)}`);
                        const data = await res.json();
                        this.loading = false;

                        if (!res.ok) {
                            this.errMsg = data.message || 'Terjadi kesalahan, coba lagi.';
                            return;
                        }

                        this.result = true;
                        if (data.data && data.data.length > 0) {
                            this.head  = `${data.data.length} tugas ditemukan — ${data.data[0].nama}`;
                            this.tasks = data.data;
                        } else {
                            this.head  = `Belum ada tugas untuk NIM ${this.nim}.`;
                            this.tasks = [];
                        }
                    } catch {
                        this.loading = false;
                        this.errMsg  = 'Gagal terhubung ke server.';
                    }
                }
            }));
        });
    </script>

</body>
</html>
