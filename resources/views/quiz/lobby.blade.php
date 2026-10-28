<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Kuis: {{ $quiz->title }} &mdash; LabDrive</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <span class="text-base tracking-tight heading-font font-bold">Lab<span class="text-emerald-400">Quiz</span></span>
                    <span class="text-[10px] text-gray-500 block -mt-1 font-mono">LABORATORIUM DRIVE</span>
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
            {{-- Active Quiz Lobby --}}
            <div class="space-y-6">

                {{-- Alert Messages --}}
                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Hero Banner Card --}}
                <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 md:p-8 glow-card relative overflow-hidden">
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            Sesi Kuis Terbuka
                        </span>

                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-[#142019] text-gray-300 border border-[#1E3327]">
                            Kode: {{ $quiz->access_code }}
                        </span>

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
                                <p class="text-xs text-gray-400">Masukkan NIM dan Nama Lengkap Anda sebelum mulai</p>
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

                            <div class="pt-3">
                                <button type="submit"
                                        class="w-full py-3.5 px-6 rounded-2xl text-white font-bold text-base glow-button flex items-center justify-center gap-2 transition-all">
                                    <span>🚀 Masuk ke Arena Kuis</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
                                    <span><strong>Timer Otomatis:</strong> Jawaban akan otomatis terkumpul ketika waktu pengerjaan berakhir.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span><strong>Shortcut Keyboard:</strong> Anda dapat menekan tombol 1, 2, 3, 4 atau A, B, C, D di keyboard untuk memilih jawaban dengan cepat.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span><strong>Hasil & Podium:</strong> Nilai akhir dan posisi peringkat akan langsung tampil sesaat setelah Anda mengumpulkan jawaban.</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-6 pt-4 border-t border-[#1A2820] text-center">
                            <span class="text-[11px] text-gray-500">LabDrive Assessment Security Engine</span>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </main>

    <footer class="py-6 border-t border-[#1A2820] text-center text-xs text-gray-600 mt-auto">
        &copy; {{ date('Y') }} LabDrive &bull; Platform Distribusi & Evaluasi Laboratorium
    </footer>

</body>
</html>
