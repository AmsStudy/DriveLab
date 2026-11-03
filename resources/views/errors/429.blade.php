@extends('errors.layout')

@section('title', '429 — Terlalu Banyak Permintaan')
@section('code', '429')

@section('icon')
<svg class="w-10 h-10 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
</svg>
@endsection

@section('heading', 'Batas Permintaan Terlampaui')

@section('message', 'Sistem mendeteksi terlalu banyak permintaan berturut-turut dari komputer/jaringan Anda demi perlindungan keamanan. Silakan tunggu beberapa saat sebelum mencoba kembali.')
