@extends('errors.layout')

@section('title', '419 — Sesi Kedaluwarsa')
@section('code', '419')

@section('icon')
<svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('heading', 'Sesi Anda Telah Berakhir')

@section('message', 'Halaman ini telah terbuka terlalu lama tanpa aktivitas sehingga token keamanan CSRF kedaluwarsa. Silakan muat ulang halaman untuk melanjutkan.')

