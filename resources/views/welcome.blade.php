<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabDrive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #090D0B;
            --surface: #0C1410;
            --card:    #0F1713;
            --border:  #162118;
            --border2: #1E2D25;
            --emerald: #10B981;
            --emerald2:#059669;
            --mint:    #34D399;
            --teal:    #0D9488;
            --sky:     #38BDF8;
            --muted:   #5A6B62;
            --muted2:  #8FA898;
            --text:    #E2EBE6;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        /* ── Background ── */
        .bg-wrap {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
        }
        .bg-noise {
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: 0.4;
        }
        .bg-glow-1 {
            position: absolute;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,.07) 0%, transparent 70%);
            top: -200px; left: -200px;
            animation: drift1 18s ease-in-out infinite alternate;
        }
        .bg-glow-2 {
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13,148,136,.06) 0%, transparent 70%);
            bottom: -150px; right: -100px;
            animation: drift2 22s ease-in-out infinite alternate;
        }
        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(16,185,129,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16,185,129,.025) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
        }
        @keyframes drift1 { from { transform: translate(0,0) scale(1); } to { transform: translate(60px, 40px) scale(1.1); } }
        @keyframes drift2 { from { transform: translate(0,0) scale(1.05); } to { transform: translate(-50px, -30px) scale(0.95); } }

        /* ── Layout ── */
        .gateway {
            position: relative; z-index: 10;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ── Admin corner link ── */
        .corner-link {
            position: fixed; top: 24px; right: 28px;
            font-size: 12px; font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            display: flex; align-items: center; gap: 6px;
            letter-spacing: .02em;
            transition: color .2s;
            z-index: 20;
        }
        .corner-link:hover { color: var(--muted2); }
        .corner-link svg { opacity: .6; }

        /* ── Card ── */
        .gate-card {
            width: 100%;
            max-width: 420px;
        }

        /* ── Wordmark ── */
        .wordmark {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 36px;
        }
        .wordmark-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--emerald), var(--teal));
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 20px rgba(16,185,129,.22), 0 2px 8px rgba(0,0,0,.4);
            flex-shrink: 0;
        }
        .wordmark-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 22px;
            color: #fff;
            letter-spacing: -.3px;
        }

        /* ── Tabs ── */
        .tabs {
            display: flex; gap: 0;
            margin-bottom: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 4px;
        }
        .tab-btn {
            flex: 1;
            background: none; border: none;
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 12px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .tab-btn.active-doc  { background: rgba(16,185,129,.15); color: var(--mint); }
        .tab-btn.active-quiz { background: rgba(56,189,248,.12); color: var(--sky); }
        .tab-btn.active-lacak { background: rgba(52,211,153,.1); color: var(--mint); }
        .tab-btn:not([class*="active"]):hover { color: var(--muted2); }

        /* ── Input group ── */
        .input-group {
            display: flex;
            background: var(--card);
            border: 1px solid var(--border2);
            border-radius: 14px;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }
        .input-group:focus-within {
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px rgba(16,185,129,.1), 0 4px 16px rgba(0,0,0,.3);
        }
        .input-group.quiz-focus:focus-within {
            border-color: var(--sky);
            box-shadow: 0 0 0 3px rgba(56,189,248,.1), 0 4px 16px rgba(0,0,0,.3);
        }
        .input-icon {
            display: flex; align-items: center;
            padding: 0 15px;
            color: var(--muted);
            flex-shrink: 0;
        }
        .main-input {
            flex: 1;
            background: transparent; border: none; outline: none;
            color: #fff;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            padding: 15px 0;
            letter-spacing: .01em;
        }
        .main-input::placeholder { color: #2D3D34; font-size: 14px; }
        .submit-btn {
            border: none; cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 13px; font-weight: 700;
            padding: 0 22px;
            flex-shrink: 0;
            transition: opacity .2s, filter .15s;
            letter-spacing: .05em;
        }
        .submit-btn:hover { opacity: .88; filter: brightness(1.1); }
        .submit-btn:disabled { opacity: .5; cursor: not-allowed; }
        .btn-green  { background: linear-gradient(135deg, var(--emerald), var(--teal)); color: #fff; }
        .btn-blue   { background: linear-gradient(135deg, #0284c7, #06b6d4); color: #fff; }

        /* ── Feedback ── */
        .feedback {
            font-size: 12px;
            margin-top: 10px;
            padding-left: 2px;
            min-height: 18px;
            transition: opacity .2s;
        }
        .feedback.err  { color: #FC8181; }
        .feedback.ok   { color: var(--mint); }

        /* ── Separator ── */
        .sep {
            display: flex; align-items: center; gap: 10px;
            margin: 22px 0 0;
        }
        .sep-line { flex: 1; height: 1px; background: var(--border); }
        .sep-label {
            font-size: 10px; font-weight: 600;
            color: #253328;
            text-transform: uppercase; letter-spacing: .1em;
        }

        /* ── Track result ── */
        .track-result {
            margin-top: 14px;
            background: var(--card);
            border: 1px solid var(--border2);
            border-radius: 12px;
            padding: 14px;
            max-height: 220px;
            overflow-y: auto;
        }
        .track-result::-webkit-scrollbar { width: 4px; }
        .track-result::-webkit-scrollbar-track { background: transparent; }
        .track-result::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }
        .track-title {
            font-size: 12px; font-weight: 600;
            color: var(--muted2);
            margin-bottom: 10px;
        }
        .task-item {
            background: rgba(255,255,255,.025);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 6px;
        }
        .task-item:last-child { margin-bottom: 0; }
        .task-name  { font-size: 13px; font-weight: 500; color: var(--mint); margin-bottom: 3px; }
        .task-file  { font-size: 11px; color: var(--muted2); }
        .task-date  { font-size: 10px; color: var(--muted); margin-top: 2px; }
        .badge-type {
            display: inline-block;
            font-size: 9px; font-weight: 700; font-family: monospace;
            padding: 1px 5px;
            border-radius: 4px;
            border: 1px solid rgba(16,185,129,.3);
            background: rgba(16,185,129,.1);
            color: var(--mint);
            letter-spacing: .05em;
            margin-right: 4px;
            text-transform: uppercase;
        }
        .badge-type.link { border-color: rgba(13,148,136,.4); background: rgba(13,148,136,.12); color: #5eead4; }
        .badge-type.text { border-color: rgba(251,191,36,.3); background: rgba(251,191,36,.08); color: #fbbf24; }

        /* ── Footer tagline ── */
        .tagline {
            margin-top: 32px;
            font-size: 11px;
            color: #253328;
            letter-spacing: .05em;
            text-align: center;
            text-transform: uppercase;
        }

        /* ── Mobile ── */
        @media (max-width: 480px) {
            .gateway { padding: 16px; justify-content: center; }
            html, body { overflow: auto; }
            .tab-btn { font-size: 10px; padding: 7px 6px; }
        }
    </style>
</head>
<body>

    {{-- Background --}}
    <div class="bg-wrap" aria-hidden="true">
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>
        <div class="bg-grid"></div>
        <div class="bg-noise"></div>
    </div>

    {{-- Corner admin link --}}
    @auth
        <a href="{{ route('admin.dashboard') }}" class="corner-link">
            Dashboard
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <a href="{{ route('login') }}" class="corner-link">
            Portal Admin
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @endauth

    {{-- Main gateway --}}
    <main class="gateway">
        <div class="gate-card" x-data="gateway()">

            {{-- Wordmark --}}
            <div class="wordmark">
                <div class="wordmark-icon">
                    <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="wordmark-name">LabDrive</span>
            </div>

            {{-- Tabs --}}
            <div class="tabs" role="tablist">
                <button
                    class="tab-btn"
                    :class="{ 'active-doc': tab === 'akses' }"
                    @click="tab = 'akses'"
                    role="tab"
                    :aria-selected="tab === 'akses'"
                >Dokumen</button>
                <button
                    class="tab-btn"
                    :class="{ 'active-quiz': tab === 'kuis' }"
                    @click="tab = 'kuis'"
                    role="tab"
                    :aria-selected="tab === 'kuis'"
                >Kuis</button>
                <button
                    class="tab-btn"
                    :class="{ 'active-lacak': tab === 'lacak' }"
                    @click="tab = 'lacak'"
                    role="tab"
                    :aria-selected="tab === 'lacak'"
                >Lacak Tugas</button>
            </div>

            {{-- Panel: Akses Dokumen --}}
            <div x-show="tab === 'akses'" x-transition.opacity role="tabpanel">
                <form action="{{ route('soal.accessByCode') }}" method="POST" @submit="submitting = true">
                    @csrf
                    <div class="input-group">
                        <div class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="kode_unik"
                            class="main-input"
                            placeholder="Masukkan kode akses soal"
                            required
                            autocomplete="off"
                            autofocus
                        >
                        <button type="submit" class="submit-btn btn-green" :disabled="submitting">
                            <span x-show="!submitting">Akses</span>
                            <span x-show="submitting">...</span>
                        </button>
                    </div>
                </form>
                @if(session('error'))
                    <p class="feedback err">{{ session('error') }}</p>
                @elseif(session('success'))
                    <p class="feedback ok">{{ session('success') }}</p>
                @else
                    <div class="feedback"></div>
                @endif
            </div>

            {{-- Panel: Gabung Kuis --}}
            <div x-show="tab === 'kuis'" x-transition.opacity role="tabpanel" style="display:none">
                <form action="{{ route('quiz.join') }}" method="POST" @submit="submitting = true">
                    @csrf
                    <div class="input-group quiz-focus">
                        <div class="input-icon" style="color:var(--sky)">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="access_code"
                            class="main-input"
                            placeholder="Masukkan kode kuis"
                            required
                            autocomplete="off"
                            style="text-transform:uppercase"
                        >
                        <button type="submit" class="submit-btn btn-blue" :disabled="submitting">
                            <span x-show="!submitting">Masuk</span>
                            <span x-show="submitting">...</span>
                        </button>
                    </div>
                </form>
                @if(session('quiz_error'))
                    <p class="feedback err">{{ session('quiz_error') }}</p>
                @else
                    <div class="feedback"></div>
                @endif
            </div>

            {{-- Panel: Lacak Tugas --}}
            <div x-show="tab === 'lacak'" x-transition.opacity role="tabpanel" style="display:none" x-data="lacakTugas()">
                <form @submit.prevent="submitLacak">
                    <div class="input-group">
                        <div class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            x-model="nim"
                            class="main-input"
                            placeholder="Masukkan NIM"
                            autocomplete="off"
                        >
                        <button type="submit" class="submit-btn btn-green" :disabled="loading">
                            <span x-show="!loading">Cari</span>
                            <span x-show="loading">...</span>
                        </button>
                    </div>
                </form>

                {{-- Error --}}
                <p class="feedback err" x-show="errorMsg" x-text="errorMsg"></p>

                {{-- Result --}}
                <div class="track-result" x-show="result" x-transition.opacity>
                    <p class="track-title" x-text="resultTitle"></p>
                    <template x-for="item in tasks" :key="item.id">
                        <div class="task-item">
                            <p class="task-name" x-text="item.soal_title"></p>
                            <p class="task-file">
                                <span class="badge-type" :class="item.submission_type === 'link' ? 'link' : (item.submission_type === 'text' ? 'text' : '')" x-text="item.submission_type || 'file'"></span>
                                <template x-if="item.submission_type === 'link'">
                                    <a :href="item.link_url" target="_blank" rel="noopener" style="color:var(--sky);text-decoration:none;" x-text="item.file_name"></a>
                                </template>
                                <template x-if="item.submission_type !== 'link'">
                                    <span x-text="item.file_name"></span>
                                </template>
                            </p>
                            <p class="task-date" x-text="'Dikumpulkan: ' + item.created_at"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Bottom separator --}}
            <div class="sep">
                <div class="sep-line"></div>
                <span class="sep-label">Internal Platform</span>
                <div class="sep-line"></div>
            </div>

            {{-- Tagline --}}
            <p class="tagline">Sistem Distribusi Soal &amp; Modul Laboratorium</p>

        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('gateway', () => ({
                tab: 'akses',
                submitting: false,
            }));

            Alpine.data('lacakTugas', () => ({
                nim: '',
                loading: false,
                result: false,
                resultTitle: '',
                errorMsg: '',
                tasks: [],

                async submitLacak() {
                    if (!this.nim.trim()) return;
                    this.loading  = true;
                    this.result   = false;
                    this.errorMsg = '';

                    try {
                        const res  = await fetch(`{{ route('soal.trackTask') }}?nim=${encodeURIComponent(this.nim)}`);
                        const data = await res.json();
                        this.loading = false;

                        if (!res.ok) {
                            this.errorMsg = data.message || 'Terjadi kesalahan saat melacak tugas.';
                            return;
                        }

                        this.result = true;
                        if (data.data && data.data.length > 0) {
                            this.resultTitle = `${data.data.length} tugas ditemukan — ${data.data[0].nama}`;
                            this.tasks = data.data;
                        } else {
                            this.resultTitle = `Belum ada tugas yang dikumpulkan untuk NIM ${this.nim}.`;
                            this.tasks = [];
                        }
                    } catch (e) {
                        this.loading  = false;
                        this.errorMsg = 'Gagal terhubung ke server.';
                    }
                }
            }));
        });
    </script>

</body>
</html>
