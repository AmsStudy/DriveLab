@extends('layouts.admin')
@section('title', 'Backup Database')
@section('page-title', 'Backup Database')
@section('page-description', 'Pusat pencadangan database mandiri dan snapshot pemulihan sistem')

@section('content')

{{-- Info Banner Ketahanan Server --}}
<div class="mb-6 p-4 sm:p-5 rounded-2xl border border-emerald-500/20 bg-emerald-950/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-start sm:items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center flex-shrink-0 text-emerald-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-emerald-300">Perlindungan Data Aktif & Terjadwal</h3>
            <p class="text-xs text-gray-400 mt-0.5">
                Perintah destruktif (seperti <code class="text-emerald-400 font-mono bg-black/40 px-1 py-0.5 rounded">migrate:fresh</code>) telah dilindungi. Backup otomatis dijadwalkan setiap pukul <strong>02:00 dini hari</strong> dengan kompresi gzip.
            </p>
        </div>
    </div>

    {{-- Tombol Buat Backup Manual --}}
    <form action="{{ route('admin.backups.create') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membuat backup database sekarang? Proses ini akan membuat snapshot database terbaru.');" class="flex-shrink-0">
        @csrf
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all shadow-md shadow-emerald-500/20 whitespace-nowrap"
                style="background:linear-gradient(135deg,#10B981,#0D9488);"
                onmouseover="this.style.boxShadow='0 6px 18px rgba(16,185,129,.35)'"
                onmouseout="this.style.boxShadow='0 4px 12px rgba(16,185,129,.2)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Buat Backup Sekarang
        </button>
    </form>
</div>

{{-- Tabel Daftar File Backup --}}
<div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl overflow-hidden shadow-xl">
    <div class="px-5 py-4 border-b border-[#1A2820] flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
            <h2 class="text-sm font-semibold text-gray-200">Arsip File Snapshot Database</h2>
        </div>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-[#1A2820] text-gray-400 border border-gray-700/40">
            {{ count($backups) }} File Tersedia
        </span>
    </div>

    @if(empty($backups))
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-800/40 border border-gray-700/30 flex items-center justify-center mx-auto mb-3.5 text-gray-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-300">Belum Ada File Backup</h3>
            <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">
                Klik tombol "Buat Backup Sekarang" di atas untuk membuat arsip cadangan pertama Anda.
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-[#1A2820] text-[11px] font-bold text-gray-500 uppercase tracking-wider bg-[#0B110E]">
                        <th class="py-3 px-5">Nama File Backup</th>
                        <th class="py-3 px-5">Ukuran</th>
                        <th class="py-3 px-5">Format</th>
                        <th class="py-3 px-5">Waktu Pencadangan</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1A2820]">
                    @foreach($backups as $b)
                        <tr class="hover:bg-[#142019]/60 transition-colors">
                            <td class="py-3.5 px-5 font-mono text-xs font-medium text-emerald-400">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="truncate max-w-xs sm:max-w-md">{{ $b['filename'] }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-gray-300 font-medium">
                                {{ $b['size'] }}
                            </td>
                            <td class="py-3.5 px-5">
                                @if(str_ends_with($b['filename'], '.gz'))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        GZIP (Hemat Kuota)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-700/20 text-gray-400 border border-gray-600/30">
                                        SQL Mentah
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs text-gray-400">
                                {{ $b['created_at'] }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    {{-- Download --}}
                                    <a href="{{ route('admin.backups.download', $b['filename']) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/20 transition-colors"
                                       title="Unduh File">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Unduh
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.backups.destroy', $b['filename']) }}" method="POST"
                                          onsubmit="return confirm('Hapus file backup {{ $b['filename'] }}? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg text-xs font-medium text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                                title="Hapus File">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

