<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arena Kuis: {{ $quiz->title }} &mdash; IC-Labs</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iclabs-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
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
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20 flex-shrink-0">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
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

                {{-- Anti-Cheat / Open Book Mode Status Badge --}}
                @if($quiz->allow_open_book)
                    <div class="px-2.5 py-1.5 rounded-xl bg-sky-500/10 border border-sky-500/25 flex items-center gap-1.5 text-xs font-semibold text-sky-400"
                         title="Kuis ini mengizinkan mahasiswa membuka modul atau referensi materi eksternal">
                        <span>📖</span>
                        <span class="hidden sm:inline">Bisa Lihat Modul</span>
                    </div>
                @else
                    <div class="px-2.5 py-1.5 rounded-xl border flex items-center gap-1.5 text-xs font-medium"
                         :class="isLocked ? 'bg-rose-500/20 border-rose-500/40 text-rose-300 animate-pulse' : (tabSwitchCount > 0 ? 'bg-amber-500/15 border-amber-500/30 text-amber-300' : 'bg-[#142019] border-[#1E3327] text-emerald-400')"
                         :title="'Status Pengawas CBT: ' + tabSwitchCount + ' kali terdeteksi switch tab/aplikasi'">
                        <span x-text="isLocked ? '🔒' : (tabSwitchCount > 0 ? '⚠️' : '🛡️')"></span>
                        <span class="hidden sm:inline text-gray-400">Pengawas:</span>
                        <span class="font-bold" x-text="isLocked ? 'TERKUNCI' : tabSwitchCount + '/3 Pelanggaran'"></span>
                    </div>
                @endif

                {{-- Autosave Sync Status Indicator --}}
                <div class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] flex items-center gap-1.5 text-xs font-medium"
                     :title="syncStatus === 'saving' ? 'Menyimpan jawaban ke server...' : (syncStatus === 'synced' ? 'Semua jawaban tersimpan aman di server & memori lokal' : 'Jaringan terputus: jawaban tersimpan aman di perangkat')">
                    <template x-if="syncStatus === 'saving'">
                        <span class="text-sky-400 flex items-center gap-1">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span class="hidden lg:inline text-[11px]">Menyimpan...</span>
                        </span>
                    </template>
                    <template x-if="syncStatus === 'synced'">
                        <span class="text-emerald-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="hidden lg:inline text-[11px]">Tersimpan</span>
                        </span>
                    </template>
                    <template x-if="syncStatus === 'offline'">
                        <span class="text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="hidden lg:inline text-[11px]">Lokal (Offline)</span>
                        </span>
                    </template>
                </div>

                @if($quiz->allow_open_book)
                    {{-- Fullscreen Toggle allowed in Open Book mode --}}
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
                @else
                    {{-- Kiosk Enforced Indicator (No manual exit button in strict mode) --}}
                    <div class="px-2.5 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327] flex items-center gap-1.5 text-xs font-medium text-emerald-400"
                         title="Ujian berjalan dalam Mode Kiosk Layar Penuh Terkunci">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="hidden md:inline text-[11px] font-mono text-emerald-300">Kiosk Terkunci</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="max-w-5xl mx-auto mt-2.5 h-1.5 bg-[#142019] rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300 rounded-full"
                 :style="'width: ' + ((currentIndex + 1) / questions.length * 100) + '%'"></div>
        </div>
    </header>

    {{-- Main Question Arena (Zero-Visibility Curtain if not in Fullscreen during strict exam) --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-6 sm:py-10 flex flex-col justify-between transition-all duration-300"
          :class="(!allowOpenBook && !isFullscreen && !isLocked && penaltySeconds === 0) ? 'filter blur-2xl pointer-events-none select-none opacity-5 scale-95' : ''">

        <template x-if="currentQuestion">
            <div class="space-y-6">

                {{-- Question Card --}}
                <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 shadow-xl relative">
                    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-mono"
                                  x-text="'Soal #' + currentQuestion.number"></span>
                            
                            <template x-if="currentQuestion.question_type === 'multi_select'">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-500/15 text-purple-300 border border-purple-500/30 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pilihan Jamak (Centang Semua Jawaban Benar)
                                </span>
                            </template>
                            <template x-if="currentQuestion.question_type === 'true_false'">
                                <span class="px-2.5 py-1 rounded-full text-xs bg-[#142019] text-gray-400 border border-[#1E3327]">
                                    Benar / Salah
                                </span>
                            </template>
                            <template x-if="currentQuestion.question_type === 'multiple_choice'">
                                <span class="px-2.5 py-1 rounded-full text-xs bg-[#142019] text-gray-400 border border-[#1E3327]">
                                    Pilihan Tunggal
                                </span>
                            </template>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Tombol Ragu-ragu (Mark for Review) --}}
                            <button type="button"
                                    @click="toggleFlagQuestion(currentQuestion.id)"
                                    class="px-3 py-1 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 border shadow-sm cursor-pointer"
                                    :class="isFlagged(currentQuestion.id) 
                                        ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 ring-2 ring-amber-500/25 shadow-amber-500/20 animate-pulse' 
                                        : 'bg-[#142019] text-gray-400 border-[#1E3327] hover:text-amber-300 hover:border-amber-500/40 hover:bg-amber-500/10'"
                                    :title="isFlagged(currentQuestion.id) ? 'Klik untuk membatalkan tanda ragu-ragu' : 'Tandai soal ini sebagai ragu-ragu untuk ditinjau ulang'">
                                <span x-text="isFlagged(currentQuestion.id) ? '🚩' : '🏳️'"></span>
                                <span x-text="isFlagged(currentQuestion.id) ? 'Ragu-ragu' : 'Tandai Ragu'"></span>
                            </button>

                            <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20"
                                  x-text="'+' + currentQuestion.points + ' Poin'"></span>
                        </div>
                    </div>

                    {{-- Question Image (if uploaded) --}}
                    <template x-if="currentQuestion.image_url">
                        <div class="my-4 rounded-2xl overflow-hidden border border-[#1A2820] bg-black/50 max-h-80 flex items-center justify-center p-2">
                            <img :src="currentQuestion.image_url" alt="Gambar Soal"
                                 class="max-h-72 w-auto object-contain rounded-xl hover:scale-[1.01] transition-transform">
                        </div>
                    </template>

                    {{-- Question Text with Syntax Highlighting Support --}}
                    <div class="text-lg sm:text-2xl font-bold text-white heading-font leading-relaxed"
                         x-html="renderRichText(currentQuestion.question_text)"></div>
                </div>

                {{-- Options Grid (Quizizz Colorful Theme with Image & Rich Text Support) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                    <template x-for="(opt, optIdx) in currentQuestion.options" :key="opt.id">
                        <button type="button"
                                @click="selectOption(currentQuestion.id, opt.id)"
                                class="option-card p-4 sm:p-5 rounded-2xl border text-left flex items-start gap-3.5 relative overflow-hidden group cursor-pointer transition-all"
                                :class="getOptionClasses(optIdx, isSelected(currentQuestion.id, opt.id))">
                            
                            {{-- Option Badge (A, B, C, D) / Checkbox indicator --}}
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 transition-transform group-hover:scale-105 mt-0.5"
                                 :class="getOptionBadgeClasses(optIdx, isSelected(currentQuestion.id, opt.id))">
                                <span x-text="getOptionLabel(optIdx)"></span>
                            </div>

                            {{-- Option Content (Text + Image) --}}
                            <div class="flex-1 min-w-0">
                                <template x-if="opt.image_url">
                                    <div class="mb-2 max-h-36 overflow-hidden rounded-xl border border-white/10 bg-black/40 p-1">
                                        <img :src="opt.image_url" alt="Gambar Opsi" class="max-h-32 w-auto object-contain rounded-lg">
                                    </div>
                                </template>
                                <div class="text-sm sm:text-base font-semibold leading-snug"
                                     :class="isSelected(currentQuestion.id, opt.id) ? 'text-white' : 'text-gray-200'"
                                     x-html="renderRichText(opt.text)">
                                </div>
                            </div>

                            {{-- Active Selected Indicator --}}
                            <div x-show="isSelected(currentQuestion.id, opt.id)"
                                 class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-md mt-0.5">
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
                <template x-if="flaggedCount() > 0">
                    <span class="px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-xs font-mono font-bold flex items-center gap-1"
                          title="Jumlah Soal Ragu-ragu">
                        <span>🚩</span>
                        <span x-text="flaggedCount()"></span>
                    </span>
                </template>
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
                            class="h-12 rounded-xl text-xs font-bold font-mono flex flex-col items-center justify-center border transition-all relative"
                            :class="idx === currentIndex ? 'border-emerald-400 bg-emerald-500/25 text-emerald-300 ring-2 ring-emerald-500/30 font-extrabold' : (isFlagged(q.id) ? 'bg-amber-500/20 border-amber-500/60 text-amber-300' : (isQuestionAnswered(q.id) ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-400' : 'bg-[#090D0B] border-[#1A2820] text-gray-400 hover:border-gray-600'))">
                        <span x-text="idx + 1"></span>
                        <div class="flex items-center gap-0.5 text-[9px] mt-0.5 leading-none">
                            <template x-if="isFlagged(q.id)">
                                <span>🚩</span>
                            </template>
                            <template x-if="isQuestionAnswered(q.id)">
                                <span class="text-emerald-400 font-bold">✓</span>
                            </template>
                        </div>
                    </button>
                </template>
            </div>

            <div class="mt-4 pt-3 border-t border-[#1A2820] flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-3 text-gray-400 flex-wrap">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Dijawab (<span x-text="answeredCount()"></span>)</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Ragu-ragu (<span x-text="flaggedCount()"></span>)</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-gray-700"></span> Belum</span>
                </div>
                <button type="button" @click="showDrawer = false"
                        class="px-4 py-1.5 rounded-xl bg-gray-800 text-gray-200 font-semibold hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 1: Freeze Cooldown Screen (Strike 1: 60s, Strike 2: 90s)        --}}
    {{-- ========================================================================= --}}
    <div x-show="!isLocked && penaltySeconds > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
        
        <div class="bg-[#0F1713] border-2 border-amber-500/40 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            {{-- Glowing Ambient --}}
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-16 h-16 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                ⚠️
            </div>

            <template x-if="tabSwitchCount <= 1 || strike === 1">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-2">
                        Peringatan 1 dari 3: Sanksi 1 Menit
                    </span>
                    <h3 class="text-xl font-bold text-white heading-font">Terdeteksi Berpindah Tab / Layar!</h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Anda terdeteksi meninggalkan halaman kuis. Sebagai sanksi, layar kuis dibekukan selama <strong class="text-amber-400">1 Menit (60 detik)</strong>.
                    </p>
                </div>
            </template>

            <template x-if="tabSwitchCount >= 2 && strike >= 2">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30 mb-2">
                        Peringatan 2 dari 3: Sanksi 1.5 Menit
                    </span>
                    <h3 class="text-xl font-bold text-white heading-font">Pelanggaran Berulang Terdeteksi!</h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Sanksi bertambah kelipatan menjadi <strong class="text-rose-400">1 Menit 30 Detik (90s)</strong>.
                        <br><span class="text-rose-300 font-semibold">PERINGATAN: 1x lagi terdeteksi, kuis Anda akan TERKUNCI TOTAL!</span>
                    </p>
                </div>
            </template>

            {{-- Big Timer Countdown --}}
            <div class="my-6 p-4 rounded-2xl bg-[#090D0B] border border-amber-500/30">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-1">Layar Terbuka Otomatis Dalam:</div>
                <div class="text-4xl sm:text-5xl font-mono font-extrabold text-amber-400 tracking-wider animate-pulse"
                     x-text="formatTime(penaltySeconds)"></div>
            </div>

            <div class="p-3 rounded-xl bg-[#142019] border border-[#1E3327] text-[11px] text-gray-400 text-left">
                💡 <strong>Catatan Integritas:</strong> Timer sanksi disimpan di server. Merefresh (F5) atau menutup browser tidak akan menghilangkan waktu tunggu ini.
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 2: Full Lockout Screen (Strike 3 - Butuh Password Admin)           --}}
    {{-- ========================================================================= --}}
    <div x-show="isLocked"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 bg-black/95 backdrop-blur-lg flex items-center justify-center p-4">
        
        <div class="bg-[#0F1713] border-2 border-rose-500/60 rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            {{-- Glowing Ambient --}}
            <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-64 h-64 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                ⛔
            </div>

            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/25 text-rose-300 border border-rose-500/40 mb-2">
                Strike 3: Kuis Terkunci Total
            </span>
            <h3 class="text-2xl font-extrabold text-white heading-font">Ujian Dibekukan Secara Permanen</h3>
            
            <p class="text-xs sm:text-sm text-gray-300 mt-2 max-w-sm mx-auto leading-relaxed">
                Anda telah terdeteksi melakukan <strong class="text-rose-400">3 kali perpindahan layar/tab</strong>. Hak akses pengerjaan kuis Anda telah dikunci demi integritas ujian.
            </p>

            <div class="my-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-left text-xs text-rose-200">
                <div class="font-bold flex items-center gap-1.5 mb-1 text-rose-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Instruksi Pembukaan Kunci:
                </div>
                Silakan panggil <strong>Pengawas Ujian / Dosen / Asisten Lab</strong> yang bertugas. Pengawas akan memasukkan password otorisasi untuk membuka kembali sesi kuis Anda.
            </div>

            {{-- Admin Password Unlock Form --}}
            <form @submit.prevent="submitUnlockPassword()" class="space-y-3 pt-2 text-left">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1">
                        Password Otorisasi Pengawas:
                    </label>
                    <div class="relative">
                        <input type="password" x-model="adminPassword" placeholder="Masukkan password pengawas..."
                               class="w-full px-4 py-3 bg-[#090D0B] border border-rose-500/40 focus:border-rose-400 focus:ring-1 focus:ring-rose-400 rounded-xl text-sm text-white placeholder-gray-500 font-mono tracking-wider"
                               required :disabled="isUnlocking">
                    </div>
                </div>

                {{-- Error Message Alert --}}
                <template x-if="unlockError">
                    <div class="p-2.5 rounded-xl bg-red-950/50 border border-red-500/50 text-red-300 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span x-text="unlockError"></span>
                    </div>
                </template>

                <button type="submit" :disabled="isUnlocking"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 disabled:opacity-50 text-white font-bold text-sm shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
                    <span x-show="!isUnlocking">Buka Kunci Ujian Sekarang</span>
                    <span x-show="isUnlocking" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Memverifikasi Password...</span>
                    </span>
                </button>
            </form>

            <div class="mt-4 text-[11px] text-gray-500">
                Sistem Pengawasan CBT &bull; IC-Labs Security Core
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 3: Initial Fullscreen Enforced Prompt (CBT Kiosk Entrance)        --}}
    {{-- ========================================================================= --}}
    <div x-show="!isFullscreen && !showFullscreenWarning && !allowOpenBook && !isLocked && penaltySeconds === 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-[#0F1713] border-2 border-emerald-500/40 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                🖥️
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 mb-2">
                CBT Kiosk Mode
            </span>
            <h3 class="text-xl font-bold text-white heading-font">Mode Layar Penuh Diwajibkan</h3>
            <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                Untuk menjaga ketertiban dan integritas praktikum, ujian ini mewajibkan Anda mengerjakan dalam mode layar penuh (Kiosk Experience).
            </p>
            <div class="mt-6">
                <button type="button" @click="enterFullscreen()"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <span>Aktifkan Layar Penuh & Mulai Ujian</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- OVERLAY 4: Fullscreen Exit 5-Second Warning Modal (Kiosk Enforcement)     --}}
    {{-- ========================================================================= --}}
    <div x-show="showFullscreenWarning && !isFullscreen && !allowOpenBook && !isLocked && penaltySeconds === 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-[#0F1713] border-2 border-rose-500/60 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden">
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-bounce">
                ⚠️
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/25 text-rose-300 border border-rose-500/40 mb-2">
                Peringatan Kiosk CBT
            </span>
            <h3 class="text-xl font-bold text-white heading-font">Anda Keluar Dari Layar Penuh!</h3>
            <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                Ujian mewajibkan pengerjaan dalam mode layar penuh. Segera klik tombol di bawah sebelum waktu toleransi habis:
            </p>
            
            <div class="my-5 p-4 rounded-2xl bg-[#090D0B] border border-rose-500/30">
                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-1">Toleransi Waktu Kembali:</div>
                <div class="text-5xl font-mono font-extrabold text-rose-400 tracking-wider animate-pulse"
                     x-text="fullscreenToleranceSeconds + 's'"></div>
            </div>

            <button type="button" @click="enterFullscreen()"
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span>Kembali ke Layar Penuh Sekarang</span>
            </button>
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
                flaggedQuestions: [],
                syncStatus: 'synced', // 'synced', 'saving', 'offline'
                syncDebounceTimer: null,
                heartbeatInterval: null,
                tabSwitchCount: {{ (int)$attempt->tab_switch_count }},
                allowOpenBook: {{ $quiz->allow_open_book ? 'true' : 'false' }},
                isLocked: {{ $attempt->is_locked ? 'true' : 'false' }},
                penaltySeconds: {{ (int)$penaltyRemainingSeconds }},
                strike: {{ min(3, (int)$attempt->tab_switch_count) }},
                adminPassword: '',
                isUnlocking: false,
                unlockError: '',
                showDrawer: false,
                isSubmitting: false,
                timerInterval: null,
                penaltyTimerInterval: null,
                isFullscreen: false,
                isEnteringFullscreen: false,
                hasEnteredFullscreenOnce: false,
                showFullscreenWarning: false,
                fullscreenToleranceSeconds: 5,
                fullscreenToleranceInterval: null,
                focusCheckInterval: null,

                get currentQuestion() {
                    return this.questions[this.currentIndex] || null;
                },

                checkFullscreenState() {
                    return Boolean(
                        document.fullscreenElement ||
                        document.webkitFullscreenElement ||
                        document.mozFullScreenElement ||
                        document.msFullscreenElement
                    );
                },

                initArena() {
                    // Initialize answers: merge server-saved draft answers with LocalStorage cache
                    const serverDraft = @json($savedAnswers);
                    const localDraft = this.loadLocalCache();
                    this.answers = Object.assign({}, serverDraft, localDraft);

                    // Initialize flagged questions
                    if (Array.isArray(this.answers['__flagged'])) {
                        this.flaggedQuestions = this.answers['__flagged'].map(String);
                    }

                    // Check initial fullscreen state
                    this.isFullscreen = this.checkFullscreenState();
                    if (this.isFullscreen) {
                        this.hasEnteredFullscreenOnce = true;
                    }

                    // Start main quiz timer if applicable
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

                    // Start penalty countdown if student refreshed with an active penalty
                    if (this.penaltySeconds > 0 && !this.isLocked) {
                        this.initPenaltyCountdown();
                    }

                    // Setup background heartbeat sync every 15 seconds so supervisor Live Monitor stays online
                    this.heartbeatInterval = setInterval(() => {
                        if (!this.isSubmitting && !this.isLocked) {
                            this.triggerBackgroundSync();
                        }
                    }, 15000);

                    // Initial code syntax highlighting pass
                    this.$nextTick(() => {
                        if (window.Prism) Prism.highlightAll();
                    });

                    // Comprehensive vendor-prefixed fullscreen listeners
                    const onFsChange = () => {
                        this.isFullscreen = this.checkFullscreenState();
                        if (this.isFullscreen) {
                            this.hasEnteredFullscreenOnce = true;
                            this.cancelFullscreenTolerance();
                        } else {
                            if (!this.allowOpenBook && this.hasEnteredFullscreenOnce && !this.isLocked && !this.isSubmitting && this.penaltySeconds === 0) {
                                this.startFullscreenTolerance();
                            }
                        }
                    };

                    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
                        document.addEventListener(evt, onFsChange);
                    });

                    // Setup Anti-Cheat Listeners
                    this.setupAntiCheat();

                    // Setup Keyboard shortcuts
                    this.setupKeyboardShortcuts();
                },

                enterFullscreen() {
                    this.isEnteringFullscreen = true;
                    setTimeout(() => { this.isEnteringFullscreen = false; }, 1200);

                    const el = document.documentElement;
                    const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                    if (req) {
                        req.call(el).then(() => {
                            this.isFullscreen = true;
                            this.hasEnteredFullscreenOnce = true;
                            this.cancelFullscreenTolerance();
                        }).catch((err) => {
                            console.warn('Fullscreen request blocked or failed:', err);
                            this.isFullscreen = this.checkFullscreenState();
                        });
                    }
                },

                startFullscreenTolerance() {
                    this.showFullscreenWarning = true;
                    this.fullscreenToleranceSeconds = 5;
                    if (this.fullscreenToleranceInterval) clearInterval(this.fullscreenToleranceInterval);

                    this.fullscreenToleranceInterval = setInterval(() => {
                        if (this.fullscreenToleranceSeconds > 1) {
                            this.fullscreenToleranceSeconds--;
                        } else {
                            this.cancelFullscreenTolerance();
                            this.reportViolation('fullscreen_exit');
                        }
                    }, 1000);
                },

                cancelFullscreenTolerance() {
                    this.showFullscreenWarning = false;
                    if (this.fullscreenToleranceInterval) {
                        clearInterval(this.fullscreenToleranceInterval);
                        this.fullscreenToleranceInterval = null;
                    }
                },

                loadLocalCache() {
                    try {
                        const raw = localStorage.getItem('cbt_quiz_{{ $attempt->id }}_answers');
                        return raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        return {};
                    }
                },

                saveLocalCache() {
                    try {
                        localStorage.setItem('cbt_quiz_{{ $attempt->id }}_answers', JSON.stringify(this.answers));
                        localStorage.setItem('cbt_quiz_{{ $attempt->id }}_last_save', Date.now());
                    } catch (e) {
                        console.warn('LocalStorage save failed:', e);
                    }
                },

                triggerBackgroundSync() {
                    this.syncStatus = 'saving';
                    if (this.syncDebounceTimer) clearTimeout(this.syncDebounceTimer);

                    this.syncDebounceTimer = setTimeout(() => {
                        fetch('{{ route('quiz.autosave', [$quiz->unique_token, $attempt->id]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ answers: this.answers })
                        })
                        .then(res => {
                            if (res.status === 419) {
                                return res.json().then(data => {
                                    if (data && data.csrf_token) {
                                        const meta = document.querySelector('meta[name="csrf-token"]');
                                        if (meta) meta.content = data.csrf_token;
                                    }
                                    return { success: false, expired: true };
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.syncStatus = 'synced';

                                // Check if supervisor force submitted this attempt
                                if (data.is_force_submitted || data.redirect) {
                                    window.location.href = data.redirect || '{{ route('quiz.results', [$quiz->unique_token, $attempt->id]) }}';
                                    return;
                                }

                                // Check if supervisor adjusted/added extra time
                                if (data.time_remaining !== undefined && data.time_remaining !== null) {
                                    const newTime = Math.max(0, Math.floor(Number(data.time_remaining)));
                                    if (this.remainingSeconds !== null && Math.abs(this.remainingSeconds - newTime) > 60) {
                                        this.remainingSeconds = newTime;
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'Waktu ujian telah disesuaikan oleh pengawas.',
                                            showConfirmButton: false,
                                            timer: 3500,
                                            background: '#0F1713',
                                            color: '#34D399',
                                            customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                                        });
                                    }
                                }
                            } else {
                                this.syncStatus = 'offline';
                            }
                        })
                        .catch(() => {
                            this.syncStatus = 'offline';
                        });
                    }, 400);
                },

                initPenaltyCountdown() {
                    if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                    this.penaltyTimerInterval = setInterval(() => {
                        if (this.penaltySeconds > 0) {
                            this.penaltySeconds--;
                        } else {
                            clearInterval(this.penaltyTimerInterval);
                            this.penaltyTimerInterval = null;
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Waktu sanksi pembekuan berakhir. Silakan lanjutkan ujian.',
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#0F1713',
                                color: '#34D399',
                                customClass: { popup: 'border border-emerald-500/40 rounded-xl' }
                            });
                        }
                    }, 1000);
                },

                setupAntiCheat() {
                    // If exam is open book ("Bisa lihat modul"), do not enforce tab switch penalty!
                    if (this.allowOpenBook) {
                        return;
                    }

                    let lastReportedTime = 0;
                    this.reportViolation = (reason = 'tab_switch') => {
                        if (this.isLocked || this.isSubmitting || this.isEnteringFullscreen) return;

                        // Throttle rapid triggers within 1.5 seconds
                        const now = Date.now();
                        if (now - lastReportedTime < 1500) return;
                        lastReportedTime = now;

                        fetch('{{ route('quiz.tab-switch', [$quiz->unique_token, $attempt->id]) }}', {
                            method: 'POST',
                            keepalive: true,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ reason: reason })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success || data.allow_open_book) return;

                            this.tabSwitchCount = data.tab_switch_count;
                            this.strike = data.strike;

                            if (data.is_locked) {
                                this.isLocked = true;
                                this.penaltySeconds = 0;
                                if (this.penaltyTimerInterval) clearInterval(this.penaltyTimerInterval);
                                if (this.focusCheckInterval) clearInterval(this.focusCheckInterval);
                            } else if (data.penalty_seconds > 0) {
                                this.penaltySeconds = data.penalty_seconds;
                                this.initPenaltyCountdown();
                            }
                        })
                        .catch(err => console.error('Violation record error:', err));
                    };

                    // 1. Tab switch or window minimize (Visibility API)
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.reportViolation('tab_switch');
                        }
                    });

                    // 2. Window loss of focus (Clicking outside, opening another app, alt-tab)
                    window.addEventListener('blur', () => {
                        if (!this.allowOpenBook && !this.isLocked && this.penaltySeconds === 0 && !this.isSubmitting && !this.isEnteringFullscreen) {
                            this.reportViolation('tab_switch');
                        }
                    });

                    // 3. Heartbeat Focus Scanner:
                    if (this.focusCheckInterval) clearInterval(this.focusCheckInterval);
                    this.focusCheckInterval = setInterval(() => {
                        if (!this.allowOpenBook && !this.isLocked && this.penaltySeconds === 0 && !this.isSubmitting && !this.isEnteringFullscreen && this.hasEnteredFullscreenOnce) {
                            if (document.hidden || !document.hasFocus()) {
                                this.reportViolation('tab_switch');
                            }
                        }
                    }, 750);

                    // Anti-Cheat: Disable right click, copy, and devtools shortcuts in strict exam mode
                    document.addEventListener('contextmenu', (e) => {
                        if (!this.allowOpenBook) e.preventDefault();
                    });
                    document.addEventListener('copy', (e) => {
                        if (!this.allowOpenBook) e.preventDefault();
                    });
                    document.addEventListener('cut', (e) => {
                        if (!this.allowOpenBook) e.preventDefault();
                    });
                },

                submitUnlockPassword() {
                    if (!this.adminPassword.trim()) {
                        this.unlockError = 'Silakan masukkan password pengawas!';
                        return;
                    }
                    this.isUnlocking = true;
                    this.unlockError = '';

                    fetch('{{ route('quiz.unlock-password', [$quiz->unique_token, $attempt->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ admin_password: this.adminPassword })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(({ status, body }) => {
                        this.isUnlocking = false;
                        if (status === 200 && body.success) {
                            this.isLocked = false;
                            this.penaltySeconds = 0;
                            this.adminPassword = '';
                            this.tabSwitchCount = body.tab_switch_count || 2;
                            this.strike = 2;
                            Swal.fire({
                                icon: 'success',
                                title: 'Kuis Berhasil Dibuka!',
                                text: body.message || 'Sesi ujian Anda telah diizinkan kembali oleh pengawas.',
                                background: '#0F1713',
                                color: '#f3f4f6',
                                confirmButtonColor: '#10B981',
                                confirmButtonText: 'Lanjutkan Ujian',
                                customClass: { popup: 'border border-emerald-500/40 rounded-2xl' }
                            });
                        } else {
                            this.unlockError = body.message || 'Password pengawas salah! Silakan coba lagi.';
                        }
                    })
                    .catch(() => {
                        this.isUnlocking = false;
                        this.unlockError = 'Terjadi gangguan jaringan saat memverifikasi password.';
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
                    if (this.currentQuestion && this.currentQuestion.question_type === 'multi_select') {
                        let current = this.answers[questionId];
                        if (!Array.isArray(current)) {
                            current = current ? [current] : [];
                        }
                        const idx = current.indexOf(optionId);
                        if (idx > -1) {
                            current.splice(idx, 1);
                        } else {
                            current.push(optionId);
                        }
                        this.answers[questionId] = current;
                    } else {
                        this.answers[questionId] = optionId;
                    }
                    this.saveLocalCache();
                    this.triggerBackgroundSync();
                },

                isSelected(questionId, optionId) {
                    const val = this.answers[questionId];
                    if (Array.isArray(val)) {
                        return val.includes(optionId);
                    }
                    return val === optionId;
                },

                toggleFlagQuestion(questionId) {
                    const qIdStr = String(questionId);
                    const idx = this.flaggedQuestions.indexOf(qIdStr);
                    if (idx > -1) {
                        this.flaggedQuestions.splice(idx, 1);
                    } else {
                        this.flaggedQuestions.push(qIdStr);
                    }
                    this.answers['__flagged'] = [...this.flaggedQuestions];
                    this.saveLocalCache();
                    this.triggerBackgroundSync();
                },

                isFlagged(questionId) {
                    return this.flaggedQuestions.includes(String(questionId));
                },

                flaggedCount() {
                    return this.flaggedQuestions.length;
                },

                isQuestionAnswered(questionId) {
                    if (questionId === '__flagged') return false;
                    const val = this.answers[questionId];
                    if (Array.isArray(val)) {
                        return val.length > 0;
                    }
                    return val !== undefined && val !== null && val !== '';
                },

                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                        this.$nextTick(() => { if (window.Prism) Prism.highlightAll(); });
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                        this.$nextTick(() => { if (window.Prism) Prism.highlightAll(); });
                    }
                },

                jumpToQuestion(index) {
                    this.currentIndex = index;
                    this.showDrawer = false;
                    this.$nextTick(() => { if (window.Prism) Prism.highlightAll(); });
                },

                answeredCount() {
                    return this.questions.filter(q => this.isQuestionAnswered(q.id)).length;
                },

                renderRichText(raw) {
                    if (!raw) return '';
                    const codeBlocks = [];
                    let text = String(raw);

                    // 1. Extract and protect code blocks ```lang ... ```
                    text = text.replace(/```([a-zA-Z0-9_\-\+#]*)\r?\n?([\s\S]*?)```/g, (match, lang, code) => {
                        const cleanLang = (lang || 'markup').trim().toLowerCase();
                        const placeholder = `___CODE_BLOCK_${codeBlocks.length}___`;
                        
                        const escEl = document.createElement('div');
                        escEl.textContent = code.trim();
                        const escCode = escEl.innerHTML;

                        codeBlocks.push(
                            `<div class="my-3 rounded-xl overflow-hidden border border-[#1A2820] bg-[#070b09] text-left">` +
                            `<div class="px-3 py-1 bg-[#101713] border-b border-[#1A2820] text-[10px] font-mono text-gray-400 uppercase tracking-wider flex items-center justify-between">` +
                            `<span class="text-emerald-400 font-bold">${cleanLang || 'code'}</span>` +
                            `<span class="text-gray-500 text-[9px]">Syntax Highlighted</span>` +
                            `</div>` +
                            `<pre class="language-${cleanLang} !bg-transparent !p-3 !m-0 text-xs sm:text-sm font-mono overflow-x-auto"><code class="language-${cleanLang}">${escCode}</code></pre>` +
                            `</div>`
                        );
                        return placeholder;
                    });

                    // 2. Extract and protect inline code `...`
                    const inlineBlocks = [];
                    text = text.replace(/`([^`\n\r]+)`/g, (match, code) => {
                        const placeholder = `___INLINE_CODE_${inlineBlocks.length}___`;
                        const escEl = document.createElement('div');
                        escEl.textContent = code;
                        inlineBlocks.push(`<code class="px-1.5 py-0.5 rounded bg-[#16221B] border border-[#23382C] text-emerald-300 font-mono text-xs sm:text-sm">${escEl.innerHTML}</code>`);
                        return placeholder;
                    });

                    // 3. Escape normal text
                    const escapeEl = document.createElement('div');
                    escapeEl.textContent = text;
                    let safe = escapeEl.innerHTML;

                    // 4. Convert single newlines to <br>
                    safe = safe.replace(/\n/g, '<br>');

                    // 5. Restore inline code
                    inlineBlocks.forEach((html, i) => {
                        safe = safe.replace(`___INLINE_CODE_${i}___`, html);
                    });

                    // 6. Restore code blocks
                    codeBlocks.forEach((html, i) => {
                        safe = safe.replace(`___CODE_BLOCK_${i}___`, html);
                    });

                    return safe;
                },

                confirmSubmit() {
                    const answered = this.answeredCount();
                    const total = this.questions.length;
                    const unanswered = total - answered;
                    const flagged = this.flaggedCount();

                    let message = `Anda telah menjawab ${answered} dari ${total} pertanyaan.`;
                    if (flagged > 0) {
                        message += `\n⚠️ Anda masih menandai ${flagged} soal sebagai RAGU-RAGU.`;
                    }
                    if (unanswered > 0) {
                        message += `\n⚠️ Terdapat ${unanswered} pertanyaan yang belum dijawab.`;
                    } else if (flagged === 0) {
                        message += `\nSemua pertanyaan telah terisi.`;
                    }
                    message += `\n\nYakin ingin mengumpulkan ujian sekarang?`;

                    Swal.fire({
                        title: 'Kumpulkan Jawaban?',
                        text: message,
                        icon: (unanswered > 0 || flagged > 0) ? 'warning' : 'question',
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
                    if (this.focusCheckInterval) clearInterval(this.focusCheckInterval);
                    if (this.fullscreenToleranceInterval) clearInterval(this.fullscreenToleranceInterval);
                    if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);

                    if (window.showLoading) {
                        window.showLoading('MENGIRIM JAWABAN...', 'Sistem sedang menghitung akurasi dan skor evaluasi...');
                    }

                    fetch('{{ route('quiz.submit', [$quiz->unique_token, $attempt->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ answers: this.answers })
                    })
                    .then(res => {
                        if (res.status === 419) {
                            return res.json().then(data => {
                                if (data && data.csrf_token) {
                                    const meta = document.querySelector('meta[name="csrf-token"]');
                                    if (meta) meta.content = data.csrf_token;
                                }
                                throw new Error('Sesi keamanan sempat terputus karena padatnya koneksi server. Token telah diperbarui otomatis, silakan klik tombol Kirim Jawaban sekali lagi.');
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        try {
                            localStorage.removeItem('cbt_quiz_{{ $attempt->id }}_answers');
                            localStorage.removeItem('cbt_quiz_{{ $attempt->id }}_last_save');
                        } catch (e) {}

                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch((err) => {
                        this.isSubmitting = false;
                        if (window.hideLoading) window.hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Pemberitahuan Sistem',
                            text: (err && err.message) ? err.message : 'Terjadi gangguan koneksi internet. Silakan coba lagi.',
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
                        this.enterFullscreen();
                    } else {
                        if (!this.allowOpenBook) {
                            Swal.fire({
                                title: 'Keluar Layar Penuh?',
                                text: 'Ujian ini mewajibkan mode layar penuh. Keluar dari layar penuh akan memicu peringatan toleransi 5 detik.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Tetap di Layar Penuh',
                                cancelButtonText: 'Keluar (Beresiko)',
                                confirmButtonColor: '#10B981',
                                cancelButtonColor: '#1A2820',
                                background: '#0F1713',
                                color: '#f3f4f6'
                            }).then((res) => {
                                if (res.dismiss === Swal.DismissReason.cancel) {
                                    if (document.exitFullscreen) document.exitFullscreen();
                                }
                            });
                        } else {
                            if (document.exitFullscreen) document.exitFullscreen();
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
        window.quizArena = quizArena;
        document.addEventListener('alpine:init', () => {
            if (window.Alpine && typeof Alpine.data === 'function') {
                Alpine.data('quizArena', quizArena);
            }
        });
    </script>

    {{-- ─── UNIFIED CYBER LOADING POPUP COMPONENT ─── --}}
    @include('components.cyber-loading')
</body>
</html>
