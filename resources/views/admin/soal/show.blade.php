@extends('layouts.admin')
@section('title', 'Detail Dokumen: ' . $soal->title)
@section('page-title', 'Detail Dokumen')
@section('page-description', 'Manajemen berkas, kode akses, dan pengumpulan tugas mahasiswa')

@section('content')

{{-- Back nav --}}
<div class="mb-5">
    <a href="{{ route('admin.soal.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
       style="color:#4B8C6A;"
       onmouseover="this.style.color='#10B981'"
       onmouseout="this.style.color='#4B8C6A'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Arsip Dokumen
    </a>
</div>

{{-- ─── TOP DOCUMENT SUMMARY BANNER ─── --}}
<div class="rounded-2xl p-6 mb-6 shadow-xl relative overflow-hidden" style="background:#0F1713;border:1px solid #1A2820;">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-gray-800">
        <div>
            <div class="flex items-center gap-2.5 mb-2 flex-wrap">
                @if($soal->category)
                    <span class="text-xs px-2.5 py-1 rounded-lg font-semibold" style="background:{{ $soal->category->color }}20;color:{{ $soal->category->color }};border:1px solid {{ $soal->category->color }}40;">
                        {{ $soal->category->name }}
                    </span>
                @endif

                @php $st = $soal->access_status; @endphp
                <span class="text-xs px-2.5 py-1 rounded-lg font-semibold
                    {{ $st === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $st === 'inactive' ? 'bg-gray-500/15 text-gray-400 border border-gray-500/30' : '' }}
                    {{ $st === 'expired' ? 'bg-red-500/15 text-red-400 border border-red-500/30' : '' }}
                    {{ $st === 'scheduled' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : '' }}">
                    ● {{ ['active'=>'Akses Aktif','inactive'=>'Nonaktif','expired'=>'Expired','scheduled'=>'Terjadwal'][$st] }}
                </span>

                @if($soal->is_downloadable)
                    <span class="text-xs px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">
                        Unduhan Dokumen Diizinkan
                    </span>
                @else
                    <span class="text-xs px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/20 font-medium">
                        Pratinjau Only (Unduhan Global Mati)
                    </span>
                @endif
            </div>

            <h1 class="text-xl md:text-2xl font-extrabold text-white tracking-tight">{{ $soal->title }}</h1>
            @if($soal->description)
                <p class="text-sm text-gray-400 mt-1 max-w-3xl leading-relaxed">{{ $soal->description }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('soal.access', $soal->unique_token) }}" target="_blank" rel="noopener noreferrer"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview Mahasiswa
            </a>
            <a href="{{ route('admin.soal.edit', $soal) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors flex items-center gap-1.5 shadow-lg shadow-emerald-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Dokumen
            </a>
        </div>
    </div>

    {{-- KPI Highlights --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-5">
        <div>
            <p class="text-xs text-gray-400">Total Kunjungan / Dilihat</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ number_format($soal->access_count) }}<span class="text-xs font-normal text-gray-400 ml-1">kali</span></p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Tugas Mahasiswa Terkumpul</p>
            <p class="text-xl font-bold text-teal-400 mt-0.5">{{ $submissions->count() }}<span class="text-xs font-normal text-gray-400 ml-1">NIM</span></p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Masa Berlaku Akses</p>
            <p class="text-xs font-semibold text-gray-200 mt-1">
                @if($soal->access_type === 'lifetime')
                    ∞ Selamanya (Lifetime)
                @else
                    {{ optional($soal->active_from)->format('d M Y') }} &mdash; {{ optional($soal->active_until)->format('d M Y') }}
                @endif
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Batas Waktu Pengumpulan</p>
            @if($soal->is_submission_enabled && $soal->deadline_at)
                @php $isExpired = now()->greaterThan($soal->deadline_at); @endphp
                <p class="text-xs font-semibold {{ $isExpired ? 'text-red-400' : 'text-emerald-400' }} mt-1">
                    {{ $soal->deadline_at->format('d M Y, H:i') }} ({{ $isExpired ? 'Berakhir' : $soal->deadline_at->diffForHumans() }})
                </p>
            @elseif(!$soal->is_submission_enabled)
                <p class="text-xs font-medium text-gray-500 mt-1">Pengumpulan Dinonaktifkan</p>
            @else
                <p class="text-xs font-medium text-emerald-400 mt-1">Tidak Ada Batas Waktu</p>
            @endif
        </div>
    </div>
</div>

{{-- ─── MAIN 2-COLUMN GRID ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT COLUMN: INFO & LAMPIRAN (1 Col) --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Card: Kode Akses & Kredensial --}}
        <div class="rounded-2xl p-5 shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Kode Akses & Keamanan
            </h3>

            <div class="space-y-3.5 text-xs">
                {{-- Kode Akses --}}
                <div class="p-3 rounded-xl bg-gray-900/80 border border-gray-800">
                    <div class="flex items-center justify-between text-gray-400 mb-1">
                        <span>Kode Unik Dokumen</span>
                        <button type="button" onclick="copyText('{{ $soal->kode_unik }}', 'Kode unik disalin!')" class="text-emerald-400 hover:text-emerald-300">Salin</button>
                    </div>
                    <p class="font-mono text-base font-bold text-white tracking-widest">{{ $soal->kode_unik }}</p>
                </div>

                {{-- Link Akses Cepat --}}
                <div class="p-3 rounded-xl bg-gray-900/80 border border-gray-800">
                    <div class="flex items-center justify-between text-gray-400 mb-1">
                        <span>Link Akses Mahasiswa</span>
                        <button type="button" onclick="copyText('{{ route('soal.access', $soal->unique_token) }}', 'Link akses disalin!')" class="text-emerald-400 hover:text-emerald-300">Salin</button>
                    </div>
                    <input type="text" readonly value="{{ route('soal.access', $soal->unique_token) }}" class="w-full bg-transparent text-[11px] text-emerald-400 focus:outline-none font-mono truncate">
                </div>

                {{-- Metadata Tambahan --}}
                <div class="pt-2 border-t border-gray-800 space-y-2 text-gray-400">
                    <div class="flex justify-between">
                        <span>Admin Pembuat</span>
                        <span class="text-gray-200 font-medium">{{ $soal->admin->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kategori</span>
                        <span class="text-gray-200 font-medium">{{ $soal->category->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dibuat Pada</span>
                        <span class="text-gray-200">{{ $soal->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                {{-- Toggle Status --}}
                <div class="pt-3 border-t border-gray-800 flex gap-2">
                    <form method="POST" action="{{ route('admin.soal.toggle', $soal) }}" class="w-full">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full py-2.5 rounded-xl text-xs font-semibold transition-colors flex items-center justify-center gap-2
                                {{ $soal->is_active ? 'bg-orange-500/15 hover:bg-orange-500/25 text-orange-400 border border-orange-500/30' : 'bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 border border-emerald-500/30' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            {{ $soal->is_active ? 'Nonaktifkan Akses Dokumen' : 'Aktifkan Akses Dokumen' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Card: Teks Soal / Konten Langsung (Jika Ada) --}}
        @if($soal->content_text)
        <div class="rounded-2xl p-5 shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Teks / Soal Langsung
                </h3>
                <button type="button" onclick="copyText({{ json_encode($soal->content_text) }}, 'Teks soal disalin!')" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium">Salin</button>
            </div>
            <div class="p-3.5 bg-gray-950/80 rounded-xl border border-gray-800 text-xs text-gray-300 font-mono max-h-56 overflow-y-auto whitespace-pre-wrap leading-relaxed select-text">
{{ $soal->content_text }}
            </div>
        </div>
        @endif

        {{-- Card: File Lampiran & Tautan (Maks. 15 Item) --}}
        <div class="rounded-2xl p-5 shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Lampiran & Tautan ({{ $soal->attachments->count() }})
                </h3>
                <span class="text-[11px] text-gray-500">Maks. 15 File/Link</span>
            </div>

            @if($soal->attachments->isEmpty())
                <div class="py-8 text-center text-gray-500 text-xs">Belum ada file atau tautan terlampir.</div>
            @else
                <div class="space-y-2.5">
                    @foreach($soal->attachments as $attachment)
                        @if($attachment->isLink())
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-900/60 border border-teal-500/20 hover:border-teal-500/40 transition-colors">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-[10px] font-bold text-teal-400 bg-teal-500/10 px-2 py-1 rounded-md flex-shrink-0">LINK</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-200 truncate">{{ $attachment->file_original_name ?: 'Tautan Eksternal' }}</p>
                                        <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-teal-400 hover:underline truncate block font-mono">
                                            {{ $attachment->link_url }}
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer" class="p-1.5 text-teal-400 hover:bg-teal-500/20 rounded-lg transition-colors flex-shrink-0 ml-2" title="Buka Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @else
                            {{-- File Lampiran --}}
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-900/60 border border-gray-800 hover:border-gray-700 transition-colors">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-md flex-shrink-0 uppercase">
                                        {{ $attachment->file_type ?: 'FILE' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-200 truncate">{{ $attachment->file_original_name }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-gray-500 font-mono">{{ $attachment->file_size_formatted }}</span>
                                            @if($attachment->is_downloadable)
                                                <span class="text-[9px] bg-emerald-500/15 text-emerald-400 px-1.5 py-0.2 rounded font-medium border border-emerald-500/20">Bisa Diunduh</span>
                                            @else
                                                <span class="text-[9px] bg-amber-500/15 text-amber-400 px-1.5 py-0.2 rounded font-medium border border-amber-500/20">Hanya Preview</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.soal.download', $attachment->id) }}" class="p-1.5 text-emerald-400 hover:bg-emerald-500/20 rounded-lg transition-colors flex-shrink-0 ml-2" title="Unduh File Ini (Admin)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- RIGHT COLUMN: RIWAYAT PENGUMPULAN TUGAS (2 Cols) --}}
    <div class="lg:col-span-2">
        <div class="rounded-2xl overflow-hidden shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            
            {{-- Header Pengumpulan Tugas --}}
            <div class="p-6 border-b border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Riwayat Pengumpulan Tugas
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Satu baris per mahasiswa (pengumpulan ulang otomatis menimpa versi sebelumnya)</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @if(!$submissions->isEmpty())
                        <a href="{{ route('admin.soal.download-submissions-zip', $soal) }}"
                           class="bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Semua (ZIP)
                        </a>
                    @endif
                    <a href="{{ route('admin.soal.export-submissions', $soal) }}"
                       class="bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Rekap (CSV)
                    </a>
                </div>
            </div>

            {{-- Filter & Search Bar --}}
            @if(!$submissions->isEmpty())
            <div class="px-6 py-3 bg-gray-950/40 border-b border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-72">
                    <input type="text" id="submissionSearchInput" onkeyup="filterSubmissions()"
                           placeholder="Cari NIM atau Nama mahasiswa..."
                           class="w-full bg-gray-900 border border-gray-800 rounded-xl pl-9 pr-3 py-1.5 text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-emerald-500 transition-colors">
                    <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                {{-- Type Filter Pills --}}
                <div class="flex items-center gap-1.5 self-start sm:self-auto text-xs" id="typeFilterPills">
                    <button type="button" onclick="setSubFilter('all')" class="sub-filter-pill active px-3 py-1 rounded-lg font-medium transition-colors bg-emerald-500/20 text-emerald-300 border border-emerald-500/30" data-type="all">Semua ({{ $submissions->count() }})</button>
                    <button type="button" onclick="setSubFilter('file')" class="sub-filter-pill px-3 py-1 rounded-lg font-medium transition-colors bg-gray-900 text-gray-400 hover:text-white border border-gray-800" data-type="file">File ({{ $submissions->where('submission_type', 'file')->count() }})</button>
                    <button type="button" onclick="setSubFilter('link')" class="sub-filter-pill px-3 py-1 rounded-lg font-medium transition-colors bg-gray-900 text-gray-400 hover:text-white border border-gray-800" data-type="link">Link ({{ $submissions->where('submission_type', 'link')->count() }})</button>
                    <button type="button" onclick="setSubFilter('text')" class="sub-filter-pill px-3 py-1 rounded-lg font-medium transition-colors bg-gray-900 text-gray-400 hover:text-white border border-gray-800" data-type="text">Teks ({{ $submissions->where('submission_type', 'text')->count() }})</button>
                </div>
            </div>
            @endif

            {{-- Table Content --}}
            @if($submissions->isEmpty())
                <div class="py-20 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-base font-semibold text-gray-300">Belum ada tugas yang dikumpulkan</p>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Ketika mahasiswa mengirimkan file, link, atau teks jawaban, data mereka akan langsung ditampilkan di tabel ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="submissionsTable">
                        <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider" style="background:rgba(255,255,255,.01);">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Mahasiswa</th>
                                <th class="px-4 py-3.5 text-left">Tipe</th>
                                <th class="px-4 py-3.5 text-left">Berkas / Jawaban</th>
                                <th class="px-4 py-3.5 text-left">Waktu Pengumpulan</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60" id="submissionsTableBody">
                            @foreach($submissions as $sub)
                            <tr class="hover:bg-gray-800/30 transition-colors submission-row" data-type="{{ $sub->submission_type ?: 'file' }}">
                                {{-- Mahasiswa Identity --}}
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                            {{ substr($sub->nama, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-200 truncate sub-nama">{{ $sub->nama }}</p>
                                            <p class="text-xs font-mono text-gray-500 sub-nim">{{ $sub->nim }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Submission Type Badge --}}
                                <td class="px-4 py-3.5 text-xs">
                                    @if($sub->isLink())
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-teal-500/15 text-teal-400 border border-teal-500/30">Link</span>
                                    @elseif($sub->isText())
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">Teks</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/15 text-blue-400 border border-blue-500/30">File</span>
                                    @endif
                                </td>

                                {{-- Jawaban Content --}}
                                <td class="px-4 py-3.5 text-xs">
                                    @if($sub->isLink())
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-300 hover:underline max-w-xs truncate inline-flex items-center gap-1 font-mono">
                                            {{ Str::limit($sub->link_url, 40) }}
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @elseif($sub->isText())
                                        <button type="button" onclick="showTextModal({{ json_encode($sub->nama) }}, {{ json_encode($sub->nim) }}, {{ json_encode($sub->text_content) }})" class="text-amber-400 hover:text-amber-300 font-mono text-xs max-w-xs truncate text-left block hover:underline">
                                            {{ Str::limit($sub->text_content, 45) }}
                                        </button>
                                    @else
                                        <span class="text-gray-300 font-medium">{{ $sub->file_original_name }}</span>
                                    @endif
                                </td>

                                {{-- Waktu --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                                    <p class="font-medium text-gray-300">{{ $sub->created_at->format('d M Y, H:i') }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $sub->created_at->diffForHumans() }}</p>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if($sub->isLink())
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer" class="p-2 text-teal-400 hover:bg-teal-500/10 rounded-xl transition-colors inline-block" title="Buka Link Tautan">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @elseif($sub->isText())
                                        <button type="button" onclick="showTextModal({{ json_encode($sub->nama) }}, {{ json_encode($sub->nim) }}, {{ json_encode($sub->text_content) }})" class="p-2 text-amber-400 hover:bg-amber-500/10 rounded-xl transition-colors inline-block" title="Baca Teks Lengkap">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.soal.download-submissions', $sub->id) }}" class="p-2 text-blue-400 hover:bg-blue-500/10 rounded-xl transition-colors inline-block" title="Unduh File Mahasiswa">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

</div>

{{-- ─── TEXT SUBMISSION PREVIEW MODAL ─── --}}
<div id="textSubmissionModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="border border-gray-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
            <div>
                <h3 class="text-white font-semibold text-base" id="modalStudentName">Jawaban Mahasiswa</h3>
                <p class="text-xs text-gray-400 font-mono mt-0.5" id="modalStudentNim"></p>
            </div>
            <button onclick="closeTextModal()" class="text-gray-400 hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <pre class="bg-gray-950 p-4 rounded-xl border border-gray-800 text-gray-200 font-mono text-xs whitespace-pre-wrap leading-relaxed select-text" id="modalTextContent"></pre>
        </div>
        <div class="px-6 py-3 border-t border-gray-800 flex justify-end gap-3 bg-gray-900/50">
            <button type="button" onclick="copyModalText()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-lg shadow-emerald-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Salin Teks
            </button>
            <button type="button" onclick="closeTextModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl text-xs font-semibold transition-colors">Tutup</button>
        </div>
    </div>
</div>

<script>
let currentModalText = '';
let currentFilterType = 'all';

function copyText(str, msg) {
    navigator.clipboard.writeText(str).then(() => {
        alert(msg || 'Berhasil disalin!');
    });
}

function showTextModal(name, nim, content) {
    document.getElementById('modalStudentName').textContent = name;
    document.getElementById('modalStudentNim').textContent = 'NIM: ' + nim;
    document.getElementById('modalTextContent').textContent = content;
    currentModalText = content;
    document.getElementById('textSubmissionModal').classList.remove('hidden');
}

function closeTextModal() {
    document.getElementById('textSubmissionModal').classList.add('hidden');
}

function copyModalText() {
    navigator.clipboard.writeText(currentModalText).then(() => {
        alert('Teks jawaban berhasil disalin!');
    });
}

function setSubFilter(type) {
    currentFilterType = type;
    document.querySelectorAll('.sub-filter-pill').forEach(btn => {
        const isActive = btn.getAttribute('data-type') === type;
        if (isActive) {
            btn.className = 'sub-filter-pill active px-3 py-1 rounded-lg font-medium transition-colors bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
        } else {
            btn.className = 'sub-filter-pill px-3 py-1 rounded-lg font-medium transition-colors bg-gray-900 text-gray-400 hover:text-white border border-gray-800';
        }
    });
    filterSubmissions();
}

function filterSubmissions() {
    const input = document.getElementById('submissionSearchInput');
    const query = input ? input.value.toLowerCase().trim() : '';
    const rows = document.querySelectorAll('.submission-row');

    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        const nama = row.querySelector('.sub-nama').textContent.toLowerCase();
        const nim = row.querySelector('.sub-nim').textContent.toLowerCase();

        const matchesQuery = query === '' || nama.includes(query) || nim.includes(query);
        const matchesType = currentFilterType === 'all' || type === currentFilterType;

        if (matchesQuery && matchesType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

@endsection
