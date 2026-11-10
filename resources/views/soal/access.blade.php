<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Dokumen — IC-Labs Repository</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@700;800;900&display=swap" rel="stylesheet">
    <x-theme-head-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #090D0B; color: #E5E7EB; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative; overflow-x: hidden; }

        .logo-text, .soal-title { color: #fff; }
        html:not(.dark) body { background: #F4F7F5 !important; color: #0F1F17 !important; }
        html:not(.dark) .card { background: #FFFFFF !important; border-color: #E2EAE5 !important; box-shadow: 0 25px 60px rgba(0,0,0,.08) !important; }
        html:not(.dark) .pwd-input { background: #FFFFFF !important; border-color: #D1DCD6 !important; color: #0F1F17 !important; }
        html:not(.dark) label { color: #374151 !important; }
        html:not(.dark) .logo-text,
        html:not(.dark) .soal-title { color: #0F1F17 !important; }
        html:not(.dark) .badge-lifetime { background: rgba(16,185,129,.12); color: #059669 !important; border-color: rgba(16,185,129,.3); }
        html:not(.dark) .badge-time { background: rgba(217,119,6,.12); color: #B45309 !important; }
        html:not(.dark) .footer-note { color: #64748B !important; }
        html:not(.dark) .submit-btn { color: #FFFFFF !important; }
        .blob { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .blob-1 { width: 400px; height: 400px; background: rgba(16,185,129,.09); top: -10%; left: -15%; }
        .blob-2 { width: 400px; height: 400px; background: rgba(52,211,153,.06); bottom: -15%; right: -15%; }
        .wrap { position: relative; z-index: 10; width: 100%; max-width: 420px; }
        .logo-wrap { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 32px; text-decoration: none; }
        .card { background: #0F1713; border: 1px solid #1A2820; border-radius: 20px; padding: 24px; margin-bottom: 16px; }
        .soal-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(16,185,129,.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .badge-lifetime { font-size: 11px; background: rgba(16,185,129,.12); color: #34D399; padding: 3px 10px; border-radius: 50px; border: 1px solid rgba(16,185,129,.25); }
        .badge-time { font-size: 11px; background: rgba(234,179,8,.1); color: #FCD34D; padding: 3px 10px; border-radius: 50px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #9CA3AF; margin-bottom: 7px; }
        .pwd-wrap { position: relative; }
        .pwd-input { width: 100%; background: #090E0B; border: 1px solid #1A2820; color: #fff; font-size: 14px; font-family: 'Inter', sans-serif; padding: 13px 48px 13px 16px; border-radius: 12px; outline: none; transition: border-color .2s, box-shadow .2s; }
        .pwd-input:focus { border-color: #10B981; box-shadow: 0 0 0 3px rgba(16,185,129,.15); }
        .pwd-input.error { border-color: #EF4444; }
        .pwd-input::-ms-reveal,
        .pwd-input::-ms-clear {
            display: none !important;
        }
        .pwd-input:-webkit-autofill,
        .pwd-input:-webkit-autofill:hover,
        .pwd-input:-webkit-autofill:focus,
        .pwd-input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #090E0B inset !important;
            -webkit-text-fill-color: #ffffff !important;
            caret-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        .toggle-btn { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6B7280; padding: 0; display: flex; transition: color .2s; }
        .toggle-btn:hover { color: #D1D5DB; }
        .error-msg { font-size: 12px; color: #FB7185; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
        .submit-btn { width: 100%; margin-top: 20px; background: linear-gradient(135deg, #10B981, #0D9488); color: #fff; font-size: 14px; font-weight: 700; font-family: 'Inter', sans-serif; border: none; padding: 14px 24px; border-radius: 12px; cursor: pointer; transition: opacity .2s, box-shadow .2s; box-shadow: 0 0 20px rgba(16,185,129,.25); }
        .submit-btn:hover { opacity: .9; box-shadow: 0 0 30px rgba(16,185,129,.4); }
        .inactive-card {
            background: rgba(15, 23, 19, 0.9);
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 20px;
            padding: 28px 22px;
            text-align: center;
            backdrop-filter: blur(16px);
            box-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }
        .inactive-card.is-expired {
            border-color: rgba(239, 68, 68, 0.35);
        }
        .inactive-radar-wrap {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .inactive-radar-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px dashed rgba(245, 158, 11, 0.4);
            animation: radarRotate 10s linear infinite;
        }
        .inactive-radar-pulse {
            position: absolute;
            inset: 6px;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.12);
            animation: radarPulse 2.2s ease-out infinite;
        }
        .inactive-icon-core {
            position: relative;
            z-index: 2;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(245, 158, 11, 0.16);
            border: 1px solid rgba(245, 158, 11, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FBBF24;
        }
        .inactive-card.is-expired .inactive-radar-ring {
            border-color: rgba(239, 68, 68, 0.4);
        }
        .inactive-card.is-expired .inactive-radar-pulse {
            background: rgba(239, 68, 68, 0.12);
        }
        .inactive-card.is-expired .inactive-icon-core {
            background: rgba(239, 68, 68, 0.16);
            border-color: rgba(239, 68, 68, 0.45);
            color: #F87171;
        }
        @keyframes radarRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes radarPulse {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 0.35; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }
        .inactive-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Chakra Petch', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 9999px;
            background: rgba(245, 158, 11, 0.12);
            color: #FBBF24;
            border: 1px solid rgba(245, 158, 11, 0.35);
            margin-bottom: 12px;
        }
        .inactive-card.is-expired .inactive-badge {
            background: rgba(239, 68, 68, 0.12);
            color: #F87171;
            border-color: rgba(239, 68, 68, 0.35);
        }
        .inactive-ping-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #FBBF24;
            box-shadow: 0 0 8px #FBBF24;
            display: inline-block;
        }
        .inactive-card.is-expired .inactive-ping-dot {
            background: #F87171;
            box-shadow: 0 0 8px #F87171;
        }
        .sync-box {
            margin-top: 16px;
            padding: 10px 14px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            color: #9CA3AF;
        }
        html:not(.dark) .sync-box {
            background: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            color: #64748B !important;
        }
        .btn-check-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 14px;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            background: rgba(16, 185, 129, 0.12);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-check-status:hover {
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.5);
            transform: translateY(-1px);
        }
        .btn-home-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            margin-top: 10px;
            padding: 11px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            color: #9CA3AF;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-home-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }
        html:not(.dark) .inactive-card {
            background: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.06) !important;
        }
        html:not(.dark) .btn-home-link {
            background: #F1F5F9 !important;
            border-color: #CBD5E1 !important;
            color: #475569 !important;
        }
        html:not(.dark) .btn-home-link:hover {
            background: #E2E8F0 !important;
            color: #0F172A !important;
        }
        .footer-note { margin-top: 20px; text-align: center; font-size: 12px; color: #374151; }
        .footer-note span { color: #10B981; }
        @media (max-width: 480px) {
            body { padding: 16px 12px !important; }
            .card { padding: 18px 16px !important; border-radius: 16px !important; }
            .logo-wrap { margin-bottom: 24px !important; }
        }
    </style>
</head>
<body>
    <div style="position:fixed;top:16px;right:16px;z-index:50;">
        <x-theme-switcher :compact="true" />
    </div>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="wrap">
        <a href="/" class="logo-wrap" style="gap:12px;text-decoration:none;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(16,185,129,0.08);border:1px solid rgba(52,211,153,0.25);display:flex;align-items:center;justify-content:center;padding:6px;box-shadow:0 0 20px rgba(16,185,129,0.15);">
                <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs Logo" style="width:100%;height:100%;object-fit:contain;" draggable="false">
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-start;">
                <span class="logo-text" style="font-family:'Orbitron',sans-serif;font-weight:800;font-size:20px;letter-spacing:1px;line-height:1;">IC-LABS</span>
                <span style="font-family:'Chakra Petch',sans-serif;font-weight:600;font-size:10px;color:#34D399;letter-spacing:2px;margin-top:4px;">REPOSITORY</span>
            </div>
        </a>

        {{-- Flash Warning (Misal: Auto recovery sesi expired) --}}
        @if(session('warning'))
            <div style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.3);border-radius:14px;padding:12px 16px;margin-bottom:16px;font-size:12px;color:#FCD34D;display:flex;align-items:center;gap:10px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Soal Info Card --}}
        <div class="card">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <div class="soal-icon">
                    <svg width="20" height="20" fill="none" stroke="#10B981" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <h2 class="soal-title" style="font-size:14px;font-weight:600;line-height:1.4;">{{ $soal->title }}</h2>
                    @if($soal->description)
                        <p style="font-size:12px;color:#6B7280;margin-top:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $soal->description }}</p>
                    @endif
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">
                        @if($soal->category)
                            <span style="font-size:11px;padding:3px 10px;border-radius:50px;background-color:{{ $soal->category->color }}18;color:{{ $soal->category->color }};border:1px solid {{ $soal->category->color }}35;">
                                {{ $soal->category->name }}
                            </span>
                        @endif
                        @if($soal->access_type === 'lifetime')
                            <span class="badge-lifetime">∞ Lifetime</span>
                        @elseif($soal->active_until)
                            <span class="badge-time">Berakhir {{ $soal->active_until->format('d M Y H:i') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Check --}}
        @if($soal->access_status !== 'active')
            <div class="inactive-card {{ $soal->access_status === 'expired' ? 'is-expired' : '' }}">
                <div class="inactive-radar-wrap">
                    <div class="inactive-radar-ring"></div>
                    <div class="inactive-radar-pulse"></div>
                    <div class="inactive-icon-core">
                        @if($soal->access_status === 'scheduled')
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($soal->access_status === 'expired')
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        @else
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="inactive-badge">
                    <span class="inactive-ping-dot"></span>
                    <span>
                        @if($soal->access_status === 'inactive')
                            STANDBY &bull; MENUNGGU PENGURUS
                        @elseif($soal->access_status === 'scheduled')
                            TERJADWAL &bull; BELUM DIMULAI
                        @elseif($soal->access_status === 'expired')
                            DITUTUP &bull; WAKTU AKSES BERAKHIR
                        @endif
                    </span>
                </div>

                <h3 style="font-size:16px;font-weight:700;color:var(--text1, #fff);margin-bottom:8px;font-family:'Chakra Petch',sans-serif;letter-spacing:0.3px;">
                    @if($soal->access_status === 'inactive')
                        Akses Dokumen Sedang Dijeda
                    @elseif($soal->access_status === 'scheduled')
                        Jadwal Akses Belum Dimulai
                    @elseif($soal->access_status === 'expired')
                        Waktu Akses Dokumen Telah Berakhir
                    @endif
                </h3>

                <p style="font-size:13px;color:#9CA3AF;line-height:1.6;margin:0 auto;max-width:320px;">
                    @if($soal->access_status === 'inactive')
                        Asisten atau pengawas laboratorium sedang menyiapkan sesi ini. Sistem otomatis mendeteksi perubahan status secara berkala tanpa perlu me-refresh halaman.
                    @elseif($soal->access_status === 'scheduled')
                        Dokumen soal ini dijadwalkan aktif pada: <strong style="color:#FBBF24;">{{ $soal->active_from ? $soal->active_from->format('d M Y H:i') : '-' }}</strong>. Halaman ini otomatis terbuka saat jadwal tiba.
                    @elseif($soal->access_status === 'expired')
                        Waktu pengerjaan dan akses berkas dokumen ini telah berakhir sesuai jadwal laboratorium.
                    @endif
                </p>

                @if($soal->access_status !== 'expired')
                    <div class="sync-box">
                        <svg id="sync-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color:#10B981;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span id="poll-status-text">Sinkronisasi otomatis aktif (cek berkala)...</span>
                    </div>

                    <button type="button" onclick="checkStatusNow(true)" id="btn-check-status" class="btn-check-status">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Cek Status Sekarang</span>
                    </button>
                @endif

                <a href="{{ route('home') }}" class="btn-home-link">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        @else
            {{-- Password Form --}}
            <div class="card">
                <form method="POST" action="{{ route('soal.verify', $soal->slug ?: $soal->unique_token) }}" id="access-form">
                    @csrf
                    <label for="password">Password</label>
                    <div class="pwd-wrap">
                        <input type="password" id="password" name="password" autofocus required
                               placeholder="Masukkan Password Dokumen"
                               class="pwd-input {{ $errors->has('password') ? 'error' : '' }}">
                        <button type="button" onclick="togglePwd()" class="toggle-btn">
                            <svg id="eye-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-msg">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <button type="submit" class="submit-btn">Buka Dokumen →</button>
                </form>
            </div>
        @endif

        <p class="footer-note">Powered by <span>IC-Labs Repository</span> — Platform Distribusi Dokumen Lab</p>
    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // Anti double-click & CSRF Keep-alive
        const accessForm = document.getElementById('access-form');
        if (accessForm) {
            accessForm.addEventListener('submit', function() {
                if (window.showLoading) {
                    window.showLoading('MEMVERIFIKASI KUNCI...', 'Mengecek otorisasi akses ke dokumen...');
                }
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.style.pointerEvents = 'none';
                    btn.textContent = 'Memverifikasi...';
                }
            });
        }

        // Auto refresh CSRF token every 5 minutes if page is kept idle
        setInterval(() => {
            fetch('{{ Route::has('csrf.refresh') ? route('csrf.refresh') : url('/csrf-refresh') }}')
                .then(r => r.json())
                .then(data => {
                    if (data && data.csrf_token) {
                        const tokenInput = document.querySelector('input[name="_token"]');
                        if (tokenInput) tokenInput.value = data.csrf_token;
                    }
                })
                .catch(() => {});
        }, 5 * 60 * 1000);

        @if($soal->access_status !== 'active' && $soal->access_status !== 'expired')
        let isCheckingStatus = false;
        const statusUrl = '{{ route('soal.status', $soal->slug ?: $soal->unique_token) }}';

        async function checkStatusNow(isManual = false) {
            if (isCheckingStatus) return;
            isCheckingStatus = true;
            const btn = document.getElementById('btn-check-status');
            const statusText = document.getElementById('poll-status-text');
            const syncIcon = document.getElementById('sync-icon');

            if (syncIcon) syncIcon.style.animation = 'radarRotate 0.8s linear infinite';
            if (isManual && btn) {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                const label = btn.querySelector('span');
                if (label) label.textContent = 'Memeriksa status...';
            }

            try {
                const res = await fetch(statusUrl, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                const data = await res.json();
                if (data && data.access_status === 'active') {
                    if (statusText) statusText.textContent = 'Akses dibuka! Memuat formulir...';
                    if (window.showLoading) {
                        window.showLoading('AKSES TERBUKA', 'Pengawas telah membuka sesi. Memuat dokumen...');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                    return;
                } else if (data && data.access_status === 'expired') {
                    // Jika sesi kadaluarsa
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                    return;
                } else {
                    if (statusText) {
                        const now = new Date();
                        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        statusText.innerHTML = `Terakhir dicek pukul <strong>${timeStr}</strong> &bull; Masih menunggu`;
                    }
                }
            } catch (err) {
                // Abaikan jika network sementara
            } finally {
                isCheckingStatus = false;
                if (syncIcon) syncIcon.style.animation = '';
                if (isManual && btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    const label = btn.querySelector('span');
                    if (label) label.textContent = 'Cek Status Sekarang';
                }
            }
        }

        // Auto polling status setiap 6 detik
        setInterval(() => {
            checkStatusNow(false);
        }, 6000);
        @endif
    </script>

    {{-- ─── UNIFIED CYBER LOADING POPUP COMPONENT ─── --}}
    @include('components.cyber-loading')
</body>
</html>
