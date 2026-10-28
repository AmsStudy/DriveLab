@extends('layouts.admin')
@section('title', 'Buat Kuis Baru')
@section('page-title', 'Buat Kuis Interaktif')
@section('page-description', 'Atur parameter kuis interaktif tipe Quizizz untuk mahasiswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Breadcrumb / Back button --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.quizzes.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Kuis
        </a>
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

    <form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-6">
        @csrf

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
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Kuis Pre-Test Modul 3: Struktur Data & Pointer"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Deskripsi / Petunjuk Mahasiswa
                    </label>
                    <textarea name="description" rows="3"
                              placeholder="Tuliskan petunjuk kuis, materi yang diuji, atau instruksi pengerjaan..."
                              class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500">{{ old('description') }}</textarea>
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
                            <option value="{{ $s->id }}" {{ old('soal_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1">Jika ditautkan, kuis ini akan terasosiasi dengan arsip dokumen praktikum terkait.</p>
                </div>
            </div>
        </div>

        {{-- Section 2: Waktu & Percobaan --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-[#1A2820]">
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Durasi Waktu & Batas Percobaan</h3>
                    <p class="text-xs text-gray-400">Atur batasan pengerjaan kuis untuk mahasiswa</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Batas Waktu Total --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Batas Waktu Total (Menit)
                    </label>
                    <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', 15) }}" min="0" max="300"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Isi 0 jika tidak ada batas waktu kuis keseluruhan.</p>
                </div>

                {{-- Batas Waktu Per Soal --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Timer Per Soal (Detik)
                    </label>
                    <input type="number" name="time_per_question" value="{{ old('time_per_question', 0) }}" min="0" max="300"
                           class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    <p class="text-[11px] text-gray-500 mt-1">Gaya Quizizz per soal. Isi 0 untuk menonaktifkan.</p>
                </div>

                {{-- Maksimal Percobaan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Maksimal Percobaan (Per NIM)
                    </label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" max="20" required
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
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', '1') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Acak Urutan Soal (Shuffle Questions)</span>
                        <span class="text-[11px] text-gray-400">Setiap mahasiswa menerima urutan soal yang berbeda-beda.</span>
                    </div>
                </label>

                {{-- Acak Opsi --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="shuffle_options" value="1" {{ old('shuffle_options', '1') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Acak Pilihan Jawaban (Shuffle Options)</span>
                        <span class="text-[11px] text-gray-400">Urutan pilihan A, B, C, D diacak secara otomatis.</span>
                    </div>
                </label>

                {{-- Tampilkan Leaderboard --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="show_leaderboard" value="1" {{ old('show_leaderboard', '1') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Tampilkan Podium / Leaderboard</span>
                        <span class="text-[11px] text-gray-400">Mahasiswa dapat melihat peringkat mereka setelah selesai.</span>
                    </div>
                </label>

                {{-- Tampilkan Pembahasan --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors">
                    <input type="checkbox" name="show_review" value="1" {{ old('show_review', '1') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-white block">Tampilkan Kunci & Pembahasan Soal</span>
                        <span class="text-[11px] text-gray-400">Mahasiswa dapat meninjau jawaban benar dan catatan penjelasan.</span>
                    </div>
                </label>

                {{-- Status Aktif Kuis --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820] hover:border-emerald-500/30 cursor-pointer transition-colors md:col-span-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-700 text-emerald-500 focus:ring-emerald-500 bg-gray-900">
                    <div>
                        <span class="text-xs font-bold text-emerald-400 block">Aktifkan Kuis Langsung</span>
                        <span class="text-[11px] text-gray-400">Jika dicentang, kuis langsung dapat diakses mahasiswa menggunakan kode unik atau link publik.</span>
                    </div>
                </label>
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
                <span>Simpan & Lanjut Buat Soal</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </form>
</div>
@endsection
