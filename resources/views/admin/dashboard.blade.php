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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[560px]">
                        <thead class="text-xs text-gray-500 uppercase tracking-wider" style="border-bottom:1px solid #1A2820;background:rgba(255,255,255,.01);">
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
                <a href="{{ route('admin.soal.create') }}"
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
                <div class="divide-y divide-gray-800/60">
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
