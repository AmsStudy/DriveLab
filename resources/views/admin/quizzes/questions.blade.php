@extends('layouts.admin')
@section('title', 'Bank Soal: ' . $quiz->title)
@section('page-title', 'CMS Bank Soal Kuis')
@section('page-description', 'Kelola daftar pertanyaan, gambar soal/jawaban, kunci jawaban jamak, dan pembahasan')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

<div x-data="questionManager()" class="space-y-6">

    {{-- Top Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quizzes.index') }}"
               class="p-2 rounded-xl bg-[#0F1713] border border-[#1A2820] text-gray-400 hover:text-emerald-400 hover:border-emerald-500/40 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-white">{{ $quiz->title }}</h3>
                    <span class="px-2 py-0.5 rounded-md bg-[#142019] border border-[#1E3327] text-emerald-400 font-mono text-xs font-bold">{{ $quiz->access_code }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $quiz->questions->count() }} Pertanyaan &bull; Total {{ $quiz->questions->sum('points') }} Poin
                    @if($quiz->soal)
                        &bull; Terkait Dokumen: <span class="text-teal-400 font-medium">{{ $quiz->soal->title }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quizzes.monitor', $quiz) }}"
               class="px-3.5 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/15 dark:hover:bg-emerald-500/25 border border-emerald-300 dark:border-emerald-500/40 hover:border-emerald-500 text-emerald-800 dark:text-emerald-300 hover:text-emerald-900 dark:hover:text-white text-xs sm:text-sm font-semibold transition-all flex items-center gap-2 shadow-xs">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600 dark:bg-emerald-500"></span>
                </span>
                <span>Live Monitor</span>
            </a>

            <a href="{{ route('admin.quizzes.results', $quiz) }}"
               class="px-3.5 py-2.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-amber-500/40 text-gray-300 hover:text-amber-400 text-xs sm:text-sm font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="hidden sm:inline">Hasil Mahasiswa</span>
            </a>

            <button type="button" @click="showImportModal = true"
                    class="px-3.5 py-2.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-sky-500/40 text-sky-400 hover:text-white text-xs sm:text-sm font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import Excel/CSV</span>
            </button>

            <button type="button" @click="openCreateModal()"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Soal</span>
            </button>
        </div>
    </div>

    {{-- Questions List: 2-Column Grid (Kiri & Kanan Natural) --}}
    @if($quiz->questions->isEmpty())
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">Belum Ada Pertanyaan di Kuis Ini</h3>
            <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">Mulai bangun pertanyaan pilihan ganda, pilihan jamak, atau bergambar untuk kuis interaktif ini.</p>
            <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold hover:opacity-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pertanyaan Pertama
            </button>
        </div>
    @else
        @php
            $totalQuestions = $quiz->questions->count();
        @endphp

        <div class="flex items-center justify-between gap-3 px-1 mb-1 text-xs text-gray-400">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-semibold text-xs">
                    <span>{{ $totalQuestions }} Pertanyaan</span>
                    <span>&bull;</span>
                    <span>Total {{ $quiz->totalPoints() }} Poin</span>
                </span>
            </div>
            <p class="text-[11px] text-gray-400 hidden sm:flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Ubah nomor di kartu soal untuk atur urutan (tersimpan otomatis)</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
            @foreach($quiz->questions as $index => $q)
                @include('admin.quizzes.partials.question-card', [
                    'q' => $q,
                    'qNum' => $index + 1,
                    'totalQuestions' => $totalQuestions,
                    'quiz' => $quiz
                ])
            @endforeach
        </div>
    @endif

    {{-- Question Modal (Create / Edit) --}}
    <template x-teleport="body">
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none; z-index: 99999 !important;">

            <div @click.away="closeModal()"
                 class="quiz-modal-card bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-3xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-[#1A2820] flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white" x-text="isEdit ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru'"></h3>
                    <p class="text-xs text-gray-400">Buat soal pilihan tunggal / jamak lengkap dengan gambar tajam & pembahasan</p>
                </div>
                <button type="button" @click="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body Form --}}
            <form :action="formAction" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-5">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Baris Pengaturan: Tipe & Poin --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tipe Pertanyaan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                            Jenis Pertanyaan <span class="text-red-400">*</span>
                        </label>
                        <select name="question_type" x-model="formData.question_type" @change="handleTypeChange()"
                                class="cyber-select w-full pl-4 pr-10 py-2.5 text-sm font-medium cursor-pointer">
                            <option value="multiple_choice">Pilihan Ganda (Tunggal)</option>
                            <option value="multi_select">Pilihan Jamak (Checkbox)</option>
                            <option value="true_false">Benar / Salah (True / False)</option>
                            <option value="fill_blank">Isian Rumpang (Fill in the Blank)</option>
                        </select>
                    </div>

                    {{-- Bobot Poin --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                            Bobot Nilai (Poin) <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="points" x-model="formData.points" min="1" max="1000" required
                               class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 font-bold">
                    </div>
                </div>

                {{-- Teks Pertanyaan --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5 flex-wrap gap-2">
                        <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Teks Pertanyaan <span class="text-red-400">*</span>
                        </label>
                        {{-- Toolbar Cepat Khusus Isian Rumpang --}}
                        <div x-show="formData.question_type === 'fill_blank'" class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" @click="wrapSelectedTextAsBlank()"
                                    title="Sorot kata di dalam teks lalu klik tombol ini untuk menjadikannya titik-titik"
                                    class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/25 font-semibold flex items-center gap-1 transition-all shadow-xs cursor-pointer">
                                <span>🎯</span>
                                <span>Jadikan Teks Terpilih Sebagai Titik-Titik</span>
                            </button>
                            <button type="button" @click="insertBlankAtCursor()"
                                    title="Sisipkan titik-titik baru pada posisi kursor"
                                    class="text-[11px] px-2.5 py-1 rounded-lg bg-teal-500/15 border border-teal-500/30 text-teal-300 hover:bg-teal-500/25 font-semibold flex items-center gap-1 transition-all shadow-xs cursor-pointer">
                                <span>➕</span>
                                <span>Sisipkan Titik-Titik</span>
                            </button>
                        </div>
                    </div>
                    <textarea name="question_text" x-ref="questionTextArea" x-model="formData.question_text" @input="handleQuestionTextInput()" rows="3" required
                              :placeholder="formData.question_type === 'fill_blank' ? 'Tuliskan kalimat soal di sini, contoh: Nilai x pada Outer adalah 10 dan Inner adalah 20...' : 'Tuliskan teks atau butir pertanyaan di sini...'"
                              class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500 leading-relaxed"></textarea>
                    <p x-show="formData.question_type === 'fill_blank'" class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                        <span>💡</span>
                        <span>Admin cukup mengetik kalimat biasa. Gunakan tombol di atas atau form di bawah untuk menentukan kata kunci tanpa perlu menghafal tanda kurung siku <code>[ ]</code>.</span>
                    </p>
                </div>

                {{-- Upload Gambar Pertanyaan (Kompresi Tajam / Anti-Blur) --}}
                <div class="p-3.5 rounded-xl bg-[#090D0B] border border-[#1A2820]">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-teal-300 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Gambar Pendukung Soal (Opsional &bull; Otomatis Terkompresi Tajam)
                        </label>
                        <template x-if="formData.question_image_preview">
                            <button type="button" @click="clearQuestionImage()"
                                    class="text-xs text-rose-400 hover:text-rose-300 flex items-center gap-1 font-medium">
                                ✕ Hapus Gambar
                            </button>
                        </template>
                    </div>

                    {{-- Image Preview if existing or selected --}}
                    <template x-if="formData.question_image_preview">
                        <div class="mb-3 max-h-48 overflow-hidden rounded-lg border border-teal-500/30 bg-black/40 flex items-center justify-center p-2">
                            <img :src="formData.question_image_preview" alt="Preview Gambar Soal" class="max-h-44 object-contain rounded">
                        </div>
                    </template>

                    <input type="file" name="question_image" accept="image/*" @change="previewQuestionImage($event)"
                           class="cyber-file-input w-full text-xs text-gray-600 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border file:border-emerald-500/30 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-500/20 file:text-emerald-800 dark:file:text-emerald-300 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-500/30 file:cursor-pointer cursor-pointer transition-all shadow-sm">
                    
                    <input type="hidden" name="remove_question_image" :value="formData.remove_question_image ? 1 : 0">
                    <p class="text-[11px] text-gray-500 mt-1.5">Format: JPG, PNG, WebP. Gambar dikompresi otomatis tanpa pecah/blur agar cepat diakses mahasiswa.</p>
                </div>

                {{-- Opsi Jawaban untuk Pilihan Ganda & Benar-Salah --}}
                <div x-show="formData.question_type !== 'fill_blank'">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">
                                Pilihan Jawaban & Kunci
                            </label>
                            <p class="text-[11px] text-gray-400" x-text="formData.question_type === 'multi_select' ? 'Centang kotak (checkbox) untuk SEMUA jawaban yang benar.' : 'Centang tombol radio untuk satu kunci jawaban yang benar.'"></p>
                        </div>

                        <button type="button" x-show="formData.question_type !== 'true_false' && formData.options.length < 6"
                                @click="addOption()"
                                class="text-xs text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-semibold px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Opsi
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(opt, idx) in formData.options" :key="idx">
                            <div class="p-3 rounded-xl bg-[#090D0B] border transition-colors space-y-2"
                                 :class="isCorrect(idx) ? 'border-emerald-500/60 bg-emerald-500/5' : 'border-[#1A2820]'">
                                
                                <div class="flex items-center gap-2">
                                    {{-- Multi-Select Checkbox OR Single Radio --}}
                                    <template x-if="formData.question_type === 'multi_select'">
                                        <label class="flex items-center gap-2 cursor-pointer px-1 py-1">
                                            <input type="checkbox" name="correct_indices[]" :value="idx"
                                                   :checked="isCorrect(idx)"
                                                   @change="toggleCorrect(idx)"
                                                   class="rounded text-purple-500 focus:ring-purple-500 w-4 h-4">
                                            <span class="text-xs font-bold font-mono"
                                                  :class="isCorrect(idx) ? 'text-purple-300' : 'text-gray-400'"
                                                  x-text="getOptionLabel(idx)"></span>
                                        </label>
                                    </template>

                                    <template x-if="formData.question_type !== 'multi_select'">
                                        <label class="flex items-center gap-2 cursor-pointer px-1 py-1">
                                            <input type="radio" name="correct_index" :value="idx"
                                                   :checked="parseInt(formData.correct_index) === idx"
                                                   @change="formData.correct_index = idx"
                                                   class="text-emerald-500 focus:ring-emerald-500 w-4 h-4">
                                            <span class="text-xs font-bold font-mono"
                                                  :class="parseInt(formData.correct_index) === idx ? 'text-emerald-400' : 'text-gray-400'"
                                                  x-text="getOptionLabel(idx)"></span>
                                        </label>
                                    </template>

                                    {{-- Text input for option --}}
                                    <input type="text" :name="'options[' + idx + ']'" x-model="formData.options[idx]"
                                           :placeholder="'Tuliskan teks opsi jawaban ' + getOptionLabel(idx) + ' (atau unggah gambar di bawah)...'"
                                           class="flex-1 px-3 py-1.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 rounded-lg text-sm text-gray-100 placeholder-gray-600">

                                    {{-- Remove Option (if MC and > 2) --}}
                                    <button type="button" x-show="formData.question_type !== 'true_false' && formData.options.length > 2"
                                            @click="removeOption(idx)"
                                            title="Hapus opsi ini"
                                            class="p-1.5 text-gray-500 hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                {{-- Option Image Upload & Preview --}}
                                <div class="pl-8 flex items-center gap-3">
                                    <template x-if="formData.option_image_previews[idx]">
                                        <div class="relative group">
                                            <img :src="formData.option_image_previews[idx]" alt="Preview Opsi" class="h-10 w-auto rounded border border-white/20 object-contain bg-black/50">
                                            <button type="button" @click="clearOptionImage(idx)"
                                                    class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 text-[10px] flex items-center justify-center">✕</button>
                                        </div>
                                    </template>

                                    <input type="file" :name="'option_images[' + idx + ']'" accept="image/*"
                                           @change="previewOptionImage($event, idx)"
                                           class="cyber-file-input text-[11px] text-gray-600 dark:text-gray-400 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border file:border-emerald-500/30 file:text-[11px] file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-500/20 file:text-emerald-800 dark:file:text-emerald-300 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-500/30 file:cursor-pointer cursor-pointer transition-all shadow-sm">

                                    <input type="hidden" :name="'existing_option_images[' + idx + ']'" :value="formData.existing_option_images[idx] || ''">
                                    <input type="hidden" :name="'remove_option_images[' + idx + ']'" :value="formData.remove_option_images[idx] ? 1 : 0">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Opsi Jawaban Khusus ISIAN RUMPANG (Fill in the Blank) --}}
                <div x-show="formData.question_type === 'fill_blank'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-semibold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Daftar Kunci Jawaban Titik-Titik
                            </label>
                            <p class="text-[11px] text-gray-400">Admin langsung menginput kunci jawaban di bawah ini tanpa perlu mengetik tanda kurung <code>[ ]</code>.</p>
                        </div>

                        <button type="button" @click="insertBlankAtEnd()"
                                class="text-xs text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-semibold px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Titik-Titik
                        </button>
                    </div>

                    {{-- Hidden Form Inputs for Options Payload (used by standard form submit) --}}
                    <template x-if="formData.question_type === 'fill_blank'">
                        <div>
                            <template x-for="(blank, bIdx) in formData.blanks" :key="'opt-hidden-' + bIdx">
                                <input type="hidden" :name="'options[' + bIdx + ']'" :value="getBlankOptionValue(blank)">
                            </template>
                            <template x-if="formData.blanks.length === 0">
                                <input type="hidden" name="options[0]" value="">
                            </template>
                        </div>
                    </template>

                    {{-- Cards Daftar Titik-Titik --}}
                    <div class="space-y-3">
                        <template x-for="(blank, bIdx) in formData.blanks" :key="'blank-' + bIdx">
                            <div class="p-3.5 rounded-xl bg-[#090D0B] border border-emerald-500/30 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 font-bold font-mono text-xs flex items-center justify-center"
                                              x-text="'#' + (bIdx + 1)"></span>
                                        <span class="text-xs font-semibold text-gray-200" x-text="'Titik-Titik Nomor ' + (bIdx + 1)"></span>
                                    </div>

                                    <button type="button" @click="removeBlank(bIdx)"
                                            class="text-xs text-rose-400 hover:text-rose-300 flex items-center gap-1 font-medium transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    {{-- Kunci Jawaban Utama --}}
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">
                                            Kunci Jawaban Utama <span class="text-red-400">*</span>
                                        </label>
                                        <input type="text" x-model="blank.key" @input="syncBlanksToText()"
                                               placeholder="Contoh: 20 atau public"
                                               class="w-full px-3 py-1.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 rounded-lg text-sm text-emerald-300 font-mono font-bold placeholder-gray-600">
                                    </div>

                                    {{-- Jawaban Alternatif / Sinonim --}}
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-400 mb-1">
                                            Alternatif / Sinonim (Opsional)
                                        </label>
                                        <input type="text" x-model="blank.synonyms" @input="syncBlanksToText()"
                                               placeholder="Pisahkan dengan | contoh: dua puluh | 20.0"
                                               class="w-full px-3 py-1.5 bg-[#0F1713] border border-[#1A2820] focus:border-emerald-500 rounded-lg text-sm text-gray-200 font-mono placeholder-gray-600">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="formData.blanks.length === 0">
                            <div class="p-6 text-center rounded-xl border border-dashed border-gray-700 bg-[#090D0B] text-gray-400 text-xs">
                                <p class="mb-2">Belum ada titik-titik yang ditentukan.</p>
                                <button type="button" @click="insertBlankAtEnd()"
                                        class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30 hover:bg-emerald-500/30 transition-all cursor-pointer">
                                    + Tambah Titik-Titik Pertama
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Live Preview Kartu Soal di Sisi Mahasiswa --}}
                    <div class="p-4 rounded-xl bg-[#090D0B] border border-emerald-500/20 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                                <span>👁️</span>
                                <span>Pratinjau Tampilan Mahasiswa (Real-time)</span>
                            </span>
                            <span class="text-[11px] text-gray-400" x-text="formData.blanks.length + ' Bagian Titik-Titik'"></span>
                        </div>
                        <div class="p-3.5 rounded-lg bg-[#0F1713] border border-[#1A2820] text-sm text-gray-200 leading-loose">
                            <div x-html="renderAdminLivePreview()"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Pembahasan & Kunci Jawaban (Opsional)
                    </label>
                    <textarea name="explanation" x-model="formData.explanation" rows="2"
                              placeholder="Tuliskan catatan pembahasan mengapa jawaban tersebut benar untuk ditinjau mahasiswa..."
                              class="w-full px-4 py-2 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500"></textarea>
                </div>

                {{-- Footer Submit --}}
                <div class="pt-4 border-t border-[#1A2820] flex items-center justify-end gap-3">
                    <button type="button" @click="closeModal()"
                            class="px-4 py-2 rounded-xl border border-[#1A2820] hover:bg-gray-800 text-gray-300 text-sm font-medium transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-emerald-500/20">
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Tambahkan Pertanyaan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Modal Import Massal Soal (Excel / CSV Spreadsheet) --}}
    <template x-teleport="body">
        <div x-show="showImportModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 overflow-y-auto bg-black/85 backdrop-blur-md flex items-center justify-center p-4"
             style="display: none; z-index: 99999 !important;">

            <div @click.away="showImportModal = false"
                 class="quiz-modal-card bg-[#0F1713] border border-[#1A2820] rounded-3xl w-full max-w-2xl max-h-[92vh] overflow-y-auto p-6 sm:p-8 shadow-2xl relative">
            
            {{-- Glowing Ambient Top --}}
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-80 h-32 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-5 border-b border-[#1A2820] relative">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/20 border border-emerald-500/35 flex items-center justify-center text-emerald-400 shadow-md shadow-emerald-500/10 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-white heading-font tracking-tight">Import Massal Soal Kuis</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Unggah spreadsheet Excel (.xlsx) atau CSV untuk mengisi bank soal secara otomatis</p>
                    </div>
                </div>
                <button type="button" @click="showImportModal = false"
                        class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-[#1A2820] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Template Download Showcase Card --}}
            <div class="import-template-card my-5 p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-emerald-50 via-teal-50/60 to-emerald-100/50 dark:from-[#122219] dark:to-[#0D1812] border border-emerald-200 dark:border-emerald-500/30 relative overflow-hidden shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">Format Template Resmi (Excel & CSV)</span>
                        </div>
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                            Dilengkapi <strong class="text-gray-900 dark:text-white font-bold">Dropdown Otomatis</strong> pada kolom <em class="text-emerald-900 dark:text-emerald-300 not-italic font-semibold">Tipe Soal</em> &amp; <em class="text-emerald-900 dark:text-emerald-300 not-italic font-semibold">Kunci Jawaban</em> (tinggal klik &amp; pilih tanpa ketik manual), serta contoh nyata blok kode C++, Pilihan Jamak, dan Benar/Salah.
                        </p>
                    </div>

                    {{-- Dual Download Buttons --}}
                    <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                        {{-- Excel Template with Native Dropdowns (.XLSX) --}}
                        <a href="{{ route('admin.quizzes.questions.template', [$quiz, 'format' => 'excel']) }}"
                           title="Rekomendasi Utama: Template resmi Microsoft Excel (.xlsx) dengan pilihan dropdown otomatis pada kolom pilihan"
                           class="px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-emerald-600/20 group">
                            <svg class="w-4 h-4 text-emerald-100 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Excel (Dropdown)</span>
                        </a>

                        {{-- Standard CSV (Comma) --}}
                        <a href="{{ route('admin.quizzes.questions.template', [$quiz, 'format' => 'csv']) }}"
                           title="Format teks murni CSV untuk Google Sheets, WPS Office, LibreOffice, atau Text Editor"
                           class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-emerald-50/80 border border-emerald-300/80 hover:border-emerald-500 text-emerald-800 hover:text-emerald-950 dark:bg-[#142019] dark:hover:bg-[#1C2E24] dark:border-[#1E3327] dark:hover:border-emerald-500/40 dark:text-gray-300 dark:hover:text-white font-semibold text-xs transition-all flex items-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>CSV Standar (Koma)</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Import Form --}}
            <form action="{{ route('admin.quizzes.questions.import', $quiz) }}"
                  method="POST" enctype="multipart/form-data"
                  @submit="isSubmittingImport = true"
                  class="space-y-4">
                @csrf

                {{-- Hidden Real File Input --}}
                <input type="file" x-ref="importFileInput" name="file"
                       @change="handleFileSelect($event)"
                       accept=".xlsx,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                       class="hidden">

                {{-- Modern Drag & Drop Zone --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                        Pilih File Spreadsheet:
                    </label>

                    {{-- Dropzone Area --}}
                    <div @dragover.prevent="isDraggingFile = true"
                         @dragleave.prevent="isDraggingFile = false"
                         @drop.prevent="handleDrop($event)"
                         @click="$refs.importFileInput.click()"
                         class="relative border-2 border-dashed rounded-2xl p-6 sm:p-7 text-center cursor-pointer transition-all duration-200 group"
                         :class="isDraggingFile ? 'border-emerald-500 bg-emerald-500/15 scale-[1.01]' : (importFile ? 'border-emerald-500/60 bg-emerald-50/80 dark:bg-[#122219]' : 'border-gray-200 dark:border-[#1E3327] hover:border-emerald-500/50 bg-white dark:bg-[#090D0B] hover:bg-emerald-50/30 dark:hover:bg-[#0c1410]')">

                        {{-- State 1: No file chosen --}}
                        <template x-if="!importFile">
                            <div>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3 transition-transform group-hover:scale-110 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                                    <span class="text-emerald-600 dark:text-emerald-400 underline decoration-emerald-500/40 underline-offset-4">Klik untuk memilih file</span> atau seret file ke sini
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Mendukung format <strong class="text-gray-800 dark:text-gray-200">.XLSX</strong> (Excel) dan <strong class="text-gray-800 dark:text-gray-200">.CSV</strong> (Maksimal 10 MB)
                                </p>
                            </div>
                        </template>

                        {{-- State 2: File Selected with Details --}}
                        <template x-if="importFile">
                            <div class="flex items-center justify-between gap-3 text-left p-3.5 rounded-xl bg-emerald-50/90 dark:bg-[#090D0B] border border-emerald-300 dark:border-emerald-500/40 shadow-sm">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-300/80 dark:border-emerald-500/40 flex items-center justify-center font-mono font-bold text-xs uppercase flex-shrink-0">
                                        <span x-text="importFileExt || 'FILE'"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="importFileName"></p>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 flex-shrink-0">Siap Diimpor</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5" x-text="importFileSize + ' • Klik untuk ganti file'"></p>
                                    </div>
                                </div>
                                <button type="button" @click.stop="clearImportFile()"
                                        class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors flex-shrink-0"
                                        title="Hapus file ini">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Interactive Collapsible Guide: Panduan Kolom Spreadsheet --}}
                <div class="rounded-2xl bg-gray-50/90 dark:bg-[#090D0B] border border-gray-200 dark:border-[#1A2820] overflow-hidden shadow-sm">
                    <button type="button" @click="showGuideTable = !showGuideTable"
                            class="w-full px-4 py-3 text-left flex items-center justify-between text-xs font-semibold text-gray-700 dark:text-gray-300 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                        <span class="flex items-center gap-2">
                            <span>💡</span>
                            <span>Panduan Format Kolom Spreadsheet (RFC 4180)</span>
                        </span>
                        <div class="flex items-center gap-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                            <span x-text="showGuideTable ? 'Sembunyikan' : 'Lihat Detail Kolom'"></span>
                            <svg class="w-4 h-4 transition-transform duration-200"
                                 :class="showGuideTable ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <div x-show="showGuideTable" x-collapse class="border-t border-gray-200 dark:border-[#1A2820] p-4 text-xs space-y-2.5 bg-white dark:bg-transparent">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[11px] border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-[#1A2820] text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">
                                        <th class="py-2 pr-3">Kolom</th>
                                        <th class="py-2 px-3">Status</th>
                                        <th class="py-2 px-3">Tipe Data / Pilihan</th>
                                        <th class="py-2 pl-3">Contoh Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-[#1A2820] text-gray-700 dark:text-gray-300 font-mono">
                                    <tr>
                                        <td class="py-2 pr-3 font-bold text-gray-900 dark:text-white">Pertanyaan</td>
                                        <td class="py-2 px-3"><span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 font-sans font-bold text-[10px]">Wajib</span></td>
                                        <td class="py-2 px-3 font-sans text-gray-600 dark:text-gray-300">Teks biasa atau blok kode Markdown</td>
                                        <td class="py-2 pl-3 text-gray-500 dark:text-gray-400 font-sans">Perhatikan kode <code>```cpp int x; ```</code></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 pr-3 font-bold text-gray-900 dark:text-white">Tipe_Soal</td>
                                        <td class="py-2 px-3">
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 font-sans font-bold text-[10px]">Wajib</span>
                                            <span class="px-1.5 py-0.5 rounded bg-sky-100 dark:bg-sky-500/15 text-sky-800 dark:text-sky-300 font-sans font-bold text-[10px]">Dropdown</span>
                                        </td>
                                        <td class="py-2 px-3 text-sky-700 dark:text-sky-300">multiple_choice | multi_select | true_false | fill_blank</td>
                                        <td class="py-2 pl-3 text-gray-500 dark:text-gray-400">multiple_choice</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 pr-3 font-bold text-gray-900 dark:text-white">Poin</td>
                                        <td class="py-2 px-3"><span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-sans text-[10px]">Opsional</span></td>
                                        <td class="py-2 px-3 font-sans text-gray-600 dark:text-gray-300">Angka bulat positif (default: 10)</td>
                                        <td class="py-2 pl-3 text-amber-600 dark:text-amber-400 font-bold">10</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 pr-3 font-bold text-gray-900 dark:text-white">Pilihan_A s/d E</td>
                                        <td class="py-2 px-3"><span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 font-sans font-bold text-[10px]">A & B Wajib</span></td>
                                        <td class="py-2 px-3 font-sans text-gray-600 dark:text-gray-300">Teks pilihan jawaban (C s/d E opsional)</td>
                                        <td class="py-2 pl-3 text-gray-500 dark:text-gray-400 font-sans">A: malloc(), B: free()</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 pr-3 font-bold text-gray-900 dark:text-white">Kunci_Jawaban</td>
                                        <td class="py-2 px-3">
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/15 text-emerald-800 dark:text-emerald-400 font-sans font-bold text-[10px]">Wajib</span>
                                            <span class="px-1.5 py-0.5 rounded bg-teal-100 dark:bg-teal-500/15 text-teal-800 dark:text-teal-300 font-sans font-bold text-[10px]">Dropdown</span>
                                        </td>
                                        <td class="py-2 px-3 font-sans text-teal-700 dark:text-teal-300 font-bold">Huruf kunci jawaban benar</td>
                                        <td class="py-2 pl-3 text-emerald-600 dark:text-emerald-400 font-bold">A (atau "A, C" untuk jamak)</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 pr-3 font-bold text-gray-900 dark:text-white">Pembahasan</td>
                                        <td class="py-2 px-3"><span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-sans text-[10px]">Opsional</span></td>
                                        <td class="py-2 px-3 font-sans text-gray-600 dark:text-gray-300">Penjelasan kunci untuk mahasiswa</td>
                                        <td class="py-2 pl-3 text-gray-500 dark:text-gray-400 font-sans">malloc bertugas di heap...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Action Footer --}}
                <div class="pt-4 border-t border-gray-200 dark:border-[#1A2820] flex items-center justify-between gap-3">
                    <button type="button" @click="showImportModal = false"
                            class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-[#1A2820] bg-white dark:bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-semibold transition-colors shadow-sm cursor-pointer">
                        Batal
                    </button>

                    <button type="submit" :disabled="!importFile || isSubmittingImport"
                            class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:from-gray-200 disabled:to-gray-200 disabled:text-gray-400 disabled:border disabled:border-gray-300 dark:disabled:from-emerald-950 dark:disabled:to-teal-950 dark:disabled:text-emerald-300/40 disabled:cursor-not-allowed disabled:shadow-none text-white px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 cursor-pointer">
                        <template x-if="!isSubmittingImport">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Mulai Import Soal Sekarang</span>
                            </span>
                        </template>
                        <template x-if="isSubmittingImport">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Memproses & Menyimpan Soal...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>

<script>
    function questionManager() {
        return {
            showModal: false,
            showImportModal: false,
            importFile: null,
            importFileName: '',
            importFileSize: '',
            importFileExt: '',
            isDraggingFile: false,
            isSubmittingImport: false,
            showGuideTable: false,
            isEdit: false,
            formAction: '{{ route('admin.quizzes.questions.store', $quiz) }}',
            formData: {
                question_text: '',
                question_type: 'multiple_choice',
                points: 10,
                options: ['', '', '', ''],
                correct_index: 0,
                correct_indices: [0],
                blanks: [],
                explanation: '',
                question_image_preview: null,
                remove_question_image: false,
                existing_option_images: {},
                option_image_previews: {},
                remove_option_images: {}
            },
            openCreateModal() {
                this.isEdit = false;
                this.formAction = '{{ route('admin.quizzes.questions.store', $quiz) }}';
                this.formData = {
                    question_text: '',
                    question_type: 'multiple_choice',
                    points: 10,
                    options: ['', '', '', ''],
                    correct_index: 0,
                    correct_indices: [0],
                    blanks: [],
                    explanation: '',
                    question_image_preview: null,
                    remove_question_image: false,
                    existing_option_images: {},
                    option_image_previews: {},
                    remove_option_images: {}
                };
                this.showModal = true;
            },
            openEditModal(question) {
                this.isEdit = true;
                this.formAction = `{{ url('admin/quizzes/' . $quiz->id . '/questions') }}/${question.id}`;
                
                let optTexts = [];
                let correctIdx = 0;
                let correctIndices = [];
                let blanks = [];
                let existingOptImages = {};
                let optImagePreviews = {};

                if (question.question_type === 'fill_blank') {
                    if (question.options && question.options.length > 0) {
                        question.options.forEach((opt, idx) => {
                            const raw = opt.option_text || '';
                            const parts = raw.split('|');
                            const key = parts[0] ? parts[0].trim() : '';
                            const syn = parts.slice(1).join(' | ').trim();
                            blanks.push({ key: key, synonyms: syn });
                            optTexts.push(raw);
                        });
                    } else {
                        // Parse from brackets if options table empty
                        const regex = /\[([^\]]+)\]/g;
                        let match;
                        while ((match = regex.exec(question.question_text)) !== null) {
                            const raw = match[1] || '';
                            const parts = raw.split('|');
                            blanks.push({
                                key: parts[0] ? parts[0].trim() : '',
                                synonyms: parts.slice(1).join(' | ').trim()
                            });
                            optTexts.push(raw);
                        }
                    }
                    if (blanks.length === 0) {
                        blanks.push({ key: '', synonyms: '' });
                        optTexts.push('');
                    }
                } else if (question.options && question.options.length > 0) {
                    question.options.forEach((opt, idx) => {
                        optTexts.push(opt.option_text || '');
                        if (opt.is_correct) {
                            correctIdx = idx;
                            correctIndices.push(idx);
                        }
                        if (opt.image_url) {
                            existingOptImages[idx] = opt.image_path;
                            optImagePreviews[idx] = opt.image_url;
                        }
                    });
                } else {
                    optTexts = ['', '', '', ''];
                    correctIndices = [0];
                }

                if (correctIndices.length === 0) {
                    correctIndices = [0];
                }

                const isMulti = question.question_type === 'multi_select' || (question.question_type !== 'fill_blank' && correctIndices.length > 1);

                this.formData = {
                    question_text: question.question_text,
                    question_type: question.question_type === 'fill_blank' ? 'fill_blank' : (isMulti ? 'multi_select' : (question.question_type || 'multiple_choice')),
                    points: question.points,
                    options: optTexts,
                    correct_index: correctIdx,
                    correct_indices: correctIndices,
                    blanks: blanks,
                    explanation: question.explanation || '',
                    question_image_preview: question.image_url || null,
                    remove_question_image: false,
                    existing_option_images: existingOptImages,
                    option_image_previews: optImagePreviews,
                    remove_option_images: {}
                };
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },
            handleTypeChange() {
                if (this.formData.question_type === 'true_false') {
                    this.formData.options = ['Benar', 'Salah'];
                    this.formData.correct_index = 0;
                    this.formData.correct_indices = [0];
                } else if (this.formData.question_type === 'fill_blank') {
                    this.parseBlanksFromText();
                    if (!this.formData.blanks || this.formData.blanks.length === 0) {
                        this.formData.blanks = [{ key: '', synonyms: '' }];
                    }
                } else if (this.formData.options.length < 2) {
                    this.formData.options = ['', '', '', ''];
                }
            },
            getBlankOptionValue(blank) {
                if (!blank) return '';
                const k = (blank.key || '').trim();
                const s = (blank.synonyms || '').trim();
                return s ? `${k}|${s}` : k;
            },
            parseBlanksFromText() {
                const text = this.formData.question_text || '';
                const regex = /\[([^\]]+)\]/g;
                let match;
                const found = [];
                while ((match = regex.exec(text)) !== null) {
                    const raw = match[1] || '';
                    const parts = raw.split('|');
                    found.push({
                        key: parts[0] ? parts[0].trim() : '',
                        synonyms: parts.slice(1).join(' | ').trim()
                    });
                }
                if (found.length > 0) {
                    this.formData.blanks = found;
                }
            },
            handleQuestionTextInput() {
                if (this.formData.question_type !== 'fill_blank') return;
                const regex = /\[([^\]]+)\]/g;
                let match;
                const found = [];
                while ((match = regex.exec(this.formData.question_text)) !== null) {
                    const raw = match[1] || '';
                    const parts = raw.split('|');
                    found.push({
                        key: parts[0] ? parts[0].trim() : '',
                        synonyms: parts.slice(1).join(' | ').trim()
                    });
                }
                if (found.length > 0) {
                    this.formData.blanks = found;
                }
            },
            wrapSelectedTextAsBlank() {
                const ta = this.$refs.questionTextArea;
                if (!ta) return;
                const start = ta.selectionStart;
                const end = ta.selectionEnd;
                const text = this.formData.question_text || '';
                const selected = text.substring(start, end).trim();

                if (selected.length > 0) {
                    const before = text.substring(0, start);
                    const after = text.substring(end);
                    this.formData.question_text = `${before}[${selected}]${after}`;
                    this.formData.blanks.push({ key: selected, synonyms: '' });
                    this.$nextTick(() => {
                        ta.focus();
                        ta.setSelectionRange(start + 1, start + 1 + selected.length);
                    });
                } else {
                    this.insertBlankAtCursor();
                }
            },
            insertBlankAtCursor() {
                const ta = this.$refs.questionTextArea;
                const text = this.formData.question_text || '';
                const start = ta ? ta.selectionStart : text.length;
                const end = ta ? ta.selectionEnd : text.length;
                const before = text.substring(0, start);
                const after = text.substring(end);
                const defaultKey = 'Kunci';

                this.formData.question_text = `${before}[${defaultKey}]${after}`;
                this.formData.blanks.push({ key: defaultKey, synonyms: '' });
                this.$nextTick(() => {
                    if (ta) {
                        ta.focus();
                        ta.setSelectionRange(start + 1, start + 1 + defaultKey.length);
                    }
                });
            },
            insertBlankAtEnd() {
                const text = this.formData.question_text || '';
                const defaultKey = 'Kunci';
                this.formData.question_text = text.trim() ? `${text.trim()} [${defaultKey}]` : `[${defaultKey}]`;
                this.formData.blanks.push({ key: defaultKey, synonyms: '' });
                const ta = this.$refs.questionTextArea;
                this.$nextTick(() => {
                    if (ta) ta.focus();
                });
            },
            removeBlank(index) {
                if (index < 0 || index >= this.formData.blanks.length) return;
                const targetBlank = this.formData.blanks[index];
                this.formData.blanks.splice(index, 1);

                let count = 0;
                this.formData.question_text = (this.formData.question_text || '').replace(/\[([^\]]*)\]/g, (match, content) => {
                    if (count === index) {
                        count++;
                        return targetBlank ? targetBlank.key : '';
                    }
                    count++;
                    return match;
                });
            },
            syncBlanksToText() {
                if (this.formData.question_type !== 'fill_blank') return;
                let bIdx = 0;
                const blanks = this.formData.blanks;
                this.formData.question_text = (this.formData.question_text || '').replace(/\[([^\]]*)\]/g, (match) => {
                    if (bIdx < blanks.length) {
                        const b = blanks[bIdx++];
                        const val = this.getBlankOptionValue(b);
                        return `[${val}]`;
                    }
                    return match;
                });
            },
            renderAdminLivePreview() {
                const raw = this.formData.question_text || '';
                if (!raw) {
                    return '<span class="italic text-gray-500">Tulis teks pertanyaan untuk melihat pratinjau...</span>';
                }
                let bCount = 0;
                const escRaw = raw
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');

                const rendered = escRaw.replace(/\[([^\]]*)\]/g, (match, content) => {
                    bCount++;
                    const parts = content.split('|');
                    const key = parts[0] ? parts[0].trim() : ('Titik-titik #' + bCount);
                    return `<span class="inline-flex items-center gap-1 mx-1 px-2.5 py-0.5 rounded-lg border border-emerald-500/50 bg-emerald-500/10 text-emerald-300 font-mono font-bold text-xs shadow-inner">
                        <span class="text-[10px] text-emerald-500/80">#${bCount}:</span> ${key}
                    </span>`;
                });

                return rendered.replace(/\n/g, '<br>');
            },
            isCorrect(idx) {
                if (this.formData.question_type === 'multi_select') {
                    return this.formData.correct_indices.includes(idx);
                }
                return parseInt(this.formData.correct_index) === idx;
            },
            toggleCorrect(idx) {
                const i = this.formData.correct_indices.indexOf(idx);
                if (i > -1) {
                    if (this.formData.correct_indices.length > 1) {
                        this.formData.correct_indices.splice(i, 1);
                    }
                } else {
                    this.formData.correct_indices.push(idx);
                }
            },
            addOption() {
                if (this.formData.options.length < 6) {
                    this.formData.options.push('');
                }
            },
            removeOption(index) {
                if (this.formData.options.length > 2) {
                    this.formData.options.splice(index, 1);
                    this.formData.correct_indices = this.formData.correct_indices.filter(i => i !== index).map(i => i > index ? i - 1 : i);
                    if (this.formData.correct_indices.length === 0) {
                        this.formData.correct_indices = [0];
                    }
                    if (this.formData.correct_index >= this.formData.options.length) {
                        this.formData.correct_index = 0;
                    }
                }
            },
            getOptionLabel(index) {
                const labels = ['A', 'B', 'C', 'D', 'E', 'F'];
                return labels[index] || (index + 1);
            },
            previewQuestionImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.formData.question_image_preview = URL.createObjectURL(file);
                    this.formData.remove_question_image = false;
                }
            },
            clearQuestionImage() {
                this.formData.question_image_preview = null;
                this.formData.remove_question_image = true;
            },
            previewOptionImage(event, idx) {
                const file = event.target.files[0];
                if (file) {
                    this.formData.option_image_previews[idx] = URL.createObjectURL(file);
                    this.formData.remove_option_images[idx] = false;
                }
            },
            clearOptionImage(idx) {
                this.formData.option_image_previews[idx] = null;
                this.formData.remove_option_images[idx] = true;
                delete this.formData.existing_option_images[idx];
            },
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.setImportFile(file);
                }
            },
            handleDrop(event) {
                this.isDraggingFile = false;
                const file = event.dataTransfer?.files[0];
                if (file) {
                    this.setImportFile(file);
                    if (this.$refs.importFileInput && window.DataTransfer) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        this.$refs.importFileInput.files = dt.files;
                    }
                }
            },
            setImportFile(file) {
                this.importFile = file;
                this.importFileName = file.name;
                const ext = file.name.split('.').pop() || '';
                this.importFileExt = ext.toUpperCase();
                
                if (file.size < 1024) {
                    this.importFileSize = file.size + ' B';
                } else if (file.size < 1024 * 1024) {
                    this.importFileSize = (file.size / 1024).toFixed(1) + ' KB';
                } else {
                    this.importFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                }
            },
            clearImportFile() {
                this.importFile = null;
                this.importFileName = '';
                this.importFileSize = '';
                this.importFileExt = '';
                if (this.$refs.importFileInput) {
                    this.$refs.importFileInput.value = '';
                }
            }
        };
    }

    async function getFreshCsrfToken() {
        try {
            const res = await fetch('{{ route('csrf.refresh') }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                if (data.csrf_token) {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) {
                        meta.setAttribute('content', data.csrf_token);
                    }
                    return data.csrf_token;
                }
            }
        } catch (e) {
            console.warn('Gagal memuat token CSRF baru:', e);
        }
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async function changeQuestionOrder(selectEl, url, currentNum) {
        const newNum = parseInt(selectEl.value, 10);
        if (!newNum || newNum === currentNum) return;

        selectEl.disabled = true;
        const badge = selectEl.closest('.group\\/order');
        if (badge) badge.classList.add('opacity-50', 'pointer-events-none');

        if (window.Swal) {
            Swal.fire({
                title: 'Memindahkan Soal...',
                text: `Mengubah urutan pertanyaan #${currentNum} menjadi #${newNum}`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#0F1713',
                color: '#F3F4F6'
            });
        }

        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (!csrfToken) {
            csrfToken = await getFreshCsrfToken();
        }

        const sendPayload = async (token) => {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    order: newNum,
                    _token: token
                })
            });
        };

        try {
            let res = await sendPayload(csrfToken);

            // Jika status 419 (CSRF token kadaluarsa), segarkan token & coba kirim ulang 1x otomatis
            if (res.status === 419) {
                const freshToken = await getFreshCsrfToken();
                if (freshToken) {
                    res = await sendPayload(freshToken);
                }
            }

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Gagal mengubah urutan');
            }

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: data.message || `Soal berhasil dipindahkan ke nomor #${newNum}! Urutan soal lainnya otomatis bergeser.`,
                    timer: 900,
                    showConfirmButton: false,
                    background: '#0F1713',
                    color: '#F3F4F6'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                window.location.reload();
            }
        } catch (err) {
            selectEl.value = currentNum;
            selectEl.disabled = false;
            if (badge) badge.classList.remove('opacity-50', 'pointer-events-none');
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan saat memindahkan nomor soal.',
                    background: '#0F1713',
                    color: '#F3F4F6'
                });
            } else {
                alert(err.message || 'Gagal mengubah urutan.');
            }
        }
    }

    // Refresh token CSRF secara berkala di latar belakang (tiap 10 menit)
    setInterval(() => {
        getFreshCsrfToken();
    }, 10 * 60 * 1000);
</script>
@endsection
