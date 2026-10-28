<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arena Kuis: {{ $quiz->title }} &mdash; LabDrive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg: #090D0B;
            --card: #0F1713;
            --border: #1A2820;
            --emerald: #10B981;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #f3f4f6;
            min-height: 100vh;
            user-select: none;
            -webkit-user-select: none;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .option-card {
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .option-card:hover {
            transform: translateY(-2px);
        }
        .option-card:active {
            transform: translateY(0);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="quizArena()" x-init="initArena()" x-cloak class="flex flex-col min-h-screen bg-[#090D0B]">

    {{-- Top App Bar --}}
    <header class="border-b border-[#1A2820] bg-[#090D0B]/95 backdrop-blur-md sticky top-0 z-40 px-4 py-3">
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
            {{-- Quiz Title & Progress --}}
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 font-bold text-sm">
                    ⚡
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white heading-font truncate max-w-[180px] sm:max-w-xs">{{ $quiz->title }}</h2>
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <span class="font-mono text-emerald-400 font-bold" x-text="'Soal ' + (currentIndex + 1) + ' / ' + questions.length"></span>
                        <span>&bull;</span>
                        <span class="truncate">{{ $attempt->student_name }}</span>
                    </div>
                </div>
            </div>

            {{-- Timer & Anti-Cheat Controls --}}
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Countdown Timer (Jam:Menit:Detik) --}}
                <template x-if="remainingSeconds !== null">
                    <div class="px-3.5 py-1.5 rounded-xl border flex items-center gap-2 font-mono text-xs sm:text-sm font-bold transition-all shadow-sm"
                         :class="remainingSeconds <= 30 ? 'bg-red-500/20 border-red-500/40 text-red-400 animate-pulse' : (remainingSeconds <= 90 ? 'bg-amber-500/20 border-amber-500/40 text-amber-300' : 'bg-[#142019] border-[#1E3327] text-emerald-400')"
                         title="Sisa Waktu Pengerjaan">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="formatTime(remainingSeconds)"></span>
                    </div>
                </template>

                {{-- Tab Switch Badge --}}
                <div class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] flex items-center gap-1.5 text-xs font-medium text-gray-300"
                     :title="'Pelanggaran perpindahan tab: ' + tabSwitchCount">
                    <span class="text-xs">🛡️</span>
                    <span class="hidden sm:inline text-gray-400">Pengawas:</span>
                    <span :class="tabSwitchCount > 0 ? 'text-amber-400 font-bold' : 'text-emerald-400 font-bold'" x-text="tabSwitchCount + ' Switch'"></span>
                </div>

                {{-- Fullscreen Toggle --}}
                <button type="button" @click="toggleFullscreen()"
                        :title="isFullscreen ? 'Keluar dari Mode Layar Penuh' : 'Mode Layar Penuh (Menghilangkan gangguan browser agar fokus ujian)'"
                        class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] hover:border-emerald-500/40 text-gray-400 hover:text-white transition-all flex items-center gap-1.5 text-xs font-medium">
                    <template x-if="!isFullscreen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </template>
                    <template x-if="isFullscreen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                    <span class="hidden md:inline text-[11px]" x-text="isFullscreen ? 'Keluar Fullscreen' : 'Layar Penuh'"></span>
                </button>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="max-w-5xl mx-auto mt-2.5 h-1.5 bg-[#142019] rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300 rounded-full"
                 :style="'width: ' + ((currentIndex + 1) / questions.length * 100) + '%'"></div>
        </div>
    </header>

    {{-- Main Question Arena --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-6 sm:py-10 flex flex-col justify-between">

        <template x-if="currentQuestion">
            <div class="space-y-6">

                {{-- Question Card --}}
                <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 shadow-xl relative">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-mono"
                                  x-text="'Soal #' + currentQuestion.number"></span>
                            <span class="px-2.5 py-1 rounded-full text-xs bg-[#142019] text-gray-400 border border-[#1E3327]"
                                  x-text="currentQuestion.question_type === 'true_false' ? 'Benar / Salah' : 'Pilihan Ganda'"></span>
                        </div>

                        <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20"
                              x-text="'+' + currentQuestion.points + ' Poin'"></span>
                    </div>

                    <h1 class="text-lg sm:text-2xl font-bold text-white heading-font leading-relaxed whitespace-pre-wrap"
                        x-text="currentQuestion.question_text"></h1>
                </div>

                {{-- Options Grid (Quizizz Colorful Theme) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                    <template x-for="(opt, optIdx) in currentQuestion.options" :key="opt.id">
                        <button type="button"
                                @click="selectOption(currentQuestion.id, opt.id)"
                                class="option-card p-4 sm:p-5 rounded-2xl border text-left flex items-center gap-3.5 relative overflow-hidden group cursor-pointer"
                                :class="getOptionClasses(optIdx, isSelected(currentQuestion.id, opt.id))">
                            
                            {{-- Option Badge (A, B, C, D) --}}
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 transition-transform group-hover:scale-105"
                                 :class="getOptionBadgeClasses(optIdx, isSelected(currentQuestion.id, opt.id))">
                                <span x-text="getOptionLabel(optIdx)"></span>
                            </div>

                            {{-- Option Text --}}
                            <div class="flex-1 text-sm sm:text-base font-semibold leading-snug"
                                 :class="isSelected(currentQuestion.id, opt.id) ? 'text-white' : 'text-gray-200'"
                                 x-text="opt.text">
                            </div>

                            {{-- Active Selected Indicator --}}
                            <div x-show="isSelected(currentQuestion.id, opt.id)"
                                 class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Keyboard hint --}}
                <div class="text-center">
                    <span class="text-[11px] text-gray-500">
                        💡 Tips: Tekan angka <strong class="text-gray-400">1 - 4</strong> atau huruf <strong class="text-gray-400">A - D</strong> di keyboard untuk memilih jawaban cepat.
                    </span>
                </div>

            </div>
        </template>

        {{-- Bottom Navigation Controls --}}
        <div class="pt-6 sm:pt-8 flex items-center justify-between gap-3 border-t border-[#1A2820] mt-6">
            {{-- Prev Question --}}
            <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                    class="px-4 py-2.5 rounded-xl border border-[#1A2820] text-gray-300 hover:text-white hover:bg-[#142019] text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 disabled:opacity-30 disabled:pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Sebelumnya</span>
            </button>

            {{-- Question Drawer / Grid Button --}}
            <button type="button" @click="showDrawer = true"
                    class="px-3.5 py-2.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-emerald-500/40 text-xs sm:text-sm text-gray-300 hover:text-emerald-400 font-semibold transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                <span class="hidden sm:inline">Daftar Soal</span>
                <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400 text-xs font-mono font-bold"
                      x-text="answeredCount() + '/' + questions.length"></span>
            </button>

            {{-- Next or Submit Button --}}
            <div>
                <template x-if="currentIndex < questions.length - 1">
                    <button type="button" @click="nextQuestion()"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                        <span>Selanjutnya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </template>

                <template x-if="currentIndex === questions.length - 1">
                    <button type="button" @click="confirmSubmit()"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs sm:text-sm font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/30 animate-bounce">
                        <span>Selesai & Kumpulkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </template>
            </div>
        </div>

    </main>

    {{-- Question Navigation Drawer / Grid Modal --}}
    <div x-show="showDrawer"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">

        <div @click.away="showDrawer = false"
             class="bg-[#0F1713] border border-[#1A2820] rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#1A2820]">
                <div>
                    <h3 class="text-base font-bold text-white heading-font">Navigasi Butir Soal</h3>
                    <p class="text-xs text-gray-400">Pilih nomor soal untuk melompat langsung</p>
                </div>
                <button type="button" @click="showDrawer = false" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Grid of Questions --}}
            <div class="grid grid-cols-5 sm:grid-cols-6 gap-2.5 max-h-60 overflow-y-auto p-1">
                <template x-for="(q, idx) in questions" :key="q.id">
                    <button type="button"
                            @click="jumpToQuestion(idx)"
                            class="h-11 rounded-xl text-xs font-bold font-mono flex flex-col items-center justify-center border transition-all"
                            :class="idx === currentIndex ? 'border-emerald-400 bg-emerald-500/20 text-emerald-300 ring-2 ring-emerald-500/30' : (answers[q.id] ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-400' : 'bg-[#090D0B] border-[#1A2820] text-gray-400 hover:border-gray-600')">
                        <span x-text="idx + 1"></span>
                        <span class="text-[9px]" x-text="answers[q.id] ? '✓' : ''"></span>
                    </button>
                </template>
            </div>

            <div class="mt-4 pt-3 border-t border-[#1A2820] flex items-center justify-between text-xs">
                <div class="flex items-center gap-3 text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Dijawab</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-gray-700"></span> Belum</span>
                </div>
                <button type="button" @click="showDrawer = false"
                        class="px-4 py-1.5 rounded-xl bg-gray-800 text-gray-200 font-semibold hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Script Quiz Arena Logic --}}
    <script>
        function quizArena() {
            return {
                questions: @json($clientQuestions),
                remainingSeconds: {{ $timeRemaining !== null ? (int)$timeRemaining : 'null' }},
                currentIndex: 0,
                answers: {},
                tabSwitchCount: {{ (int)$attempt->tab_switch_count }},
                showDrawer: false,
                isSubmitting: false,
                timerInterval: null,
                isFullscreen: !!document.fullscreenElement,

                get currentQuestion() {
                    return this.questions[this.currentIndex] || null;
                },

                initArena() {
                    // Start timer if applicable (strictly integer countdown)
                    if (this.remainingSeconds !== null && this.remainingSeconds > 0) {
                        this.remainingSeconds = Math.max(0, Math.floor(Number(this.remainingSeconds)));
                        this.timerInterval = setInterval(() => {
                            if (this.remainingSeconds > 0) {
                                this.remainingSeconds--;
                            } else {
                                clearInterval(this.timerInterval);
                                this.autoSubmitExpired();
                            }
                        }, 1000);
                    }

                    // Fullscreen change listener
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });

                    // Setup Anti-Cheat Listeners
                    this.setupAntiCheat();

                    // Setup Keyboard shortcuts
                    this.setupKeyboardShortcuts();
                },

                setupAntiCheat() {
                    const notifyTabSwitch = () => {
                        this.tabSwitchCount++;
                        fetch('{{ route('quiz.tab-switch', [$quiz->unique_token, $attempt->id]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Peringatan: Perpindahan tab dicatat oleh pengawas kuis (' + this.tabSwitchCount + 'x)!',
                            showConfirmButton: false,
                            timer: 3500,
                            background: '#0F1713',
                            color: '#f87171',
                            customClass: { popup: 'border border-red-500/40 rounded-xl' }
                        });
                    };

                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            notifyTabSwitch();
                        }
                    });

                    window.addEventListener('blur', () => {
                        // Notify blur
                    });
                },

                setupKeyboardShortcuts() {
                    window.addEventListener('keydown', (e) => {
                        if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;

                        const key = e.key.toUpperCase();
                        if (['1', '2', '3', '4'].includes(key)) {
                            const optIdx = parseInt(key) - 1;
                            if (this.currentQuestion && this.currentQuestion.options[optIdx]) {
                                this.selectOption(this.currentQuestion.id, this.currentQuestion.options[optIdx].id);
                            }
                        } else if (['A', 'B', 'C', 'D'].includes(key)) {
                            const optIdx = key.charCodeAt(0) - 65;
                            if (this.currentQuestion && this.currentQuestion.options[optIdx]) {
                                this.selectOption(this.currentQuestion.id, this.currentQuestion.options[optIdx].id);
                            }
                        } else if (e.key === 'ArrowRight') {
                            this.nextQuestion();
                        } else if (e.key === 'ArrowLeft') {
                            this.prevQuestion();
                        }
                    });
                },

                selectOption(questionId, optionId) {
                    this.answers[questionId] = optionId;
                },

                isSelected(questionId, optionId) {
                    return this.answers[questionId] === optionId;
                },

                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },

                jumpToQuestion(index) {
                    this.currentIndex = index;
                    this.showDrawer = false;
                },

                answeredCount() {
                    return Object.keys(this.answers).length;
                },

                confirmSubmit() {
                    const answered = this.answeredCount();
                    const total = this.questions.length;
                    const unanswered = total - answered;

                    let message = `Anda telah menjawab ${answered} dari ${total} pertanyaan.`;
                    if (unanswered > 0) {
                        message += ` Terdapat ${unanswered} pertanyaan yang belum dijawab. Yakin ingin mengumpulkan sekarang?`;
                    } else {
                        message += ` Seluruh pertanyaan telah terisi. Yakin ingin mengumpulkan?`;
                    }

                    Swal.fire({
                        title: 'Kumpulkan Jawaban?',
                        text: message,
                        icon: unanswered > 0 ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#1A2820',
                        confirmButtonText: 'Ya, Kumpulkan Sekarang',
                        cancelButtonText: 'Periksa Kembali',
                        background: '#0F1713',
                        color: '#f3f4f6',
                        customClass: {
                            popup: 'border border-[#1A2820] rounded-2xl',
                            confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold',
                            cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submitQuiz();
                        }
                    });
                },

                submitQuiz() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    Swal.fire({
                        title: 'Mengirim Jawaban...',
                        text: 'Sistem sedang menghitung skor Anda.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        background: '#0F1713',
                        color: '#f3f4f6'
                    });

                    fetch('{{ route('quiz.submit', [$quiz->unique_token, $attempt->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ answers: this.answers })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(() => {
                        this.isSubmitting = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengirim',
                            text: 'Terjadi gangguan koneksi internet. Silakan coba lagi.',
                            background: '#0F1713',
                            color: '#f3f4f6'
                        });
                    });
                },

                autoSubmitExpired() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Waktu Pengerjaan Habis!',
                        text: 'Sistem mengumpulkan jawaban Anda secara otomatis.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 2000,
                        background: '#0F1713',
                        color: '#f3f4f6'
                    }).then(() => {
                        this.submitQuiz();
                    });
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(() => {});
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        }
                    }
                },

                formatTime(totalSec) {
                    if (totalSec === null || totalSec === undefined || isNaN(totalSec)) return '00:00:00';
                    const secInt = Math.max(0, Math.floor(Number(totalSec)));
                    const hours = Math.floor(secInt / 3600);
                    const minutes = Math.floor((secInt % 3600) / 60);
                    const seconds = secInt % 60;

                    const pad = (n) => String(n).padStart(2, '0');
                    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                },

                getOptionLabel(idx) {
                    return ['A', 'B', 'C', 'D', 'E', 'F'][idx] || (idx + 1);
                },

                getOptionClasses(idx, selected) {
                    if (selected) {
                        return 'border-emerald-400 bg-emerald-500/20 shadow-lg shadow-emerald-500/10 ring-2 ring-emerald-500/40';
                    }
                    const palettes = [
                        'bg-[#0F1713] border-emerald-500/30 hover:border-emerald-500/70',
                        'bg-[#0F1713] border-sky-500/30 hover:border-sky-500/70',
                        'bg-[#0F1713] border-amber-500/30 hover:border-amber-500/70',
                        'bg-[#0F1713] border-purple-500/30 hover:border-purple-500/70'
                    ];
                    return palettes[idx % 4] || 'bg-[#0F1713] border-[#1A2820]';
                },

                getOptionBadgeClasses(idx, selected) {
                    if (selected) {
                        return 'bg-emerald-500 text-white';
                    }
                    const badgePalettes = [
                        'bg-emerald-500/15 text-emerald-400',
                        'bg-sky-500/15 text-sky-400',
                        'bg-amber-500/15 text-amber-400',
                        'bg-purple-500/15 text-purple-400'
                    ];
                    return badgePalettes[idx % 4] || 'bg-gray-800 text-gray-300';
                }
            };
        }
    </script>
</body>
</html>
