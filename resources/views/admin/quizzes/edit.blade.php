@extends('layouts.admin')
@section('title', 'Edit Kuis: ' . $quiz->title)
@section('page-title', 'Pengaturan Kuis')
@section('page-description', 'Perbarui parameter kuis interaktif')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Breadcrumb / Back button --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.quizzes.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Kuis
        </a>

        <a href="{{ route('admin.quizzes.questions', $quiz) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-400 text-xs font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Buka Bank Soal ({{ $quiz->questions()->count() }} Soal)
        </a>
    </div>

    {{-- Quick Access Bar --}}
    <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Kode Akses Mahasiswa:</span>
            <span class="px-3 py-1 rounded-lg bg-[#142019] border border-[#1E3327] text-emerald-300 font-mono text-sm font-bold">{{ $quiz->access_code }}</span>
            <button type="button" onclick="copyToClipboard('{{ $quiz->access_code }}', 'Kode Kuis berhasil disalin!')"
                    class="p-1.5 text-gray-400 hover:text-emerald-400 rounded-lg hover:bg-emerald-500/10 transition-colors" title="Salin Kode">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="copyToClipboard('{{ route('quiz.lobby', $quiz->unique_token) }}', 'Link kuis mahasiswa berhasil disalin!')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#142019] border border-[#1E3327] hover:border-emerald-500/40 text-xs text-gray-300 hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Salin Link Mahasiswa
            </button>
            <a href="{{ route('quiz.lobby', $quiz->unique_token) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#142019] border border-[#1E3327] hover:border-sky-500/40 text-xs text-sky-400 hover:text-sky-300 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Pratinjau Kuis
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm">
            <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Informasi Kuis --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-[#1A2820]">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Informasi Dasar Kuis</h3>
                    <p class="text-xs text-gray-400">Judul, deskripsi petunjuk pengerjaan, dan relasi dokumen</p>
                </div>
            </div>

            <div class="space-y-4">
                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Judul Kuis <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Deskripsi / Petunjuk Mahasiswa
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">{{ old('description', $quiz->description) }}</textarea>
                </div>

                {{-- Tautkan ke Dokumen Soal --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Tautkan ke Dokumen Soal Praktikum (Opsional)
                    </label>
                    <select name="soal_id"
                            class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                        <option value="">-- Tidak Ditautkan (Kuis Mandiri) --</option>
                        @foreach($soals as $s)
                            <option value="{{ $s->id }}" {{ old('soal_id', $quiz->soal_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Password Akses Mahasiswa (Opsional) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Password Akses Soal (Opsional)
                    </label>
                    <input type="text" name="access_password" value="{{ old('access_password', $quiz->access_password) }}"
                           placeholder="Kosongkan jika mahasiswa bebas akses tanpa password soal"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                    <p class="text-[11px] text-gray-500 mt-1">Jika diisi, mahasiswa wajib memasukkan password ini setelah mengisi NIM & Nama agar soal terbuka.</p>
                </div>
            </div>
        </div>

        {{-- Section 2: Jadwal & Waktu Akses --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-[#1A2820]">
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Jadwal Akses & Batas Waktu</h3>
                    <p class="text-xs text-gray-400">Atur rentang jadwal pengerjaan, hitungan mundur, dan batas waktu kuis</p>
                </div>
            </div>

            {{-- Jadwal Mulai & Selesai --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 pb-4 border-b border-[#1A2820]/60">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Jadwal Mulai Akses Soal (Opsional)
                    </label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $quiz->starts_at ? $quiz->starts_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Jika diisi dan waktu belum tiba, mahasiswa akan menunggu dengan hitungan mundur (countdown timer).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Batas Akhir Akses Soal (Opsional)
                    </label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $quiz->ends_at ? $quiz->ends_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Setelah batas waktu ini lewat, soal otomatis ditandai tutup dan tidak dapat diakses lagi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Batas Waktu Total --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Durasi Pengerjaan (Menit)
                    </label>
                    <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="0" max="300"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Isi 0 jika tanpa durasi pengerjaan.</p>
                </div>

                {{-- Batas Waktu Per Soal --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Timer Per Soal (Detik)
                    </label>
                    <input type="number" name="time_per_question" value="{{ old('time_per_question', $quiz->time_per_question) }}" min="0" max="300"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Gaya Quizizz per soal. Isi 0 untuk menonaktifkan.</p>
                </div>

                {{-- Maksimal Percobaan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Maksimal Percobaan (Per NIM)
                    </label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" max="20" required
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Berapa kali mahasiswa dengan NIM yang sama dapat mencoba.</p>
                </div>
            </div>
        </div>

        {{-- Section 3: Konfigurasi Interaktif (Toggles) --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-[#1A2820]">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Fitur Interaktif & Tampilan Hasil</h3>
                    <p class="text-xs text-gray-400">Kustomisasi mekanisme acak dan transparansi hasil</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Acak Soal --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Acak Urutan Soal (Shuffle Questions)</span>
                        <span class="text-[11px] text-gray-400">Setiap mahasiswa menerima urutan soal yang berbeda-beda.</span>
                    </div>
                </label>

                {{-- Acak Opsi --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="shuffle_options" value="1" {{ old('shuffle_options', $quiz->shuffle_options) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Acak Pilihan Jawaban (Shuffle Options)</span>
                        <span class="text-[11px] text-gray-400">Urutan pilihan A, B, C, D diacak secara otomatis.</span>
                    </div>
                </label>

                {{-- Tampilkan Leaderboard --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="show_leaderboard" value="1" {{ old('show_leaderboard', $quiz->show_leaderboard) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Tampilkan Podium / Leaderboard</span>
                        <span class="text-[11px] text-gray-400">Mahasiswa dapat melihat peringkat mereka setelah selesai.</span>
                    </div>
                </label>

                {{-- Tampilkan Pembahasan --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="show_review" value="1" {{ old('show_review', $quiz->show_review) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Tampilkan Kunci & Pembahasan Soal</span>
                        <span class="text-[11px] text-gray-400">Mahasiswa dapat meninjau jawaban benar dan catatan penjelasan.</span>
                    </div>
                </label>

                {{-- Status Aktif Kuis --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors md:col-span-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-emerald-400 block">Status Kuis Aktif</span>
                        <span class="text-[11px] text-gray-400">Jika dinonaktifkan, mahasiswa tidak dapat masuk atau mengerjakan kuis ini.</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Section 4: Keamanan Ujian & Anti-Kecurangan (Anti-Cheat) --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6" x-data="{ openBook: '{{ old('allow_open_book', $quiz->allow_open_book ? '1' : '0') }}' }">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-[#1A2820]">
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Keamanan Ujian & Mode Pengawasan</h3>
                    <p class="text-xs text-gray-400">Atur proteksi anti-kecurangan dan akses modul mahasiswa saat ujian berlangsung</p>
                </div>
            </div>

            {{-- Radio Buttons: Mode Ujian --}}
            <div class="space-y-3 mb-6">
                <label class="text-xs font-semibold text-gray-300 uppercase tracking-wider block">
                    Mode Pengawasan Ujian:
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    {{-- Mode 1: Ujian Ketat (Default) --}}
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                           :class="openBook === '0' ? 'bg-emerald-500/10 border-emerald-500/40 ring-1 ring-emerald-500/30' : 'bg-[#090D0B] border-[#1A2820] hover:border-gray-700'">
                        <input type="radio" name="allow_open_book" value="0" x-model="openBook"
                               class="mt-1 text-emerald-500 focus:ring-emerald-500 bg-gray-900 border-gray-700">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white">Mode Ujian Ketat (Anti-Cheat Aktif)</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Default</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">
                                Mahasiswa <span class="text-rose-400 font-semibold">DILARANG</span> berpindah tab atau membuka aplikasi lain.
                            </p>
                            <div class="mt-2.5 pt-2 border-t border-gray-800/60 text-[10.5px] text-gray-400 space-y-1">
                                <div class="flex items-center gap-1.5"><span class="text-amber-400 font-bold">1x Switch:</span> Layar beku / Freeze 1 Menit (60s)</div>
                                <div class="flex items-center gap-1.5"><span class="text-amber-500 font-bold">2x Switch:</span> Layar beku / Freeze 1.5 Menit (90s)</div>
                                <div class="flex items-center gap-1.5"><span class="text-rose-400 font-bold">3x Switch:</span> <strong class="text-rose-300">TERKUNCI TOTAL</strong> (Buka via Password Admin)</div>
                            </div>
                        </div>
                    </label>

                    {{-- Mode 2: Bisa Lihat Modul --}}
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                           :class="openBook === '1' ? 'bg-sky-500/10 border-sky-500/40 ring-1 ring-sky-500/30' : 'bg-[#090D0B] border-[#1A2820] hover:border-gray-700'">
                        <input type="radio" name="allow_open_book" value="1" x-model="openBook"
                               class="mt-1 text-sky-500 focus:ring-sky-500 bg-gray-900 border-gray-700">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white">Bisa Lihat Modul (Open Book)</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">Bebas Akses</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">
                                Mahasiswa diizinkan membuka tab modul praktikum atau materi materi lainnya.
                            </p>
                            <div class="mt-2.5 pt-2 border-t border-gray-800/60 text-[10.5px] text-sky-300/80">
                                ℹ️ Fitur pinalti freeze layar dan penguncian total <strong>dinonaktifkan</strong>.
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Password Buka Kunci Darurat --}}
            <div x-show="openBook === '0'" x-transition class="pt-4 border-t border-[#1A2820]">
                <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                    Password Khusus Buka Kunci Ujian (Opsional)
                </label>
                <div class="relative max-w-md">
                    <input type="text" name="admin_unlock_password" value="{{ old('admin_unlock_password', $quiz->admin_unlock_password) }}"
                           placeholder="Contoh: PENG4W45#2026 (Kosongkan jika ingin pakai password akun)"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-rose-500 focus:ring-1 focus:ring-rose-500 rounded-xl text-sm text-gray-100 placeholder-gray-600 font-mono">
                </div>
                <p class="text-[11px] text-gray-500 mt-1.5">
                    Password yang digunakan pengawas untuk membuka kuis mahasiswa yang terkunci akibat Strike 3. Jika dikosongkan, Anda dapat menggunakan <strong>password login akun Anda</strong> atau <strong>Super Admin</strong>.
                </p>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.quizzes.index') }}"
               class="px-5 py-2.5 rounded-xl border border-[#1A2820] hover:bg-gray-800 text-gray-300 text-sm font-medium transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<script>
    function copyToClipboard(text, successMessage) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => showToast(successMessage));
        } else {
            const input = document.createElement('textarea');
            input.value = text;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.focus();
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            showToast(successMessage);
        }
    }
    function showToast(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2500,
            background: '#0F1713',
            color: '#f3f4f6',
            customClass: { popup: 'border border-emerald-500/30 rounded-xl' }
        });
    }
</script>
@endsection
