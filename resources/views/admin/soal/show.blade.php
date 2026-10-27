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

            {{-- Soal Text Content --}}
            @if($soal->content_text)
            <div class="pt-4 border-t border-gray-800 space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Teks / Konten Soal
                    </p>
                    <button type="button" onclick="navigator.clipboard.writeText({{ json_encode($soal->content_text) }}).then(()=>alert('Teks soal disalin!'))" class="text-xs text-pink-400 hover:text-pink-300">Salin</button>
                </div>
                <div class="p-3 bg-gray-950/70 rounded-xl border border-gray-800 text-xs text-gray-300 font-mono max-h-48 overflow-y-auto whitespace-pre-wrap leading-relaxed">
{{ $soal->content_text }}
                </div>
            </div>
            @endif

            {{-- File List --}}
            @if($soal->attachments->count() > 0)
            <div class="pt-4 border-t border-gray-800 space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Lampiran & Tautan ({{ $soal->attachments->count() }})</p>
                    <span class="text-[11px] text-gray-500">Maks. 15 Item</span>
                </div>
                @foreach($soal->attachments as $attachment)
                    @if($attachment->isLink())
                        <div class="flex items-center justify-between bg-gray-800/50 p-3 rounded-xl border border-purple-500/20">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xs font-bold text-purple-400 bg-purple-500/10 px-2 py-1 rounded-md">LINK</span>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-200 truncate">{{ $attachment->file_original_name ?: 'Tautan Eksternal' }}</p>
                                    <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-purple-400 hover:underline truncate block">{{ $attachment->link_url }}</a>
                                </div>
                            </div>
                            <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer" class="p-2 text-purple-400 hover:bg-purple-500/20 rounded-lg transition-colors flex-shrink-0" title="Buka Tautan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @elseif($attachment->isText())
                        <div class="bg-gray-800/50 p-3 rounded-xl border border-amber-500/20 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-1 rounded-md">TEKS</span>
                                    <span class="text-sm text-gray-200 truncate">{{ $attachment->file_original_name ?: 'Lampiran Teks' }}</span>
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText({{ json_encode($attachment->content_text) }}).then(()=>alert('Teks disalin!'))" class="text-xs text-amber-400 hover:text-amber-300">Salin</button>
                            </div>
                            <p class="text-xs text-gray-400 font-mono whitespace-pre-wrap max-h-24 overflow-y-auto bg-gray-900/60 p-2 rounded-lg border border-gray-800">{{ Str::limit($attachment->content_text, 250) }}</p>
                        </div>
                    @else
                        {{-- File --}}
                        <div class="flex items-center justify-between bg-gray-800/50 p-3 rounded-xl border border-gray-700">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xs font-bold text-pink-400 bg-pink-500/10 px-2 py-1 rounded-md">{{ strtoupper($attachment->file_type) ?: 'FILE' }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-200 truncate">{{ $attachment->file_original_name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-gray-500">{{ $attachment->file_size_formatted }}</span>
                                        @if($attachment->is_downloadable)
                                            <span class="text-[10px] bg-green-500/15 text-green-400 px-1.5 py-0.5 rounded font-medium">Bisa Diunduh</span>
                                        @else
                                            <span class="text-[10px] bg-yellow-500/15 text-yellow-400 px-1.5 py-0.5 rounded font-medium">Hanya Preview</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.soal.download', $attachment->id) }}" class="p-2 text-pink-400 hover:bg-pink-500/20 rounded-lg transition-colors flex-shrink-0" title="Download File (Admin)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    @endif
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
                                <th class="px-4 py-3 text-left">Tipe</th>
                                <th class="px-4 py-3 text-left">Jawaban / Berkas</th>
                                <th class="px-4 py-3 text-left">Waktu Pengumpulan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($submissions as $sub)
                            <tr class="hover:bg-gray-800/30">
                                <td class="px-6 py-3 text-gray-300 font-mono text-xs">{{ $sub->nim }}</td>
                                <td class="px-4 py-3 text-gray-300 text-xs font-medium">{{ $sub->nama }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($sub->isLink())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-500/15 text-purple-400 border border-purple-500/20">Link</span>
                                    @elseif($sub->isText())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/20">Teks</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/15 text-blue-400 border border-blue-500/20">File</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($sub->isLink())
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-300 hover:underline max-w-xs truncate inline-flex items-center gap-1 font-mono">
                                            {{ Str::limit($sub->link_url, 40) }}
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @elseif($sub->isText())
                                        <button type="button" onclick="showTextModal({{ json_encode($sub->nama) }}, {{ json_encode($sub->nim) }}, {{ json_encode($sub->text_content) }})" class="text-amber-400 hover:text-amber-300 font-mono text-xs max-w-xs truncate text-left block hover:underline">
                                            {{ Str::limit($sub->text_content, 45) }}
                                        </button>
                                    @else
                                        <span class="text-gray-300">{{ $sub->file_original_name }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $sub->created_at->format('d M Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($sub->isLink())
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer" class="text-purple-400 hover:text-purple-300 p-1.5 inline-block hover:bg-purple-500/10 rounded-lg transition-colors" title="Buka Link">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @elseif($sub->isText())
                                        <button type="button" onclick="showTextModal({{ json_encode($sub->nama) }}, {{ json_encode($sub->nim) }}, {{ json_encode($sub->text_content) }})" class="text-amber-400 hover:text-amber-300 p-1.5 inline-block hover:bg-amber-500/10 rounded-lg transition-colors" title="Lihat Teks Lengkap">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.soal.download-submissions', $sub->id) }}" class="text-blue-400 hover:text-blue-300 p-1.5 inline-block hover:bg-blue-500/10 rounded-lg transition-colors" title="Download File">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    @endif
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

{{-- Text Submission Preview Modal --}}
<div id="textSubmissionModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
        <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
            <div>
                <h3 class="text-white font-semibold text-base" id="modalStudentName">Jawaban Mahasiswa</h3>
                <p class="text-xs text-gray-500 font-mono mt-0.5" id="modalStudentNim"></p>
            </div>
            <button onclick="closeTextModal()" class="text-gray-400 hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <pre class="bg-gray-950 p-4 rounded-xl border border-gray-800 text-gray-200 font-mono text-xs whitespace-pre-wrap leading-relaxed select-text" id="modalTextContent"></pre>
        </div>
        <div class="px-6 py-3 border-t border-gray-800 flex justify-end gap-3 bg-gray-900/50">
            <button type="button" onclick="copyModalText()" class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Salin Teks
            </button>
            <button type="button" onclick="closeTextModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl text-xs font-semibold transition-colors">Tutup</button>
        </div>
    </div>
</div>

<script>
let currentModalText = '';
function showTextModal(name, nim, content) {
    document.getElementById('modalStudentName').textContent = name;
    document.getElementById('modalStudentNim').textContent = 'NIM: ' + nim;
    document.getElementById('modalTextContent').textContent = content;
    currentModalText = content;
    document.getElementById('textSubmissionModal').classList.remove('hidden');
}
function closeTextModal() {
    document.getElementById('textSubmissionModal').classList.add('hidden');
}
function copyModalText() {
    navigator.clipboard.writeText(currentModalText).then(() => {
        alert('Teks jawaban berhasil disalin!');
    });
}
</script>
@endsection

