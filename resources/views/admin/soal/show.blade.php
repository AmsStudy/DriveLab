@extends('layouts.admin')
@section('title', 'Detail Dokumen')
@section('page-title', 'Detail Dokumen')
@section('page-description', $soal->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left Column - Soal Info --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Info Card --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-white font-semibold">{{ $soal->title }}</h2>
                @php $st = $soal->access_status; @endphp
                <span class="flex-shrink-0 text-xs px-2 py-1 rounded-full
                    {{ $st === 'active' ? 'bg-green-500/15 text-green-400' : '' }}
                    {{ $st === 'inactive' ? 'bg-gray-500/15 text-gray-400' : '' }}
                    {{ $st === 'expired' ? 'bg-red-500/15 text-red-400' : '' }}
                    {{ $st === 'scheduled' ? 'bg-yellow-500/15 text-yellow-400' : '' }}">
                    {{ ['active'=>'Aktif','inactive'=>'Nonaktif','expired'=>'Expired','scheduled'=>'Terjadwal'][$st] }}
                </span>
            </div>

            @if($soal->description)
                <p class="text-sm text-gray-400">{{ $soal->description }}</p>
            @endif

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Kategori</span>
                    <span class="text-gray-300">{{ $soal->category->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Admin</span>
                    <span class="text-gray-300">{{ $soal->admin->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total File</span>
                    <span class="text-gray-300">{{ $soal->attachments->count() }} File Terlampir</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Masa Aktif</span>
                    <span class="text-gray-300">
                        @if($soal->access_type === 'lifetime')
                            ∞ Selamanya
                        @else
                            {{ optional($soal->active_from)->format('d M Y') }} — {{ optional($soal->active_until)->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Akses</span>
                    <span class="text-white font-semibold">{{ number_format($soal->access_count) }}×</span>
                </div>
            </div>

            {{-- File List --}}
            @if($soal->attachments->count() > 0)
            <div class="pt-4 border-t border-gray-800 space-y-3">
                <p class="text-xs text-gray-500 font-medium">File Lampiran (Bisa Diunduh)</p>
                @foreach($soal->attachments as $attachment)
                    <div class="flex items-center justify-between bg-gray-800/50 p-3 rounded-xl border border-gray-700">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xs font-bold text-pink-400 bg-pink-500/10 px-2 py-1 rounded-md">{{ strtoupper($attachment->file_type) ?: 'FILE' }}</span>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-200 truncate">{{ $attachment->file_original_name }}</p>
                                <p class="text-xs text-gray-500">{{ $attachment->file_size_formatted }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.soal.download', $attachment->id) }}" class="p-2 text-pink-400 hover:bg-pink-500/20 rounded-lg transition-colors" title="Download File">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
            @endif

            <div class="pt-2 border-t border-gray-800 space-y-2">
                <p class="text-xs text-gray-500 mb-2">Link Dokumen</p>
                <div class="flex items-center gap-2 bg-gray-800 rounded-xl px-3 py-2">
                    <input type="text" readonly
                           value="{{ route('soal.access', $soal->unique_token) }}"
                           class="flex-1 text-xs text-pink-400 bg-transparent focus:outline-none truncate"
                           id="soal-link">
                    <button onclick="navigator.clipboard.writeText(document.getElementById('soal-link').value).then(()=>alert('Link disalin!'))"
                            id="copy-soal-link-btn"
                            class="text-gray-500 hover:text-pink-400 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.soal.edit', $soal) }}" id="edit-soal-detail-btn"
                   class="flex-1 text-center bg-pink-600/20 hover:bg-pink-600/30 text-pink-400 px-4 py-2 rounded-xl text-sm transition-colors">Edit</a>
                <form method="POST" action="{{ route('admin.soal.toggle', $soal) }}" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" id="toggle-soal-detail-btn"
                            class="w-full {{ $soal->is_active ? 'bg-orange-500/20 hover:bg-orange-500/30 text-orange-400' : 'bg-green-500/20 hover:bg-green-500/30 text-green-400' }} px-4 py-2 rounded-xl text-sm transition-colors">
                        {{ $soal->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Right Column - Access Logs --}}
    <div class="lg:col-span-2">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="font-semibold text-white">Riwayat Akses</h3>
            </div>
            @if($logs->isEmpty())
                <div class="py-12 text-center text-gray-500 text-sm">Belum ada riwayat akses.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($logs as $log)
                            <tr class="hover:bg-gray-800/30">
                                <td class="px-6 py-3 text-gray-400 text-xs">{{ $log->accessed_at->format('d M Y H:i:s') }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $log->status === 'success' ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400' }}">
                                        {{ $log->status === 'success' ? 'Berhasil' : 'Gagal' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ ['wrong_password'=>'Password salah','expired'=>'Sudah expired','inactive'=>'Dokumen nonaktif'][$log->reason] ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-800">{{ $logs->links() }}</div>
                @endif
            @endif
        </div>

        {{-- Submissions Table --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-white">Riwayat Pengumpulan Tugas</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Satu baris per NIM — upload ulang mahasiswa menimpa file sebelumnya.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if(!$submissions->isEmpty())
                    <a href="{{ route('admin.soal.download-submissions-zip', $soal) }}" class="bg-pink-500/20 hover:bg-pink-500/30 text-pink-400 px-3 py-1.5 rounded-lg text-sm transition-colors border border-pink-500/30 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Semua (ZIP)
                    </a>
                    @endif
                    <a href="{{ route('admin.soal.export-submissions', $soal) }}" class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 px-3 py-1.5 rounded-lg text-sm transition-colors border border-blue-500/30 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Rekap (CSV)
                    </a>
                </div>
            </div>
            @if($submissions->isEmpty())
                <div class="py-12 text-center text-gray-500 text-sm">Belum ada tugas yang dikumpulkan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-800 text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">NIM</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">File</th>
                                <th class="px-4 py-3 text-left">Waktu Pengumpulan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($submissions as $sub)
                            <tr class="hover:bg-gray-800/30">
                                <td class="px-6 py-3 text-gray-300 font-mono text-xs">{{ $sub->nim }}</td>
                                <td class="px-4 py-3 text-gray-300 text-xs">{{ $sub->nama }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $sub->file_original_name }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $sub->created_at->format('d M Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.soal.download-submissions', $sub->id) }}" class="text-blue-400 hover:text-blue-300" title="Download">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

