@extends('layouts.admin')

@section('title', 'Recycle Bin')
@section('page-title', 'Recycle Bin')
@section('page-description', 'Pulihkan dokumen yang terhapus atau hapus secara permanen')

@section('content')

{{-- Filter & Search --}}
<form method="GET" id="filter-form" class="flex flex-wrap items-center gap-3 mb-6">
    <div class="relative w-full sm:w-72 md:w-80">
        <svg class="w-4 h-4 text-gray-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="search-input" type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari judul dokumen di Recycle Bin..."
               class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl pl-10 pr-4 py-2.5 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500 transition-colors">
    </div>
    <button type="submit" id="search-btn" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-md shadow-emerald-500/20">
        Cari
    </button>
    @if(request('search'))
        <a href="{{ route('admin.soal.trash') }}"
           class="px-4 py-2.5 rounded-xl text-sm font-medium transition-colors border"
           style="background:#1A2820;border-color:#25382D;color:#9CA3AF;"
           onmouseover="this.style.background='#25382D';this.style.color='#d1d5db'"
           onmouseout="this.style.background='#1A2820';this.style.color='#9CA3AF'">
            Reset
        </a>
    @endif
    <a href="{{ route('admin.soal.index') }}" id="back-btn"
       class="bg-[#0F1713] hover:bg-gray-800 border border-[#1A2820] text-gray-300 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors ml-auto">
        Kembali ke Arsip Dokumen
    </a>
</form>

{{-- Table --}}
<div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl overflow-hidden">
    @if($soals->isEmpty())
        <div class="py-16 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <p class="text-lg font-medium">Recycle Bin kosong</p>
            <p class="text-sm mt-1">Belum ada dokumen yang dihapus.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Dokumen (Dihapus Pada)</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-right">Total File</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @foreach($soals as $soal)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-100 truncate max-w-xs">{{ $soal->title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-red-400">Dihapus: {{ $soal->deleted_at->format('d M Y H:i') }}</span>
                                        @if(auth()->user()->isSuperAdmin())
                                            <span class="text-xs text-gray-600">· Oleh: {{ $soal->admin->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($soal->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $soal->category->color }}25; color: {{ $soal->category->color }}; border: 1px solid {{ $soal->category->color }}40;">
                                    {{ $soal->category->name }}
                                </span>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-gray-300">{{ $soal->attachments->count() }} File</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Restore --}}
                                <form method="POST" action="{{ route('admin.soal.restore', $soal->id) }}" class="inline"
                                      onsubmit="confirmAction(event, 'Yakin ingin memulihkan Dokumen ini ke Arsip Dokumen?', 'Ya, Pulihkan', false)">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            title="Pulihkan Dokumen"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-green-500/10 hover:bg-green-500/20 text-green-400 rounded-lg text-xs font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Restore
                                    </button>
                                </form>
                                {{-- Force Delete --}}
                                <form method="POST" action="{{ route('admin.soal.force-delete', $soal->id) }}" class="inline"
                                      onsubmit="confirmAction(event, 'PERINGATAN: Menghapus permanen akan menghilangkan dokumen dan SELURUH FILE fisiknya dari server! Yakin ingin melanjutkan?', 'Ya, Hapus Permanen', true)">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            title="Hapus Permanen"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-xs font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($soals->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $soals->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

@endsection

