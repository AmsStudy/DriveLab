@extends('errors.layout')

@section('title', '500 — Kesalahan Server')
@section('code', '500')

@section('icon')
<svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
@endsection

@section('heading', 'Terjadi Kesalahan pada Server')

@section('message', 'Server mengalami kendala tak terduga saat memproses permintaan Anda. Tim asisten laboratorium telah menerima pemberitahuan mengenai insiden ini.')
