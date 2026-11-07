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
        }
        html:not(.dark) body {
            color: #0F1F17 !important;
        }
        html:not(.dark) .glow-card {
            background: #FFFFFF !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05) !important;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.08), transparent 70%), #0F1713;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
        }

        /* Radar Scanning Animation */
        @keyframes radarSweep {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .radar-sweep {
            animation: radarSweep 3s linear infinite;
        }
        @keyframes pulseRing {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.2; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }
        .pulse-ring {
            animation: pulseRing 2.4s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen selection:bg-emerald-500 selection:text-white"
      x-data="studentWaitingRoom({{ $quiz->id }}, {{ $attempt->id }}, '{{ $quiz->unique_token }}')"
      x-init="initWaiting()">

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
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 py-8 md:py-12 flex flex-col justify-center">

        <div class="space-y-6">

            {{-- Main Hero Waiting Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 md:p-10 glow-card relative overflow-hidden text-center shadow-lg">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Status Chip --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Ruang Tunggu Ujian Aktif</span>
                </div>

                {{-- Radar / Pulse Visualizer --}}
                <div class="relative w-28 h-28 mx-auto mb-6 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border border-emerald-500/20 pulse-ring"></div>
                    <div class="absolute inset-2 rounded-full border border-emerald-500/30"></div>
                    <div class="absolute inset-6 rounded-full border border-emerald-500/40"></div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-md">
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white heading-font mb-2 tracking-tight">
                    {{ $quiz->title }}
                </h1>

                <p class="text-sm text-gray-600 dark:text-gray-400 max-w-lg mx-auto mb-6 leading-relaxed">
                    Data Anda telah terverifikasi. Anda saat ini berada di ruang tunggu ujian laboratorium. 
                    <strong class="text-emerald-700 dark:text-emerald-400 font-semibold block mt-1">
                        Halaman ini akan otomatis beralih sendiri ke soal begitu pengawas memulai kuis. Anda tidak perlu me-refresh browser.
                    </strong>
                </p>

                {{-- Waiting Room Stats Pill --}}
                <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1E3327] shadow-inner mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Peserta di Ruang Tunggu:</span>
                    <span class="font-mono font-bold text-sm text-emerald-700 dark:text-emerald-400" x-text="waitingCount + ' Mahasiswa'">{{ $waitingCount }} Mahasiswa</span>
                </div>

                {{-- Student Identity Info Card --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-xl mx-auto pt-6 border-t border-gray-100 dark:border-[#1A2820] text-left">
                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820]">
                        <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">Nama Mahasiswa</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white mt-0.5 block truncate">{{ $attempt->student_name }}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820]">
                        <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">NIM</span>
                        <span class="text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $attempt->nim }}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820]">
                        <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">Kelas & Status</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-gray-200 mt-0.5 block">
                            {{ $attempt->kelas ? 'Kelas ' . $attempt->kelas : 'Reguler' }} &bull; <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Siap</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Exam Integrity & Instructions Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-emerald-600 dark:text-emerald-400 text-lg">💡</span>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Petunjuk Selama Menunggu</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-600 dark:text-gray-400">
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-gray-50 dark:bg-[#090D0B]/60 border border-gray-100 dark:border-[#1A2820]">
                        <span class="text-emerald-500 font-bold">•</span>
                        <span><strong>Hitungan Mundur:</strong> Waktu ujian ({{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' menit' : 'tanpa batas' }}) akan mulai berjalan saat pengawas menekan tombol Mulai.</span>
                    </div>
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-gray-50 dark:bg-[#090D0B]/60 border border-gray-100 dark:border-[#1A2820]">
                        <span class="text-emerald-500 font-bold">•</span>
                        <span><strong>Anti-Cheat CBT:</strong> Jangan beralih tab atau aplikasi setelah ujian dimulai untuk menghindari sanksi penguncian otomatis.</span>
                    </div>
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-gray-50 dark:bg-[#090D0B]/60 border border-gray-100 dark:border-[#1A2820]">
                        <span class="text-emerald-500 font-bold">•</span>
                        <span><strong>Auto-Save Real-time:</strong> Setiap jawaban yang Anda klik akan otomatis tersimpan ke server pengawas.</span>
                    </div>
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-gray-50 dark:bg-[#090D0B]/60 border border-gray-100 dark:border-[#1A2820]">
                        <span class="text-emerald-500 font-bold">•</span>
                        <span><strong>Selesai Tepat Waktu:</strong> Pengawas dapat menghentikan ujian dari monitor utama kapan saja sesuai arahan instruktur.</span>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <footer class="py-6 border-t border-gray-200 dark:border-[#1A2820] text-center text-xs text-gray-500 dark:text-gray-600 mt-auto">
        &copy; {{ date('Y') }} IC-Labs &bull; Platform Distribusi & Evaluasi Laboratorium
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
                // Poll every 2 seconds to check if instructor has started the quiz
                this.pollTimer = setInterval(() => {
                    this.checkStatus();
                }, 2000);
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
</body>
</html>

