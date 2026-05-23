@extends('layouts.admin')

@section('title', 'Daftar Dokumen')
@section('page-title', 'Daftar Dokumen')
@section('page-description', 'Kelola semua dokumen yang telah diunggah')

@section('content')

{{-- Filter & Search --}}
<form method="GET" id="filter-form" class="flex flex-wrap gap-3 mb-6">
    <div class="flex-1 min-w-60">
        <input id="search-input" type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari judul dokumen..."
               class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-2.5 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500">
    </div>
    <select id="category-filter" name="category" class="bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select id="status-filter" name="status" class="bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
        <option value="">Semua Status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
    </select>
    <button type="submit" id="search-btn" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
        Cari
    </button>
    <a href="{{ route('admin.soal.trash') }}" id="trash-btn"
       class="bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2" title="Recycle Bin">
        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        <span class="hidden sm:inline">Recycle Bin</span>
    </a>
    <button type="button" onclick="openCreateModal()" id="add-soal-btn"
       class="bg-gradient-to-r from-pink-600 to-fuchsia-700 hover:from-pink-700 hover:to-fuchsia-800 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Dokumen
    </button>
</form>

{{-- Table --}}
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    @if($soals->isEmpty())
        <div class="py-16 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-lg font-medium">Belum ada dokumen</p>
            <p class="text-sm mt-1">Tambahkan dokumen pertama Anda sekarang.</p>
            <button type="button" onclick="openCreateModal()" class="mt-4 inline-block bg-pink-600 hover:bg-pink-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-colors">
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
                        <th class="px-4 py-3 text-right">Akses</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @foreach($soals as $soal)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                {{-- File Type Icon --}}
                                <div class="w-9 h-9 rounded-lg bg-gray-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    @if($soal->attachments->count() > 0)
                                        <span class="text-blue-400 text-xs font-bold">{{ $soal->attachments->count() }} FILE</span>
                                    @else
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-100 truncate max-w-xs">{{ $soal->title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if(auth()->user()->isSuperAdmin())
                                            <span class="text-xs text-gray-600">· {{ $soal->admin->name }}</span>
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
                                <span class="text-purple-400">∞ Selamanya</span>
                            @else
                                <div>
                                    <p>{{ optional($soal->active_from)->format('d M Y H:i') ?? '—' }}</p>
                                    <p>s/d {{ optional($soal->active_until)->format('d M Y H:i') ?? '—' }}</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-gray-300 font-medium">{{ number_format($soal->access_count) }}×</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                {{-- Copy Link --}}
                                <button onclick="copyLink('{{ route('soal.access', $soal->unique_token) }}')"
                                        id="copy-link-{{ $soal->id }}"
                                        title="Salin Link"
                                        class="p-2 rounded-lg text-gray-500 hover:text-pink-400 hover:bg-pink-500/10 transition-colors">
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
                                {{-- Detail & Logs --}}
                                <a href="{{ route('admin.soal.show', $soal) }}"
                                   id="show-soal-{{ $soal->id }}"
                                   title="Detail & Riwayat Akses"
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

{{-- Create Modal --}}
<div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeCreateModal()"></div>
        
        <div class="relative inline-block w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8">
            <div class="px-5 py-3.5 border-b border-gray-800 flex justify-between items-center bg-gray-900">
                <h3 class="text-lg leading-6 font-semibold text-white" id="modal-title">Tambah Dokumen Baru</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.soal.store') }}" enctype="multipart/form-data" id="create-soal-form" class="max-h-[75vh] overflow-y-auto p-5 space-y-4">
                @csrf
                
                {{-- Judul --}}
                <div>
                    <label for="title" class="block text-xs font-medium text-gray-300 mb-1">Judul Dokumen <span class="text-red-400">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Asistensi"
                           class="w-full bg-gray-800 border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-3.5 py-2 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500">
                    @error('title') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Kode Unik --}}
                <div>
                    <label for="kode_unik" class="block text-xs font-medium text-gray-300 mb-1">Kode Unik Akses <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="text" id="kode_unik" name="kode_unik" 
                               value="{{ old('kode_unik', 'AMSSOAL-' . Str::upper(Str::random(5))) }}" required
                               class="w-full bg-gray-800 border {{ $errors->has('kode_unik') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-3.5 py-2 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500">
                    </div>
                    <p class="mt-1 text-[11px] text-gray-500">Digunakan mahasiswa di halaman depan untuk mengakses dokumen. Boleh diedit sesuka hati.</p>
                    @error('kode_unik') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-xs font-medium text-gray-300 mb-1">Deskripsi <span class="text-gray-500">(opsional)</span></label>
                    <textarea id="description" name="description" rows="2"
                              placeholder="Keterangan singkat..."
                              class="w-full bg-gray-800 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-3.5 py-2 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500 resize-none">{{ old('description') }}</textarea>
                </div>

                {{-- Kategori & Password --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="category_id" class="block text-xs font-medium text-gray-300 mb-1">Kategori</label>
                        <select id="category_id" name="category_id"
                                class="w-full bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:border-pink-500">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-medium text-gray-300 mb-1">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required minlength="4"
                                   placeholder="Min 4 char" autocomplete="new-password"
                                   class="w-full bg-gray-800 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-3.5 py-2 pr-9 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Upload File --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-medium text-gray-300">File Dokumen <span class="text-gray-500 font-normal">(Maks 5 file)</span></label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="is_downloadable" value="0">
                            <input type="checkbox" name="is_downloadable" value="1" {{ old('is_downloadable') ? 'checked' : '' }}
                                   class="text-pink-600 bg-gray-800 border-gray-600 rounded w-3 h-3 focus:ring-offset-gray-900 focus:ring-pink-500">
                            <span class="text-[11px] text-gray-400 hover:text-gray-300 transition-colors">Izinkan File Diunduh Mahasiswa</span>
                        </label>
                    </div>
                    <div id="drop-zone" class="border-2 border-dashed border-gray-700 hover:border-pink-500 rounded-xl p-3 text-center cursor-pointer transition-colors group" onclick="document.getElementById('files').click()">
                        <div id="drop-placeholder">
                            <svg class="w-6 h-6 mx-auto text-gray-600 group-hover:text-pink-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-[11px] text-gray-400">Klik atau drop file di sini</p>
                        </div>
                        <div id="file-info" class="hidden">
                            <ul id="file-list" class="text-[11px] text-green-400 font-medium space-y-0.5 text-left pl-2 list-disc list-inside"></ul>
                        </div>
                    </div>
                    <input type="file" id="files" name="files[]" multiple class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.pkt">
                    @error('files') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Masa Aktif --}}
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Masa Aktif Dokumen <span class="text-red-400">*</span></label>
                    <div class="flex gap-4 mb-2">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="access_type" value="lifetime" {{ old('access_type', 'lifetime') === 'lifetime' ? 'checked' : '' }}
                                   class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5" onchange="toggleAccessType('lifetime')">
                            <span class="text-xs text-gray-300">Selamanya</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="access_type" value="scheduled" {{ old('access_type') === 'scheduled' ? 'checked' : '' }}
                                   class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5" onchange="toggleAccessType('scheduled')">
                            <span class="text-xs text-gray-300">Terjadwal</span>
                        </label>
                    </div>

                    <div id="schedule-fields" class="{{ old('access_type') === 'scheduled' ? '' : 'hidden' }} grid grid-cols-2 gap-3 mt-2">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-0.5">Mulai</label>
                            <input type="datetime-local" id="active_from" name="active_from" value="{{ old('active_from') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-pink-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-0.5">Berakhir</label>
                            <input type="datetime-local" id="active_until" name="active_until" value="{{ old('active_until') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-pink-500">
                        </div>
                    </div>
                </div>

                {{-- Pengumpulan Tugas --}}
                <div class="border-t border-gray-800 pt-3">
                    <label class="block text-xs font-medium text-gray-300 mb-1">Fitur Pengumpulan Tugas</label>
                    <label class="flex items-center gap-2 cursor-pointer mb-2">
                        <input type="checkbox" name="is_submission_enabled" id="is_submission_enabled" value="1" {{ old('is_submission_enabled') ? 'checked' : '' }} class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" onchange="toggleSubmissionFields(this.checked)">
                        <span class="text-xs text-gray-300">Aktifkan Form Pengumpulan Tugas bagi Mahasiswa</span>
                    </label>

                    <div id="submission-fields" class="{{ old('is_submission_enabled') ? '' : 'hidden' }} space-y-3 mt-2 p-3 bg-gray-800/50 rounded-xl border border-gray-700/50">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-0.5">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                            <input type="datetime-local" id="deadline_at" name="deadline_at" value="{{ old('deadline_at') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-pink-500">
                            @error('deadline_at') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-0.5">Instruksi Tambahan (Opsional)</label>
                            <textarea name="submission_instructions" rows="2" class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-pink-500" placeholder="Contoh: Format laporan dalam PDF, ukuran max 5MB.">{{ old('submission_instructions') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1.5">Format File yang Diizinkan <span class="text-red-400">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="pdf" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded" checked>
                                    <span class="text-xs text-gray-300">PDF (.pdf)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="word" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded" checked>
                                    <span class="text-xs text-gray-300">Word (.doc, .docx)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="excel" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded">
                                    <span class="text-xs text-gray-300">Excel (.xls, .xlsx)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="ppt" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded">
                                    <span class="text-xs text-gray-300">PowerPoint (.ppt, .pptx)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="image" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded">
                                    <span class="text-xs text-gray-300">Gambar (.jpg, .png)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="archive" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded">
                                    <span class="text-xs text-gray-300">Arsip (.zip, .rar)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="pkt" class="accent-pink-500 bg-gray-800 border-gray-600 w-3.5 h-3.5 rounded">
                                    <span class="text-xs text-gray-300">Packet Tracer (.pkt)</span>
                                </label>
                            </div>
                            @error('allowed_extensions') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-3 border-t border-gray-800 flex justify-end gap-2 sticky bottom-0 bg-gray-900 pb-1">
                    <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:bg-gray-800 px-3 py-1.5 rounded-lg text-xs transition-colors border border-transparent">Batal</button>
                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-1.5 rounded-lg text-xs font-medium transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 bg-gray-800 border border-gray-700 text-green-400 px-4 py-3 rounded-xl text-sm shadow-lg z-50 flex items-center gap-2 transition-all';
        toast.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Link berhasil disalin!`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
}

// Modal Logic
const modal = document.getElementById('create-modal');
function openCreateModal() { modal.classList.remove('hidden'); }
function closeCreateModal() { modal.classList.add('hidden'); }

// Buka otomatis jika ada error validasi
@if($errors->any())
    openCreateModal();
@endif

function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function toggleAccessType(type) {
    const fields = document.getElementById('schedule-fields');
    fields.classList.toggle('hidden', type === 'lifetime');
    if (type === 'lifetime') {
        document.getElementById('active_from').value = '';
        document.getElementById('active_until').value = '';
    }
}

function toggleSubmissionFields(checked) {
    const fields = document.getElementById('submission-fields');
    fields.classList.toggle('hidden', !checked);
    if (!checked) {
        document.getElementById('deadline_at').value = '';
    }
}

// File drag & drop
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('files');
const placeholder = document.getElementById('drop-placeholder');
const fileInfo = document.getElementById('file-info');
const fileList = document.getElementById('file-list');

function updateFileDisplay(files) {
    if (files && files.length > 0) {
        placeholder.classList.add('hidden');
        fileInfo.classList.remove('hidden');
        fileList.innerHTML = '';
        
        if(files.length > 5) {
            alert('Maksimal hanya 5 file yang diperbolehkan');
            fileInput.value = '';
            placeholder.classList.remove('hidden'); fileInfo.classList.add('hidden');
            return;
        }

        let hasLargeFile = false;
        Array.from(files).forEach(file => { if (file.size > 5 * 1024 * 1024) hasLargeFile = true; });

        if (hasLargeFile) {
            alert('Satu atau lebih file melebihi batas 5MB!');
            fileInput.value = '';
            placeholder.classList.remove('hidden'); fileInfo.classList.add('hidden');
            return;
        }

        Array.from(files).forEach(file => {
            const kb = file.size / 1024;
            const sizeStr = kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb/1024).toFixed(2)} MB`;
            const li = document.createElement('li');
            li.innerHTML = `<span>${file.name}</span> <span class="text-gray-500 text-xs ml-2">(${sizeStr})</span>`;
            fileList.appendChild(li);
        });
    }
}

fileInput.addEventListener('change', (e) => updateFileDisplay(e.target.files));
dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-pink-500', 'bg-pink-500/5'); });
dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-pink-500', 'bg-pink-500/5'); });
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-pink-500', 'bg-pink-500/5');
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        updateFileDisplay(e.dataTransfer.files);
    }
});
</script>
@endpush

