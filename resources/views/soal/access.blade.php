<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Dokumen — LabDrive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #090D0B; color: #E5E7EB; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative; overflow-x: hidden; }
        .blob { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .blob-1 { width: 400px; height: 400px; background: rgba(16,185,129,.09); top: -10%; left: -15%; }
        .blob-2 { width: 400px; height: 400px; background: rgba(52,211,153,.06); bottom: -15%; right: -15%; }
        .wrap { position: relative; z-index: 10; width: 100%; max-width: 420px; }
        .logo-wrap { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 32px; text-decoration: none; }
        .logo-icon { width: 52px; height: 52px; background: linear-gradient(135deg, #10B981, #0D9488); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 30px rgba(16,185,129,.3); }
        .logo-text { font-weight: 800; font-size: 22px; color: #fff; letter-spacing: -.5px; }
        .card { background: #0F1713; border: 1px solid #1A2820; border-radius: 20px; padding: 24px; margin-bottom: 16px; }
        .soal-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(16,185,129,.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .badge-lifetime { font-size: 11px; background: rgba(16,185,129,.12); color: #34D399; padding: 3px 10px; border-radius: 50px; border: 1px solid rgba(16,185,129,.25); }
        .badge-time { font-size: 11px; background: rgba(234,179,8,.1); color: #FCD34D; padding: 3px 10px; border-radius: 50px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #9CA3AF; margin-bottom: 7px; }
        .pwd-wrap { position: relative; }
        .pwd-input { width: 100%; background: #090E0B; border: 1px solid #1A2820; color: #fff; font-size: 14px; font-family: 'Inter', sans-serif; padding: 13px 48px 13px 16px; border-radius: 12px; outline: none; transition: border-color .2s, box-shadow .2s; }
        .pwd-input:focus { border-color: #10B981; box-shadow: 0 0 0 3px rgba(16,185,129,.15); }
        .pwd-input.error { border-color: #EF4444; }
        .toggle-btn { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6B7280; padding: 0; display: flex; transition: color .2s; }
        .toggle-btn:hover { color: #D1D5DB; }
        .error-msg { font-size: 12px; color: #FB7185; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
        .submit-btn { width: 100%; margin-top: 20px; background: linear-gradient(135deg, #10B981, #0D9488); color: #fff; font-size: 14px; font-weight: 700; font-family: 'Inter', sans-serif; border: none; padding: 14px 24px; border-radius: 12px; cursor: pointer; transition: opacity .2s, box-shadow .2s; box-shadow: 0 0 20px rgba(16,185,129,.25); }
        .submit-btn:hover { opacity: .9; box-shadow: 0 0 30px rgba(16,185,129,.4); }
        .inactive-card { background: rgba(239,68,68,.06); border: 1px solid rgba(239,68,68,.2); border-radius: 20px; padding: 28px; text-align: center; }
        .footer-note { margin-top: 20px; text-align: center; font-size: 12px; color: #374151; }
        .footer-note span { color: #10B981; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="wrap">
        <a href="/" class="logo-wrap">
            <div class="logo-icon">
                <svg width="26" height="26" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <span class="logo-text">LabDrive</span>
        </a>

        {{-- Soal Info Card --}}
        <div class="card">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <div class="soal-icon">
                    <svg width="20" height="20" fill="none" stroke="#10B981" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <h2 style="font-size:14px;font-weight:600;color:#fff;line-height:1.4;">{{ $soal->title }}</h2>
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
            <div class="inactive-card">
                <svg style="width:44px;height:44px;color:#F87171;margin:0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <p style="font-size:15px;font-weight:700;color:#F87171;">Dokumen Tidak Tersedia</p>
                <p style="font-size:13px;color:#6B7280;margin-top:6px;">
                    @if($soal->access_status === 'inactive') Dokumen ini sedang tidak aktif.
                    @elseif($soal->access_status === 'scheduled') Waktu Akses Dokumen belum dimulai. (Aktif: {{ $soal->active_from->format('d M Y H:i') }})
                    @elseif($soal->access_status === 'expired') Waktu Akses Dokumen ini telah berakhir.
                    @endif
                </p>
            </div>
        @else
            {{-- Password Form --}}
            <div class="card">
                <form method="POST" action="{{ route('soal.verify', $soal->unique_token) }}" id="access-form">
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

        <p class="footer-note">Powered by <span>LabDrive</span> — Platform Distribusi Dokumen Lab</p>
    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
