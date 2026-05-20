@extends('layouts.admin')
@section('title', 'Edit Dokumen')
@section('page-title', 'Edit Dokumen')
@section('page-description', 'Perbarui informasi dokumen')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.soal.update', $soal) }}" enctype="multipart/form-data" id="edit-soal-form">
        @csrf @method('PUT')

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-6">

            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Judul Dokumen <span class="text-red-400">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $soal->title) }}" required
                       class="w-full bg-gray-800 border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500">
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="kode_unik" class="block text-sm font-medium text-gray-300 mb-2">Kode Unik Akses <span class="text-red-400">*</span></label>
                <input type="text" id="kode_unik" name="kode_unik" value="{{ old('kode_unik', $soal->kode_unik) }}" required
                       class="w-full bg-gray-800 border {{ $errors->has('kode_unik') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500">
                <p class="mt-1 text-xs text-gray-500">Boleh diubah, asal tidak sama dengan dokumen lain.</p>
                @error('kode_unik') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500 resize-none">{{ old('description', $soal->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                    <select id="category_id" name="category_id"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500">
                        <option value="">— Tanpa Kategori —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $soal->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password Baru <span class="text-gray-500 text-xs">(kosongkan jika tidak diubah)</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" minlength="4"
                               placeholder="Biarkan kosong jika tidak diubah"
                               class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 pr-10 text-sm placeholder-gray-500 focus:outline-none focus:border-pink-500">
                        <button type="button" onclick="document.getElementById('password').type = document.getElementById('password').type === 'password' ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Current Files --}}
            @if($soal->attachments->count() > 0)
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-300">File Saat Ini</label>
                <div class="bg-gray-800/50 rounded-xl p-4 space-y-3">
                    @foreach($soal->attachments as $attachment)
                    <div id="attachment-{{ $attachment->id }}" class="flex items-center justify-between bg-gray-800 p-3 rounded-lg border border-gray-700">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-pink-400 uppercase">{{ $attachment->file_type ?: 'FILE' }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-300 truncate">{{ $attachment->file_original_name }}</p>
                                <p class="text-xs text-gray-500">{{ $attachment->file_size_formatted }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="removeExistingFile({{ $attachment->id }})" class="p-2 text-red-400 hover:bg-red-500/20 rounded-md transition-colors" title="Hapus File Ini">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <script>
                let existingFileCount = {{ $soal->attachments->count() }};
                function removeExistingFile(id) {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Anda yakin ingin menghapus file ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#374151',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        background: '#111827',
                        color: '#f3f4f6',
                        customClass: {
                            popup: 'border border-gray-800 rounded-2xl',
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

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Tambahkan File Baru <span class="text-gray-500 text-xs">Maks. total 5 File (lama + baru), masing-masing 5MB</span>
                </label>
                <div class="border-2 border-dashed border-gray-700 hover:border-pink-500 rounded-xl p-6 text-center cursor-pointer transition-colors"
                     onclick="document.getElementById('files').click()">
                    <p class="text-sm text-gray-500">Klik untuk pilih file tambahan</p>
                    <ul id="new-file-list" class="text-sm text-green-400 mt-2 space-y-1"></ul>
                </div>
                <input type="file" id="files" name="files[]" multiple class="hidden"
                       onchange="
                           const list = document.getElementById('new-file-list');
                           list.innerHTML = '';
                           if(existingFileCount + this.files.length > 5) {
                               alert('Total file (tersisa + baru) tidak boleh lebih dari 5.');
                               this.value = '';
                               return;
                           }
                           let hasLargeFile = false;
                           Array.from(this.files).forEach(f => {
                               if (f.size > 5 * 1024 * 1024) hasLargeFile = true;
                           });
                           if (hasLargeFile) {
                               alert('Satu atau lebih file melebihi batas 5MB! Silakan pilih file yang ukurannya lebih kecil.');
                               this.value = '';
                               return;
                           }
                           Array.from(this.files).forEach(f => {
                               const li = document.createElement('li');
                               li.textContent = '+ ' + f.name;
                               list.appendChild(li);
                           });
                       "
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
                @error('files') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Status & Masa Aktif --}}
            <div class="grid grid-cols-1 gap-5">
                <div class="flex items-center justify-between p-4 bg-gray-800/40 rounded-xl">
                    <div>
                        <p class="text-sm font-medium text-gray-200">Status Dokumen</p>
                        <p class="text-xs text-gray-500 mt-0.5">Aktifkan atau nonaktifkan akses dokumen</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="sr-only peer"
                               {{ old('is_active', $soal->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-pink-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-800/40 rounded-xl">
                    <div>
                        <p class="text-sm font-medium text-gray-200">Izinkan Unduh File</p>
                        <p class="text-xs text-gray-500 mt-0.5">Mahasiswa dapat mengunduh file dokumen secara langsung</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_downloadable" value="0">
                        <input type="checkbox" id="is_downloadable" name="is_downloadable" value="1" class="sr-only peer"
                               {{ old('is_downloadable', $soal->is_downloadable) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-pink-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Masa Aktif</label>
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="lifetime"
                                   {{ old('access_type', $soal->access_type) === 'lifetime' ? 'checked' : '' }}
                                   class="text-pink-600 bg-gray-800 border-gray-600"
                                   onchange="document.getElementById('schedule-fields').classList.add('hidden')">
                            <span class="text-sm text-gray-300">Selamanya</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="access_type" value="scheduled"
                                   {{ old('access_type', $soal->access_type) === 'scheduled' ? 'checked' : '' }}
                                   class="text-pink-600 bg-gray-800 border-gray-600"
                                   onchange="document.getElementById('schedule-fields').classList.remove('hidden')">
                            <span class="text-sm text-gray-300">Waktu Tertentu</span>
                        </label>
                    </div>
                    <div id="schedule-fields" class="{{ old('access_type', $soal->access_type) === 'scheduled' ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Mulai Aktif</label>
                            <input type="datetime-local" name="active_from"
                                   value="{{ old('active_from', $soal->active_from?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Berakhir</label>
                            <input type="datetime-local" name="active_until"
                                   value="{{ old('active_until', $soal->active_until?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
                        </div>
                    </div>
                </div>
                
                {{-- Pengumpulan Tugas --}}
                <div class="mt-2 pt-5 border-t border-gray-800">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Pengumpulan Tugas</label>
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input type="checkbox" name="is_submission_enabled" id="is_submission_enabled" value="1" 
                               {{ old('is_submission_enabled', $soal->is_submission_enabled) ? 'checked' : '' }} 
                               class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" 
                               onchange="toggleSubmissionFields(this.checked)">
                        <span class="text-sm text-gray-300">Aktifkan Form Pengumpulan Tugas bagi Mahasiswa</span>
                    </label>

                    <div id="submission-fields" class="{{ old('is_submission_enabled', $soal->is_submission_enabled) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800/20 p-4 rounded-xl border border-gray-700/50">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                            <input type="datetime-local" id="deadline_at" name="deadline_at" 
                                   value="{{ old('deadline_at', $soal->deadline_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-pink-500">
                            @error('deadline_at') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Instruksi Tambahan (Opsional)</label>
                            <textarea name="submission_instructions" rows="3" class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-pink-500" placeholder="Contoh: Format laporan dalam PDF, ukuran max 5MB.">{{ old('submission_instructions', $soal->submission_instructions) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-400 mb-2">Format File yang Diizinkan <span class="text-red-400">*</span></label>
                            @php $exts = old('allowed_extensions', $soal->allowed_extensions ?? []); @endphp
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="pdf" class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" {{ in_array('pdf', $exts) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-300">PDF (.pdf)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="word" class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" {{ in_array('word', $exts) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-300">Word (.doc, .docx)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="excel" class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" {{ in_array('excel', $exts) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-300">Excel (.xls, .xlsx)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="ppt" class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" {{ in_array('ppt', $exts) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-300">PowerPoint (.ppt)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="image" class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" {{ in_array('image', $exts) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-300">Gambar (.jpg, .png)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="allowed_extensions[]" value="archive" class="accent-pink-500 bg-gray-800 border-gray-600 w-4 h-4 rounded" {{ in_array('archive', $exts) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-300">Arsip (.zip, .rar)</span>
                                </label>
                            </div>
                            @error('allowed_extensions') <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" id="update-soal-btn"
                    class="bg-gradient-to-r from-pink-600 to-fuchsia-700 hover:from-pink-700 hover:to-fuchsia-800 text-white px-8 py-3 rounded-xl font-medium transition-all text-sm">
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
</script>
@endpush

