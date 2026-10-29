@extends('layouts.admin')
@section('title', 'Bank Soal: ' . $quiz->title)
@section('page-title', 'CMS Bank Soal Kuis')
@section('page-description', 'Kelola daftar pertanyaan, gambar soal/jawaban, kunci jawaban jamak, dan pembahasan')

@section('content')
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

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.quizzes.results', $quiz) }}"
               class="px-4 py-2.5 rounded-xl bg-[#0F1713] border border-[#1A2820] hover:border-amber-500/40 text-gray-300 hover:text-amber-400 text-sm font-semibold transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Lihat Hasil Mahasiswa
            </a>

            <button type="button" @click="openCreateModal()"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pertanyaan Baru
            </button>
        </div>
    </div>

    {{-- Questions List: 2-Column Split (Ganjil di Kiri, Genap di Kanan) --}}
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
            $oddItems = [];
            $evenItems = [];
            foreach ($quiz->questions as $i => $q) {
                if ($i % 2 === 0) {
                    $oddItems[] = ['q' => $q, 'num' => $i + 1];
                } else {
                    $evenItems[] = ['q' => $q, 'num' => $i + 1];
                }
            }
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
            {{-- Kolom Kiri: Soal Nomor Ganjil (#1, #3, #5, ...) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 px-1 text-xs font-semibold text-emerald-400 uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Kolom Kiri &bull; Soal Ganjil</span>
                </div>
                @foreach($oddItems as $item)
                    @include('admin.quizzes.partials.question-card', ['q' => $item['q'], 'qNum' => $item['num']])
                @endforeach
            </div>

            {{-- Kolom Kanan: Soal Nomor Genap (#2, #4, #6, ...) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 px-1 text-xs font-semibold text-teal-400 uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                    <span>Kolom Kanan &bull; Soal Genap</span>
                </div>
                @foreach($evenItems as $item)
                    @include('admin.quizzes.partials.question-card', ['q' => $item['q'], 'qNum' => $item['num']])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Question Modal (Create / Edit) --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">

        <div @click.away="closeModal()"
             class="bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-3xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            
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
                                class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 font-medium">
                            <option value="multiple_choice">Pilihan Ganda (Tunggal - 1 Jawaban Benar)</option>
                            <option value="multi_select">Pilihan Jamak (Checkbox - Lebih dari 1 Jawaban Benar)</option>
                            <option value="true_false">Benar / Salah (True / False)</option>
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
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Teks Pertanyaan <span class="text-red-400">*</span>
                    </label>
                    <textarea name="question_text" x-model="formData.question_text" rows="3" required
                              placeholder="Tuliskan teks atau butir pertanyaan di sini..."
                              class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500 leading-relaxed"></textarea>
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
                           class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-500/20 file:text-teal-300 hover:file:bg-teal-500/30 file:cursor-pointer cursor-pointer">
                    
                    <input type="hidden" name="remove_question_image" :value="formData.remove_question_image ? 1 : 0">
                    <p class="text-[11px] text-gray-500 mt-1.5">Format: JPG, PNG, WebP. Gambar dikompresi otomatis tanpa pecah/blur agar cepat diakses mahasiswa.</p>
                </div>

                {{-- Opsi Jawaban --}}
                <div>
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
                                                   class="rounded text-purple-500 focus:ring-purple-500 bg-gray-900 border-gray-700 w-4 h-4">
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
                                                   class="text-emerald-500 focus:ring-emerald-500 bg-gray-900 border-gray-700 w-4 h-4">
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
                                           class="text-[11px] text-gray-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-medium file:bg-[#1A2820] file:text-gray-300 hover:file:bg-gray-800 file:cursor-pointer cursor-pointer">

                                    <input type="hidden" :name="'existing_option_images[' + idx + ']'" :value="formData.existing_option_images[idx] || ''">
                                    <input type="hidden" :name="'remove_option_images[' + idx + ']'" :value="formData.remove_option_images[idx] ? 1 : 0">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Penjelasan / Pembahasan --}}
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

</div>

<script>
    function questionManager() {
        return {
            showModal: false,
            isEdit: false,
            formAction: '{{ route('admin.quizzes.questions.store', $quiz) }}',
            formData: {
                question_text: '',
                question_type: 'multiple_choice',
                points: 10,
                options: ['', '', '', ''],
                correct_index: 0,
                correct_indices: [0],
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
                let existingOptImages = {};
                let optImagePreviews = {};

                if (question.options && question.options.length > 0) {
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

                const isMulti = question.question_type === 'multi_select' || correctIndices.length > 1;

                this.formData = {
                    question_text: question.question_text,
                    question_type: isMulti ? 'multi_select' : (question.question_type || 'multiple_choice'),
                    points: question.points,
                    options: optTexts,
                    correct_index: correctIdx,
                    correct_indices: correctIndices,
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
                } else if (this.formData.options.length < 2) {
                    this.formData.options = ['', '', '', ''];
                }
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
            }
        };
    }
</script>
@endsection
