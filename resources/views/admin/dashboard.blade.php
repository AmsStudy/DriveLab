@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', auth()->user()->isSuperAdmin() ? 'Ringkasan performa dan operasional sistem IC-Labs Repository' : 'Ringkasan dokumen soal dan pengumpulan tugas Anda')

@section('content')

{{-- ─── STATS CARDS ─── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-8">

    {{-- Total Dokumen --}}
    <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-emerald-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dokumen</span>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(16,185,129,.12);">
                <svg class="w-4 h-4" style="color:#10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_soal']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Arsip Soal</p>
    </div>

    {{-- Dokumen Aktif --}}
    <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-emerald-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-emerald-400/80 uppercase tracking-wider">Aktif</span>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(16,185,129,.12);">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-emerald-400 tracking-tight">{{ number_format($stats['soal_aktif']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Siap Diakses</p>
    </div>

    {{-- Total Kunjungan / Views --}}
    <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-blue-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Kunjungan</span>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(59,130,246,.12);">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-blue-400 tracking-tight">{{ number_format($stats['total_views']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Kali Dilihat</p>
    </div>

    {{-- Total Tugas Masuk --}}
    <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-teal-500/30 group" style="background:#0F1713;border:1px solid rgba(13,148,136,.2);">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-teal-400/80 uppercase tracking-wider">Tugas</span>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(13,148,136,.12);">
                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-teal-400 tracking-tight">{{ number_format($stats['tugas_masuk']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Pengumpulan</p>
    </div>

    {{-- Tugas Hari Ini --}}
    <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-teal-500/30 group" style="background:#0F1713;border:1px solid rgba(13,148,136,.2);">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-teal-400/80 uppercase tracking-wider">Hari Ini</span>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(13,148,136,.12);">
                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-teal-300 tracking-tight">{{ number_format($stats['tugas_hari_ini']) }}</p>
        <p class="text-xs text-gray-400 mt-1">Tugas Hari Ini</p>
    </div>

    {{-- Total Lampiran & Link --}}
    <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-amber-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-amber-400/80 uppercase tracking-wider">Lampiran</span>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(245,158,11,.12);">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-amber-400 tracking-tight">{{ number_format($stats['total_lampiran']) }}</p>
        <p class="text-xs text-gray-400 mt-1">File & Tautan</p>
    </div>

</div>

{{-- ─── CBT STATS CARDS ─── --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Kuis Interaktif (CBT Exam)
        </h3>
        <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-gray-400 hover:text-emerald-400 font-medium transition-colors">
            Kelola Kuis →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Kuis --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-emerald-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Kuis</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(16,185,129,.12);">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_quiz'] ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Bank Soal Ujian</p>
        </div>

        {{-- Kuis Aktif --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-emerald-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Kuis Aktif</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(16,185,129,.12);">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-emerald-400 tracking-tight">{{ number_format($stats['quiz_aktif'] ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Dapat Dikerjakan</p>
        </div>

        {{-- Sesi Ujian / Peserta --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-purple-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Peserta Selesai</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(168,85,247,.12);">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-purple-400 tracking-tight">{{ number_format($stats['total_attempts'] ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Lembar Selesai</p>
        </div>

        {{-- Rata-rata Skor --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden transition-all duration-200 hover:border-amber-500/30 group" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Rata-rata Skor</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" style="background:rgba(245,158,11,.12);">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-amber-400 tracking-tight">{{ number_format($stats['avg_quiz_score'] ?? 0, 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">Skala 0 - 100</p>
        </div>
    </div>
</div>

{{-- ─── SUPER ADMIN EXTRA BANNER / STATS ─── --}}
@if(auth()->user()->isSuperAdmin())
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="rounded-2xl p-4 flex items-center justify-between" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,.12);">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Total Akun Admin / Asisten</p>
                <p class="text-lg font-bold text-white">{{ $stats['total_admin'] }} Pengguna</p>
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium">Kelola →</a>
    </div>

    <div class="rounded-2xl p-4 flex items-center justify-between" style="background:#0F1713;border:1px solid {{ $stats['admin_pending'] > 0 ? 'rgba(234,179,8,.3)' : '#1A2820' }};">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(234,179,8,.12);">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Menunggu Persetujuan</p>
                <p class="text-lg font-bold {{ $stats['admin_pending'] > 0 ? 'text-yellow-400' : 'text-white' }}">{{ $stats['admin_pending'] }} Akun</p>
            </div>
        </div>
        @if($stats['admin_pending'] > 0)
            <a href="{{ route('admin.users.index') }}" class="text-xs bg-yellow-500/20 text-yellow-400 px-2.5 py-1 rounded-lg border border-yellow-500/30 hover:bg-yellow-500/30 font-semibold">Tinjau</a>
        @else
            <span class="text-xs text-gray-500">Semua disetujui</span>
        @endif
    </div>

    <div class="rounded-2xl p-4 flex items-center justify-between" style="background:#0F1713;border:1px solid #1A2820;">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(249,115,22,.12);">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Dokumen Telah Expired</p>
                <p class="text-lg font-bold text-orange-400">{{ $stats['soal_expired'] }} Dokumen</p>
            </div>
        </div>
        <a href="{{ route('admin.soal.index') }}?status=expired" class="text-xs text-orange-400 hover:text-orange-300 font-medium">Filter →</a>
    </div>
</div>
@endif

{{-- ─── MAIN DASHBOARD CONTENT (2 COLUMNS) ─── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Pengumpulan Tugas Terbaru (2 Cols) --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl overflow-hidden shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid #1A2820;">
                <div>
                    <h3 class="font-bold text-white text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Pengumpulan Tugas Terbaru
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Aktivitas mahasiswa mengumpulkan tugas secara real-time</p>
                </div>
                <a href="{{ route('admin.soal.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1 transition-colors">
                    Semua Dokumen
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if($recent_submissions->isEmpty())
                <div class="py-16 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium">Belum ada tugas yang dikumpulkan mahasiswa.</p>
                    <p class="text-xs text-gray-600 mt-1">Pengumpulan mahasiswa akan otomatis tampil di sini saat mereka mengirimkan tugas.</p>
                </div>
            @else
                <div class="overflow-x-auto overflow-y-auto max-h-[220px]" style="scrollbar-width: thin;">
                    <table class="w-full text-sm min-w-[560px]">
                        <thead class="text-xs text-gray-500 uppercase tracking-wider sticky top-0 z-10 bg-[#0F1713]" style="border-bottom:1px solid #1A2820;">
                            <tr>
                                <th class="px-6 py-3 text-left">Mahasiswa</th>
                                <th class="px-4 py-3 text-left">Dokumen Soal</th>
                                <th class="px-4 py-3 text-left">Tipe</th>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            @foreach($recent_submissions as $sub)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                            {{ substr($sub->nama, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-200 truncate">{{ $sub->nama }}</p>
                                            <p class="text-xs font-mono text-gray-500">{{ $sub->nim }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($sub->soal)
                                        <a href="{{ route('admin.soal.show', $sub->soal) }}" class="text-xs text-gray-300 hover:text-emerald-400 font-medium truncate block max-w-xs transition-colors">
                                            {{ $sub->soal->title }}
                                        </a>
                                        @if($sub->soal->category)
                                            <span class="text-[10px] text-gray-500">{{ $sub->soal->category->name }}</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-500 italic">Dokumen Dihapus</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($sub->isLink())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-teal-500/15 text-teal-400 border border-teal-500/30">Link</span>
                                    @elseif($sub->isText())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">Teks</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/15 text-blue-400 border border-blue-500/30">File</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                                    {{ $sub->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($sub->soal)
                                        <a href="{{ route('admin.soal.show', $sub->soal) }}" class="text-xs text-emerald-400 hover:text-emerald-300 p-1.5 hover:bg-emerald-500/10 rounded-lg transition-colors inline-block" title="Lihat di Soal">
                                            Buka →
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

        {{-- Sesi Ujian CBT Terbaru --}}
        <div class="rounded-2xl overflow-hidden shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid #1A2820;">
                <div>
                    <h3 class="font-bold text-white text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Sesi Ujian CBT Terbaru
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Hasil pengerjaan kuis mahasiswa secara real-time</p>
                </div>
                <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1 transition-colors">
                    Semua Kuis
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if(!isset($recent_quiz_attempts) || $recent_quiz_attempts->isEmpty())
                <div class="py-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="text-sm font-medium">Belum ada mahasiswa yang menyelesaikan sesi ujian CBT.</p>
                    <p class="text-xs text-gray-600 mt-1">Hasil pengerjaan kuis akan otomatis dicatat di sini begitu mahasiswa menuntaskan ujian.</p>
                </div>
            @else
                <div class="overflow-x-auto overflow-y-auto max-h-[220px]" style="scrollbar-width: thin;">
                    <table class="w-full text-sm min-w-[560px]">
                        <thead class="text-xs text-gray-500 uppercase tracking-wider sticky top-0 z-10 bg-[#0F1713]" style="border-bottom:1px solid #1A2820;">
                            <tr>
                                <th class="px-6 py-3 text-left">Mahasiswa</th>
                                <th class="px-4 py-3 text-left">Kuis</th>
                                <th class="px-4 py-3 text-center">Skor</th>
                                <th class="px-4 py-3 text-center">Anti-Cheat</th>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-right">Hasil</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            @foreach($recent_quiz_attempts as $qatt)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                            {{ substr($qatt->student_name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-200 truncate">{{ $qatt->student_name }}</p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-mono text-gray-500">{{ $qatt->nim }}</span>
                                                @if($qatt->kelas)
                                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">{{ $qatt->kelas }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($qatt->quiz)
                                        <a href="{{ route('admin.quizzes.results', $qatt->quiz) }}" class="text-xs text-gray-300 hover:text-emerald-400 font-medium truncate block max-w-xs transition-colors">
                                            {{ $qatt->quiz->title }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-500 italic">Kuis Dihapus</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono inline-block
                                        {{ $qatt->score >= 70 ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : ($qatt->score >= 50 ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30') }}">
                                        {{ number_format($qatt->score, 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md
                                        {{ $qatt->tab_switch_count === 0 ? 'text-emerald-400 bg-emerald-500/10' : 'text-amber-400 bg-amber-500/10' }}">
                                        {{ $qatt->tab_switch_count === 0 ? '0 Switch 🛡️' : $qatt->tab_switch_count . 'x Switch' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                                    {{ $qatt->completed_at ? $qatt->completed_at->diffForHumans() : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($qatt->quiz)
                                        <a href="{{ route('admin.quizzes.results', $qatt->quiz) }}" class="text-xs text-purple-400 hover:text-purple-300 p-1.5 hover:bg-purple-500/10 rounded-lg transition-colors inline-block" title="Lihat Hasil Ujian">
                                            Tinjau →
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

    {{-- Right: Dokumen Populer & Quick Actions (1 Col) --}}
    <div class="space-y-6">

        {{-- Quick Actions --}}
        <div class="rounded-2xl p-5 shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <h3 class="font-bold text-white text-sm">Aksi Cepat</h3>
            </div>
            <div class="space-y-2">
                {{-- Buat Soal Baru --}}
                <a href="{{ route('admin.soal.index') }}?action=create"
                   class="w-full flex items-center gap-3 p-3 rounded-xl text-sm font-semibold text-white transition-all group"
                   style="background:linear-gradient(135deg,#10B981,#0D9488);box-shadow:0 4px 12px rgba(16,185,129,.2);"
                   onmouseover="this.style.boxShadow='0 6px 18px rgba(16,185,129,.35)'"
                   onmouseout="this.style.boxShadow='0 4px 12px rgba(16,185,129,.2)'">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span>Buat Soal Baru</span>
                    <svg class="w-4 h-4 ml-auto opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Buat Kuis Baru --}}
                <a href="{{ route('admin.quizzes.index') }}?action=create"
                   class="w-full flex items-center gap-3 p-3 rounded-xl text-sm font-semibold text-white transition-all group"
                   style="background:linear-gradient(135deg,#8B5CF6,#6366F1);box-shadow:0 4px 12px rgba(139,92,246,.2);"
                   onmouseover="this.style.boxShadow='0 6px 18px rgba(139,92,246,.35)'"
                   onmouseout="this.style.boxShadow='0 4px 12px rgba(139,92,246,.2)'">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span>Buat Kuis Baru</span>
                    <svg class="w-4 h-4 ml-auto opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Bank Kuis CBT --}}
                <a href="{{ route('admin.quizzes.index') }}"
                   class="w-full flex items-center gap-3 p-3 rounded-xl text-sm font-semibold transition-all"
                   style="background:rgba(168,85,247,.07);border:1px solid #1A2820;color:#e9d5ff;"
                   onmouseover="this.style.background='rgba(168,85,247,.13)';this.style.borderColor='rgba(168,85,247,.3)'"
                   onmouseout="this.style.background='rgba(168,85,247,.07)';this.style.borderColor='#1A2820'">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(168,85,247,.15);">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <span class="text-gray-300">Bank Kuis CBT</span>
                    <svg class="w-4 h-4 ml-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Kelola Kategori --}}
                <a href="{{ route('admin.categories.index') }}"
                   class="w-full flex items-center gap-3 p-3 rounded-xl text-sm font-semibold transition-all"
                   style="background:rgba(16,185,129,.07);border:1px solid #1A2820;color:#d1fae5;"
                   onmouseover="this.style.background='rgba(16,185,129,.13)';this.style.borderColor='rgba(16,185,129,.3)'"
                   onmouseout="this.style.background='rgba(16,185,129,.07)';this.style.borderColor='#1A2820'">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,.15);">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <span class="text-gray-300">Kelola Kategori</span>
                    <svg class="w-4 h-4 ml-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                {{-- Portal Mahasiswa --}}
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                   class="w-full flex items-center gap-3 p-3 rounded-xl text-sm font-medium transition-all"
                   style="background:rgba(255,255,255,.03);border:1px solid #1A2820;color:#9CA3AF;"
                   onmouseover="this.style.background='rgba(255,255,255,.06)';this.style.color='#d1d5db'"
                   onmouseout="this.style.background='rgba(255,255,255,.03)';this.style.color='#9CA3AF'">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,.05);">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </div>
                    Portal Mahasiswa
                </a>
            </div>
        </div>

        {{-- Top Dokumen Paling Banyak Diakses --}}
        <div class="rounded-2xl overflow-hidden shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Dokumen Terpopuler
                </h3>
                <span class="text-[11px] text-gray-500">Berdasarkan Akses</span>
            </div>

            @if($top_soals->isEmpty())
                <div class="py-10 text-center text-gray-500 text-xs">Belum ada dokumen yang dibuat.</div>
            @else
                <div class="divide-y divide-gray-800/60 overflow-y-auto max-h-[220px]" style="scrollbar-width: thin;">
                    @foreach($top_soals as $topSoal)
                    <div class="p-4 hover:bg-gray-800/30 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-1">
                            <a href="{{ route('admin.soal.show', $topSoal) }}" class="text-xs font-semibold text-gray-200 hover:text-emerald-400 transition-colors line-clamp-1">
                                {{ $topSoal->title }}
                            </a>
                            <span class="text-xs font-bold text-blue-400 flex-shrink-0 bg-blue-500/10 px-2 py-0.5 rounded-md border border-blue-500/20">
                                {{ number_format($topSoal->access_count) }}×
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-500 mt-2">
                            <span>{{ $topSoal->category->name ?? 'Tanpa Kategori' }}</span>
                            <span class="text-teal-400 font-medium">{{ $topSoal->submissions_count }} tugas terkumpul</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(auth()->user()->isSuperAdmin() && isset($recent_activities) && !$recent_activities->isEmpty())
        {{-- Activity Log (Super Admin Audit) --}}
        <div class="rounded-2xl overflow-hidden shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
            <div class="px-5 py-4 border-b border-gray-800">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Aktivitas Sistem Terakhir
                </h3>
            </div>
            <div class="divide-y divide-gray-800/60 p-2">
                @foreach($recent_activities as $act)
                <div class="px-3 py-2 text-xs">
                    <p class="text-gray-300">{{ $act->description }}</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $act->created_at->diffForHumans() }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</div>

@endsection
