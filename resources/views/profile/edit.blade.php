@extends('layouts.admin')
@section('title', 'Pengaturan Profil')
@section('page-title', 'Pengaturan Profil')
@section('page-description', 'Kelola informasi identitas, kredensial login, dan preferensi akun Anda')

@section('content')
@php
    $totalDokumen = $user->soals()->count();
    $totalKuis = $user->quizzes()->count();
    $totalKategori = $user->categories()->count();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start max-w-7xl">

    {{-- ─── KOLOM KIRI (4 Kolom): Kartu Identitas Profil & Ringkasan Aktivitas ─── --}}
    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

        {{-- ID Card Utama --}}
        <div class="rounded-3xl p-6 border border-[#1A2820] shadow-2xl relative overflow-hidden text-center bg-[#0F1713]"
             style="background: radial-gradient(circle at 50% 0%, rgba(16,185,129,0.12), transparent 70%), #0F1713;">
            
            {{-- Accent Top Glow Line --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600"></div>

            {{-- Big Cyber Avatar --}}
            <div class="relative inline-block mx-auto mt-2 mb-4">
                <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-emerald-600 via-teal-500 to-emerald-400 p-1 shadow-xl shadow-emerald-500/25 flex items-center justify-center">
                    <div class="w-full h-full rounded-[22px] bg-[#090D0B] flex items-center justify-center">
                        <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                </div>
                {{-- Active Status Dot --}}
                <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-[#0F1713] flex items-center justify-center shadow-md shadow-emerald-500/50" title="Akun Aktif">
                    <span class="w-2 h-2 rounded-full bg-white"></span>
                </span>
            </div>

            {{-- Name & Role --}}
            <h2 class="text-lg font-bold text-white tracking-tight">{{ $user->name }}</h2>
            <p class="text-xs text-gray-400 font-mono mt-0.5 truncate">{{ $user->email }}</p>

            <div class="mt-3 flex items-center justify-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $user->isSuperAdmin() ? 'bg-purple-500/15 text-purple-300 border border-purple-500/30' : 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    {{ $user->isSuperAdmin() ? 'Super Administrator' : 'Admin Dosen / Asisten' }}
                </span>
            </div>

            {{-- Separator --}}
            <div class="border-t border-[#1A2820] my-5"></div>

            {{-- Info Details Grid --}}
            <div class="space-y-3 text-left text-xs">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Institusi
                    </span>
                    <span class="font-semibold text-gray-200">{{ $user->institution ?: 'IC-Labs Laboratory' }}</span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bergabung
                    </span>
                    <span class="font-semibold text-gray-200">{{ $user->created_at ? $user->created_at->isoFormat('D MMMM Y') : '—' }}</span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#090D0B] border border-[#1A2820]">
                    <span class="text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verifikasi
                    </span>
                    @if($user->email_verified_at)
                        <span class="font-semibold text-emerald-400 inline-flex items-center gap-1">Terverifikasi</span>
                    @else
                        <span class="font-semibold text-amber-400 inline-flex items-center gap-1">Belum Terverifikasi</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Mini Stat Card: Statistik Kontribusi --}}
        <div class="rounded-3xl p-5 border border-[#1A2820] shadow-xl bg-[#0F1713]">
            <h3 class="text-xs font-bold text-gray-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Statistik Akun
            </h3>
            <div class="grid grid-cols-3 gap-2.5 text-center">
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <div class="text-lg font-black text-emerald-400">{{ $totalDokumen }}</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">Dokumen</div>
                </div>
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <div class="text-lg font-black text-teal-400">{{ $totalKuis }}</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">Kuis CBT</div>
                </div>
                <div class="p-3 rounded-2xl bg-[#090D0B] border border-[#1A2820]">
                    <div class="text-lg font-black text-sky-400">{{ $totalKategori }}</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">Kategori</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ─── KOLOM KANAN (8 Kolom): Form Edit Profil, Password & Danger Zone ─── --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Form 1: Ubah Informasi Profil --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 shadow-xl">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Form 2: Ubah Kata Sandi --}}
        <div class="bg-[#0F1713] border border-[#1A2820] rounded-3xl p-6 sm:p-8 shadow-xl">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Form 3: Zona Bahaya --}}
        <div class="bg-[#0F1713] border border-rose-900/30 rounded-3xl p-6 sm:p-8 shadow-xl">
            @include('profile.partials.delete-user-form')
        </div>

    </div>

</div>
@endsection
