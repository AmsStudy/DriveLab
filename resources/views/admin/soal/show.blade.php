@extends('layouts.admin')
@section('title', 'Detail Dokumen: ' . $soal->title)
@section('page-title', 'Detail Dokumen')
@section('page-description', 'Manajemen berkas, kode akses, dan pengumpulan tugas mahasiswa')

@section('header-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('soal.access', $soal->unique_token) }}" target="_blank" rel="noopener noreferrer"
       class="px-3 py-1.5 rounded-xl text-xs font-semibold text-gray-300 hover:text-white transition-all flex items-center gap-1.5 border"
       style="background:#0F1713;border-color:#1A2820;"
       onmouseover="this.style.borderColor='rgba(16,185,129,.4)'"
       onmouseout="this.style.borderColor='#1A2820'">
        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        Pratinjau Mahasiswa
    </a>
    <button type="button" onclick="openEditModal()"
       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-white transition-all flex items-center gap-1.5 shadow-md shadow-emerald-500/20 cursor-pointer"
       style="background:linear-gradient(135deg,#10B981,#0D9488);">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Dokumen
    </button>
</div>
@endsection

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
<div class="rounded-2xl p-6 mb-6 shadow-xl relative overflow-hidden"
     style="background:radial-gradient(circle at top right, rgba(16,185,129,0.06), transparent 55%), #0F1713; border:1px solid #1A2820;">
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 pb-6" style="border-bottom:1px solid #1A2820;">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($soal->category)
                    <span class="text-xs px-2.5 py-1 rounded-lg font-semibold" style="background:{{ $soal->category->color }}20;color:{{ $soal->category->color }};border:1px solid {{ $soal->category->color }}40;">
                        {{ $soal->category->name }}
                    </span>
                @endif

                @php $st = $soal->access_status; @endphp
                <span class="text-xs px-2.5 py-1 rounded-lg font-semibold inline-flex items-center gap-1.5
                    {{ $st === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $st === 'inactive' ? 'bg-gray-500/15 text-gray-400 border border-gray-500/30' : '' }}
                    {{ $st === 'expired' ? 'bg-red-500/15 text-red-400 border border-red-500/30' : '' }}
                    {{ $st === 'scheduled' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : '' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $st === 'active' ? 'bg-emerald-400 animate-pulse' : ($st === 'scheduled' ? 'bg-amber-400' : ($st === 'expired' ? 'bg-red-400' : 'bg-gray-400')) }}"></span>
                    {{ ['active'=>'Akses Aktif','inactive'=>'Nonaktif','expired'=>'Expired','scheduled'=>'Terjadwal'][$st] }}
                </span>

                @if($soal->is_downloadable)
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduhan Diizinkan
                    </span>
                @else
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 font-medium inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Hanya Pratinjau (Unduhan Mati)
                    </span>
                @endif

                @if($soal->is_submission_enabled)
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/20 font-medium">
                        Pengumpulan Tugas Aktif
                    </span>
                @else
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-gray-500/10 text-gray-400 border border-gray-500/20 font-medium">
                        Pengumpulan Nonaktif
                    </span>
                @endif
            </div>

            <h1 class="text-xl md:text-2xl font-extrabold text-white tracking-tight break-words">{{ $soal->title }}</h1>
            @if($soal->description)
                <p class="text-sm text-gray-400 mt-2 max-w-3xl leading-relaxed">{{ $soal->description }}</p>
            @endif
        </div>
    </div>

    {{-- KPI Highlights (Clean Metric Cards) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 pt-5">
        {{-- Stat 1: Total Kunjungan --}}
        <div class="p-3.5 rounded-xl border flex items-start gap-3" style="background:#090D0B;border-color:#1A2820;">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,.12);color:#10B981;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-medium text-gray-400">Total Kunjungan</p>
                <p class="text-lg font-bold text-white leading-tight mt-0.5">
                    {{ number_format($soal->access_count) }}
                    <span class="text-xs font-normal text-gray-500">kali</span>
                </p>
            </div>
        </div>

        {{-- Stat 2: Tugas Mahasiswa --}}
        <div class="p-3.5 rounded-xl border flex items-start gap-3" style="background:#090D0B;border-color:#1A2820;">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(13,148,136,.15);color:#2DD4BF;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-medium text-gray-400">Tugas Terkumpul</p>
                <p class="text-lg font-bold text-teal-300 leading-tight mt-0.5">
                    {{ $submissions->count() }}
                    <span class="text-xs font-normal text-gray-500">NIM</span>
                </p>
            </div>
        </div>

        {{-- Stat 3: Masa Berlaku --}}
        <div class="p-3.5 rounded-xl border flex items-start gap-3" style="background:#090D0B;border-color:#1A2820;">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(52,211,153,.12);color:#34D399;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-medium text-gray-400">Masa Berlaku Akses</p>
                <p class="text-xs font-semibold text-gray-200 leading-tight mt-1 truncate">
                    @if($soal->access_type === 'lifetime')
                        ∞ Selamanya (Lifetime)
                    @else
                        {{ optional($soal->active_from)->format('d M Y') }} &mdash; {{ optional($soal->active_until)->format('d M Y') }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Stat 4: Batas Pengumpulan --}}
        <div class="p-3.5 rounded-xl border flex items-start gap-3" style="background:#090D0B;border-color:#1A2820;">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.12);color:#FBBF24;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-medium text-gray-400">Batas Pengumpulan</p>
                @if($soal->is_submission_enabled && $soal->deadline_at)
                    @php $isExpired = now()->greaterThan($soal->deadline_at); @endphp
                    <p class="text-xs font-semibold {{ $isExpired ? 'text-red-400' : 'text-emerald-400' }} leading-tight mt-1 truncate">
                        {{ $soal->deadline_at->format('d M, H:i') }}
                        <span class="text-[10px] text-gray-500 font-normal">({{ $isExpired ? 'Selesai' : $soal->deadline_at->diffForHumans() }})</span>
                    </p>
                @elseif(!$soal->is_submission_enabled)
                    <p class="text-xs font-medium text-gray-500 leading-tight mt-1">Nonaktif</p>
                @else
                    <p class="text-xs font-medium text-emerald-400 leading-tight mt-1">Tanpa Batas Waktu</p>
                @endif
            </div>
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
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(16,185,129,.12);color:#10B981;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                Kode Akses & Keamanan
            </h3>

            <div class="space-y-3.5 text-xs">
                {{-- Kode Akses --}}
                <div class="p-3.5 rounded-xl border" style="background:#090D0B;border-color:#1A2820;">
                    <div class="flex items-center justify-between text-gray-400 mb-1.5">
                        <span class="text-[11px] font-medium text-gray-400">Kode Unik Dokumen</span>
                        <button type="button" onclick="copyText('{{ $soal->kode_unik }}', 'Kode unik berhasil disalin!')"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Salin
                        </button>
                    </div>
                    <p class="font-mono text-base font-bold text-emerald-400 tracking-wider">{{ $soal->kode_unik }}</p>
                </div>

                {{-- Password Akses Dokumen --}}
                <div class="p-3.5 rounded-xl border" style="background:#090D0B;border-color:#1A2820;">
                    <div class="flex items-center justify-between text-gray-400 mb-1.5">
                        <span class="text-[11px] font-medium text-gray-400">Password Akses Dokumen</span>
                        @if(!empty($soal->plain_password))
                            <button type="button" onclick="copyText('{{ $soal->plain_password }}', 'Password akses berhasil disalin!')"
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Salin
                            </button>
                        @endif
                    </div>
                    @if(!empty($soal->plain_password))
                        <div class="flex items-center justify-between">
                            <span id="passwordTextVal" class="font-mono text-base font-bold text-emerald-400 tracking-wider">••••••••</span>
                            <button type="button" onclick="togglePasswordVisibility()" class="p-1 text-gray-400 hover:text-gray-200 transition-colors cursor-pointer" title="Lihat / Sembunyikan Password">
                                <svg id="passwordEyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-amber-400 font-mono">Tersimpan terenkripsi</span>
                            <button type="button" onclick="openEditModal()" class="text-[11px] text-emerald-400 hover:underline cursor-pointer">
                                Atur Password
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Link Akses Cepat --}}
                <div class="p-3.5 rounded-xl border" style="background:#090D0B;border-color:#1A2820;">
                    <div class="flex items-center justify-between text-gray-400 mb-1.5">
                        <span class="text-[11px] font-medium text-gray-400">Tautan Akses Mahasiswa</span>
                        <button type="button" onclick="copyText('{{ route('soal.access', $soal->unique_token) }}', 'Tautan akses berhasil disalin!')"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Salin
                        </button>
                    </div>
                    <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg border text-gray-300 font-mono text-[11px]" style="background:#0F1713;border-color:#1A2820;">
                        <span class="truncate flex-1 select-all text-gray-300">{{ route('soal.access', $soal->unique_token) }}</span>
                    </div>
                </div>

                {{-- Metadata Tambahan --}}
                <div class="pt-3 space-y-2.5 text-gray-400" style="border-top:1px solid #1A2820;">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Admin Pembuat</span>
                        <span class="text-gray-200 font-medium">{{ $soal->admin->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Kategori</span>
                        <span class="text-gray-200 font-medium">{{ $soal->category->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Dibuat Pada</span>
                        <span class="text-gray-300 font-mono text-[11px]">{{ $soal->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Terakhir Update</span>
                        <span class="text-gray-300 font-mono text-[11px]">{{ $soal->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Toggle Status --}}
                <div class="pt-3" style="border-top:1px solid #1A2820;">
                    <form method="POST" action="{{ route('admin.soal.toggle', $soal) }}" class="w-full">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 border
                                {{ $soal->is_active
                                    ? 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border-amber-500/30'
                                    : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border-emerald-500/30' }}">
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
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(13,148,136,.15);color:#2DD4BF;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    Teks / Soal Langsung
                </h3>
                <button type="button"
                        data-copy="{{ $soal->content_text }}"
                        onclick="copyText(this.dataset.copy, 'Teks soal berhasil disalin!')"
                        class="inline-flex items-center gap-1 text-xs text-emerald-400 hover:text-emerald-300 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Salin
                </button>
            </div>
            <div class="p-3.5 rounded-xl border text-xs text-gray-300 font-mono max-h-56 overflow-y-auto whitespace-pre-wrap leading-relaxed select-text"
                 style="background:#090D0B;border-color:#1A2820;">
{{ $soal->content_text }}
            </div>
        </div>
        @endif

        {{-- Card: File Lampiran & Tautan (Maks. 15 Item) --}}
        <div class="rounded-2xl p-5 shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(16,185,129,.12);color:#10B981;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    </div>
                    Lampiran & Tautan
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:rgba(16,185,129,.12);color:#34D399;">
                        {{ $soal->attachments->count() }}
                    </span>
                </h3>
                <span class="text-[11px] text-gray-500 font-mono">Maks. 15 Item</span>
            </div>

            @if($soal->attachments->isEmpty())
                <div class="py-8 text-center text-gray-500 text-xs">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Belum ada file atau tautan terlampir.
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($soal->attachments as $attachment)
                        @if($attachment->isLink())
                            <div class="flex items-center justify-between p-3 rounded-xl border transition-colors group"
                                 style="background:#090D0B;border-color:#1A2820;"
                                 onmouseover="this.style.borderColor='rgba(13,148,136,.4)'"
                                 onmouseout="this.style.borderColor='#1A2820'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-[10px] font-bold text-teal-400 bg-teal-500/10 px-2 py-1 rounded-md flex-shrink-0 border border-teal-500/20 font-mono">LINK</span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-200 truncate">{{ $attachment->file_original_name ?: 'Tautan Eksternal' }}</p>
                                        <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-teal-400 hover:underline truncate block font-mono">
                                            {{ $attachment->link_url }}
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer"
                                   class="p-1.5 text-teal-400 hover:bg-teal-500/20 rounded-lg transition-colors flex-shrink-0 ml-2" title="Buka Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @else
                            {{-- File Lampiran --}}
                            <div class="flex items-center justify-between p-3 rounded-xl border transition-colors group"
                                 style="background:#090D0B;border-color:#1A2820;"
                                 onmouseover="this.style.borderColor='rgba(16,185,129,.4)'"
                                 onmouseout="this.style.borderColor='#1A2820'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-md flex-shrink-0 uppercase font-mono border border-emerald-500/20">
                                        {{ $attachment->file_type ?: 'FILE' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-200 truncate">{{ $attachment->file_original_name }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-gray-500 font-mono">{{ $attachment->file_size_formatted }}</span>
                                            @if($attachment->is_downloadable)
                                                <span class="text-[9px] bg-emerald-500/15 text-emerald-400 px-1.5 py-0.5 rounded font-medium border border-emerald-500/20">Bisa Diunduh</span>
                                            @else
                                                <span class="text-[9px] bg-amber-500/15 text-amber-400 px-1.5 py-0.5 rounded font-medium border border-amber-500/20">Hanya Preview</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.soal.download', $attachment->id) }}"
                                   class="p-1.5 text-emerald-400 hover:bg-emerald-500/20 rounded-lg transition-colors flex-shrink-0 ml-2" title="Unduh File Ini (Admin)">
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
            <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4" style="border-bottom:1px solid #1A2820;">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(13,148,136,.15);color:#2DD4BF;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        Riwayat Pengumpulan Tugas
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">Satu baris per mahasiswa (pengumpulan ulang otomatis menimpa versi sebelumnya)</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @if(!$submissions->isEmpty())
                        @if($soal->hasClasses())
                            {{-- Dropdown Unduh ZIP per Kelas --}}
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false" type="button"
                                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-300 transition-colors flex items-center gap-1.5 border cursor-pointer"
                                        style="background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);"
                                        onmouseover="this.style.background='rgba(16,185,129,.2)'"
                                        onmouseout="this.style.background='rgba(16,185,129,.12)'">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh ZIP</span>
                                    <svg class="w-3.5 h-3.5 ml-0.5 text-emerald-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                                     class="absolute right-0 mt-2 w-60 rounded-2xl shadow-2xl py-2 z-30 border backdrop-blur-md"
                                     style="background:rgba(15,23,19,0.96);border-color:rgba(16,185,129,0.25);box-shadow:0 20px 30px -10px rgba(0,0,0,0.8), 0 0 15px rgba(16,185,129,0.08);">
                                    <div class="px-4 py-1.5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">Unduh Berdasarkan Kelas</div>
                                    <a href="{{ route('admin.soal.download-submissions-zip', $soal) }}"
                                       class="flex items-center justify-between px-4 py-2 text-xs text-emerald-400 hover:bg-emerald-500/10 font-semibold transition-colors">
                                        <span>Unduh Semua Kelas</span>
                                        <span class="text-[10px] text-gray-500 font-mono">{{ $submissions->count() }} file</span>
                                    </a>
                                    <div class="border-t my-1.5" style="border-color:#1A2820;"></div>
                                    @foreach($soal->getClassesList() as $cls)
                                        @php $cCount = $submissions->where('kelas', $cls)->count(); @endphp
                                        <a href="{{ route('admin.soal.download-submissions-zip', [$soal, 'kelas' => $cls]) }}"
                                           class="flex items-center justify-between px-4 py-2 text-xs text-gray-300 hover:text-emerald-300 hover:bg-emerald-500/10 transition-colors">
                                            <span>Kelas {{ $cls }}</span>
                                            <span class="text-[10px] font-mono {{ $cCount > 0 ? 'text-emerald-400' : 'text-gray-600' }}">{{ $cCount }} tugas</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('admin.soal.download-submissions-zip', $soal) }}"
                               class="px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-300 transition-colors flex items-center gap-1.5 border"
                               style="background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);"
                               onmouseover="this.style.background='rgba(16,185,129,.2)'"
                               onmouseout="this.style.background='rgba(16,185,129,.12)'">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh Semua (ZIP)
                            </a>
                        @endif
                    @endif

                    @if($soal->hasClasses() && !$submissions->isEmpty())
                        {{-- Dropdown Export CSV per Kelas --}}
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" type="button"
                                    class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-200 transition-colors flex items-center gap-1.5 border cursor-pointer"
                                    style="background:#090D0B;border-color:#1A2820;"
                                    onmouseover="this.style.borderColor='rgba(16,185,129,.4)';this.style.color='#10B981'"
                                    onmouseout="this.style.borderColor='#1A2820';this.style.color='#E5E7EB'">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Export CSV</span>
                                <svg class="w-3.5 h-3.5 ml-0.5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                                 class="absolute right-0 mt-2 w-60 rounded-2xl shadow-2xl py-2 z-30 border backdrop-blur-md"
                                 style="background:rgba(15,23,19,0.96);border-color:rgba(16,185,129,0.25);box-shadow:0 20px 30px -10px rgba(0,0,0,0.8), 0 0 15px rgba(16,185,129,0.08);">
                                <div class="px-4 py-1.5 text-[10px] uppercase font-bold text-gray-500 tracking-wider">Rekap Tugas CSV</div>
                                <a href="{{ route('admin.soal.export-submissions', $soal) }}"
                                   class="flex items-center justify-between px-4 py-2 text-xs text-emerald-400 hover:bg-emerald-500/10 font-semibold transition-colors">
                                    <span>Semua Kelas</span>
                                    <span class="text-[10px] text-gray-500 font-mono">{{ $submissions->count() }}</span>
                                </a>
                                <div class="border-t my-1.5" style="border-color:#1A2820;"></div>
                                @foreach($soal->getClassesList() as $cls)
                                    @php $cCount = $submissions->where('kelas', $cls)->count(); @endphp
                                    <a href="{{ route('admin.soal.export-submissions', [$soal, 'kelas' => $cls]) }}"
                                       class="flex items-center justify-between px-4 py-2 text-xs text-gray-300 hover:text-emerald-300 hover:bg-emerald-500/10 transition-colors">
                                        <span>Kelas {{ $cls }}</span>
                                        <span class="text-[10px] font-mono {{ $cCount > 0 ? 'text-emerald-400' : 'text-gray-600' }}">{{ $cCount }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route('admin.soal.export-submissions', $soal) }}"
                           class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-200 transition-colors flex items-center gap-1.5 border"
                           style="background:#090D0B;border-color:#1A2820;"
                           onmouseover="this.style.borderColor='rgba(16,185,129,.4)';this.style.color='#10B981'"
                           onmouseout="this.style.borderColor='#1A2820';this.style.color='#E5E7EB'">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Rekap (CSV)
                        </a>
                    @endif
                </div>
            </div>

            {{-- Filter & Search Bar --}}
            @if(!$submissions->isEmpty())
            <div class="px-5 md:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-3"
                 style="background:rgba(9,13,11,.6);border-bottom:1px solid #1A2820;">
                <div class="flex items-center gap-2.5 w-full sm:w-auto flex-1">
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="submissionSearchInput" onkeyup="filterSubmissions()"
                               placeholder="Cari NIM atau Nama..."
                               class="w-full border rounded-xl pl-9 pr-3.5 py-2 text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-emerald-500 transition-colors"
                               style="background:#090D0B;border-color:#1A2820;">
                        <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    @if($soal->hasClasses())
                        <div class="relative flex-shrink-0">
                            <select id="classFilterSelect" onchange="filterSubmissions()"
                                    class="cyber-select text-xs pl-3.5 pr-10 py-2 cursor-pointer">
                                <option value="all">Semua Kelas ({{ $submissions->count() }})</option>
                                @foreach($soal->getClassesList() as $cls)
                                    <option value="{{ $cls }}">Kelas {{ $cls }} ({{ $submissions->where('kelas', $cls)->count() }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                {{-- Type Filter Pills --}}
                <div class="flex items-center gap-1.5 self-start sm:self-auto text-xs flex-wrap" id="typeFilterPills">
                    <button type="button" onclick="setSubFilter('all')"
                            class="sub-filter-pill active px-3 py-1.5 rounded-lg font-medium transition-colors"
                            style="background:rgba(16,185,129,.15);color:#34D399;border:1px solid rgba(16,185,129,.3);"
                            data-type="all">Semua ({{ $submissions->count() }})</button>
                    <button type="button" onclick="setSubFilter('file')"
                            class="sub-filter-pill px-3 py-1.5 rounded-lg font-medium transition-colors text-gray-400 hover:text-white border"
                            style="background:#090D0B;border-color:#1A2820;"
                            data-type="file">File ({{ $submissions->where('submission_type', 'file')->count() }})</button>
                    <button type="button" onclick="setSubFilter('link')"
                            class="sub-filter-pill px-3 py-1.5 rounded-lg font-medium transition-colors text-gray-400 hover:text-white border"
                            style="background:#090D0B;border-color:#1A2820;"
                            data-type="link">Link ({{ $submissions->where('submission_type', 'link')->count() }})</button>
                    <button type="button" onclick="setSubFilter('text')"
                            class="sub-filter-pill px-3 py-1.5 rounded-lg font-medium transition-colors text-gray-400 hover:text-white border"
                            style="background:#090D0B;border-color:#1A2820;"
                            data-type="text">Teks ({{ $submissions->where('submission_type', 'text')->count() }})</button>
                </div>
            </div>
            @endif

            {{-- Table Content --}}
            @if($submissions->isEmpty())
                <div class="py-20 text-center text-gray-500">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center" style="background:rgba(16,185,129,.08);color:#10B981;">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-base font-semibold text-gray-200">Belum ada tugas yang dikumpulkan</p>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Ketika mahasiswa mengirimkan file, link, atau teks jawaban, data mereka akan otomatis ditampilkan di sini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="submissionsTable">
                        <thead class="text-xs text-gray-400 uppercase tracking-wider" style="background:#090D0B;border-bottom:1px solid #1A2820;">
                            <tr>
                                <th class="px-6 py-3.5 text-left font-semibold">Mahasiswa</th>
                                <th class="px-4 py-3.5 text-left font-semibold">Kelas</th>
                                <th class="px-4 py-3.5 text-left font-semibold">Tipe</th>
                                <th class="px-4 py-3.5 text-left font-semibold">Berkas / Jawaban</th>
                                <th class="px-4 py-3.5 text-left font-semibold">Waktu Pengumpulan</th>
                                <th class="px-4 py-3.5 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:#1A2820;" id="submissionsTableBody">
                            @foreach($submissions as $sub)
                            <tr class="hover:bg-emerald-950/10 transition-colors submission-row" style="border-color:#1A2820;" data-type="{{ $sub->submission_type ?: 'file' }}" data-kelas="{{ $sub->kelas ?? '' }}">
                                {{-- Mahasiswa Identity --}}
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs uppercase flex-shrink-0"
                                             style="background:rgba(16,185,129,.12);color:#34D399;border:1px solid rgba(16,185,129,.25);">
                                            {{ substr($sub->nama, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-200 truncate sub-nama">{{ $sub->nama }}</p>
                                            <p class="text-xs font-mono text-gray-500 sub-nim">{{ $sub->nim }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kelas Badge --}}
                                <td class="px-4 py-3.5 text-xs">
                                    @if($sub->kelas)
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold inline-block"
                                              style="background:rgba(16,185,129,.12);color:#34D399;border:1px solid rgba(16,185,129,.25);">
                                            Kelas {{ $sub->kelas }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 font-mono text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Submission Type Badge --}}
                                <td class="px-4 py-3.5 text-xs">
                                    @if($sub->isLink())
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-teal-500/15 text-teal-400 border border-teal-500/30">Link</span>
                                    @elseif($sub->isText())
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">Teks</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">File</span>
                                    @endif
                                </td>

                                {{-- Jawaban Content --}}
                                <td class="px-4 py-3.5 text-xs">
                                    @if($sub->isLink())
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer"
                                           class="text-teal-400 hover:text-teal-300 hover:underline max-w-xs truncate inline-flex items-center gap-1 font-mono">
                                            {{ Str::limit($sub->link_url, 40) }}
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @elseif($sub->isText())
                                        <button type="button"
                                                data-nama="{{ $sub->nama }}"
                                                data-nim="{{ $sub->nim }}"
                                                data-content="{{ $sub->text_content }}"
                                                onclick="showTextModal(this.dataset.nama, this.dataset.nim, this.dataset.content)"
                                                class="text-amber-400 hover:text-amber-300 font-mono text-xs max-w-xs truncate text-left block hover:underline">
                                            {{ Str::limit($sub->text_content, 45) }}
                                        </button>
                                    @else
                                        <span class="text-gray-300 font-medium">{{ $sub->file_original_name }}</span>
                                    @endif
                                </td>

                                {{-- Waktu --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                                    <p class="font-medium text-gray-200">{{ $sub->created_at->format('d M Y, H:i') }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5 font-mono">{{ $sub->created_at->diffForHumans() }}</p>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if($sub->isLink())
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer"
                                           class="p-2 text-teal-400 hover:bg-teal-500/10 rounded-xl transition-colors inline-block" title="Buka Link Tautan">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @elseif($sub->isText())
                                        <button type="button"
                                                data-nama="{{ $sub->nama }}"
                                                data-nim="{{ $sub->nim }}"
                                                data-content="{{ $sub->text_content }}"
                                                onclick="showTextModal(this.dataset.nama, this.dataset.nim, this.dataset.content)"
                                                class="p-2 text-amber-400 hover:bg-amber-500/10 rounded-xl transition-colors inline-block" title="Baca Teks Lengkap">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.soal.download-submissions', $sub->id) }}"
                                           class="p-2 text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-colors inline-block" title="Unduh File Mahasiswa">
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
<div id="textSubmissionModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm p-4 items-center justify-center">
    <div class="rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh] border"
         style="background:#0F1713;border-color:#1A2820;">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid #1A2820;">
            <div>
                <h3 class="text-white font-semibold text-base" id="modalStudentName">Jawaban Mahasiswa</h3>
                <p class="text-xs text-gray-400 font-mono mt-0.5" id="modalStudentNim"></p>
            </div>
            <button onclick="closeTextModal()" class="text-gray-400 hover:text-white p-1 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <pre class="p-4 rounded-xl border text-gray-200 font-mono text-xs whitespace-pre-wrap leading-relaxed select-text"
                 style="background:#090D0B;border-color:#1A2820;" id="modalTextContent"></pre>
        </div>
        <div class="px-6 py-3 flex justify-end gap-3" style="border-top:1px solid #1A2820;background:#090D0B;">
            <button type="button" onclick="copyModalText()"
                    class="px-4 py-2 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all shadow-md shadow-emerald-500/20"
                    style="background:linear-gradient(135deg,#10B981,#0D9488);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Salin Teks
            </button>
            <button type="button" onclick="closeTextModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 transition-colors border"
                    style="background:#1A2820;border-color:#25382D;"
                    onmouseover="this.style.background='#25382D'"
                    onmouseout="this.style.background='#1A2820'">Tutup</button>
        </div>
    </div>
</div>

{{-- ─── MODAL EDIT DOKUMEN ─── --}}
<div id="editModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm p-4 overflow-y-auto items-center justify-center"
     onclick="if(event.target === this) closeEditModal()">
    <div class="rounded-2xl w-full max-w-4xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh] border my-auto"
         style="background:#0F1713;border-color:#1A2820;">

        {{-- Modal Header --}}
        <div class="px-6 py-4 flex items-center justify-between border-b flex-shrink-0" style="border-color:#1A2820;background:#0F1713;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs"
                     style="background:rgba(16,185,129,.15);color:#34D399;border:1px solid rgba(16,185,129,.3);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        Edit Dokumen Soal
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui informasi, berkas lampiran, dan form pengumpulan tugas</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg transition-colors cursor-pointer" title="Tutup Modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body / Form --}}
        <form method="POST" action="{{ route('admin.soal.update', $soal) }}" enctype="multipart/form-data" id="modal-edit-soal-form" class="overflow-y-auto flex-1 p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. INFORMASI UTAMA --}}
            <div>
                <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Informasi Dokumen
                </h4>

                <div class="space-y-4">
                    <div>
                        <label for="modal_title" class="block text-xs font-medium text-gray-300 mb-1.5">Judul Dokumen <span class="text-red-400">*</span></label>
                        <input type="text" id="modal_title" name="title" value="{{ old('title', $soal->title) }}" required
                               class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                        @error('title') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_kode_unik" class="block text-xs font-medium text-gray-300 mb-1.5">Kode Unik Akses <span class="text-red-400">*</span></label>
                            <input type="text" id="modal_kode_unik" name="kode_unik" value="{{ old('kode_unik', $soal->kode_unik) }}" required
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                            <p class="mt-1 text-[11px] text-gray-500">Boleh diubah, asal unik.</p>
                            @error('kode_unik') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="modal_category_id" class="block text-xs font-medium text-gray-300 mb-1.5">Kategori</label>
                            <select id="modal_category_id" name="category_id"
                                    class="cyber-select w-full pl-3.5 pr-10 py-2.5 text-xs cursor-pointer">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $soal->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_password" class="block text-xs font-medium text-gray-300 mb-1.5">
                                Password Baru <span class="text-gray-500 text-[11px]">(kosongkan jika tidak diubah)</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="modal_password" name="password" minlength="4"
                                       placeholder="Biarkan kosong jika tetap menggunakan password lama"
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 pr-10 text-xs placeholder-gray-500 focus:outline-none focus:border-emerald-500">
                                <button type="button" onclick="const p = document.getElementById('modal_password'); p.type = p.type === 'password' ? 'text' : 'password'"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="modal_description" class="block text-xs font-medium text-gray-300 mb-1.5">Deskripsi Singkat</label>
                            <input type="text" id="modal_description" name="description" value="{{ old('description', $soal->description) }}"
                                   placeholder="Keterangan singkat materi..."
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. SOAL / MATERI BERUPA TEKS LANGSUNG --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Soal / Materi Berupa Teks Langsung <span class="text-[11px] text-gray-500 font-normal lowercase">(opsional)</span>
                </h4>
                <p class="text-[11px] text-gray-400 mb-2.5">Teks soal akan langsung ditampilkan rapi di layar mahasiswa tanpa harus mengunduh file.</p>
                <textarea id="modal_content_text" name="content_text" rows="4"
                          placeholder="Ketik isi soal atau instruksi langsung di sini..."
                          class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500 font-mono">{{ old('content_text', $soal->content_text) }}</textarea>
            </div>

            {{-- 3. LAMPIRAN SAAT INI --}}
            @if($soal->attachments->count() > 0)
            <div class="pt-5 border-t border-[#1A2820] space-y-3">
                <div class="flex justify-between items-center">
                    <h4 class="text-xs font-bold text-yellow-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Lampiran Dokumen Saat Ini
                    </h4>
                    <span class="text-[11px] text-gray-500">{{ $soal->attachments->count() }} Lampiran Tersimpan</span>
                </div>

                <div class="bg-[#090E0B]/50 rounded-xl p-3 space-y-2 border border-[#1A2820]">
                    @foreach($soal->attachments as $att)
                    <div id="modal-attachment-{{ $att->id }}" class="flex flex-wrap items-center justify-between gap-2.5 bg-gray-800/80 p-2.5 rounded-xl border border-gray-700">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center flex-shrink-0">
                                @if($att->isLink())
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @else
                                    <span class="text-[10px] font-bold text-emerald-400 uppercase font-mono">{{ $att->file_type ?: 'FILE' }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-200 font-medium truncate">{{ $att->file_original_name }}</p>
                                <div class="flex items-center gap-2 text-[11px] text-gray-500 font-mono">
                                    @if($att->isLink())
                                        <a href="{{ $att->link_url }}" target="_blank" rel="noopener noreferrer" class="text-purple-400 hover:underline truncate max-w-xs">{{ $att->link_url }}</a>
                                    @else
                                        <span>{{ $att->file_size_formatted }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            @if($att->isFile())
                            <label class="flex items-center gap-1.5 cursor-pointer bg-gray-900/70 px-2.5 py-1 rounded-lg border border-gray-700 text-[11px]">
                                <input type="hidden" name="attachment_downloadable[{{ $att->id }}]" value="0">
                                <input type="checkbox" name="attachment_downloadable[{{ $att->id }}]" value="1"
                                       {{ $att->is_downloadable ? 'checked' : '' }}
                                       class="accent-emerald-500 w-3.5 h-3.5 rounded cursor-pointer">
                                <span class="text-gray-300">Boleh Diunduh</span>
                            </label>
                            @endif

                            <button type="button" onclick="removeModalExistingFile({{ $att->id }})" class="p-1.5 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer" title="Hapus Lampiran Ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 4. TAMBAH FILE BARU --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-xs font-bold text-green-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Tambahkan File Baru <span class="text-[11px] text-gray-500 font-normal lowercase">(Maks. total 15 file)</span>
                    </h4>
                </div>
                <div class="border-2 border-dashed border-gray-700 hover:border-emerald-500 rounded-xl p-5 text-center cursor-pointer transition-colors bg-[#090E0B]/40"
                     onclick="document.getElementById('modal_files').click()">
                    <p class="text-xs text-gray-400">Klik untuk memilih file tambahan (Maks. 5MB per file)</p>
                </div>
                <input type="file" id="modal_files" name="files[]" multiple class="hidden"
                       onchange="handleModalNewFiles(this)"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.pkt">
                @error('files') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                <div id="modal-new-files-container" class="mt-3 space-y-2 hidden">
                    <p class="text-[11px] text-gray-400 font-semibold mb-1">File Baru yang Akan Diunggah & Hak Unduh:</p>
                    <div id="modal-new-files-list" class="space-y-2"></div>
                </div>
            </div>

            {{-- 5. TAMBAH TAUTAN LINK BARU --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Tambah Tautan / Link Materi Baru
                    </h4>
                    <button type="button" onclick="addModalEditLinkRow()" class="text-xs bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 px-2.5 py-1.5 rounded-lg border border-purple-500/30 transition-colors flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Link
                    </button>
                </div>
                <div id="modal-edit-links-container" class="space-y-2"></div>
            </div>

            {{-- 6. STATUS & MASA AKTIF --}}
            <div class="pt-5 border-t border-[#1A2820] space-y-4">
                <div class="flex items-center justify-between p-3.5 bg-[#090E0B]/50 rounded-xl border border-[#1A2820]">
                    <div>
                        <p class="text-xs font-medium text-gray-200">Status Dokumen</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Aktifkan atau nonaktifkan akses dokumen bagi mahasiswa</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="modal_is_active" name="is_active" value="1" class="sr-only peer"
                               {{ old('is_active', $soal->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-[#090E0B]/50 rounded-xl border border-[#1A2820]">
                    <div>
                        <p class="text-xs font-medium text-gray-200">Master Hak Unduh Dokumen</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Pengaturan global izin unduh (jika aktif, default file baru diizinkan unduh)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_downloadable" value="0">
                        <input type="checkbox" id="modal_is_downloadable" name="is_downloadable" value="1" class="sr-only peer"
                               {{ old('is_downloadable', $soal->is_downloadable) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-2">Masa Aktif Dokumen</label>
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="lifetime"
                                   {{ old('access_type', $soal->access_type) === 'lifetime' ? 'checked' : '' }}
                                   class="text-emerald-500 bg-gray-800 border-gray-600 cursor-pointer"
                                   onchange="document.getElementById('modal-schedule-fields').classList.add('hidden')">
                            <span class="text-xs text-gray-300">Selamanya</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="scheduled"
                                   {{ old('access_type', $soal->access_type) === 'scheduled' ? 'checked' : '' }}
                                   class="text-emerald-500 bg-gray-800 border-gray-600 cursor-pointer"
                                   onchange="document.getElementById('modal-schedule-fields').classList.remove('hidden')">
                            <span class="text-xs text-gray-300">Waktu Tertentu</span>
                        </label>
                    </div>
                    <div id="modal-schedule-fields" class="{{ old('access_type', $soal->access_type) === 'scheduled' ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-3 bg-[#090E0B]/40 p-3.5 rounded-xl border border-[#1A2820]">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Mulai Aktif</label>
                            <input type="datetime-local" name="active_from"
                                   value="{{ old('active_from', $soal->active_from?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Berakhir</label>
                            <input type="datetime-local" name="active_until"
                                   value="{{ old('active_until', $soal->active_until?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. PENGUMPULAN TUGAS --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <label class="block text-xs font-medium text-gray-300 mb-2">Fitur Pengumpulan Tugas bagi Mahasiswa</label>
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="is_submission_enabled" id="modal_is_submission_enabled" value="1" 
                           {{ old('is_submission_enabled', $soal->is_submission_enabled) ? 'checked' : '' }} 
                           class="accent-emerald-500 bg-gray-800 border-gray-600 w-4 h-4 rounded cursor-pointer" 
                           onchange="toggleModalSubmissionFields(this.checked)">
                    <span class="text-xs text-gray-300">Aktifkan Form Pengumpulan Tugas bagi Mahasiswa</span>
                </label>

                <div id="modal-submission-fields" class="{{ old('is_submission_enabled', $soal->is_submission_enabled) ? '' : 'hidden' }} space-y-4 bg-[#090E0B]/40 p-3.5 rounded-xl border border-[#1A2820]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                            <input type="datetime-local" id="modal_deadline_at" name="deadline_at" 
                                   value="{{ old('deadline_at', $soal->deadline_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            @error('deadline_at') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Instruksi Tambahan (Opsional)</label>
                            <textarea name="submission_instructions" rows="2" class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-emerald-500 resize-none" placeholder="Contoh: Format laporan dalam PDF, ukuran max 5MB.">{{ old('submission_instructions', $soal->submission_instructions) }}</textarea>
                        </div>
                    </div>

                    {{-- Daftar Pilihan Kelas (Opsional) --}}
                    @php
                        $classesStr = old('classes', is_array($soal->classes) ? implode(', ', $soal->classes) : '');
                    @endphp
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-1">Daftar Pilihan Kelas (Opsional)</label>
                        <input type="text" name="classes" value="{{ $classesStr }}"
                               placeholder="Contoh: A1, A2, B1, B2 (Pisahkan dengan tanda koma)"
                               class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        <p class="text-[11px] text-gray-500 mt-1">Jika diisi, mahasiswa wajib memilih salah satu kelas saat mengumpulkan tugas.</p>
                        @error('classes') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pilihan Tipe Pengumpulan --}}
                    @php
                        $modalSubTypes = old('submission_types', $soal->submission_types ?? ['file']);
                        if(empty($modalSubTypes)) $modalSubTypes = ['file'];
                    @endphp
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-2">Metode Pengumpulan yang Diizinkan Mahasiswa <span class="text-red-400">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                                <input type="checkbox" name="submission_types[]" value="file" {{ in_array('file', $modalSubTypes) ? 'checked' : '' }} class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-200">📁 File Dokumen</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                                <input type="checkbox" name="submission_types[]" value="link" {{ in_array('link', $modalSubTypes) ? 'checked' : '' }} class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-200">🔗 Tautan / Link URL</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                                <input type="checkbox" name="submission_types[]" value="text" {{ in_array('text', $modalSubTypes) ? 'checked' : '' }} class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-200">📝 Teks Langsung</span>
                            </label>
                        </div>
                    </div>

                    {{-- Format File yang Diizinkan --}}
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-1">Format File yang Diizinkan (Jika Mengunggah File) <span class="text-red-400">*</span></label>
                        @php $modalExts = old('allowed_extensions', $soal->allowed_extensions ?? []); @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer bg-emerald-500/10 border border-emerald-500/30 p-2 rounded-lg">
                                <input type="checkbox" name="allowed_extensions[]" value="all" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('all', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-emerald-300 font-bold">Semua Format (Aman)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="pdf" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('pdf', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">PDF (.pdf)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="word" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('word', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Word (.doc, .docx)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="excel" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('excel', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Excel (.xls, .xlsx)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="ppt" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('ppt', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">PowerPoint (.ppt)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="image" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('image', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Gambar (.jpg, .png)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="archive" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('archive', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Arsip (.zip, .rar)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="pkt" class="accent-emerald-500 w-3.5 h-3.5 rounded" {{ in_array('pkt', $modalExts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Packet Tracer (.pkt)</span>
                            </label>
                        </div>
                        @error('allowed_extensions') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

        </form>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t flex-shrink-0" style="border-color:#1A2820;background:#090E0B;">
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-200 px-4 py-2.5 rounded-xl text-xs font-semibold cursor-pointer transition-colors">
                Batal
            </button>
            <button type="submit" form="modal-edit-soal-form"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-xs shadow-lg shadow-emerald-500/20 cursor-pointer transition-all">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<script>
let currentModalText = '';
let currentFilterType = 'all';
let passwordVisible = false;
const rawPassword = {!! json_encode($soal->plain_password ?? '') !!};
let modalExistingFileCount = {{ $soal->attachments()->where('type', 'file')->count() }};
let modalEditLinkCount = 0;

function togglePasswordVisibility() {
    const textEl = document.getElementById('passwordTextVal');
    const eyeIcon = document.getElementById('passwordEyeIcon');
    if (!textEl) return;
    passwordVisible = !passwordVisible;
    if (passwordVisible) {
        textEl.textContent = rawPassword;
        if (eyeIcon) {
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
        }
    } else {
        textEl.textContent = '••••••••';
        if (eyeIcon) {
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    }
}

function openEditModal() {
    const modal = document.getElementById('editModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

function toggleModalSubmissionFields(checked) {
    const fields = document.getElementById('modal-submission-fields');
    if (!fields) return;
    fields.classList.toggle('hidden', !checked);
    if (!checked) {
        const dl = document.getElementById('modal_deadline_at');
        if (dl) dl.value = '';
    }
}

function removeModalExistingFile(id) {
    if (window.Swal) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Anda yakin ingin menghapus lampiran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#374151',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            background: '#0F1713',
            color: '#f3f4f6',
            customClass: {
                popup: 'border border-[#1A2820] rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2 text-sm font-medium',
                cancelButton: 'rounded-xl px-4 py-2 text-sm font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                doRemoveModalAttachment(id);
            }
        });
    } else {
        if (confirm('Anda yakin ingin menghapus lampiran ini?')) {
            doRemoveModalAttachment(id);
        }
    }
}

function doRemoveModalAttachment(id) {
    const el = document.getElementById('modal-attachment-' + id);
    if (el) el.style.display = 'none';
    modalExistingFileCount--;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_attachments[]';
    input.value = id;
    document.getElementById('modal-edit-soal-form').appendChild(input);
}

function handleModalNewFiles(input) {
    const container = document.getElementById('modal-new-files-container');
    const list = document.getElementById('modal-new-files-list');
    list.innerHTML = '';

    if (!input.files || input.files.length === 0) {
        container.classList.add('hidden');
        return;
    }

    if (modalExistingFileCount + input.files.length > 15) {
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'Batas Maksimum Berkas',
                text: 'Total file (tersisa + baru) tidak boleh lebih dari 15 file.',
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-warning',
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-warning'
                }
            });
        } else {
            alert('Total file (tersisa + baru) tidak boleh lebih dari 15 file.');
        }
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    let hasLarge = false;
    Array.from(input.files).forEach(f => {
        if (f.size > 5 * 1024 * 1024) hasLarge = true;
    });
    if (hasLarge) {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Berkas Terlalu Besar',
                text: 'Satu atau lebih berkas melebihi batas maksimal 5 MB per file!',
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Perbaiki',
                customClass: {
                    popup: 'cyber-swal-popup cyber-swal-error',
                    title: 'cyber-swal-title',
                    htmlContainer: 'cyber-swal-text',
                    confirmButton: 'cyber-swal-btn cyber-swal-btn-error'
                }
            });
        } else {
            alert('Satu atau lebih file melebihi batas 5MB!');
        }
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');
    const isMasterDl = document.getElementById('modal_is_downloadable').checked;

    Array.from(input.files).forEach((file, index) => {
        const kb = file.size / 1024;
        const sizeStr = kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb/1024).toFixed(2)} MB`;

        const row = document.createElement('div');
        row.className = 'flex items-center justify-between bg-gray-800/80 p-2.5 rounded-lg border border-gray-700 text-xs';
        row.innerHTML = `
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-emerald-400 font-bold">+ Baru</span>
                <span class="text-gray-200 truncate max-w-xs">${file.name}</span>
                <span class="text-gray-500">(${sizeStr})</span>
            </div>
            <label class="flex items-center gap-1.5 cursor-pointer ml-3 flex-shrink-0">
                <input type="hidden" name="file_downloadable[${index}]" value="0">
                <input type="checkbox" name="file_downloadable[${index}]" value="1" ${isMasterDl ? 'checked' : ''}
                       class="accent-emerald-500 w-3.5 h-3.5 rounded cursor-pointer">
                <span class="text-[11px] text-gray-300">Boleh Diunduh</span>
            </label>
        `;
        list.appendChild(row);
    });
}

function addModalEditLinkRow() {
    const container = document.getElementById('modal-edit-links-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 link-row bg-[#090E0B]/50 p-2.5 rounded-xl border border-[#1A2820]';
    div.innerHTML = `
        <div class="md:col-span-4">
            <input type="text" name="links[${modalEditLinkCount}][title]" placeholder="Label Link (misal: Bahan Modul)"
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-7">
            <input type="url" name="links[${modalEditLinkCount}][url]" placeholder="https://..."
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5 cursor-pointer" title="Hapus Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
    modalEditLinkCount++;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeTextModal();
    }
});

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    openEditModal();
});
@endif

function showToast(msg) {
    if (window.Swal) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            background: '#0F1713',
            color: '#E5E7EB',
            icon: 'success',
            title: msg,
            customClass: {
                popup: 'border border-emerald-500/30'
            }
        });
    } else {
        alert(msg);
    }
}

function copyText(str, msg) {
    navigator.clipboard.writeText(str).then(() => {
        showToast(msg || 'Berhasil disalin!');
    });
}

function showTextModal(name, nim, content) {
    document.getElementById('modalStudentName').textContent = name;
    document.getElementById('modalStudentNim').textContent = 'NIM: ' + nim;
    document.getElementById('modalTextContent').textContent = content;
    currentModalText = content;
    const modal = document.getElementById('textSubmissionModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeTextModal() {
    const modal = document.getElementById('textSubmissionModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function copyModalText() {
    navigator.clipboard.writeText(currentModalText).then(() => {
        showToast('Teks jawaban berhasil disalin!');
    });
}

function setSubFilter(type) {
    currentFilterType = type;
    document.querySelectorAll('.sub-filter-pill').forEach(btn => {
        const isActive = btn.getAttribute('data-type') === type;
        if (isActive) {
            btn.className = 'sub-filter-pill active px-3 py-1.5 rounded-lg font-medium transition-colors';
            btn.style.background = 'rgba(16,185,129,.15)';
            btn.style.color = '#34D399';
            btn.style.borderColor = 'rgba(16,185,129,.3)';
        } else {
            btn.className = 'sub-filter-pill px-3 py-1.5 rounded-lg font-medium transition-colors text-gray-400 hover:text-white border';
            btn.style.background = '#090D0B';
            btn.style.color = '#9CA3AF';
            btn.style.borderColor = '#1A2820';
        }
    });
    filterSubmissions();
}

function filterSubmissions() {
    const input = document.getElementById('submissionSearchInput');
    const query = input ? input.value.toLowerCase().trim() : '';
    const classSelect = document.getElementById('classFilterSelect');
    const selectedClass = classSelect ? classSelect.value : 'all';
    const rows = document.querySelectorAll('.submission-row');

    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        const kelas = row.getAttribute('data-kelas') || '';
        const nama = row.querySelector('.sub-nama').textContent.toLowerCase();
        const nim = row.querySelector('.sub-nim').textContent.toLowerCase();

        const matchesQuery = query === '' || nama.includes(query) || nim.includes(query);
        const matchesType = currentFilterType === 'all' || type === currentFilterType;
        const matchesClass = selectedClass === 'all' || kelas === selectedClass;

        if (matchesQuery && matchesType && matchesClass) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

@endsection
