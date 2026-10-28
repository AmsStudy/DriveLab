@extends('layouts.admin')
@section('title', 'Bank Soal: ' . $quiz->title)
@section('page-title', 'CMS Bank Soal Kuis')
@section('page-description', 'Kelola daftar pertanyaan, pilihan opsi, kunci jawaban, dan pembahasan')

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

    {{-- Questions List --}}
    @if($quiz->questions->isEmpty())
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">Belum Ada Pertanyaan di Kuis Ini</h3>
            <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">Mulai bangun pertanyaan pilihan ganda atau benar-salah untuk kuis interaktif ini.</p>
            <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold hover:opacity-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pertanyaan Pertama
            </button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($quiz->questions as $index => $q)
                <div class="bg-[#0F1713] border border-[#1A2820] hover:border-[#283d31] rounded-2xl p-5 transition-all">
                    {{-- Header Pertanyaan --}}
                    <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b border-[#1A2820]">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold text-xs flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-[#131E17] border border-[#1A2820] text-gray-300 text-xs font-medium">
                                {{ $q->question_type === 'true_false' ? 'Benar / Salah' : 'Pilihan Ganda' }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold">
                                +{{ $q->points }} Poin
                            </span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    @click="openEditModal({{ $q->toJson() }})"
                                    title="Edit Pertanyaan"
                                    class="p-2 rounded-lg text-gray-400 hover:text-amber-400 hover:bg-amber-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            <form method="POST" action="{{ route('admin.quizzes.questions.destroy', [$quiz, $q]) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus pertanyaan No. {{ $index + 1 }} ini?', 'Ya, Hapus', true)">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus Pertanyaan"
                                        class="p-2 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Question Text --}}
                    <div class="text-sm font-semibold text-white mb-4 whitespace-pre-wrap leading-relaxed">
                        {{ $q->question_text }}
                    </div>

                    {{-- Options List --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-3">
                        @php
                            $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
                        @endphp
                        @foreach($q->options as $optIndex => $opt)
                            <div class="flex items-center gap-3 p-3 rounded-xl border transition-colors {{ $opt->is_correct ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-200' : 'bg-[#090D0B] border-[#1A2820] text-gray-300' }}">
                                <span class="w-6 h-6 rounded-lg text-xs font-bold flex items-center justify-center flex-shrink-0 {{ $opt->is_correct ? 'bg-emerald-500 text-white' : 'bg-gray-800 text-gray-400' }}">
                                    {{ $labels[$optIndex] ?? ($optIndex + 1) }}
                                </span>
                                <span class="text-xs font-medium flex-1">{{ $opt->option_text }}</span>
                                @if($opt->is_correct)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 bg-emerald-500/20 px-2 py-0.5 rounded-full flex-shrink-0">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        Kunci
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Explanation (if provided) --}}
                    @if($q->explanation)
                        <div class="mt-3 p-3 rounded-xl bg-teal-500/5 border border-teal-500/20 text-teal-300 text-xs flex items-start gap-2">
                            <span class="font-bold flex-shrink-0">💡 Pembahasan:</span>
                            <span class="text-gray-300">{{ $q->explanation }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
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
             class="bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-[#1A2820] flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white" x-text="isEdit ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru'"></h3>
                    <p class="text-xs text-gray-400" x-text="isEdit ? 'Perbarui konten pertanyaan dan pilihan jawaban' : 'Buat soal pilihan ganda atau benar-salah'"></p>
                </div>
                <button type="button" @click="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body Form --}}
            <form :action="formAction" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Teks Pertanyaan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Teks Pertanyaan <span class="text-red-400">*</span>
                    </label>
                    <textarea name="question_text" x-model="formData.question_text" rows="3" required
                              placeholder="Tuliskan butir soal di sini..."
                              class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100 placeholder-gray-500"></textarea>
                </div>

                {{-- Baris Pengaturan: Tipe & Poin --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tipe Pertanyaan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                            Tipe Pertanyaan <span class="text-red-400">*</span>
                        </label>
                        <select name="question_type" x-model="formData.question_type" @change="handleTypeChange()"
                                class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                            <option value="multiple_choice">Pilihan Ganda (A, B, C, D...)</option>
                            <option value="true_false">Benar / Salah (True / False)</option>
                        </select>
                    </div>

                    {{-- Bobot Poin --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                            Bobot Poin <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="points" x-model="formData.points" min="1" max="1000" required
                               class="w-full px-4 py-2.5 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-100">
                    </div>
                </div>

                {{-- Opsi Jawaban --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Pilihan Jawaban (Centang tombol radio untuk Kunci Jawaban Benar) <span class="text-red-400">*</span>
                        </label>
                        <button type="button" x-show="formData.question_type === 'multiple_choice' && formData.options.length < 6"
                                @click="addOption()"
                                class="text-xs text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Opsi
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="(opt, idx) in formData.options" :key="idx">
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-[#090D0B] border"
                                 :class="parseInt(formData.correct_index) === idx ? 'border-emerald-500/60 bg-emerald-500/5' : 'border-[#1A2820]'">
                                
                                {{-- Radio for Correct Option --}}
                                <label class="flex items-center gap-2 cursor-pointer px-2 py-1">
                                    <input type="radio" name="correct_index" :value="idx"
                                           :checked="parseInt(formData.correct_index) === idx"
                                           @change="formData.correct_index = idx"
                                           class="text-emerald-500 focus:ring-emerald-500 bg-gray-900 border-gray-700">
                                    <span class="text-xs font-bold font-mono"
                                          :class="parseInt(formData.correct_index) === idx ? 'text-emerald-400' : 'text-gray-400'"
                                          x-text="getOptionLabel(idx)"></span>
                                </label>

                                {{-- Text input for option --}}
                                <input type="text" :name="'options[' + idx + ']'" x-model="formData.options[idx]" required
                                       :placeholder="'Tuliskan opsi jawaban ' + getOptionLabel(idx) + '...'"
                                       class="flex-1 px-3 py-1.5 bg-transparent border-0 focus:ring-0 text-sm text-gray-100 placeholder-gray-600">

                                {{-- Remove Option (if MC and > 2) --}}
                                <button type="button" x-show="formData.question_type === 'multiple_choice' && formData.options.length > 2"
                                        @click="removeOption(idx)"
                                        class="p-1.5 text-gray-500 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Penjelasan / Pembahasan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">
                        Pembahasan / Penjelasan Kunci Jawaban (Opsional)
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
                explanation: ''
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
                    explanation: ''
                };
                this.showModal = true;
            },
            openEditModal(question) {
                this.isEdit = true;
                this.formAction = `{{ url('admin/quizzes/' . $quiz->id . '/questions') }}/${question.id}`;
                
                let optTexts = [];
                let correctIdx = 0;
                if (question.options && question.options.length > 0) {
                    question.options.forEach((opt, idx) => {
                        optTexts.push(opt.option_text);
                        if (opt.is_correct) {
                            correctIdx = idx;
                        }
                    });
                } else {
                    optTexts = ['', '', '', ''];
                }

                this.formData = {
                    question_text: question.question_text,
                    question_type: question.question_type,
                    points: question.points,
                    options: optTexts,
                    correct_index: correctIdx,
                    explanation: question.explanation || ''
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
                } else if (this.formData.options.length < 2) {
                    this.formData.options = ['', '', '', ''];
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
                    if (this.formData.correct_index >= this.formData.options.length) {
                        this.formData.correct_index = 0;
                    }
                }
            },
            getOptionLabel(index) {
                const labels = ['A', 'B', 'C', 'D', 'E', 'F'];
                return labels[index] || (index + 1);
            }
        };
    }
</script>
@endsection
