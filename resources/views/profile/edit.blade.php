@extends('layouts.admin')
@section('title', 'Profil Admin')
@section('page-title', 'Pengaturan Profil')
@section('page-description', 'Kelola informasi profil dan keamanan akun Anda')

@section('content')
<div class="space-y-6">
    
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="bg-gray-900 border border-red-900/30 rounded-2xl p-4 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
