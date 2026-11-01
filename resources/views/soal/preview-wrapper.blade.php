<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $attachment->file_original_name }} — Preview Aman IC-Labs</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #090D0B;
            --surface: #0F1713;
            --surface2: #142019;
            --border: #1A2820;
            --emerald: #10B981;
            --emerald-dim: rgba(16, 185, 129, 0.15);
            --mint: #34D399;
            --teal: #0D9488;
            --text: #F1EEF6;
            --muted: #8E8A9F;
        }

        body, html {
            width: 100%;
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            user-select: none;
            -webkit-user-select: none;
        }

        /* Top Bar */
        .viewer-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: rgba(15, 23, 19, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-back:hover {
            border-color: var(--emerald);
            color: #fff;
        }

        .file-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .file-title {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 320px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .badge-secure {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--emerald-dim);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34D399;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.02em;
        }
        .badge-secure svg { width: 12px; height: 12px; }

        @media (max-width: 640px) {
            .file-title { max-width: 120px !important; font-size: 12px !important; }
            .badge-secure span { display: none !important; }
            .badge-secure { padding: 4px 6px !important; }
            .zoom-text { min-width: 34px !important; font-size: 10px !important; padding: 0 4px !important; }
            .zoom-btn { width: 24px !important; height: 24px !important; font-size: 12px !important; }
            .btn-back { padding: 5px 8px !important; font-size: 11px !important; }
            .viewer-topbar { padding: 0 10px !important; }
        }

        .zoom-controls {
            display: flex;
            align-items: center;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        .zoom-btn {
            background: transparent;
            border: none;
            color: var(--muted);
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: color 0.15s, background 0.15s;
        }
        .zoom-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }
        .zoom-text {
            font-size: 11px;
            font-weight: 600;
            padding: 0 8px;
            color: var(--text);
            min-width: 44px;
            text-align: center;
        }

        /* Content Area */
        .viewer-viewport {
            position: absolute;
            top: 56px;
            left: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
            overflow-x: auto;
            padding: 32px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #06060c;
        }

        .zoom-container {
            transition: transform 0.12s ease-out;
            transform-origin: top center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        canvas {
            max-width: 100%;
            height: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
            border-radius: 6px;
            background: #fff;
            pointer-events: none;
        }

        /* Loading & Error */
        #loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            color: var(--muted);
            font-size: 13px;
            z-index: 50;
        }
        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid rgba(16, 185, 129, 0.2);
            border-top-color: var(--emerald);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Security Warning Toast */
        .security-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #EF4444;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
            transition: transform 0.25s ease;
            z-index: 9999;
            pointer-events: none;
        }
        .security-toast.show {
            transform: translateX(-50%) translateY(0);
        }

        /* Screen Flash for PrintScreen */
        #screen-flash {
            position: fixed;
            inset: 0;
            background: #000;
            opacity: 0;
            pointer-events: none;
            z-index: 10000;
            transition: opacity 0.15s ease;
        }
    </style>
</head>
<body oncontextmenu="return false;">

    <div id="screen-flash"></div>

    {{-- Top Toolbar --}}
    <div class="viewer-topbar">
        <div class="topbar-left">
            <button onclick="window.close(); if(!window.closed){ location.href='{{ route('soal.view', $soal->slug ?: $soal->unique_token) }}'; }" class="btn-back">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Tutup
            </button>
            <div class="file-meta">
                <span class="file-title" title="{{ $attachment->file_original_name }}">{{ $attachment->file_original_name }}</span>
            </div>
        </div>

        <div class="topbar-right">
            <div class="zoom-controls">
                <button class="zoom-btn" onclick="adjustZoom(-0.1)" title="Perkecil">−</button>
                <span class="zoom-text" id="zoom-label">100%</span>
                <button class="zoom-btn" onclick="adjustZoom(0.1)" title="Perbesar">+</button>
                <button class="zoom-btn" onclick="resetZoom()" title="Reset Ukuran" style="font-size:11px; width:auto; padding:0 6px;">1:1</button>
            </div>

            <div class="badge-secure">
                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                <span>Mode Terproteksi</span>
            </div>
        </div>
    </div>

    {{-- Viewport --}}
    <div class="viewer-viewport" id="viewport">
        <div id="loading">
            <div class="spinner"></div>
        </div>

        <div class="zoom-container" id="zoom-container">
            <div id="viewer-elements"></div>
        </div>
    </div>

    <div class="security-toast" id="toast">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="toast-msg">Tindakan ini diblokir demi keamanan dokumen.</span>
    </div>

    @php
        $ext = strtolower($attachment->file_type ?? pathinfo($attachment->file_original_name, PATHINFO_EXTENSION));
        $rawUrl = route('soal.file', ['token' => $soal->slug ?: $soal->unique_token, 'attachment' => $attachment->id]);
        if($isAdminPreview) $rawUrl .= '?admin_preview=1';
    @endphp

    {{-- PDF.js CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let currentZoom = 1.0;
        const zoomContainer = document.getElementById('zoom-container');
        const zoomLabel     = document.getElementById('zoom-label');
        const viewerTarget  = document.getElementById('viewer-elements');
        const loadingEl     = document.getElementById('loading');

        function adjustZoom(delta) {
            currentZoom = Math.min(2.0, Math.max(0.6, currentZoom + delta));
            applyZoom();
        }

        function resetZoom() {
            currentZoom = 1.0;
            applyZoom();
        }

        function applyZoom() {
            zoomContainer.style.transform = 'scale(' + currentZoom + ')';
            zoomLabel.textContent = Math.round(currentZoom * 100) + '%';
        }

        // ─── Dynamic Canvas Watermark ───
        function drawWatermark(canvas) {
            const ctx = canvas.getContext('2d');
            ctx.save();

            const angle = -28 * Math.PI / 180;
            const stepY = 240;
            const stepX = 360;

            for (let y = 80; y < canvas.height + 250; y += stepY) {
                for (let x = -80; x < canvas.width + 250; x += stepX) {
                    ctx.save();
                    ctx.translate(x, y);
                    ctx.rotate(angle);

                    // Baris 1: Kode Unik & Brand
                    ctx.font = 'bold ' + Math.max(13, Math.floor(canvas.width / 42)) + 'px Inter, sans-serif';
                    ctx.fillStyle = 'rgba(16, 185, 129, 0.16)';
                    ctx.textAlign = 'center';
                    ctx.fillText('IC-LABS · {{ $soal->kode_unik }}', 0, 0);

                    // Baris 2: Status & Tanggal
                    ctx.font = '500 ' + Math.max(10, Math.floor(canvas.width / 58)) + 'px Inter, sans-serif';
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.11)';
                    ctx.fillText('DOKUMEN RAHASIA · {{ now()->format("d/m/Y") }}', 0, 16);

                    ctx.restore();
                }
            }

            ctx.restore();
        }

        // ─── Render Handler ───
        const isPdf = '{{ $ext }}' === 'pdf';

        if (isPdf) {
            const loadingTask = pdfjsLib.getDocument({
                url: '{{ $rawUrl }}',
                httpHeaders: {
                    'X-Lab-Preview': '{{ $previewToken }}'
                },
                withCredentials: true
            });

            loadingTask.promise.then(function(pdf) {
                loadingEl.style.display = 'none';

                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        const baseScale = 1.4;
                        const outputScale = window.devicePixelRatio || 1;
                        const viewport = page.getViewport({ scale: baseScale });

                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');

                        canvas.width = Math.floor(viewport.width * outputScale);
                        canvas.height = Math.floor(viewport.height * outputScale);
                        canvas.style.width = Math.floor(viewport.width) + "px";
                        canvas.style.height = Math.floor(viewport.height) + "px";
                        canvas.style.marginBottom = "20px";

                        viewerTarget.appendChild(canvas);

                        const renderContext = {
                            canvasContext: context,
                            transform: outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null,
                            viewport: viewport
                        };

                        page.render(renderContext).promise.then(function() {
                            drawWatermark(canvas);
                        });
                    });
                }
            }).catch(function(err) {
                loadingEl.innerHTML = '<span style="color:#f87171;">Gagal memuat dokumen: ' + (err.message || 'Akses ditolak.') + '</span>';
            });

        } else {
            // Render Gambar ke Canvas Aman
            fetch('{{ $rawUrl }}', {
                headers: {
                    'X-Lab-Preview': '{{ $previewToken }}'
                },
                credentials: 'same-origin'
            })
            .then(res => {
                if (!res.ok) throw new Error('Akses file ditolak.');
                return res.blob();
            })
            .then(blob => {
                const img = new Image();
                img.onload = () => {
                    loadingEl.style.display = 'none';
                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth || img.width;
                    canvas.height = img.naturalHeight || img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    drawWatermark(canvas);
                    viewerTarget.appendChild(canvas);
                    URL.revokeObjectURL(img.src);
                };
                img.src = URL.createObjectURL(blob);
            })
            .catch(err => {
                loadingEl.innerHTML = '<span style="color:#f87171;">Gagal memuat gambar: ' + err.message + '</span>';
            });
        }

        // ─── Proteksi Keamanan ───
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-msg');
        const screenFlash = document.getElementById('screen-flash');

        function triggerSecurityWarning(msg) {
            toastMsg.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        document.addEventListener('contextmenu', e => {
            e.preventDefault();
            triggerSecurityWarning('Klik kanan dinonaktifkan demi keamanan.');
        });

        document.addEventListener('dragstart', e => e.preventDefault());

        document.addEventListener('keydown', e => {
            // Blokir PrintScreen
            if (e.key === 'PrintScreen') {
                if (navigator.clipboard) navigator.clipboard.writeText('');
                screenFlash.style.opacity = '1';
                setTimeout(() => screenFlash.style.opacity = '0', 800);
                triggerSecurityWarning('Tangkapan layar dinonaktifkan demi keamanan dokumen!');
            }

            // Blokir Pintasan Keyboard Penyimpanan & Inspeksi
            const isCtrl = e.ctrlKey || e.metaKey;
            const key = e.key.toLowerCase();

            if (isCtrl && ['s', 'p', 'u', 'a'].includes(key)) {
                e.preventDefault();
                triggerSecurityWarning('Pintasan Ctrl+' + key.toUpperCase() + ' dinonaktifkan.');
            }

            // Blokir DevTools (F12, Ctrl+Shift+I/J/C)
            if (e.key === 'F12' || (isCtrl && e.shiftKey && ['i', 'j', 'c'].includes(key))) {
                e.preventDefault();
                triggerSecurityWarning('Alat pengembang dinonaktifkan.');
            }
        });

        // ─── Polling Status Akses & CSRF Keepalive ───
        setInterval(() => {
            fetch('{{ route('csrf.refresh') }}').catch(() => {});
        }, 5 * 60 * 1000);

        @if(!isset($isAdminPreview) || !$isAdminPreview)
        setInterval(() => {
            fetch('{{ route("soal.status", $soal->slug ?: $soal->unique_token) }}')
                .then(r => r.json())
                .then(data => {
                    if (data.access_status !== 'active') {
                        alert('Waktu akses untuk soal ini telah berakhir.');
                        window.location.reload();
                    }
                })
                .catch(() => {});
        }, 15000);
        @endif
    </script>
</body>
</html>
