@extends('layouts.admin')

@section('title', 'Daftar Dokumen')
@section('page-title', 'Daftar Dokumen')
@section('page-description', 'Kelola semua dokumen yang telah diunggah')

@section('content')

{{-- Action Toolbar: Search, Filters, and Tambah Dokumen on the same line --}}
<div class="flex flex-wrap items-center gap-2.5 sm:gap-3 mb-6">
    <form method="GET" id="filter-form" class="flex flex-wrap items-center gap-2.5 sm:gap-3">
        {{-- Search Input (Compact, not too long, so space beside it is filled) --}}
        <div class="relative w-48 sm:w-56 md:w-60 flex-shrink-0">
            <svg class="w-4 h-4 text-gray-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="search-input" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul dokumen..."
                   class="w-full border text-gray-100 rounded-xl pr-3.5 py-2 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500 transition-colors"
                   style="background-color:#090D0B;border-color:#1A2820;padding-left:2.5rem !important;">
        </div>

        {{-- Category Select (Single chevron via Tailwind Forms) --}}
        <select id="category-filter" name="category"
                class="border text-gray-300 rounded-xl pl-3.5 pr-10 py-2 text-sm focus:outline-none focus:border-emerald-500 transition-colors"
                style="background-color:#090D0B;border-color:#1A2820;">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        {{-- Status Select (Single chevron via Tailwind Forms) --}}
        <select id="status-filter" name="status"
                class="border text-gray-300 rounded-xl pl-3.5 pr-10 py-2 text-sm focus:outline-none focus:border-emerald-500 transition-colors"
                style="background-color:#090D0B;border-color:#1A2820;">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>

        <button type="submit" id="search-btn"
                class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-colors flex-shrink-0"
                style="background:linear-gradient(135deg,#10B981,#0D9488);">
            Cari
        </button>

        @if(request()->hasAny(['search','category','status']))
            <a href="{{ route('admin.soal.index') }}"
               class="px-3 py-2 rounded-xl text-sm font-medium transition-colors border flex-shrink-0"
               style="background:#1A2820;border-color:#25382D;color:#9CA3AF;"
               onmouseover="this.style.background='#25382D';this.style.color='#d1d5db'"
               onmouseout="this.style.background='#1A2820';this.style.color='#9CA3AF'">
                Reset
            </a>
        @endif
    </form>

    {{-- Tambah Dokumen (Direct link, placed beside the filter items) --}}
    <a href="{{ route('admin.soal.create') }}" id="add-soal-btn"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all shadow-md shadow-emerald-500/20 whitespace-nowrap flex-shrink-0"
       style="background:linear-gradient(135deg,#10B981,#0D9488);"
       onmouseover="this.style.boxShadow='0 6px 18px rgba(16,185,129,.35)'"
       onmouseout="this.style.boxShadow='0 4px 12px rgba(16,185,129,.2)'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Dokumen</span>
    </a>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden shadow-xl" style="background:#0F1713;border:1px solid #1A2820;">
    @if($soals->isEmpty())
        <div class="py-16 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-lg font-medium">Belum ada dokumen</p>
            <p class="text-sm mt-1">Tambahkan dokumen pertama Anda sekarang.</p>
            <button type="button" onclick="openCreateModal()" class="mt-4 inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-lg shadow-emerald-600/20">
                + Tambah Dokumen
            </button>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Dokumen</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Masa Aktif</th>
                        <th class="px-4 py-3 text-center">Tugas</th>
                        <th class="px-4 py-3 text-right">Dilihat</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @foreach($soals as $soal)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                {{-- File Type Icon --}}
                                <div class="w-9 h-9 rounded-lg bg-gray-800 flex items-center justify-center flex-shrink-0 mt-0.5 border border-gray-700/50">
                                    @if($soal->attachments->count() > 0)
                                        <span class="text-emerald-400 text-[10px] font-bold font-mono">{{ $soal->attachments->count() }} ITEM</span>
                                    @elseif($soal->content_text)
                                        <span class="text-amber-400 text-[10px] font-bold font-mono">TEKS</span>
                                    @else
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.soal.show', $soal) }}" class="font-medium text-gray-100 hover:text-emerald-400 transition-colors truncate block max-w-xs">{{ $soal->title }}</a>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if(auth()->user()->isSuperAdmin())
                                            <span class="text-xs text-gray-500">{{ $soal->admin->name }}</span>
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
                        <td class="px-4 py-4">
                            @php $accessStatus = $soal->access_status; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $accessStatus === 'active' ? 'bg-green-500/15 text-green-400' : '' }}
                                {{ $accessStatus === 'inactive' ? 'bg-gray-500/15 text-gray-400' : '' }}
                                {{ $accessStatus === 'expired' ? 'bg-red-500/15 text-red-400' : '' }}
                                {{ $accessStatus === 'scheduled' ? 'bg-yellow-500/15 text-yellow-400' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $accessStatus === 'active' ? 'bg-green-500' : '' }}
                                    {{ $accessStatus === 'inactive' ? 'bg-gray-500' : '' }}
                                    {{ $accessStatus === 'expired' ? 'bg-red-500' : '' }}
                                    {{ $accessStatus === 'scheduled' ? 'bg-yellow-500' : '' }}"></span>
                                {{ ['active'=>'Aktif','inactive'=>'Nonaktif','expired'=>'Expired','scheduled'=>'Terjadwal'][$accessStatus] ?? $accessStatus }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-500">
                            @if($soal->access_type === 'lifetime')
                                <span class="text-teal-400 font-medium">∞ Selamanya</span>
                            @else
                                <div>
                                    <p>{{ optional($soal->active_from)->format('d M Y H:i') ?? '—' }}</p>
                                    <p>s/d {{ optional($soal->active_until)->format('d M Y H:i') ?? '—' }}</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($soal->is_submission_enabled)
                                <a href="{{ route('admin.soal.show', $soal) }}" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-teal-500/15 text-teal-400 border border-teal-500/25 hover:bg-teal-500/25 transition-colors" title="Lihat Pengumpulan Tugas">
                                    {{ $soal->submissions_count }} Tugas
                                </a>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-gray-300 font-medium font-mono text-xs">{{ number_format($soal->access_count) }}×</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                {{-- Copy Link --}}
                                <button onclick="copyLink('{{ route('soal.access', $soal->unique_token) }}')"
                                        id="copy-link-{{ $soal->id }}"
                                        title="Salin Link"
                                        class="p-2 rounded-lg text-gray-500 hover:text-emerald-400 hover:bg-emerald-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                {{-- Preview (Simulasikan Mhs) --}}
                                <a href="{{ route('admin.soal.preview', $soal) }}" target="_blank"
                                   id="preview-soal-{{ $soal->id }}"
                                   title="Simulasi / Preview Dokumen"
                                   class="p-2 rounded-lg text-gray-500 hover:text-green-400 hover:bg-green-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </a>
                                {{-- Detail & Pengumpulan --}}
                                <a href="{{ route('admin.soal.show', $soal) }}"
                                   id="show-soal-{{ $soal->id }}"
                                   title="Detail & Pengumpulan Tugas"
                                   class="p-2 rounded-lg text-gray-500 hover:text-blue-400 hover:bg-blue-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('admin.soal.edit', $soal) }}"
                                   id="edit-soal-{{ $soal->id }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-yellow-400 hover:bg-yellow-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                {{-- Toggle Active --}}
                                <form method="POST" action="{{ route('admin.soal.toggle', $soal) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            id="toggle-soal-{{ $soal->id }}"
                                            title="{{ $soal->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            class="p-2 rounded-lg text-gray-500 transition-colors {{ $soal->is_active ? 'hover:text-orange-400 hover:bg-orange-500/10' : 'hover:text-green-400 hover:bg-green-500/10' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $soal->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                        </svg>
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('admin.soal.destroy', $soal) }}" class="inline"
                                      onsubmit="confirmAction(event, 'Yakin ingin menghapus dokumen ini?', 'Ya, Hapus', true)">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            id="delete-soal-{{ $soal->id }}"
                                            class="p-2 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-colors">
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

        {{-- Pagination --}}
        @if($soals->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $soals->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#0F1713',
                color: '#E5E7EB',
                icon: 'success',
                title: 'Tautan berhasil disalin!',
                customClass: { popup: 'border border-emerald-500/30' }
            });
        } else {
            alert('Tautan berhasil disalin!');
        }
    });
}
</script>
@endpush
