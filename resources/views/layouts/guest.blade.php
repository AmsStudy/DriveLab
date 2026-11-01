<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IC-Labs Repository') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #090D0B;
            color: #E5E7EB;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        .blob { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .blob-1 { width: 500px; height: 500px; background: rgba(16,185,129,.08); top: -10%; left: -15%; }
        .blob-2 { width: 500px; height: 500px; background: rgba(13,148,136,.06); bottom: -15%; right: -15%; }

        .auth-container { position: relative; z-index: 10; width: 100%; max-width: 420px; }

        .logo-wrap { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 36px; text-decoration: none; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #10B981, #0D9488); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 24px rgba(16,185,129,.35); }
        .logo-text { font-weight: 800; font-size: 22px; color: #fff; letter-spacing: -.5px; }

        .auth-card {
            background: #0F1713;
            border: 1px solid #1A2820;
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px rgba(0,0,0,.5);
        }
        @media (max-width: 480px) {
            body { padding: 16px 12px !important; }
            .auth-card { padding: 28px 18px 24px !important; border-radius: 16px !important; }
            .logo-wrap { margin-bottom: 24px !important; }
        }
        .auth-card h2 { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .auth-card p { font-size: 13px; color: #6B7280; margin-bottom: 28px; }

        label { display: block; font-size: 13px; font-weight: 600; color: #9CA3AF; margin-bottom: 6px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            background: #080C0A;
            border: 1px solid #1A2820;
            color: #fff;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            padding: 12px 16px;
            border-radius: 12px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16,185,129,.15);
        }
        input::placeholder { color: #374151; }

        .form-group { margin-bottom: 18px; }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #10B981, #0D9488);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none;
            padding: 13px 24px;
            border-radius: 12px;
            cursor: pointer;
            transition: opacity .2s, box-shadow .2s;
            box-shadow: 0 0 20px rgba(16,185,129,.25);
        }
        .btn-primary:hover { opacity: .9; box-shadow: 0 0 30px rgba(16,185,129,.4); }

        .link-emerald, .link-pink { color: #10B981; text-decoration: none; font-weight: 600; font-size: 13px; transition: opacity .2s; }
        .link-emerald:hover, .link-pink:hover { opacity: .75; }

        .link-muted { color: #6B7280; text-decoration: none; font-size: 13px; transition: color .2s; }
        .link-muted:hover { color: #fff; }

        .divider-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }

        .error-msg { font-size: 12px; color: #FB7185; margin-top: 5px; }

        .checkbox-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .checkbox-row input[type="checkbox"] { width: 16px; height: 16px; accent-color: #10B981; border-radius: 4px; flex-shrink: 0; }
        .checkbox-row span { font-size: 13px; color: #6B7280; }

        .footer-note { margin-top: 24px; text-align: center; font-size: 12px; color: #374151; }

        .session-status { background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.3); color: #34D399; font-size: 13px; padding: 10px 14px; border-radius: 10px; margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="auth-container">
        <a href="/" class="logo-wrap" style="gap:12px;text-decoration:none;">
            <div style="width:44px;height:44px;border-radius:14px;background:rgba(16,185,129,0.08);border:1px solid rgba(52,211,153,0.25);display:flex;align-items:center;justify-content:center;padding:5px;box-shadow:0 0 20px rgba(16,185,129,0.15);">
                <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs Logo" style="width:100%;height:100%;object-fit:contain;" draggable="false">
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-start;">
                <span style="font-family:'Orbitron',sans-serif;font-weight:800;font-size:18px;color:#fff;letter-spacing:1px;line-height:1;">IC-LABS</span>
                <span style="font-family:'Chakra Petch',sans-serif;font-weight:600;font-size:10px;color:#34D399;letter-spacing:2px;margin-top:3px;">REPOSITORY</span>
            </div>
        </a>

        <div class="auth-card">
            {{ $slot }}
        </div>

        <p class="footer-note">© {{ date('Y') }} Platform Distribusi Dokumen Laboratorium</p>
    </div>
</body>
</html>
