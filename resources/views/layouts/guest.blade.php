<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LabDrive') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0D0D14;
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
        .blob-1 { width: 500px; height: 500px; background: rgba(247,37,133,.07); top: -10%; left: -15%; }
        .blob-2 { width: 500px; height: 500px; background: rgba(168,85,247,.05); bottom: -15%; right: -15%; }

        .auth-container { position: relative; z-index: 10; width: 100%; max-width: 420px; }

        .logo-wrap { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 36px; text-decoration: none; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #F72585, #7209B7); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 24px rgba(247,37,133,.3); }
        .logo-text { font-weight: 800; font-size: 22px; color: #fff; letter-spacing: -.5px; }

        .auth-card {
            background: #13131F;
            border: 1px solid #1E1E2E;
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px rgba(0,0,0,.5);
        }
        .auth-card h2 { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .auth-card p { font-size: 13px; color: #6B7280; margin-bottom: 28px; }

        label { display: block; font-size: 13px; font-weight: 600; color: #9CA3AF; margin-bottom: 6px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            background: #0D0D14;
            border: 1px solid #1E1E2E;
            color: #fff;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            padding: 12px 16px;
            border-radius: 12px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #F72585;
            box-shadow: 0 0 0 3px rgba(247,37,133,.15);
        }
        input::placeholder { color: #374151; }

        .form-group { margin-bottom: 18px; }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #F72585, #7209B7);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none;
            padding: 13px 24px;
            border-radius: 12px;
            cursor: pointer;
            transition: opacity .2s, box-shadow .2s;
            box-shadow: 0 0 20px rgba(247,37,133,.25);
        }
        .btn-primary:hover { opacity: .9; box-shadow: 0 0 30px rgba(247,37,133,.4); }

        .link-pink { color: #F72585; text-decoration: none; font-weight: 600; font-size: 13px; transition: opacity .2s; }
        .link-pink:hover { opacity: .75; }

        .link-muted { color: #6B7280; text-decoration: none; font-size: 13px; transition: color .2s; }
        .link-muted:hover { color: #fff; }

        .divider-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }

        .error-msg { font-size: 12px; color: #FB7185; margin-top: 5px; }

        .checkbox-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .checkbox-row input[type="checkbox"] { width: 16px; height: 16px; accent-color: #F72585; border-radius: 4px; flex-shrink: 0; }
        .checkbox-row span { font-size: 13px; color: #6B7280; }

        .footer-note { margin-top: 24px; text-align: center; font-size: 12px; color: #374151; }

        .session-status { background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.3); color: #34D399; font-size: 13px; padding: 10px 14px; border-radius: 10px; margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="auth-container">
        <a href="/" class="logo-wrap">
            <div class="logo-icon">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <span class="logo-text">LabDrive</span>
        </a>

        <div class="auth-card">
            {{ $slot }}
        </div>

        <p class="footer-note">© {{ date('Y') }} Platform Distribusi Dokumen Laboratorium</p>
    </div>
</body>
</html>
