@extends('layouts.admin')
@section('title', 'Kuis Interaktif')
@section('page-title', 'Kuis Interaktif (LabQuiz)')
@section('page-description', 'Kelola kuis interaktif tipe Quizizz, bangun bank soal, dan pantau leaderboard nilai mahasiswa')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Kuis --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Kuis</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalQuizzes) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Kuis interaktif dibuat</div>
        </div>

        {{-- Total Pertanyaan --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-sky-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pertanyaan</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalQuestions) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Soal pilihan ganda & benar-salah</div>
        </div>

        {{-- Total Pengerjaan --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-teal-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Peserta</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalAttempts) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Sesi kuis mahasiswa tercatat</div>
        </div>

        {{-- Rata-rata Skor --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-5 relative overflow-hidden group hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rata-rata Nilai</p>
                    <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($avgScore, 1) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-gray-500">Rata-rata pengerjaan selesai</div>
        </div>
    </div>

    {{-- Filter & Actions Toolbar --}}
    <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                {{-- Search Box --}}
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari judul atau kode kuis..."
                           class="w-full pl-9 pr-3 py-2 bg-[#090D0B] border border-[#1A2820] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl text-sm text-gray-200 placeholder-gray-500">
                </div>

                {{-- Status Filter --}}
                <select name="status" onchange="this.form.submit()"
                        class="bg-[#090D0B] border border-[#1A2820] text-gray-300 text-sm rounded-xl px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                {{-- Soal Attachment Filter --}}
                @if(isset($soals) && $soals->count() > 0)
                <select name="soal_id" onchange="this.form.submit()"
                        class="bg-[#090D0B] border border-[#1A2820] text-gray-300 text-sm rounded-xl px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 max-w-xs truncate">
                    <option value="">Semua Dokumen Soal</option>
                    @foreach($soals as $s)
                        <option value="{{ $s->id }}" {{ request('soal_id') == $s->id ? 'selected' : '' }}>{{ Str::limit($s->title, 25) }}</option>
                    @endforeach
                </select>
                @endif

                @if(request()->anyFilled(['search', 'status', 'soal_id']))
                    <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-gray-400 hover:text-emerald-400 px-2 py-2 flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.quizzes.create') }}"
                   class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/20 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Kuis Baru
                </a>
            </div>
        </form>
    </div>

    {{-- Quizzes Cards Grid --}}
    @if($quizzes->isEmpty())
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">Belum Ada Kuis</h3>
            <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">Buat kuis interaktif tipe Quizizz untuk menguji pemahaman mahasiswa sebelum atau setelah praktikum.</p>
            <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold hover:opacity-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Kuis Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($quizzes as $quiz)
                <div class="bg-[#0F1713] border border-[#1A2820] hover:border-[#283d31] rounded-2xl flex flex-col justify-between overflow-hidden transition-all duration-200 hover:-translate-y-0.5">
                    
                    {{-- Card Header --}}
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            {{-- Active Status & Toggle --}}
                            <form method="POST" action="{{ route('admin.quizzes.toggle', $quiz) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Klik untuk mengubah status aktif"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-all {{ $quiz->is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:bg-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $quiz->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-gray-500' }}"></span>
                                    {{ $quiz->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>

                            {{-- Access Code Badge with Copy --}}
                            <button type="button"
                                    onclick="copyToClipboard('{{ $quiz->access_code }}', 'Kode Kuis berhasil disalin!')"
                                    title="Klik untuk salin kode kuis"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#142019] border border-[#1E3327] hover:border-emerald-500/50 text-emerald-300 font-mono text-xs font-bold transition-all">
                                <span>{{ $quiz->access_code }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>

                        {{-- Title & Soal Link --}}
                        <h4 class="text-base font-bold text-white mb-1.5 line-clamp-1 hover:text-emerald-400 transition-colors">
                            <a href="{{ route('admin.quizzes.questions', $quiz) }}">{{ $quiz->title }}</a>
                        </h4>
                        <p class="text-xs text-gray-400 line-clamp-2 mb-4">{{ $quiz->description ?: 'Tidak ada deskripsi kuis.' }}</p>

                        {{-- Linked Document Soal Badge --}}
                        @if($quiz->soal)
                            <div class="mb-4 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 text-[11px] truncate">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="truncate">Dokumen: {{ $quiz->soal->title }}</span>
                            </div>
                        @endif

                        {{-- Metadata Metrics --}}
                        <div class="grid grid-cols-3 gap-2 py-3 border-y border-[#1A2820] text-center">
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $quiz->questions_count }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Pertanyaan</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $quiz->attempts_count }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Peserta</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-amber-400 block">{{ $quiz->avg_score !== null ? number_format($quiz->avg_score, 1) : '-' }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Avg Nilai</span>
                            </div>
                        </div>

                        {{-- Settings Pills --}}
                        <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] text-gray-400">
                            <span class="px-2 py-0.5 rounded-md bg-[#131E17] border border-[#1A2820]">
                                ⏱ {{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' Menit' : ($quiz->time_per_question > 0 ? $quiz->time_per_question . ' dtk/soal' : 'Tanpa Waktu') }}
                            </span>
                            @if($quiz->shuffle_questions)
                                <span class="px-2 py-0.5 rounded-md bg-[#131E17] border border-[#1A2820]">🔀 Acak Soal</span>
                            @endif
                            @if($quiz->show_leaderboard)
                                <span class="px-2 py-0.5 rounded-md bg-[#131E17] border border-[#1A2820]">🏆 Leaderboard</span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer Actions --}}
                    <div class="p-3 bg-[#090D0B] border-t border-[#1A2820] flex items-center justify-between gap-1.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            {{-- Button CMS Pertanyaan --}}
                            <a href="{{ route('admin.quizzes.questions', $quiz) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-emerald-400 text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Bank Soal
                            </a>

                            {{-- Button Live Monitor --}}
                            <a href="{{ route('admin.quizzes.monitor', $quiz) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/25 text-emerald-400 text-xs font-semibold transition-colors"
                               title="Pantau Mahasiswa Ujian Realtime">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                </span>
                                <span>Monitor</span>
                            </a>

                            {{-- Button Hasil & Leaderboard --}}
                            <a href="{{ route('admin.quizzes.results', $quiz) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#142019] hover:bg-[#1A2B22] border border-[#1E3327] text-gray-300 hover:text-white text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Hasil
                            </a>
                        </div>

                        <div class="flex items-center gap-1">
                            {{-- Copy Direct URL --}}
                            <button type="button"
                                    onclick="copyToClipboard('{{ route('quiz.lobby', $quiz->unique_token) }}', 'Link kuis mahasiswa berhasil disalin!')"
                                    title="Salin Link Mahasiswa"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-400 hover:bg-emerald-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>

                            {{-- Edit Quiz Settings --}}
                            <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                               title="Edit Pengaturan Kuis"
                               class="p-1.5 rounded-lg text-gray-400 hover:text-amber-400 hover:bg-amber-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>

                            {{-- Delete Quiz --}}
                            <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus kuis \'{{ $quiz->title }}\' beserta seluruh pertanyaan dan riwayat pengerjaan?', 'Ya, Hapus Kuis', true)">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus Kuis"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $quizzes->links() }}
        </div>
    @endif

</div>

{{-- Clipboard helper --}}
<script>
    function copyToClipboard(text, successMessage) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(successMessage);
            });
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
            customClass: {
                popup: 'border border-emerald-500/30 rounded-xl'
            }
        });
    }
</script>
@endsection
