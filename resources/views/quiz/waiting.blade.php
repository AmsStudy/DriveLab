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
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.08), transparent 70%), #0F1713;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
        }
        html:not(.dark) .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.06), transparent 70%), #FFFFFF !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05) !important;
        }

        /* Ambient Glow & Grid Pattern */
        .ambient-grid {
            background-image: radial-gradient(rgba(16, 185, 129, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        html:not(.dark) .ambient-grid {
            background-image: radial-gradient(rgba(16, 185, 129, 0.12) 1px, transparent 1px);
        }

        /* Radar Pulse Animation */
        @keyframes pulseRingOuter {
            0% { transform: scale(0.85); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 0; }
            100% { transform: scale(0.85); opacity: 0; }
        }
        @keyframes pulseRingInner {
            0% { transform: scale(0.9); opacity: 0.9; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.9); opacity: 0.9; }
        }
        .pulse-ring-outer {
            animation: pulseRingOuter 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .pulse-ring-inner {
            animation: pulseRingInner 2.2s ease-in-out infinite;
        }

        /* Subtle Sweep Animation */
        @keyframes sweepRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .sweep-effect {
            animation: sweepRotate 4s linear infinite;
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
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 py-8 md:py-12 flex flex-col justify-center relative">

        <div class="space-y-6">

            {{-- Main Hero Waiting Card --}}
            <div class="bg-white dark:bg-[#0F1713] border border-gray-200 dark:border-[#1A2820] rounded-3xl p-6 sm:p-10 glow-card relative overflow-hidden text-center shadow-xl">
                {{-- Decorative Ambient Glows --}}
                <div class="absolute -right-20 -top-20 w-72 h-72 bg-emerald-500/15 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-teal-500/10 dark:bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Status Chip --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-7 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Ruang Tunggu Ujian Aktif</span>
                </div>

                {{-- Enhanced Radar / Pulse Visualizer --}}
                <div class="relative w-32 h-32 mx-auto mb-7 flex items-center justify-center">
                    {{-- Outer pulsing radar circles --}}
                    <div class="absolute inset-0 rounded-full border-2 border-emerald-500/25 pulse-ring-outer"></div>
                    <div class="absolute inset-2.5 rounded-full border border-emerald-500/30 pulse-ring-inner"></div>
                    <div class="absolute inset-5 rounded-full border border-dashed border-emerald-500/40"></div>
                    
                    {{-- Rotating Sweep Beam Effect --}}
                    <div class="absolute inset-2 rounded-full overflow-hidden pointer-events-none opacity-40 dark:opacity-30">
                        <div class="w-full h-full sweep-effect origin-center bg-gradient-to-tr from-transparent via-transparent to-emerald-400/30"></div>
                    </div>

                    {{-- Core Icon Box --}}
                    <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-[#14231B] dark:to-[#0D1812] border-2 border-emerald-400/50 dark:border-emerald-500/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-lg shadow-emerald-500/20">
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Quiz Title --}}
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white heading-font mb-3 tracking-tight">
                    {{ $quiz->title }}
                </h1>

                {{-- Status Descriptions --}}
                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto mb-6 leading-relaxed">
                    Data Anda telah terverifikasi dan siap mengikuti ujian.
                    <span class="text-emerald-700 dark:text-emerald-400 font-semibold block mt-1.5 text-xs sm:text-sm">
                        Halaman ini akan otomatis beralih sendiri ke lembar soal saat pengawas menekan tombol <strong>Mulai</strong>. Anda tidak perlu me-refresh browser.
                    </span>
                </p>

                {{-- Waiting Room Live Participants Pill --}}
                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-2xl bg-emerald-50/70 dark:bg-[#090D0B] border border-emerald-200 dark:border-[#1E3327] shadow-xs mb-7">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">Peserta di Ruang Tunggu:</span>
                    <span class="font-mono font-bold text-sm text-emerald-800 dark:text-emerald-400" x-text="waitingCount + ' Mahasiswa'">{{ $waitingCount }} Mahasiswa</span>
                </div>

                {{-- Student Identity Info Card --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-xl mx-auto pt-6 border-t border-gray-100 dark:border-[#1A2820] text-left">
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-[#090D0B] border border-gray-200/80 dark:border-[#1A2820] hover:border-emerald-500/30 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">Nama Mahasiswa</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white mt-0.5 block truncate">{{ $attempt->student_name }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-[#090D0B] border border-gray-200/80 dark:border-[#1A2820] hover:border-emerald-500/30 transition-colors">
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-bold">NIM</span>
                        <span class="text-xs font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-0.5 block">{{ $attempt->nim }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-[#090D0B] border border-gray-200/80 dark:border-[#1A2820] hover:border-emerald-500/30 transition-colors">
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
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-sm text-amber-500">
                        💡
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Petunjuk Selama Menunggu</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-600 dark:text-gray-300">
                    <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-gray-50/70 dark:bg-[#090D0B]/70 border border-gray-100 dark:border-[#1A2820] hover:border-emerald-500/30 transition-all">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                            ⏱️
                        </div>
                        <div class="leading-relaxed">
                            <strong class="text-gray-900 dark:text-white block mb-0.5">Hitungan Mundur Waktu</strong>
                            Waktu ujian ({{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' menit' : 'tanpa batas' }}) akan mulai berjalan serentak begitu pengawas menekan tombol Mulai.
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-gray-50/70 dark:bg-[#090D0B]/70 border border-gray-100 dark:border-[#1A2820] hover:border-emerald-500/30 transition-all">
                        <div class="w-6 h-6 rounded-lg bg-rose-500/10 dark:bg-rose-500/20 border border-rose-500/30 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                            🛡️
                        </div>
                        <div class="leading-relaxed">
                            <strong class="text-gray-900 dark:text-white block mb-0.5">Sistem Anti-Cheat CBT</strong>
                            Dilarang berpindah tab, membuka jendela lain, atau keluar dari layar penuh guna mencegah sanksi pembekuan dan penguncian ujian otomatis.
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-gray-50/70 dark:bg-[#090D0B]/70 border border-gray-100 dark:border-[#1A2820] hover:border-emerald-500/30 transition-all">
                        <div class="w-6 h-6 rounded-lg bg-teal-500/10 dark:bg-teal-500/20 border border-teal-500/30 text-teal-600 dark:text-teal-400 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                            💾
                        </div>
                        <div class="leading-relaxed">
                            <strong class="text-gray-900 dark:text-white block mb-0.5">Penyimpanan Otomatis</strong>
                            Setiap pilihan jawaban otomatis tersimpan di cache lokal browser dan disinkronkan ke server pengawas secara berkala.
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-gray-50/70 dark:bg-[#090D0B]/70 border border-gray-100 dark:border-[#1A2820] hover:border-emerald-500/30 transition-all">
                        <div class="w-6 h-6 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                            🔔
                        </div>
                        <div class="leading-relaxed">
                            <strong class="text-gray-900 dark:text-white block mb-0.5">Kontrol Penuh Pengawas</strong>
                            Pengawas dapat menyesuaikan waktu tambahan atau mengakhiri sesi kapan saja sesuai arahan instruktur laboratorium.
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

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
</body>
</html>

