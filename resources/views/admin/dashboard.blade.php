@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', auth()->user()->isSuperAdmin() ? 'Overview keseluruhan sistem LabDrive' : 'Overview soal dan aktivitas akses Anda')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid #1E1E2E;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(247,37,133,.12);">
                <svg class="w-5 h-5" style="color:#F72585;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['total_soal'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Total Dokumen</p>
    </div>

    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid #1E1E2E;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(52,211,153,.1);">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['soal_aktif'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Dokumen Aktif</p>
    </div>

    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid #1E1E2E;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(59,130,246,.1);">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['total_akses'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Total Akses</p>
    </div>

    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid #1E1E2E;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(234,179,8,.1);">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['akses_hari_ini'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Akses Hari Ini</p>
    </div>

    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid rgba(168,85,247,.2);">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(168,85,247,.1);">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-purple-400">{{ $stats['tugas_masuk'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Total Tugas</p>
    </div>

    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid rgba(168,85,247,.2);">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(168,85,247,.1);">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-purple-400">{{ $stats['tugas_hari_ini'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Tugas Hari Ini</p>
    </div>

</div>

@if(auth()->user()->isSuperAdmin())
{{-- Super Admin Extra Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid #1E1E2E;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:rgba(247,37,133,.12);">
            <svg class="w-5 h-5" style="color:#F72585;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['total_admin'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Total Admin</p>
    </div>
    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid #1E1E2E;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:rgba(52,211,153,.1);">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-green-400">{{ $stats['akses_sukses'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Akses Berhasil</p>
    </div>
    <div class="rounded-2xl p-5" style="background:#13131F;border:1px solid rgba(249,115,22,.2);">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:rgba(249,115,22,.1);">
            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-orange-400">{{ $stats['soal_expired'] }}</p>
        <p class="text-sm text-gray-400 mt-1">Dokumen Expired</p>
    </div>
</div>
@endif

{{-- Recent Logs --}}
<div class="rounded-2xl overflow-hidden" style="background:#13131F;border:1px solid #1E1E2E;">
    <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid #1E1E2E;">
        <h3 class="font-semibold text-white">Log Akses Terbaru</h3>
        <a href="{{ route('admin.logs.index') }}" class="text-sm transition-colors"
           style="color:#F72585;"
           onmouseover="this.style.color='#e01070'" onmouseout="this.style.color='#F72585'">Lihat semua →</a>
    </div>

    @if($recent_logs->isEmpty())
        <div class="px-6 py-12 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p>Belum ada log akses.</p>
        </div>
    @else
        <div class="divide-y" style="border-color:#1E1E2E;">
            @foreach($recent_logs as $log)
            <div class="px-6 py-3 flex items-center gap-4 transition-colors" style="hover:background:#1E1E2E;">
                <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $log->status === 'success' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-200 truncate">{{ $log->soal->title ?? 'Dokumen Dihapus' }}</p>
                    <p class="text-xs text-gray-500">{{ $log->ip_address }} · {{ $log->accessed_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $log->status === 'success' ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400' }}">
                    {{ $log->status === 'success' ? 'Berhasil' : 'Gagal' }}
                </span>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
