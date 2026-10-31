<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Kuis: {{ $quiz->title }} &mdash; IC-Labs</title>
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #f3f4f6;
            min-height: 100vh;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .glow-card {
            background: radial-gradient(circle at top left, rgba(16,185,129,0.08), transparent 70%), #0F1713;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
        }
        .glow-button {
            background: linear-gradient(135deg, #10B981 0%, #0D9488 100%);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.35);
        }
        .glow-button:hover {
            box-shadow: 0 6px 28px rgba(16, 185, 129, 0.5);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen selection:bg-emerald-500 selection:text-white">

    {{-- Top Navbar --}}
    <header class="border-b border-[#1A2820] bg-[#090D0B]/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-white font-bold group">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center p-1 shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <img src="{{ asset('images/iclabs-logo.png') }}" alt="IC-Labs" class="w-full h-full object-contain" draggable="false">
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-wider leading-none block" style="font-family:'Orbitron',sans-serif;">IC-LABS</span>
                    <span class="text-[9px] text-emerald-400 font-semibold tracking-widest block mt-0.5" style="font-family:'Chakra Petch',sans-serif;">CBT EVALUATION</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 font-mono hidden sm:inline">Kode: <strong class="text-emerald-400 font-bold">{{ $quiz->access_code }}</strong></span>
                <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-white transition-colors">
                    Beranda
                </a>
            </div>
        </div>
    </header>

    {{-- Main Lobby Content --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-8 md:py-12 flex flex-col justify-center">

        @if(!$quiz->is_active)
            {{-- Inactive Quiz State --}}
            <div class="bg-[#0F1713] border border-amber-500/30 rounded-3xl p-8 md:p-12 text-center max-w-lg mx-auto glow-card">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2 heading-font">Kuis Belum Dibuka</h2>
                <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                    Kuis "<strong>{{ $quiz->title }}</strong>" saat ini sedang dinonaktifkan oleh instruktur atau asisten laboratorium. Silakan tanyakan kepada asisten untuk membuka sesi kuis.
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1A2820] hover:bg-gray-800 text-gray-200 text-sm font-semibold transition-all">
                    Kembali ke Beranda
                </a>
            </div>
        @else
            {{-- Active Quiz Lobby with Schedule & Access Check --}}
            <div class="space-y-6"
                 x-data="{
                     secondsLeft: {{ $secondsUntilStart ?? 0 }},
                     isUpcoming: {{ $isUpcoming ? 'true' : 'false' }},
                     isExpired: {{ $isExpired ? 'true' : 'false' }},
                     showPassword: false,
                     timerInterval: null,
                     formatTime(s) {
                         if (s <= 0) return '00:00:00';
                         const h = Math.floor(s / 3600);
                         const m = Math.floor((s % 3600) / 60);
                         const sec = s % 60;
                         if (h > 24) {
                             const days = Math.floor(h / 24);
                             const remH = h % 24;
                             return `${days} Hari ${remH} Jam ${String(m).padStart(2, '0')}m`;
                         }
                         return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
                     },
                     init() {
                         if (this.isUpcoming && this.secondsLeft > 0) {
                             this.timerInterval = setInterval(() => {
                                 this.secondsLeft--;
                                 if (this.secondsLeft <= 0) {
                                     clearInterval(this.timerInterval);
                                     this.isUpcoming = false;
                                     window.location.reload();
                                 }
                             }, 1000);
                         }
                     }
                 }">

                {{-- Alert Messages --}}
                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm flex items-center gap-3 animate-shake">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Countdown Timer Banner (if quiz is upcoming / not started yet) --}}
                <template x-if="isUpcoming">
                    <div class="p-6 md:p-8 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-center glow-card relative overflow-hidden">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 animate-pulse">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-widest block mb-1">Kuis Belum Dimulai</span>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-white heading-font mb-2">Hitungan Mundur Akses Soal</h2>
                        <p class="text-xs text-gray-400 mb-4">
                            Soal dijadwalkan dapat diakses pada: <strong class="text-amber-300">{{ $quiz->starts_at ? $quiz->starts_at->translatedFormat('l, d F Y &bull; H:i') : '-' }} WITA</strong>
                        </p>
                        <div class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-[#090D0B] border border-amber-500/40 shadow-inner">
                            <span class="text-xs font-mono text-gray-400 uppercase tracking-wider">Mulai Dalam:</span>
                            <span class="text-2xl md:text-3xl font-mono font-extrabold text-amber-400 tracking-wider" x-text="formatTime(secondsLeft)"></span>
                        </div>
                    </div>
                </template>

                {{-- Schedule Expired Banner (if quiz ends_at passed) --}}
                <template x-if="isExpired">
                    <div class="p-6 md:p-8 rounded-3xl bg-rose-500/10 border border-rose-500/30 text-center glow-card">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <span class="text-xs font-bold text-rose-400 uppercase tracking-widest block mb-1">Akses Ditutup</span>
                        <h2 class="text-2xl font-extrabold text-white heading-font mb-2">Batas Waktu Akses Telah Berakhir</h2>
                        <p class="text-xs text-gray-300 max-w-lg mx-auto leading-relaxed">
                            Batas waktu pengerjaan kuis ini telah melewati jadwal yang ditetapkan (<strong>{{ $quiz->ends_at ? $quiz->ends_at->translatedFormat('d M Y, H:i') : '' }} WITA</strong>). Mahasiswa tidak dapat lagi mengakses soal.
                        </p>
                    </div>
                </template>

                {{-- Hero Banner Card --}}
                <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 md:p-8 glow-card relative overflow-hidden">
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @if($isExpired)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                ⛔ Akses Kuis Ditutup
                            </span>
                        @elseif($isUpcoming)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                ⏳ Menunggu Jadwal Mulai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                Sesi Kuis Terbuka
                            </span>
                        @endif

                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-[#142019] text-gray-300 border border-[#1E3327]">
                            Kode: {{ $quiz->access_code }}
                        </span>

                        @if($hasPassword)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-300 border border-amber-500/20 flex items-center gap-1">
                                🔒 Dilindungi Password Soal
                            </span>
                        @endif

                        @if($quiz->soal)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-teal-500/10 text-teal-300 border border-teal-500/20">
                                📚 {{ $quiz->soal->title }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-3 heading-font tracking-tight">
                        {{ $quiz->title }}
                    </h1>

                    @if($quiz->description)
                        <p class="text-sm text-gray-400 mb-6 leading-relaxed max-w-2xl">
                            {{ $quiz->description }}
                        </p>
                    @endif

                    {{-- Metrics Highlights --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-[#1A2820]">
                        <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">Total Soal</span>
                            <span class="text-base font-bold text-white mt-0.5 block">{{ $questionsCount }} Pertanyaan</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">Batas Waktu</span>
                            <span class="text-base font-bold text-emerald-400 mt-0.5 block">
                                {{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' Menit' : ($quiz->time_per_question > 0 ? $quiz->time_per_question . ' dtk/soal' : 'Bebas') }}
                            </span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">Total Nilai</span>
                            <span class="text-base font-bold text-amber-400 mt-0.5 block">{{ $totalPoints }} Poin</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider block font-semibold">Kesempatan</span>
                            <span class="text-base font-bold text-sky-400 mt-0.5 block">{{ $quiz->max_attempts }}x Pengerjaan</span>
                        </div>
                    </div>
                </div>

                {{-- Student Entry Form --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="md:col-span-3 bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 md:p-8">
                        <div class="flex items-center gap-2.5 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white heading-font">Data Identitas Mahasiswa</h3>
                                <p class="text-xs text-gray-400">Masukkan NIM, Nama Lengkap, dan Password Akses</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('quiz.start', $quiz->unique_token) }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                                    Nomor Induk Mahasiswa (NIM) <span class="text-emerald-400">*</span>
                                </label>
                                <input type="text" name="nim" value="{{ old('nim') }}" required autocomplete="off"
                                       placeholder="Contoh: 220101004"
                                       class="w-full px-4 py-3 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                                @error('nim')
                                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                                    Nama Lengkap Mahasiswa <span class="text-emerald-400">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama') }}" required autocomplete="off"
                                       placeholder="Contoh: Ahmad Fauzan"
                                       class="w-full px-4 py-3 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600">
                                @error('nama')
                                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pilihan Kelas Mahasiswa --}}
                            @if($quiz->hasClasses())
                                <div>
                                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                        <span>Pilih Kelas <span class="text-emerald-400">*</span></span>
                                        <span class="text-[10px] text-gray-500 font-normal">Wajib dipilih sesuai kelas Anda</span>
                                    </label>
                                    <div class="relative group">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-500 group-focus-within:text-emerald-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <select name="kelas" required
                                                class="w-full pl-10 pr-10 py-3 bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/40 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 cursor-pointer appearance-none transition-all shadow-inner">
                                            <option value="" disabled {{ old('kelas') ? '' : 'selected' }} class="bg-[#0F1713] text-gray-500">— Pilih Kelas Anda —</option>
                                            @foreach($quiz->getClassesList() as $cls)
                                                <option value="{{ $cls }}" {{ old('kelas') == $cls ? 'selected' : '' }} class="bg-[#0F1713] text-gray-100 py-2">Kelas {{ $cls }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-emerald-400 transition-transform group-focus-within:rotate-180">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                    @error('kelas')
                                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Password Akses Soal (jika kuis dilindungi password) --}}
                            @if($hasPassword)
                                <div>
                                    <label class="block text-xs font-semibold text-amber-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <span>🔒 Password Akses Soal</span>
                                            <span class="text-red-400">*</span>
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-normal">Diberikan oleh Dosen / Asisten</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                               name="access_password"
                                               value="{{ old('access_password') }}"
                                               required
                                               placeholder="Masukkan password akses soal..."
                                               class="w-full pl-4 pr-11 py-3 bg-[#090D0B] border border-amber-500/40 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                                        <button type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 p-1">
                                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1">Pastikan password diisi dengan benar sebelum menekan tombol masuk.</p>
                                </div>
                            @endif

                            <div class="pt-3">
                                <button type="submit"
                                        :disabled="isUpcoming || isExpired"
                                        :class="(isUpcoming || isExpired) ? 'opacity-50 cursor-not-allowed bg-gray-800 text-gray-500' : 'glow-button text-white'"
                                        class="w-full py-3.5 px-6 rounded-2xl font-bold text-base flex items-center justify-center gap-2 transition-all">
                                    <template x-if="isUpcoming">
                                        <span class="flex items-center gap-2">
                                            <span>⏳ Menunggu Jadwal Kuis...</span>
                                        </span>
                                    </template>
                                    <template x-if="isExpired">
                                        <span>⛔ Akses Kuis Telah Berakhir</span>
                                    </template>
                                    <template x-if="!isUpcoming && !isExpired">
                                        <span class="flex items-center gap-2">
                                            <span>🚀 Masuk ke Arena Kuis</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Rules & Integrity Card --}}
                    <div class="md:col-span-2 bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-emerald-400 text-lg">🛡️</span>
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Peraturan & Anti-Cheat</h4>
                            </div>

                            <ul class="space-y-3 text-xs text-gray-400">
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span><strong>Pengawas Tab Switch:</strong> Setiap kali Anda berpindah aplikasi atau tab browser, sistem pengawas otomatis mencatat pelanggaran.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span><strong>Soal Gambar & Pilihan Jamak:</strong> Beberapa soal mungkin memiliki gambar diagram dan lebih dari satu jawaban benar (kotak centang).</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span><strong>Timer Otomatis:</strong> Jawaban akan otomatis terkumpul ketika waktu pengerjaan berakhir.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span><strong>Hasil & Podium:</strong> Nilai akhir dan posisi peringkat akan langsung tampil sesaat setelah Anda mengumpulkan jawaban.</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-6 pt-4 border-t border-[#1A2820] text-center">
                            <span class="text-[11px] text-gray-500">IC-Labs Assessment Security Engine</span>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </main>

    <footer class="py-6 border-t border-[#1A2820] text-center text-xs text-gray-600 mt-auto">
        &copy; {{ date('Y') }} IC-Labs &bull; Platform Distribusi & Evaluasi Laboratorium
    </footer>

</body>
</html>
