@extends('layouts.admin')

@section('title', 'Daftar Dokumen')
@section('page-title', 'Daftar Dokumen')
@section('page-description', 'Kelola semua dokumen yang telah diunggah')

@section('content')

{{-- Action Toolbar: Search, Filters, and Tambah Dokumen on the same line --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <form method="GET" id="filter-form" class="flex flex-wrap items-center gap-2.5 sm:gap-3 flex-1">
        {{-- Search Input (Compact, not too long, so space beside it is filled) --}}
        <div class="relative w-full sm:w-56 md:w-60 flex-shrink-0">
            <svg class="w-4 h-4 text-gray-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="search-input" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul dokumen..."
                   class="w-full border text-gray-100 rounded-xl pr-3.5 py-2 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500 transition-colors"
                   style="background-color:#090D0B;border-color:#1A2820;padding-left:2.5rem !important;">
        </div>

        {{-- Category Select --}}
        <select id="category-filter" name="category"
                class="cyber-select text-sm pl-3.5 pr-10 py-2">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        {{-- Status Select --}}
        <select id="status-filter" name="status"
                class="cyber-select text-sm pl-3.5 pr-10 py-2">
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

    {{-- Tambah Dokumen (Opens Pop Up Modal) --}}
    <button type="button" onclick="openCreateModal()" id="add-soal-btn"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all shadow-md shadow-emerald-500/20 whitespace-nowrap flex-shrink-0 cursor-pointer"
       style="background:linear-gradient(135deg,#10B981,#0D9488);"
       onmouseover="this.style.boxShadow='0 6px 18px rgba(16,185,129,.35)'"
       onmouseout="this.style.boxShadow='0 4px 12px rgba(16,185,129,.2)'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Dokumen</span>
    </button>
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
            <table class="w-full text-sm min-w-[700px]">
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
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.soal.show', $soal) }}" class="font-medium text-gray-100 hover:text-emerald-400 transition-colors truncate block max-w-xs">{{ $soal->title }}</a>
                                        <button type="button"
                                                onclick="copyToClipboard('{{ $soal->kode_unik }}', 'Kode Dokumen ({{ $soal->kode_unik }}) disalin!')"
                                                title="Klik untuk salin kode akses dokumen"
                                                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-[#142019] border border-[#1E3327] hover:border-emerald-500/50 text-emerald-300 font-mono text-[10px] font-semibold transition-all flex-shrink-0">
                                            <span>{{ $soal->kode_unik }}</span>
                                            <svg class="w-3 h-3 text-gray-400 hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
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
                                <button onclick="copyLink('{{ route('soal.access', $soal->slug ?: $soal->unique_token) }}')"
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
                                <button type="button"
                                        onclick="openEditModalForSoal({{ $soal->id }})"
                                        id="edit-soal-{{ $soal->id }}"
                                        title="Edit Dokumen"
                                        class="p-2 rounded-lg text-gray-500 hover:text-yellow-400 hover:bg-yellow-500/10 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
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

{{-- ─── Pop Up Modal: Tambah Dokumen Baru ─── --}}
<div id="create-modal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-[#0F1713] border border-[#1A2820] rounded-2xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl z-10 overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#1A2820] bg-[#0F1713] flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white leading-tight">Tambah Dokumen Baru</h2>
                    <p class="text-xs text-gray-400">Unggah materi, teks soal, tautan referensi, dan atur pengumpulan tugas</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition-colors" title="Tutup Modal (Esc)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.soal.store') }}" enctype="multipart/form-data" id="create-soal-modal-form" class="flex flex-col flex-1 min-h-0">
            @csrf

            {{-- Modal Body (Scrollable form content, preserving exact structure from create.blade.php) --}}
            <div class="overflow-y-auto px-6 py-6 space-y-6 flex-1">

                {{-- ─── 1. INFORMASI DOKUMEN ─── --}}
                <div>
                    <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Informasi Dokumen
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="modal_title" class="block text-sm font-medium text-gray-300 mb-2">Judul Dokumen / Soal <span class="text-red-400">*</span></label>
                            <input type="text" id="modal_title" name="title" value="{{ old('title') }}" required
                                   placeholder="Contoh: Modul 3 — Konfigurasi Routing RIP & OSPF"
                                   class="w-full bg-[#090E0B] border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                            @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_kode_unik" class="block text-sm font-medium text-gray-300 mb-2">Kode Unik Akses <span class="text-red-400">*</span></label>
                                <input type="text" id="modal_kode_unik" name="kode_unik" 
                                       value="{{ old('kode_unik', 'AMSSOAL-' . Str::upper(Str::random(5))) }}" required
                                       class="w-full bg-[#090E0B] border {{ $errors->has('kode_unik') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                                <p class="mt-1 text-xs text-gray-500">Kode yang dimasukkan mahasiswa untuk mengakses dokumen.</p>
                                @error('kode_unik') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="modal_category_id" class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                                <select id="modal_category_id" name="category_id"
                                        class="cyber-select w-full pl-4 pr-10 py-3 text-sm">
                                    <option value="">— Pilih Kategori —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_password" class="block text-sm font-medium text-gray-300 mb-2">Password Akses <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <input type="password" id="modal_password" name="password" required minlength="4"
                                           placeholder="Minimal 4 karakter"
                                           class="w-full bg-[#090E0B] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-emerald-500">
                                    <button type="button" onclick="const p = document.getElementById('modal_password'); p.type = p.type === 'password' ? 'text' : 'password'"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                                @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="modal_description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Singkat</label>
                                <input type="text" id="modal_description" name="description" value="{{ old('description') }}"
                                       placeholder="Catatan / deskripsi umum..."
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── 2. KONTEN SOAL BERUPA TEKS (OPSIONAL) ─── --}}
                <div class="pt-5 border-t border-[#1A2820]">
                    <h3 class="text-sm font-bold text-blue-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Soal / Materi Berupa Teks Langsung <span class="text-xs text-gray-500 font-normal lowercase">(opsional)</span>
                    </h3>
                    <p class="text-xs text-gray-400 mb-3">Tulis butir-butir soal atau instruksi praktikum langsung di sini jika ingin ditampilkan di layar mahasiswa tanpa harus mendownload file.</p>
                    <textarea id="modal_content_text" name="content_text" rows="5"
                              placeholder="Ketik isi soal atau instruksi langsung di sini... (Mendukung paragraf, baris baru, dan studi kasus)"
                              class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 font-mono">{{ old('content_text') }}</textarea>
                </div>

                {{-- ─── 3. TAUTAN / LINK MATERI (OPSIONAL) ─── --}}
                <div class="pt-5 border-t border-[#1A2820]">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-sm font-bold text-purple-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span> Tautan / Link Materi & Soal <span class="text-xs text-gray-500 font-normal lowercase">(opsional)</span>
                        </h3>
                        <button type="button" onclick="addModalLinkRow()" class="text-xs bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 px-3 py-1.5 rounded-lg border border-purple-500/30 transition-colors flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Link
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">Tambahkan link eksternal (Google Drive, Repository GitHub, Video Tutorial, Figma, dll).</p>

                    <div id="modal-links-container" class="space-y-2">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 link-row bg-gray-800/40 p-3 rounded-xl border border-[#1A2820]">
                            <div class="md:col-span-4">
                                <input type="text" name="links[0][title]" placeholder="Label Link (misal: GitHub Repo)"
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="md:col-span-7">
                                <input type="url" name="links[0][url]" placeholder="https://..."
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="md:col-span-1 flex items-center justify-center">
                                <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5 cursor-pointer" title="Hapus Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── 4. UPLOAD FILE LAMPIRAN (MAKS. 15 FILE) ─── --}}
                <div class="pt-5 border-t border-[#1A2820]">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-2">
                        <h3 class="text-sm font-bold text-green-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> File Lampiran Dokumen <span class="text-xs text-gray-500 font-normal lowercase">(Maksimal 15 File, 5MB per file)</span>
                        </h3>
                        
                        {{-- Master Download Toggle --}}
                        <div class="flex items-center gap-2 bg-gray-800/80 px-3 py-1.5 rounded-xl border border-gray-700">
                            <input type="hidden" name="is_downloadable" value="0">
                            <input type="checkbox" id="modal_master_downloadable" name="is_downloadable" value="1" checked
                                   onchange="toggleModalAllFileDownloads(this.checked)"
                                   class="accent-emerald-500 bg-gray-700 border-gray-600 w-4 h-4 rounded">
                            <label for="modal_master_downloadable" class="text-xs text-gray-200 cursor-pointer">
                                Izinkan Unduh Semua File
                            </label>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">Dukungan format: PDF, Word, Excel, PPT, Gambar, ZIP/RAR, dan Cisco Packet Tracer (.pkt). Anda dapat mengatur izin download per-file di bawah.</p>

                    <div id="modal-drop-zone" class="border-2 border-dashed border-gray-700 hover:border-emerald-500 rounded-xl p-6 text-center cursor-pointer transition-colors group"
                         onclick="document.getElementById('modal_files').click()">
                        <svg class="w-8 h-8 mx-auto text-gray-600 group-hover:text-emerald-500 mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-sm text-gray-300 font-medium">Klik atau drop file lampiran di sini (maks. 15 file)</p>
                        <p class="text-xs text-gray-500 mt-1">Bisa pilih banyak file sekaligus (hingga 15 file, 5MB/file)</p>
                    </div>
                    <input type="file" id="modal_files" name="files[]" multiple class="hidden"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.pkt"
                           onchange="handleModalFilesSelected(this)">
                    @error('files') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    @error('files.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                    {{-- Selected Files List with Per-File Download Toggle --}}
                    <div id="modal-selected-files-container" class="mt-3 space-y-2 hidden">
                        <p class="text-xs text-gray-400 font-semibold mb-1">Daftar File yang Dipilih & Hak Unduh Per-File:</p>
                        <div id="modal-selected-files-list" class="space-y-2"></div>
                    </div>
                </div>

                {{-- ─── 5. MASA AKTIF DOKUMEN ─── --}}
                <div class="pt-5 border-t border-[#1A2820]">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Masa Aktif Akses Dokumen</label>
                    <div class="flex gap-5 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="lifetime" checked
                                   class="accent-emerald-500 bg-[#090E0B] border-gray-600"
                                   onchange="document.getElementById('modal-schedule-fields').classList.add('hidden')">
                            <span class="text-sm text-gray-300">Selamanya (Tanpa Batas Waktu)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="scheduled"
                                   class="accent-emerald-500 bg-[#090E0B] border-gray-600"
                                   onchange="document.getElementById('modal-schedule-fields').classList.remove('hidden')">
                            <span class="text-sm text-gray-300">Terjadwal (Waktu Tertentu)</span>
                        </label>
                    </div>
                    <div id="modal-schedule-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-[#090E0B]/40 p-4 rounded-xl border border-[#1A2820]">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Mulai Aktif</label>
                            <input type="datetime-local" name="active_from"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Berakhir</label>
                            <input type="datetime-local" name="active_until"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- ─── 6. FITUR PENGUMPULAN TUGAS (SUBMISSIONS) ─── --}}
                <div class="pt-5 border-t border-[#1A2820]">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Fitur Pengumpulan Tugas bagi Mahasiswa</label>
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input type="checkbox" name="is_submission_enabled" id="modal_is_submission_enabled" value="1"
                               class="accent-emerald-500 bg-[#090E0B] border-gray-600 w-4 h-4 rounded"
                               onchange="toggleModalSubmissionSection(this.checked)">
                        <span class="text-sm text-gray-300">Aktifkan Form Pengumpulan Tugas pada Soal Ini</span>
                    </label>

                    <div id="modal-submission-options" class="hidden space-y-4 bg-[#090E0B]/40 p-4 rounded-xl border border-[#1A2820]">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                                <input type="datetime-local" id="modal_deadline_at" name="deadline_at"
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Instruksi Tambahan (Opsional)</label>
                                <textarea name="submission_instructions" rows="2"
                                          placeholder="Contoh: Cantumkan link GitHub/Drive publik atau upload laporan PDF."
                                          class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                            </div>
                        </div>

                        {{-- Daftar Pilihan Kelas (Opsional) --}}
                        <div>
                            <label class="block text-xs text-gray-300 font-semibold mb-1">Daftar Pilihan Kelas (Opsional)</label>
                            <input type="text" name="classes" value="{{ old('classes') }}"
                                   placeholder="Contoh: A1, A2, B1, B2 (Pisahkan dengan tanda koma)"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                            <p class="text-[11px] text-gray-400 mt-1">Jika diisi, mahasiswa wajib memilih salah satu kelas saat mengumpulkan tugas. Anda juga dapat mengunduh arsip tugas berdasarkan kelasnya.</p>
                            @error('classes') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tipe Pengumpulan yang Diizinkan --}}
                        <div>
                            <label class="block text-xs text-gray-300 font-semibold mb-2">Metode Pengumpulan yang Diizinkan Mahasiswa <span class="text-red-400">*</span></label>
                            <div class="flex flex-wrap gap-4">
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="submission_types[]" value="file" checked class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-200">📁 File Dokumen</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="submission_types[]" value="link" checked class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-200">🔗 Tautan / Link URL</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="submission_types[]" value="text" checked class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-200">📝 Teks Langsung</span>
                                </label>
                            </div>
                        </div>

                        {{-- Format File yang Diizinkan (Dengan Security Check) --}}
                        <div>
                            <label class="block text-xs text-gray-300 font-semibold mb-1">Format File yang Diizinkan (Jika Mengunggah File)</label>
                            <p class="text-[11px] text-gray-400 mb-2">Setiap file yang diupload mahasiswa wajib lolos security check (anti-shell, anti-executable, anti-spoof MIME).</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                                <label class="flex items-center gap-2 cursor-pointer bg-emerald-500/10 border border-emerald-500/30 p-2 rounded-lg">
                                    <input type="checkbox" name="allowed_extensions[]" value="all" class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-emerald-300 font-bold">Semua Format (Aman)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="pdf" checked class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">PDF (.pdf)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="word" checked class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">Word (.doc, .docx)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="excel" class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">Excel (.xls, .xlsx)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="ppt" class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">PowerPoint (.ppt)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="image" class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">Gambar (.jpg, .png)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="archive" class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">Arsip (.zip, .rar)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                    <input type="checkbox" name="allowed_extensions[]" value="pkt" class="accent-emerald-500 w-4 h-4 rounded">
                                    <span class="text-xs text-gray-300">Packet Tracer (.pkt)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#1A2820] bg-[#0F1713] flex-shrink-0">
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-200 px-4 py-2.5 rounded-xl text-sm transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="save-soal-modal-btn"
                        class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all text-sm shadow-lg shadow-emerald-500/20 cursor-pointer">
                    Simpan Dokumen Soal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── POP UP MODAL EDIT DOKUMEN ─── --}}
<div id="editModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm p-4 items-center justify-center">
    <div class="rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh] border"
         style="background:#0F1713;border-color:#1A2820;">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0" style="border-color:#1A2820;background:#090E0B;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-emerald-400" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        Edit Dokumen Soal
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbarui informasi, berkas lampiran, dan form pengumpulan tugas</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-white p-1.5 rounded-lg transition-colors cursor-pointer" title="Tutup Modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Body / Form --}}
        <form method="POST" action="" enctype="multipart/form-data" id="modal-edit-soal-form" class="overflow-y-auto flex-1 p-6 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" value="index">
            <input type="hidden" name="soal_id" id="edit_modal_soal_id" value="{{ old('soal_id') }}">

            {{-- 1. INFORMASI UTAMA --}}
            <div>
                <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Informasi Dokumen
                </h4>

                <div class="space-y-4">
                    <div>
                        <label for="edit_modal_title" class="block text-xs font-medium text-gray-300 mb-1.5">Judul Dokumen <span class="text-red-400">*</span></label>
                        <input type="text" id="edit_modal_title" name="title" required
                               class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_modal_kode_unik" class="block text-xs font-medium text-gray-300 mb-1.5">Kode Unik Akses <span class="text-red-400">*</span></label>
                            <input type="text" id="edit_modal_kode_unik" name="kode_unik" required
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                            <p class="mt-1 text-[11px] text-gray-500">Boleh diubah, asal unik.</p>
                        </div>

                        <div>
                            <label for="edit_modal_category_id" class="block text-xs font-medium text-gray-300 mb-1.5">Kategori</label>
                            <select id="edit_modal_category_id" name="category_id"
                                    class="cyber-select w-full pl-3.5 pr-10 py-2.5 text-xs cursor-pointer">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_modal_password" class="block text-xs font-medium text-gray-300 mb-1.5">
                                Password Baru <span class="text-gray-500 text-[11px]">(kosongkan jika tidak diubah)</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="edit_modal_password" name="password" minlength="4"
                                       placeholder="Biarkan kosong jika tetap menggunakan password lama"
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 pr-10 text-xs placeholder-gray-500 focus:outline-none focus:border-emerald-500">
                                <button type="button" onclick="const p = document.getElementById('edit_modal_password'); p.type = p.type === 'password' ? 'text' : 'password'"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="edit_modal_description" class="block text-xs font-medium text-gray-300 mb-1.5">Deskripsi Singkat</label>
                            <input type="text" id="edit_modal_description" name="description"
                                   placeholder="Keterangan singkat materi..."
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. SOAL / MATERI BERUPA TEKS LANGSUNG --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Soal / Materi Berupa Teks Langsung <span class="text-[11px] text-gray-500 font-normal lowercase">(opsional)</span>
                </h4>
                <p class="text-[11px] text-gray-400 mb-2.5">Teks soal akan langsung ditampilkan rapi di layar mahasiswa tanpa harus mengunduh file.</p>
                <textarea id="edit_modal_content_text" name="content_text" rows="4"
                          placeholder="Ketik isi soal atau instruksi langsung di sini..."
                          class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500 font-mono"></textarea>
            </div>

            {{-- 3. LAMPIRAN SAAT INI --}}
            <div id="edit-modal-existing-attachments-section" class="pt-5 border-t border-[#1A2820] space-y-3 hidden">
                <div class="flex justify-between items-center">
                    <h4 class="text-xs font-bold text-yellow-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Lampiran Dokumen Saat Ini
                    </h4>
                    <span id="edit-modal-existing-attachments-count" class="text-[11px] text-gray-500">0 Lampiran Tersimpan</span>
                </div>

                <div id="edit-modal-existing-attachments-list" class="bg-[#090E0B]/50 rounded-xl p-3 space-y-2 border border-[#1A2820]"></div>
            </div>

            {{-- 4. TAMBAH FILE BARU --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-xs font-bold text-green-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Tambahkan File Baru <span class="text-[11px] text-gray-500 font-normal lowercase">(Maks. total 15 file)</span>
                    </h4>
                </div>
                <div class="border-2 border-dashed border-gray-700 hover:border-emerald-500 rounded-xl p-5 text-center cursor-pointer transition-colors bg-[#090E0B]/40"
                     onclick="document.getElementById('edit_modal_files').click()">
                    <p class="text-xs text-gray-400">Klik untuk memilih file tambahan (Maks. 5MB per file)</p>
                </div>
                <input type="file" id="edit_modal_files" name="files[]" multiple class="hidden"
                       onchange="handleEditModalNewFiles(this)"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.pkt">

                <div id="edit-modal-new-files-container" class="mt-3 space-y-2 hidden">
                    <p class="text-[11px] text-gray-400 font-semibold mb-1">File Baru yang Akan Diunggah & Hak Unduh:</p>
                    <div id="edit-modal-new-files-list" class="space-y-2"></div>
                </div>
            </div>

            {{-- 5. TAMBAH TAUTAN LINK BARU --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Tambah Tautan / Link Materi Baru
                    </h4>
                    <button type="button" onclick="addEditModalLinkRow()" class="text-xs bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 px-2.5 py-1.5 rounded-lg border border-purple-500/30 transition-colors flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Link
                    </button>
                </div>
                <div id="edit-modal-links-container" class="space-y-2"></div>
            </div>

            {{-- 6. STATUS & MASA AKTIF --}}
            <div class="pt-5 border-t border-[#1A2820] space-y-4">
                <div class="flex items-center justify-between p-3.5 bg-[#090E0B]/50 rounded-xl border border-[#1A2820]">
                    <div>
                        <p class="text-xs font-medium text-gray-200">Status Dokumen</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Aktifkan atau nonaktifkan akses dokumen bagi mahasiswa</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="edit_modal_is_active" name="is_active" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-[#090E0B]/50 rounded-xl border border-[#1A2820]">
                    <div>
                        <p class="text-xs font-medium text-gray-200">Master Hak Unduh Dokumen</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Pengaturan global izin unduh (jika aktif, default file baru diizinkan unduh)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_downloadable" value="0">
                        <input type="checkbox" id="edit_modal_is_downloadable" name="is_downloadable" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-2">Masa Aktif Dokumen</label>
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" id="edit_access_type_lifetime" value="lifetime"
                                   class="text-emerald-500 bg-gray-800 border-gray-600 cursor-pointer"
                                   onchange="document.getElementById('edit-modal-schedule-fields').classList.add('hidden')">
                            <span class="text-xs text-gray-300">Selamanya</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" id="edit_access_type_scheduled" value="scheduled"
                                   class="text-emerald-500 bg-gray-800 border-gray-600 cursor-pointer"
                                   onchange="document.getElementById('edit-modal-schedule-fields').classList.remove('hidden')">
                            <span class="text-xs text-gray-300">Waktu Tertentu</span>
                        </label>
                    </div>
                    <div id="edit-modal-schedule-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-3 bg-[#090E0B]/40 p-3.5 rounded-xl border border-[#1A2820]">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Mulai Aktif</label>
                            <input type="datetime-local" id="edit_modal_active_from" name="active_from"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Berakhir</label>
                            <input type="datetime-local" id="edit_modal_active_until" name="active_until"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. PENGUMPULAN TUGAS --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <label class="block text-xs font-medium text-gray-300 mb-2">Fitur Pengumpulan Tugas bagi Mahasiswa</label>
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="is_submission_enabled" id="edit_modal_is_submission_enabled" value="1" 
                           class="accent-emerald-500 bg-gray-800 border-gray-600 w-4 h-4 rounded cursor-pointer" 
                           onchange="toggleEditModalSubmissionFields(this.checked)">
                    <span class="text-xs text-gray-300">Aktifkan Form Pengumpulan Tugas bagi Mahasiswa</span>
                </label>

                <div id="edit-modal-submission-fields" class="hidden space-y-4 bg-[#090E0B]/40 p-3.5 rounded-xl border border-[#1A2820]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                            <input type="datetime-local" id="edit_modal_deadline_at" name="deadline_at" 
                                    class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">Instruksi Tambahan (Opsional)</label>
                            <textarea id="edit_modal_submission_instructions" name="submission_instructions" rows="2" class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-emerald-500 resize-none" placeholder="Contoh: Format laporan dalam PDF, ukuran max 5MB."></textarea>
                        </div>
                    </div>

                    {{-- Daftar Pilihan Kelas (Opsional) --}}
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-1">Daftar Pilihan Kelas (Opsional)</label>
                        <input type="text" id="edit_modal_classes" name="classes"
                               placeholder="Contoh: A1, A2, B1, B2 (Pisahkan dengan tanda koma)"
                               class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
                        <p class="text-[11px] text-gray-500 mt-1">Jika diisi, mahasiswa wajib memilih salah satu kelas saat mengumpulkan tugas.</p>
                    </div>

                    {{-- Pilihan Tipe Pengumpulan --}}
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-2">Metode Pengumpulan yang Diizinkan Mahasiswa <span class="text-red-400">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_sub_type_file" name="submission_types[]" value="file" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-200">📁 File Dokumen</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_sub_type_link" name="submission_types[]" value="link" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-200">🔗 Tautan / Link URL</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_sub_type_text" name="submission_types[]" value="text" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-200">📝 Teks Langsung</span>
                            </label>
                        </div>
                    </div>

                    {{-- Format File yang Diizinkan --}}
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-1">Format File yang Diizinkan (Jika Mengunggah File) <span class="text-red-400">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <label class="flex items-center gap-2 cursor-pointer bg-emerald-500/10 border border-emerald-500/30 p-2 rounded-lg">
                                <input type="checkbox" id="edit_ext_all" name="allowed_extensions[]" value="all" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-emerald-300 font-bold">Semua Format (Aman)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_pdf" name="allowed_extensions[]" value="pdf" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">PDF (.pdf)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_word" name="allowed_extensions[]" value="word" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">Word (.doc, .docx)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_excel" name="allowed_extensions[]" value="excel" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">Excel (.xls, .xlsx)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_ppt" name="allowed_extensions[]" value="ppt" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">PowerPoint (.ppt)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_image" name="allowed_extensions[]" value="image" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">Gambar (.jpg, .png)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_archive" name="allowed_extensions[]" value="archive" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">Arsip (.zip, .rar)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" id="edit_ext_pkt" name="allowed_extensions[]" value="pkt" class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-xs text-gray-300">Packet Tracer (.pkt)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t flex-shrink-0" style="border-color:#1A2820;background:#090E0B;">
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-200 px-4 py-2.5 rounded-xl text-xs font-semibold cursor-pointer transition-colors">
                Batal
            </button>
            <button type="submit" form="modal-edit-soal-form"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-xs shadow-lg shadow-emerald-500/20 cursor-pointer transition-all">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

@endsection

@php
    $soalsJsonData = $soals->mapWithKeys(function($item) {
        return [$item->id => [
            'id' => $item->id,
            'title' => $item->title,
            'kode_unik' => $item->kode_unik,
            'category_id' => $item->category_id,
            'description' => $item->description ?? '',
            'content_text' => $item->content_text ?? '',
            'is_active' => (bool)$item->is_active,
            'is_downloadable' => (bool)$item->is_downloadable,
            'access_type' => $item->access_type ?? 'lifetime',
            'active_from' => $item->active_from ? $item->active_from->format('Y-m-d\TH:i') : '',
            'active_until' => $item->active_until ? $item->active_until->format('Y-m-d\TH:i') : '',
            'is_submission_enabled' => (bool)$item->is_submission_enabled,
            'deadline_at' => $item->deadline_at ? $item->deadline_at->format('Y-m-d\TH:i') : '',
            'submission_instructions' => $item->submission_instructions ?? '',
            'classes' => is_array($item->classes) ? implode(', ', $item->classes) : ($item->classes ?? ''),
            'submission_types' => is_array($item->submission_types) ? $item->submission_types : ['file'],
            'allowed_extensions' => is_array($item->allowed_extensions) ? $item->allowed_extensions : ['all'],
            'update_url' => route('admin.soal.update', $item),
            'attachments' => $item->attachments->map(function($att) {
                return [
                    'id' => $att->id,
                    'file_name' => $att->file_name ?? $att->file_original_name,
                    'file_original_name' => $att->file_original_name,
                    'file_type' => $att->file_type,
                    'file_size_formatted' => $att->file_size_formatted,
                    'is_link' => $att->isLink(),
                    'is_file' => $att->isFile(),
                    'link_url' => $att->link_url,
                    'is_downloadable' => (bool)$att->is_downloadable,
                ];
            })->values(),
        ]];
    });
@endphp

@push('scripts')
<script>
const soalsData = @json($soalsJsonData);
let editModalExistingCount = 0;
let editModalLinkCount = 0;

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

function openCreateModal() {
    const modal = document.getElementById('create-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeCreateModal() {
    const modal = document.getElementById('create-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function openEditModalForSoal(id) {
    const data = soalsData[id];
    if (!data) return;

    const modal = document.getElementById('editModal');
    const form = document.getElementById('modal-edit-soal-form');
    if (!modal || !form) return;

    form.action = data.update_url;

    // Reset hidden delete_attachments inputs
    form.querySelectorAll('input[name="delete_attachments[]"]').forEach(el => el.remove());

    const soalIdInput = document.getElementById('edit_modal_soal_id');
    if (soalIdInput) soalIdInput.value = id;

    // Basic Fields
    document.getElementById('edit_modal_title').value = data.title || '';
    document.getElementById('edit_modal_kode_unik').value = data.kode_unik || '';
    document.getElementById('edit_modal_category_id').value = data.category_id || '';
    document.getElementById('edit_modal_password').value = '';
    document.getElementById('edit_modal_description').value = data.description || '';
    document.getElementById('edit_modal_content_text').value = data.content_text || '';

    // Status & Downloadable
    document.getElementById('edit_modal_is_active').checked = data.is_active;
    document.getElementById('edit_modal_is_downloadable').checked = data.is_downloadable;

    // Access Type
    if (data.access_type === 'scheduled') {
        document.getElementById('edit_access_type_scheduled').checked = true;
        document.getElementById('edit-modal-schedule-fields').classList.remove('hidden');
    } else {
        document.getElementById('edit_access_type_lifetime').checked = true;
        document.getElementById('edit-modal-schedule-fields').classList.add('hidden');
    }
    document.getElementById('edit_modal_active_from').value = data.active_from || '';
    document.getElementById('edit_modal_active_until').value = data.active_until || '';

    // Submission Settings
    document.getElementById('edit_modal_is_submission_enabled').checked = data.is_submission_enabled;
    toggleEditModalSubmissionFields(data.is_submission_enabled);
    document.getElementById('edit_modal_deadline_at').value = data.deadline_at || '';
    document.getElementById('edit_modal_submission_instructions').value = data.submission_instructions || '';
    document.getElementById('edit_modal_classes').value = data.classes || '';

    // Submission Types Checkboxes
    const subTypes = data.submission_types || ['file'];
    document.getElementById('edit_sub_type_file').checked = subTypes.includes('file');
    document.getElementById('edit_sub_type_link').checked = subTypes.includes('link');
    document.getElementById('edit_sub_type_text').checked = subTypes.includes('text');

    // Allowed Extensions Checkboxes
    const exts = data.allowed_extensions || ['all'];
    ['all', 'pdf', 'word', 'excel', 'ppt', 'image', 'archive', 'pkt'].forEach(ext => {
        const cb = document.getElementById('edit_ext_' + ext);
        if (cb) cb.checked = exts.includes(ext);
    });

    // Reset new files and new links
    document.getElementById('edit_modal_files').value = '';
    document.getElementById('edit-modal-new-files-container').classList.add('hidden');
    document.getElementById('edit-modal-new-files-list').innerHTML = '';
    document.getElementById('edit-modal-links-container').innerHTML = '';
    editModalLinkCount = 0;

    // Render Existing Attachments
    renderEditModalAttachments(data.attachments || []);

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function toggleEditModalSubmissionFields(checked) {
    const fields = document.getElementById('edit-modal-submission-fields');
    if (!fields) return;
    fields.classList.toggle('hidden', !checked);
    if (!checked) {
        const dl = document.getElementById('edit_modal_deadline_at');
        if (dl) dl.value = '';
    }
}

function renderEditModalAttachments(attachments) {
    const section = document.getElementById('edit-modal-existing-attachments-section');
    const countEl = document.getElementById('edit-modal-existing-attachments-count');
    const list = document.getElementById('edit-modal-existing-attachments-list');
    list.innerHTML = '';

    editModalExistingCount = attachments.length;

    if (attachments.length === 0) {
        section.classList.add('hidden');
        return;
    }

    section.classList.remove('hidden');
    countEl.textContent = `${attachments.length} Lampiran Tersimpan`;

    attachments.forEach(att => {
        const item = document.createElement('div');
        item.id = `edit-modal-attachment-${att.id}`;
        item.className = 'flex flex-wrap items-center justify-between gap-2.5 bg-gray-800/80 p-2.5 rounded-xl border border-gray-700';

        const iconHtml = att.is_link
            ? `<svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>`
            : `<span class="text-[10px] font-bold text-emerald-400 uppercase font-mono">${att.file_type || 'FILE'}</span>`;

        const subInfoHtml = att.is_link
            ? `<a href="${att.link_url}" target="_blank" rel="noopener noreferrer" class="text-purple-400 hover:underline truncate max-w-xs">${att.link_url}</a>`
            : `<span>${att.file_size_formatted || ''}</span>`;

        const dlHtml = att.is_file ? `
            <label class="flex items-center gap-1.5 cursor-pointer bg-gray-900/70 px-2.5 py-1 rounded-lg border border-gray-700 text-[11px]">
                <input type="hidden" name="attachment_downloadable[${att.id}]" value="0">
                <input type="checkbox" name="attachment_downloadable[${att.id}]" value="1" ${att.is_downloadable ? 'checked' : ''}
                       class="accent-emerald-500 w-3.5 h-3.5 rounded cursor-pointer">
                <span class="text-gray-300">Boleh Diunduh</span>
            </label>
        ` : '';

        item.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center flex-shrink-0">
                    ${iconHtml}
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-200 font-medium truncate">${att.file_original_name || att.file_name}</p>
                    <div class="flex items-center gap-2 text-[11px] text-gray-500 font-mono">
                        ${subInfoHtml}
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                ${dlHtml}
                <button type="button" onclick="removeEditModalExistingFile(${att.id})" class="p-1.5 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer" title="Hapus Lampiran Ini">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        `;
        list.appendChild(item);
    });
}

function removeEditModalExistingFile(id) {
    if (window.Swal) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Anda yakin ingin menghapus lampiran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#374151',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            background: '#0F1713',
            color: '#f3f4f6',
            customClass: {
                popup: 'border border-[#1A2820] rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2 text-sm font-medium',
                cancelButton: 'rounded-xl px-4 py-2 text-sm font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                doRemoveEditModalAttachment(id);
            }
        });
    } else {
        if (confirm('Anda yakin ingin menghapus lampiran ini?')) {
            doRemoveEditModalAttachment(id);
        }
    }
}

function doRemoveEditModalAttachment(id) {
    const el = document.getElementById('edit-modal-attachment-' + id);
    if (el) el.remove();
    editModalExistingCount--;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_attachments[]';
    input.value = id;
    document.getElementById('modal-edit-soal-form').appendChild(input);

    const countEl = document.getElementById('edit-modal-existing-attachments-count');
    if (countEl) countEl.textContent = `${Math.max(0, editModalExistingCount)} Lampiran Tersimpan`;
    if (editModalExistingCount <= 0) {
        const sec = document.getElementById('edit-modal-existing-attachments-section');
        if (sec) sec.classList.add('hidden');
    }
}

function handleEditModalNewFiles(input) {
    const container = document.getElementById('edit-modal-new-files-container');
    const list = document.getElementById('edit-modal-new-files-list');
    list.innerHTML = '';

    if (!input.files || input.files.length === 0) {
        container.classList.add('hidden');
        return;
    }

    if (editModalExistingCount + input.files.length > 15) {
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'Batas Maksimum Berkas',
                text: 'Total file (tersisa + baru) tidak boleh lebih dari 15 file.',
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Mengerti'
            });
        } else {
            alert('Total file (tersisa + baru) tidak boleh lebih dari 15 file.');
        }
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    let hasLarge = false;
    Array.from(input.files).forEach(f => {
        if (f.size > 5 * 1024 * 1024) hasLarge = true;
    });
    if (hasLarge) {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Berkas Terlalu Besar',
                text: 'Satu atau lebih berkas melebihi batas maksimal 5 MB per file!',
                background: '#0F1713',
                color: '#F3F4F6',
                backdrop: 'rgba(9, 13, 11, 0.75)',
                confirmButtonText: 'Perbaiki'
            });
        } else {
            alert('Satu atau lebih file melebihi batas 5MB!');
        }
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');
    const isMasterDl = document.getElementById('edit_modal_is_downloadable').checked;

    Array.from(input.files).forEach((file, index) => {
        const kb = file.size / 1024;
        const sizeStr = kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb/1024).toFixed(2)} MB`;

        const row = document.createElement('div');
        row.className = 'flex items-center justify-between bg-gray-800/80 p-2.5 rounded-lg border border-gray-700 text-xs';
        row.innerHTML = `
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-emerald-400 font-bold">+ Baru</span>
                <span class="text-gray-200 truncate max-w-xs">${file.name}</span>
                <span class="text-gray-500">(${sizeStr})</span>
            </div>
            <label class="flex items-center gap-1.5 cursor-pointer ml-3 flex-shrink-0">
                <input type="hidden" name="file_downloadable[${index}]" value="0">
                <input type="checkbox" name="file_downloadable[${index}]" value="1" ${isMasterDl ? 'checked' : ''}
                       class="accent-emerald-500 w-3.5 h-3.5 rounded cursor-pointer">
                <span class="text-[11px] text-gray-300">Boleh Diunduh</span>
            </label>
        `;
        list.appendChild(row);
    });
}

function addEditModalLinkRow() {
    const container = document.getElementById('edit-modal-links-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 link-row bg-[#090E0B]/50 p-2.5 rounded-xl border border-[#1A2820]';
    div.innerHTML = `
        <div class="md:col-span-4">
            <input type="text" name="links[${editModalLinkCount}][title]" placeholder="Label Link (misal: Bahan Modul)"
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-7">
            <input type="url" name="links[${editModalLinkCount}][url]" placeholder="https://..."
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5 cursor-pointer" title="Hapus Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
    editModalLinkCount++;
}

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
    }
});

let modalLinkCount = 1;
function addModalLinkRow() {
    if (modalLinkCount >= 15) {
        alert('Maksimal 15 tautan link materi.');
        return;
    }
    const container = document.getElementById('modal-links-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 link-row bg-gray-800/40 p-3 rounded-xl border border-[#1A2820]';
    div.innerHTML = `
        <div class="md:col-span-4">
            <input type="text" name="links[${modalLinkCount}][title]" placeholder="Label Link (misal: Bahan Modul)"
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-7">
            <input type="url" name="links[${modalLinkCount}][url]" placeholder="https://..."
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5 cursor-pointer" title="Hapus Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
    modalLinkCount++;
}

function toggleModalAllFileDownloads(checked) {
    document.querySelectorAll('.modal-file-dl-checkbox').forEach(cb => {
        cb.checked = checked;
    });
}

function handleModalFilesSelected(input) {
    const container = document.getElementById('modal-selected-files-container');
    const list = document.getElementById('modal-selected-files-list');
    list.innerHTML = '';

    if (!input.files || input.files.length === 0) {
        container.classList.add('hidden');
        return;
    }

    if (input.files.length > 15) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Batas Berkas Terlampaui',
                text: 'Maksimal hanya 15 file yang dapat diunggah sekaligus.',
                background: '#0F1713',
                color: '#F3F4F6',
                customClass: { popup: 'cyber-swal-popup cyber-swal-error' }
            });
        } else {
            alert('Maksimal hanya 15 file yang dapat diunggah sekaligus.');
        }
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    let largeFiles = [];
    Array.from(input.files).forEach(f => {
        if (f.size > 5 * 1024 * 1024) {
            const mb = (f.size / (1024 * 1024)).toFixed(2);
            largeFiles.push(`${f.name} (${mb} MB)`);
        }
    });
    if (largeFiles.length > 0) {
        const msg = 'File berikut melebihi batas 5 MB:\n' + largeFiles.join('\n');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Maksimal ukuran tiap berkas adalah 5 MB. File melebihi batas: ' + largeFiles.join(', '),
                background: '#0F1713',
                color: '#F3F4F6',
                customClass: { popup: 'cyber-swal-popup cyber-swal-error' }
            });
        } else {
            alert(msg);
        }
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');
    const masterChecked = document.getElementById('modal_master_downloadable').checked;

    Array.from(input.files).forEach((file, index) => {
        const kb = file.size / 1024;
        const sizeStr = kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb/1024).toFixed(2)} MB`;

        const row = document.createElement('div');
        row.className = 'flex items-center justify-between bg-gray-800 p-2.5 rounded-lg border border-gray-700 text-xs';
        row.innerHTML = `
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-emerald-400 font-bold">#${index + 1}</span>
                <span class="text-gray-200 truncate max-w-xs">${file.name}</span>
                <span class="text-gray-500">(${sizeStr})</span>
            </div>
            <label class="flex items-center gap-1.5 cursor-pointer ml-3 flex-shrink-0">
                <input type="hidden" name="file_downloadable[${index}]" value="0">
                <input type="checkbox" name="file_downloadable[${index}]" value="1" ${masterChecked ? 'checked' : ''}
                       class="modal-file-dl-checkbox accent-emerald-500 w-3.5 h-3.5 rounded">
                <span class="text-[11px] text-gray-300">Boleh Diunduh</span>
            </label>
        `;
        list.appendChild(row);
    });
}

function toggleModalSubmissionSection(checked) {
    const el = document.getElementById('modal-submission-options');
    el.classList.toggle('hidden', !checked);
    if (!checked) {
        document.getElementById('modal_deadline_at').value = '';
    }
}

// Drag & drop support on modal drop zone
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('modal-drop-zone');
    const fileInput = document.getElementById('modal_files');
    if (dropZone && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-emerald-500', 'bg-emerald-500/5');
            });
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-emerald-500', 'bg-emerald-500/5');
            });
        });
        dropZone.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleModalFilesSelected(fileInput);
            }
        });
    }

    @if($errors->any() && old('_method') === 'PUT' && old('soal_id'))
        openEditModalForSoal({{ old('soal_id') }});
    @elseif($errors->any())
        openCreateModal();
    @endif
});
</script>
@endpush
