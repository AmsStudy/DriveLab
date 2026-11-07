{{-- resources/views/admin/quizzes/partials/question-card.blade.php --}}
@php
    $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
    $isMulti = $q->isMultiSelect();
@endphp

<div class="bg-[#0F1713] border border-[#1A2820] hover:border-[#283d31] rounded-2xl p-5 transition-all shadow-md flex flex-col justify-between h-full">
    <div>
        {{-- Header Pertanyaan --}}
        <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b border-[#1A2820]">
            <div class="flex items-center gap-2 flex-wrap">
                @if(isset($totalQuestions) && $totalQuestions > 1)
                    {{-- Dropdown Ubah Nomor Urut Soal (Otomatis Tersimpan) --}}
                    <div class="relative inline-flex items-center group/order" title="Klik untuk mengubah nomor urut soal (otomatis tersimpan)">
                        <label class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-500/15 border border-emerald-500/30 hover:border-emerald-500 text-emerald-400 font-bold text-xs transition-colors shadow-sm cursor-pointer">
                            <span class="text-emerald-500/80 font-mono text-[11px]">No.</span>
                            <select onchange="changeQuestionOrder(this, '{{ route('admin.quizzes.questions.reorder', [$quiz, $q]) }}', {{ $qNum }})"
                                    class="no-cyber-select bg-transparent text-emerald-300 font-mono font-bold text-xs cursor-pointer focus:outline-none py-0 pl-0.5 pr-0 border-0 appearance-none text-center">
                                @for($i = 1; $i <= $totalQuestions; $i++)
                                    <option value="{{ $i }}" class="bg-[#0F1713] text-gray-200" {{ $i === $qNum ? 'selected' : '' }}>
                                        #{{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <svg class="w-3 h-3 text-emerald-400 pointer-events-none group-hover/order:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </label>
                    </div>
                @else
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold text-xs flex items-center justify-center">
                        #{{ $qNum }}
                    </span>
                @endif

                @if($isMulti)
                    <span class="px-2.5 py-1 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-300 text-xs font-semibold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pilihan Jamak
                    </span>
                @elseif($q->question_type === 'true_false')
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/10 border border-sky-500/20 text-sky-300 text-xs font-medium">
                        Benar / Salah
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-md bg-[#131E17] border border-[#1A2820] text-gray-300 text-xs font-medium">
                        Pilihan Tunggal
                    </span>
                @endif

                @if($q->image_path)
                    <span class="px-2 py-0.5 rounded-md bg-teal-500/10 border border-teal-500/20 text-teal-400 text-[11px] font-semibold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bergambar
                    </span>
                @endif

                <span class="px-2.5 py-1 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold">
                    +{{ $q->points }} Poin
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-1">
                <button type="button"
                        @click="openEditModal(@js($q))"
                        title="Edit Pertanyaan"
                        class="p-2 rounded-lg text-gray-400 hover:text-amber-400 hover:bg-amber-500/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>

                <form method="POST" action="{{ route('admin.quizzes.questions.destroy', [$quiz, $q]) }}" class="inline"
                      onsubmit="confirmAction(event, 'Hapus pertanyaan No. {{ $qNum }} ini beserta gambarnya?', 'Ya, Hapus', true)">
                    @csrf @method('DELETE')
                    <button type="submit" title="Hapus Pertanyaan"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Question Image (if uploaded) --}}
        @if($q->image_path)
            <div class="mb-3 flex flex-col items-center justify-center">
                <button type="button"
                        onmouseenter="showCyberHoverBubble(event, '{{ $q->image_url }}')"
                        onmousemove="updateCyberHoverBubblePos(event)"
                        onmouseleave="hideCyberHoverBubble()"
                        onclick="openCyberLightbox('{{ $q->image_url }}', 'Gambar Soal #{{ $qNum }}')"
                        class="inline-block group cursor-zoom-in focus:outline-none rounded-xl overflow-hidden border border-[#1A2820] bg-black/40 hover:border-emerald-500/40 p-1.5 transition-all shadow-sm">
                    <img src="{{ $q->image_url }}" alt="Gambar Soal #{{ $qNum }}"
                         onerror="this.style.display='none'; if(this.parentElement.nextElementSibling) this.parentElement.nextElementSibling.style.display='flex';"
                         class="max-h-52 w-auto object-contain rounded-lg group-hover:scale-[1.02] transition-transform">
                </button>
                <div class="hidden items-center gap-1.5 px-3 py-1.5 mt-1 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-[11px] font-mono">
                    <svg class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Gambar belum dapat dimuat (cek izin file storage atau symlink)</span>
                </div>
            </div>
        @endif

        {{-- Question Text --}}
        <div class="text-sm font-semibold text-white mb-4 leading-relaxed">
            {!! App\Helpers\RichTextHelper::format($q->question_text) !!}
        </div>

        {{-- Options List --}}
        <div class="space-y-2 mb-3">
            @foreach($q->options as $optIndex => $opt)
                <div class="flex items-start gap-3 p-2.5 rounded-xl border transition-colors {{ $opt->is_correct ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-950 dark:text-emerald-200 font-semibold' : 'bg-[#090D0B] border-[#1A2820] text-gray-800 dark:text-gray-300' }}">
                    <span class="w-5 h-5 rounded-md text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5 {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-400' }}">
                        {{ $labels[$optIndex] ?? ($optIndex + 1) }}
                    </span>

                    <div class="flex-1 min-w-0">
                        @if($opt->image_path)
                            <div class="mb-1.5 inline-block">
                                <button type="button"
                                        onmouseenter="showCyberHoverBubble(event, '{{ $opt->image_url }}')"
                                        onmousemove="updateCyberHoverBubblePos(event)"
                                        onmouseleave="hideCyberHoverBubble()"
                                        onclick="openCyberLightbox('{{ $opt->image_url }}', 'Gambar Opsi {{ $labels[$optIndex] ?? '' }}')"
                                        class="block group relative cursor-zoom-in focus:outline-none rounded-lg overflow-hidden border border-gray-300 dark:border-white/10 bg-white/70 dark:bg-black/50 hover:border-emerald-500/40 p-1 transition-all">
                                    <img src="{{ $opt->image_url }}" alt="Opsi {{ $labels[$optIndex] ?? '' }}"
                                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';"
                                         class="max-h-24 w-auto object-contain group-hover:scale-105 transition-transform" loading="lazy">
                                    <span class="hidden items-center gap-1 text-[11px] font-mono text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                                        ⚠ Gambar Opsi belum tersedia di storage
                                    </span>
                                </button>
                            </div>
                        @endif
                        @if(!empty($opt->option_text))
                            <div class="text-xs font-medium block leading-relaxed">{!! App\Helpers\RichTextHelper::format($opt->option_text) !!}</div>
                        @endif
                    </div>

                    @if($opt->is_correct)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-500/20 px-2 py-0.5 rounded-full flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Kunci
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Explanation (if provided) --}}
    @if($q->explanation)
        <div class="mt-3 p-3 rounded-xl bg-teal-500/5 border border-teal-500/20 text-teal-300 text-xs flex items-start gap-2">
            <span class="font-bold flex-shrink-0">💡 Pembahasan:</span>
            <div class="text-gray-300 leading-relaxed">{!! App\Helpers\RichTextHelper::format($q->explanation) !!}</div>
        </div>
    @endif
</div>
