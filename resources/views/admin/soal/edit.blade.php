@extends('layouts.admin')
@section('title', 'Edit Dokumen')
@section('page-title', 'Edit Dokumen')
@section('page-description', 'Perbarui informasi dokumen, teks soal, file lampiran, dan pengaturan tugas')

@section('content')

{{-- Back nav --}}
<div class="mb-5">
    <a href="{{ route('admin.soal.show', $soal) }}"
       class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
       style="color:#4B8C6A;"
       onmouseover="this.style.color='#10B981'"
       onmouseout="this.style.color='#4B8C6A'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Detail Dokumen
    </a>
</div>

<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.soal.update', $soal) }}" enctype="multipart/form-data" id="edit-soal-form">
        @csrf @method('PUT')

        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6 space-y-6">

            {{-- ─── 1. INFORMASI UTAMA ─── --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Informasi Dokumen
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Judul Dokumen <span class="text-red-400">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title', $soal->title) }}" required
                               class="w-full bg-gray-800 border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                        @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kode_unik" class="block text-sm font-medium text-gray-300 mb-2">Kode Unik Akses <span class="text-red-400">*</span></label>
                            <input type="text" id="kode_unik" name="kode_unik" value="{{ old('kode_unik', $soal->kode_unik) }}" required
                                   class="w-full bg-gray-800 border {{ $errors->has('kode_unik') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                            <p class="mt-1 text-xs text-gray-500">Boleh diubah, asal tidak sama dengan dokumen lain.</p>
                            @error('kode_unik') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                            <select id="category_id" name="category_id"
                                    class="cyber-select w-full pl-4 pr-10 py-3 text-sm cursor-pointer">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $soal->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                                Password Baru <span class="text-gray-500 text-xs">(kosongkan jika tidak diubah)</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" minlength="4"
                                       placeholder="Biarkan kosong jika tidak diubah"
                                       class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-3 pr-10 text-sm placeholder-gray-500 focus:outline-none focus:border-emerald-500">
                                <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Singkat</label>
                            <input type="text" id="description" name="description" value="{{ old('description', $soal->description) }}"
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
                <p class="text-xs text-gray-400 mb-3">Teks soal akan langsung ditampilkan rapi di layar mahasiswa tanpa harus mengunduh file.</p>
                <textarea id="content_text" name="content_text" rows="5"
                          placeholder="Ketik isi soal atau instruksi langsung di sini..."
                          class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 font-mono">{{ old('content_text', $soal->content_text) }}</textarea>
            </div>

            {{-- ─── 3. LAMPIRAN SAAT INI (FILES & LINKS) ─── --}}
            @if($soal->attachments->count() > 0)
            <div class="pt-5 border-t border-[#1A2820] space-y-3">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-yellow-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Lampiran Dokumen Saat Ini
                    </h3>
                    <span class="text-xs text-gray-500">{{ $soal->attachments->count() }} Lampiran Tersimpan</span>
                </div>
                
                <div class="bg-[#090E0B]/50 rounded-xl p-4 space-y-3 border border-[#1A2820]">
                    @foreach($soal->attachments as $attachment)
                    <div id="attachment-{{ $attachment->id }}" class="flex flex-wrap items-center justify-between gap-3 bg-gray-800 p-3 rounded-xl border border-gray-700">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-lg bg-gray-700/80 flex items-center justify-center flex-shrink-0">
                                @if($attachment->isLink())
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @else
                                    <span class="text-[11px] font-bold text-emerald-400 uppercase">{{ $attachment->file_type ?: 'FILE' }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-200 font-medium truncate">{{ $attachment->file_original_name }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    @if($attachment->isLink())
                                        <a href="{{ $attachment->link_url }}" target="_blank" rel="noopener noreferrer" class="text-purple-400 hover:underline truncate max-w-xs">{{ $attachment->link_url }}</a>
                                    @else
                                        <span>{{ $attachment->file_size_formatted }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Hak Unduh Per-File --}}
                            @if($attachment->isFile())
                            <label class="flex items-center gap-1.5 cursor-pointer bg-gray-900/60 px-3 py-1.5 rounded-lg border border-gray-700 text-xs">
                                <input type="hidden" name="attachment_downloadable[{{ $attachment->id }}]" value="0">
                                <input type="checkbox" name="attachment_downloadable[{{ $attachment->id }}]" value="1"
                                       {{ $attachment->is_downloadable ? 'checked' : '' }}
                                       class="accent-emerald-500 w-3.5 h-3.5 rounded">
                                <span class="text-gray-300">Boleh Diunduh</span>
                            </label>
                            @endif

                            <button type="button" onclick="removeExistingFile({{ $attachment->id }})" class="p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors" title="Hapus Lampiran Ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <script>
                let existingFileCount = {{ $soal->attachments()->where('type', 'file')->count() }};
                function removeExistingFile(id) {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Anda yakin ingin menghapus lampiran ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#374151',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        background: '#111827',
                        color: '#f3f4f6',
                        customClass: {
                            popup: 'border border-[#1A2820] rounded-2xl',
                            confirmButton: 'rounded-xl px-4 py-2 text-sm font-medium',
                            cancelButton: 'rounded-xl px-4 py-2 text-sm font-medium'
                        }
                    }).then((result) => {
                        if(result.isConfirmed) {
                            document.getElementById('attachment-' + id).style.display = 'none';
                            existingFileCount--;
                            
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'delete_attachments[]';
                            input.value = id;
                            document.getElementById('edit-soal-form').appendChild(input);
                        }
                    });
                }
            </script>

            {{-- ─── 4. TAMBAH FILE BARU (MAKS. TOTAL 15 FILE) ─── --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-bold text-green-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Tambahkan File Baru <span class="text-xs text-gray-500 font-normal lowercase">(Maks. total 15 file)</span>
                    </h3>
                </div>
                <div class="border-2 border-dashed border-gray-700 hover:border-emerald-500 rounded-xl p-6 text-center cursor-pointer transition-colors"
                     onclick="document.getElementById('files').click()">
                    <p class="text-sm text-gray-400">Klik untuk memilih file tambahan (5MB per file)</p>
                </div>
                <input type="file" id="files" name="files[]" multiple class="hidden"
                       onchange="handleNewFiles(this)"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.pkt">
                @error('files') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                <div id="new-files-container" class="mt-3 space-y-2 hidden">
                    <p class="text-xs text-gray-400 font-semibold mb-1">File Baru yang Akan Diunggah & Hak Unduh:</p>
                    <div id="new-files-list" class="space-y-2"></div>
                </div>
            </div>

            {{-- ─── 5. TAMBAH TAUTAN LINK BARU ─── --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-bold text-purple-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Tambah Tautan / Link Materi Baru
                    </h3>
                    <button type="button" onclick="addEditLinkRow()" class="text-xs bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 px-3 py-1.5 rounded-lg border border-purple-500/30 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Link
                    </button>
                </div>
                <div id="edit-links-container" class="space-y-2"></div>
            </div>

            {{-- ─── 6. STATUS & MASA AKTIF ─── --}}
            <div class="pt-5 border-t border-[#1A2820] space-y-4">
                <div class="flex items-center justify-between p-4 bg-[#090E0B]/50 rounded-xl border border-[#1A2820]">
                    <div>
                        <p class="text-sm font-medium text-gray-200">Status Dokumen</p>
                        <p class="text-xs text-gray-500 mt-0.5">Aktifkan atau nonaktifkan akses dokumen bagi mahasiswa</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="sr-only peer"
                               {{ old('is_active', $soal->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-[#090E0B]/50 rounded-xl border border-[#1A2820]">
                    <div>
                        <p class="text-sm font-medium text-gray-200">Master Hak Unduh Dokumen</p>
                        <p class="text-xs text-gray-500 mt-0.5">Pengaturan global izin unduh (jika aktif, default file baru diizinkan unduh)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_downloadable" value="0">
                        <input type="checkbox" id="is_downloadable" name="is_downloadable" value="1" class="sr-only peer"
                               {{ old('is_downloadable', $soal->is_downloadable) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-emerald-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Masa Aktif Dokumen</label>
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="lifetime"
                                   {{ old('access_type', $soal->access_type) === 'lifetime' ? 'checked' : '' }}
                                   class="text-emerald-500 bg-gray-800 border-gray-600"
                                   onchange="document.getElementById('schedule-fields').classList.add('hidden')">
                            <span class="text-sm text-gray-300">Selamanya</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="scheduled"
                                   {{ old('access_type', $soal->access_type) === 'scheduled' ? 'checked' : '' }}
                                   class="text-emerald-500 bg-gray-800 border-gray-600"
                                   onchange="document.getElementById('schedule-fields').classList.remove('hidden')">
                            <span class="text-sm text-gray-300">Waktu Tertentu</span>
                        </label>
                    </div>
                    <div id="schedule-fields" class="{{ old('access_type', $soal->access_type) === 'scheduled' ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4 bg-[#090E0B]/40 p-4 rounded-xl border border-[#1A2820]">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Mulai Aktif</label>
                            <input type="datetime-local" name="active_from"
                                   value="{{ old('active_from', $soal->active_from?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Berakhir</label>
                            <input type="datetime-local" name="active_until"
                                   value="{{ old('active_until', $soal->active_until?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── 7. PENGUMPULAN TUGAS ─── --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <label class="block text-sm font-medium text-gray-300 mb-2">Fitur Pengumpulan Tugas bagi Mahasiswa</label>
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="is_submission_enabled" id="is_submission_enabled" value="1" 
                           {{ old('is_submission_enabled', $soal->is_submission_enabled) ? 'checked' : '' }} 
                           class="accent-emerald-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" 
                           onchange="toggleSubmissionFields(this.checked)">
                    <span class="text-sm text-gray-300">Aktifkan Form Pengumpulan Tugas bagi Mahasiswa</span>
                </label>

                <div id="submission-fields" class="{{ old('is_submission_enabled', $soal->is_submission_enabled) ? '' : 'hidden' }} space-y-4 bg-[#090E0B]/40 p-4 rounded-xl border border-[#1A2820]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                            <input type="datetime-local" id="deadline_at" name="deadline_at" 
                                   value="{{ old('deadline_at', $soal->deadline_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
                            @error('deadline_at') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Instruksi Tambahan (Opsional)</label>
                            <textarea name="submission_instructions" rows="2" class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500 resize-none" placeholder="Contoh: Format laporan dalam PDF, ukuran max 5MB.">{{ old('submission_instructions', $soal->submission_instructions) }}</textarea>
                        </div>
                    </div>

                    {{-- Daftar Pilihan Kelas (Opsional) --}}
                    @php
                        $classesStr = old('classes', is_array($soal->classes) ? implode(', ', $soal->classes) : '');
                    @endphp
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-1">Daftar Pilihan Kelas (Opsional)</label>
                        <input type="text" name="classes" value="{{ $classesStr }}"
                               placeholder="Contoh: A1, A2, B1, B2 (Pisahkan dengan tanda koma)"
                               class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-emerald-500">
                        <p class="text-[11px] text-gray-400 mt-1">Jika diisi, mahasiswa wajib memilih salah satu kelas saat mengumpulkan tugas. Anda juga dapat mengunduh arsip tugas berdasarkan kelasnya.</p>
                        @error('classes') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pilihan Tipe Pengumpulan --}}
                    @php
                        $subTypes = old('submission_types', $soal->submission_types ?? ['file']);
                        if(empty($subTypes)) $subTypes = ['file'];
                    @endphp
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-2">Metode Pengumpulan yang Diizinkan Mahasiswa <span class="text-red-400">*</span></label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="submission_types[]" value="file" {{ in_array('file', $subTypes) ? 'checked' : '' }} class="accent-emerald-500 w-4 h-4 rounded">
                                <span class="text-xs text-gray-200">📁 File Dokumen</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="submission_types[]" value="link" {{ in_array('link', $subTypes) ? 'checked' : '' }} class="accent-emerald-500 w-4 h-4 rounded">
                                <span class="text-xs text-gray-200">🔗 Tautan / Link URL</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 px-3 py-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="submission_types[]" value="text" {{ in_array('text', $subTypes) ? 'checked' : '' }} class="accent-emerald-500 w-4 h-4 rounded">
                                <span class="text-xs text-gray-200">📝 Teks Langsung</span>
                            </label>
                        </div>
                    </div>

                    {{-- Format File yang Diizinkan --}}
                    <div>
                        <label class="block text-xs text-gray-300 font-semibold mb-1">Format File yang Diizinkan (Jika Mengunggah File) <span class="text-red-400">*</span></label>
                        <p class="text-[11px] text-gray-400 mb-2">Semua file yang diunggah mahasiswa wajib lolos security check (anti-shell, anti-executable, anti-spoof MIME).</p>
                        @php $exts = old('allowed_extensions', $soal->allowed_extensions ?? []); @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                            <label class="flex items-center gap-2 cursor-pointer bg-emerald-500/10 border border-emerald-500/30 p-2 rounded-lg">
                                <input type="checkbox" name="allowed_extensions[]" value="all" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('all', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-emerald-300 font-bold">Semua Format (Aman)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="pdf" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('pdf', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">PDF (.pdf)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="word" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('word', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Word (.doc, .docx)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="excel" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('excel', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Excel (.xls, .xlsx)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="ppt" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('ppt', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">PowerPoint (.ppt)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="image" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('image', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Gambar (.jpg, .png)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="archive" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('archive', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Arsip (.zip, .rar)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-800 p-2 rounded-lg border border-gray-700">
                                <input type="checkbox" name="allowed_extensions[]" value="pkt" class="accent-emerald-500 w-4 h-4 rounded" {{ in_array('pkt', $exts) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-300">Packet Tracer (.pkt)</span>
                            </label>
                        </div>
                        @error('allowed_extensions') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" id="update-soal-btn"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-8 py-3 rounded-xl font-medium transition-all text-sm shadow-lg shadow-emerald-500/20">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.soal.index') }}" class="text-gray-400 hover:text-gray-200 px-4 py-3 rounded-xl text-sm transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleSubmissionFields(checked) {
    const fields = document.getElementById('submission-fields');
    fields.classList.toggle('hidden', !checked);
    if (!checked) {
        document.getElementById('deadline_at').value = '';
    }
}

function handleNewFiles(input) {
    const container = document.getElementById('new-files-container');
    const list = document.getElementById('new-files-list');
    list.innerHTML = '';

    if (!input.files || input.files.length === 0) {
        container.classList.add('hidden');
        return;
    }

    if (existingFileCount + input.files.length > 15) {
        alert('Total file (tersisa + baru) tidak boleh lebih dari 15 file.');
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    let hasLarge = false;
    Array.from(input.files).forEach(f => {
        if (f.size > 5 * 1024 * 1024) hasLarge = true;
    });
    if (hasLarge) {
        alert('Satu atau lebih file melebihi batas 5MB!');
        input.value = '';
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');
    const isMasterDl = document.getElementById('is_downloadable').checked;

    Array.from(input.files).forEach((file, index) => {
        const kb = file.size / 1024;
        const sizeStr = kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb/1024).toFixed(2)} MB`;

        const row = document.createElement('div');
        row.className = 'flex items-center justify-between bg-gray-800 p-2.5 rounded-lg border border-gray-700 text-xs';
        row.innerHTML = `
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-green-400 font-bold">+ Baru</span>
                <span class="text-gray-200 truncate max-w-xs">${file.name}</span>
                <span class="text-gray-500">(${sizeStr})</span>
            </div>
            <label class="flex items-center gap-1.5 cursor-pointer ml-3 flex-shrink-0">
                <input type="hidden" name="file_downloadable[${index}]" value="0">
                <input type="checkbox" name="file_downloadable[${index}]" value="1" ${isMasterDl ? 'checked' : ''}
                       class="accent-emerald-500 w-3.5 h-3.5 rounded">
                <span class="text-[11px] text-gray-300">Boleh Diunduh</span>
            </label>
        `;
        list.appendChild(row);
    });
}

let editLinkCount = 0;
function addEditLinkRow() {
    const container = document.getElementById('edit-links-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 link-row bg-[#090E0B]/50 p-3 rounded-xl border border-[#1A2820]';
    div.innerHTML = `
        <div class="md:col-span-4">
            <input type="text" name="links[${editLinkCount}][title]" placeholder="Label Link (misal: Bahan Modul)"
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-7">
            <input type="url" name="links[${editLinkCount}][url]" placeholder="https://..."
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5" title="Hapus Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
    editLinkCount++;
}
</script>
@endpush
