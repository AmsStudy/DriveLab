@extends('layouts.admin')
@section('title', 'Tambah Dokumen')
@section('page-title', 'Tambah Dokumen Baru')
@section('page-description', 'Unggah materi, teks soal, tautan referensi, dan atur pengumpulan tugas')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.soal.store') }}" enctype="multipart/form-data" id="create-soal-page-form">
        @csrf

        <div class="bg-[#0F1713] border border-[#1A2820] rounded-2xl p-6 space-y-6">

            {{-- ─── 1. INFORMASI UTAMA ─── --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Informasi Dokumen
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Judul Dokumen / Soal <span class="text-red-400">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Contoh: Modul 3 — Konfigurasi Routing RIP & OSPF"
                               class="w-full bg-[#090E0B] border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                        @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kode_unik" class="block text-sm font-medium text-gray-300 mb-2">Kode Unik Akses <span class="text-red-400">*</span></label>
                            <input type="text" id="kode_unik" name="kode_unik" 
                                   value="{{ old('kode_unik', 'AMSSOAL-' . Str::upper(Str::random(5))) }}" required
                                   class="w-full bg-[#090E0B] border {{ $errors->has('kode_unik') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500">
                            <p class="mt-1 text-xs text-gray-500">Kode yang dimasukkan mahasiswa untuk mengakses dokumen.</p>
                            @error('kode_unik') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                            <div class="relative">
                                <select id="category_id" name="category_id"
                                        class="w-full appearance-none bg-[#090E0B] border border-[#1A2820] text-gray-300 rounded-xl pl-4 pr-10 py-3 text-sm focus:outline-none focus:border-emerald-500">
                                    <option value="">— Pilih Kategori —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password Akses <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required minlength="4"
                                       placeholder="Minimal 4 karakter"
                                       class="w-full bg-[#090E0B] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-emerald-500">
                                <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Singkat</label>
                            <input type="text" id="description" name="description" value="{{ old('description') }}"
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
                <textarea id="content_text" name="content_text" rows="5"
                          placeholder="Ketik isi soal atau instruksi langsung di sini... (Mendukung paragraf, baris baru, dan studi kasus)"
                          class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 font-mono">{{ old('content_text') }}</textarea>
            </div>

            {{-- ─── 3. TAUTAN / LINK MATERI (OPSIONAL) ─── --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-bold text-purple-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Tautan / Link Materi & Soal <span class="text-xs text-gray-500 font-normal lowercase">(opsional)</span>
                    </h3>
                    <button type="button" onclick="addLinkRow()" class="text-xs bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 px-3 py-1.5 rounded-lg border border-purple-500/30 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Link
                    </button>
                </div>
                <p class="text-xs text-gray-400 mb-3">Tambahkan link eksternal (Google Drive, Repository GitHub, Video Tutorial, Figma, dll).</p>

                <div id="links-container" class="space-y-2">
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
                            <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5" title="Hapus Link">
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
                        <input type="checkbox" id="master_downloadable" name="is_downloadable" value="1" checked
                               onchange="toggleAllFileDownloads(this.checked)"
                               class="accent-emerald-500 bg-gray-700 border-gray-600 w-4 h-4 rounded">
                        <label for="master_downloadable" class="text-xs text-gray-200 cursor-pointer">
                            Izinkan Unduh Semua File
                        </label>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-3">Dukungan format: PDF, Word, Excel, PPT, Gambar, ZIP/RAR, dan Cisco Packet Tracer (.pkt). Anda dapat mengatur izin download per-file di bawah.</p>

                <div class="border-2 border-dashed border-gray-700 hover:border-emerald-500 rounded-xl p-6 text-center cursor-pointer transition-colors group"
                     onclick="document.getElementById('files').click()">
                    <svg class="w-8 h-8 mx-auto text-gray-600 group-hover:text-emerald-500 mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-gray-300 font-medium">Klik untuk memilih file lampiran (maks. 15 file)</p>
                    <p class="text-xs text-gray-500 mt-1">Bisa pilih banyak file sekaligus</p>
                </div>
                <input type="file" id="files" name="files[]" multiple class="hidden"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.pkt"
                       onchange="handleFilesSelected(this)">
                @error('files') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                {{-- Selected Files List with Per-File Download Toggle --}}
                <div id="selected-files-container" class="mt-3 space-y-2 hidden">
                    <p class="text-xs text-gray-400 font-semibold mb-1">Daftar File yang Dipilih & Hak Unduh Per-File:</p>
                    <div id="selected-files-list" class="space-y-2"></div>
                </div>
            </div>

            {{-- ─── 5. MASA AKTIF DOKUMEN ─── --}}
            <div class="pt-5 border-t border-[#1A2820]">
                <label class="block text-sm font-medium text-gray-300 mb-3">Masa Aktif Akses Dokumen</label>
                <div class="flex gap-5 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="access_type" value="lifetime" checked
                               class="accent-emerald-500 bg-[#090E0B] border-gray-600"
                               onchange="document.getElementById('schedule-fields').classList.add('hidden')">
                        <span class="text-sm text-gray-300">Selamanya (Tanpa Batas Waktu)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="access_type" value="scheduled"
                               class="accent-emerald-500 bg-[#090E0B] border-gray-600"
                               onchange="document.getElementById('schedule-fields').classList.remove('hidden')">
                        <span class="text-sm text-gray-300">Terjadwal (Waktu Tertentu)</span>
                    </label>
                </div>
                <div id="schedule-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-[#090E0B]/40 p-4 rounded-xl border border-[#1A2820]">
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
                    <input type="checkbox" name="is_submission_enabled" id="is_submission_enabled" value="1"
                           class="accent-emerald-500 bg-[#090E0B] border-gray-600 w-4 h-4 rounded"
                           onchange="toggleSubmissionSection(this.checked)">
                    <span class="text-sm text-gray-300">Aktifkan Form Pengumpulan Tugas pada Soal Ini</span>
                </label>

                <div id="submission-options" class="hidden space-y-4 bg-[#090E0B]/40 p-4 rounded-xl border border-[#1A2820]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Batas Waktu Pengumpulan (Deadline) <span class="text-red-400">*</span></label>
                            <input type="datetime-local" id="deadline_at" name="deadline_at"
                                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Instruksi Tambahan (Opsional)</label>
                            <textarea name="submission_instructions" rows="2"
                                      placeholder="Contoh: Cantumkan link GitHub/Drive publik atau upload laporan PDF."
                                      class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-100 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                        </div>
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

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 mt-6">
            <button type="submit" id="save-soal-btn"
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-8 py-3 rounded-xl font-medium transition-all text-sm shadow-lg shadow-emerald-500/20">
                Simpan Dokumen Soal
            </button>
            <a href="{{ route('admin.soal.index') }}" class="text-gray-400 hover:text-gray-200 px-4 py-3 rounded-xl text-sm transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let linkCount = 1;
function addLinkRow() {
    if (linkCount >= 15) {
        alert('Maksimal 15 tautan link materi.');
        return;
    }
    const container = document.getElementById('links-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 link-row bg-gray-800/40 p-3 rounded-xl border border-[#1A2820]';
    div.innerHTML = `
        <div class="md:col-span-4">
            <input type="text" name="links[${linkCount}][title]" placeholder="Label Link (misal: Bahan Modul)"
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-7">
            <input type="url" name="links[${linkCount}][url]" placeholder="https://..."
                   class="w-full bg-[#090E0B] border border-[#1A2820] text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-emerald-500">
        </div>
        <div class="md:col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.closest('.link-row').remove()" class="text-red-400 hover:text-red-300 p-1.5" title="Hapus Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;
    container.appendChild(div);
    linkCount++;
}

function toggleAllFileDownloads(checked) {
    document.querySelectorAll('.file-dl-checkbox').forEach(cb => {
        cb.checked = checked;
    });
}

function handleFilesSelected(input) {
    const container = document.getElementById('selected-files-container');
    const list = document.getElementById('selected-files-list');
    list.innerHTML = '';

    if (!input.files || input.files.length === 0) {
        container.classList.add('hidden');
        return;
    }

    if (input.files.length > 15) {
        alert('Maksimal hanya 15 file yang dapat diunggah sekaligus.');
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
    const masterChecked = document.getElementById('master_downloadable').checked;

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
                       class="file-dl-checkbox accent-emerald-500 w-3.5 h-3.5 rounded">
                <span class="text-[11px] text-gray-300">Boleh Diunduh</span>
            </label>
        `;
        list.appendChild(row);
    });
}

function toggleSubmissionSection(checked) {
    const el = document.getElementById('submission-options');
    el.classList.toggle('hidden', !checked);
    if (!checked) {
        document.getElementById('deadline_at').value = '';
    }
}
</script>
@endpush
