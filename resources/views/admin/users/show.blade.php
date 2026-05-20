@extends('layouts.admin')
@section('title', 'Detail Admin')
@section('page-title', 'Detail Profil Admin')
@section('page-description', 'Informasi lengkap dan Arsip Dokumen milik ' . $user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profil Info --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-pink-500 to-fuchsia-600 flex items-center justify-center shadow-lg shadow-pink-500/20 mb-4">
                <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            </div>
            <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
            <p class="text-sm text-gray-400 mt-1">{{ $user->email }}</p>

            <div class="mt-4 inline-flex">
                <span class="text-xs px-3 py-1.5 rounded-full font-medium
                    {{ $user->status === 'active' ? 'bg-green-500/15 text-green-400 border border-green-500/20' : '' }}
                    {{ $user->status === 'pending' ? 'bg-yellow-500/15 text-yellow-400 border border-yellow-500/20' : '' }}
                    {{ $user->status === 'inactive' ? 'bg-gray-500/15 text-gray-400 border border-gray-500/20' : '' }}">
                    Status: {{ ['active'=>'Aktif','pending'=>'Menunggu Approval','inactive'=>'Nonaktif'][$user->status] }}
                </span>
            </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4">Informasi Tambahan</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-800 pb-2">
                    <span class="text-gray-500">Institusi/Lab</span>
                    <span class="text-gray-200 text-right">{{ $user->institution ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-800 pb-2">
                    <span class="text-gray-500">Total Dokumen</span>
                    <span class="text-pink-400 font-bold">{{ $user->soals_count }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-800 pb-2">
                    <span class="text-gray-500">Terdaftar Sejak</span>
                    <span class="text-gray-200">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2">
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
                      onsubmit="confirmAction(event, 'Reset password admin ini ke default ({{ config('app.admin_default_password') }})? Asisten harus segera mengganti password setelah login.', 'Ya, Reset Password', true)">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 py-2 rounded-xl text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Reset Password ke Default
                    </button>
                </form>

                @if($user->status === 'pending')
                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                        @csrf @method('PATCH')
                        <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl text-sm font-medium transition-colors">Approve Admin Ini</button>
                    </form>
                @elseif($user->status === 'active')
                    <form method="POST" action="{{ route('admin.users.reject', $user) }}">
                        @csrf @method('PATCH')
                        <button class="w-full bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 py-2 rounded-xl text-sm font-medium transition-colors">Nonaktifkan Akun</button>
                    </form>
                @elseif($user->status === 'inactive')
                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                        @csrf @method('PATCH')
                        <button class="w-full bg-green-500/20 hover:bg-green-500/30 text-green-400 py-2 rounded-xl text-sm font-medium transition-colors">Aktifkan Kembali</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Arsip Dokumen --}}
    <div class="lg:col-span-2">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                <h3 class="font-semibold text-white">Dokumen yang Diunggah</h3>
                <span class="text-xs text-gray-500">{{ $user->soals_count }} dokumen</span>
            </div>

            @if($soals->isEmpty())
                <div class="py-12 text-center text-gray-500 text-sm">
                    Admin ini belum mengunggah dokumen apapun.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Dokumen</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Akses</th>
                                <th class="px-6 py-3 text-right">Lihat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($soals as $soal)
                            <tr class="hover:bg-gray-800/30">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-200 truncate max-w-[200px]">{{ $soal->title }}</p>
                                    @if($soal->category)
                                        <p class="text-xs text-gray-500 mt-1">{{ $soal->category->name }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php $st = $soal->access_status; @endphp
                                    <span class="text-[10px] px-2 py-0.5 rounded-full uppercase
                                        {{ $st === 'active' ? 'bg-green-500/15 text-green-400' : '' }}
                                        {{ $st === 'inactive' ? 'bg-gray-500/15 text-gray-400' : '' }}
                                        {{ $st === 'expired' ? 'bg-red-500/15 text-red-400' : '' }}
                                        {{ $st === 'scheduled' ? 'bg-yellow-500/15 text-yellow-400' : '' }}">
                                        {{ $st }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center text-gray-400">
                                    {{ number_format($soal->access_count) }}&times;
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.soal.show', $soal) }}" class="text-pink-400 hover:text-pink-300 text-xs font-medium">Detail &rarr;</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($soals->hasPages())
                    <div class="px-6 py-4 border-t border-gray-800">{{ $soals->links() }}</div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection

