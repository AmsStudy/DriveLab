@if($submissions->isEmpty())
    <div class="py-20 text-center text-gray-500">
        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-base font-semibold text-gray-800 dark:text-gray-200">Belum ada tugas yang dikumpulkan</p>
        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Ketika mahasiswa mengirimkan file, link, atau teks jawaban, data mereka akan otomatis ditampilkan di sini.</p>
    </div>
@else
    <div class="overflow-x-auto overflow-y-auto rounded-b-2xl relative z-0" style="max-height: 700px; overflow-y: auto; scrollbar-width: thin;">
        <table class="w-full text-sm min-w-[580px]" id="submissionsTable">
            <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky top-0 z-10 shadow-sm bg-gray-50/90 dark:bg-[#090D0B] border-b border-gray-200 dark:border-[#1A2820] backdrop-blur-sm">
                <tr>
                    <th class="px-6 py-3.5 text-left font-semibold">Mahasiswa</th>
                    <th class="px-4 py-3.5 text-left font-semibold">Kelas</th>
                    <th class="px-4 py-3.5 text-left font-semibold">Tipe</th>
                    <th class="px-4 py-3.5 text-left font-semibold">Waktu Pengumpulan</th>
                    <th class="px-4 py-3.5 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-[#1A2820]" id="submissionsTableBody">
                @foreach($submissions as $sub)
                <tr class="hover:bg-emerald-500/5 dark:hover:bg-emerald-950/10 transition-colors submission-row border-b border-gray-100 dark:border-[#1A2820]" 
                    data-type="{{ $sub->submission_type ?: 'file' }}" 
                    data-has-file="{{ $sub->hasFile() ? '1' : '0' }}"
                    data-has-link="{{ $sub->hasLink() ? '1' : '0' }}"
                    data-has-text="{{ $sub->hasText() ? '1' : '0' }}"
                    data-kelas="{{ $sub->kelas ?? '' }}">
                    {{-- Mahasiswa Identity --}}
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                {{ substr($sub->nama, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate sub-nama">{{ $sub->nama }}</p>
                                <p class="text-xs font-mono text-gray-500 sub-nim">{{ $sub->nim }}</p>
                                @if(!empty($duplicatesBySubId[$sub->id]))
                                    @php
                                        $allMatches = [];
                                        foreach ($duplicatesBySubId[$sub->id] as $dup) {
                                            $allMatches = array_merge($allMatches, $dup['matches']);
                                        }
                                        $allMatches = array_values(array_unique($allMatches));
                                    @endphp
                                    <div class="mt-1 flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-500/10 border border-rose-500/25 text-rose-500 text-[10px] font-semibold w-fit cursor-help shadow-xs"
                                         title="PERINGATAN PLAGIARISME: Berkas mahasiswa ini 100% identik dengan: {{ implode(', ', $allMatches) }}">
                                        <svg class="w-3 h-3 flex-shrink-0 animate-pulse text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Berkas Identik ({{ count($allMatches) }})</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Kelas Badge --}}
                    <td class="px-4 py-3.5 text-xs">
                        @if($sub->kelas)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold inline-block bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                {{ preg_replace('/^kelas\s+/i', '', trim($sub->kelas)) }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 font-mono text-xs">-</span>
                        @endif
                    </td>

                    {{-- Submission Type Badge (Mendukung Multi-Komponen / Komposit dengan Aksi Langsung) --}}
                    <td class="px-4 py-3.5 text-xs">
                        <div class="flex flex-col gap-1.5 items-start">
                            {{-- 1. Komponen Berkas Fisik (Dapat diklik langsung untuk pratinjau PDF / Gambar) --}}
                            @if($sub->hasFile())
                                @php
                                    $fCount = $sub->getFilesCount();
                                    $rawExt = strtolower($sub->file_extension ?: pathinfo($sub->file_original_name ?: $sub->file_path, PATHINFO_EXTENSION));
                                    $ext = strtoupper($rawExt);
                                    $isImgOrPdf = in_array($rawExt, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'svg']);
                                    $fileName = $sub->file_original_name ?: ($rawExt ? "tugas.{$rawExt}" : 'berkas_tugas');
                                @endphp
                                <div class="w-full max-w-[220px]">
                                    @if($isImgOrPdf)
                                        <button type="button"
                                                onclick="showFilePreviewModal('{{ route('admin.soal.submission.preview', $sub->id) }}', '{{ addslashes($fileName) }}', '{{ addslashes($sub->nama) }} ({{ $sub->nim }})', '{{ $rawExt }}')"
                                                title="Klik untuk pratinjau berkas {{ $fileName }}"
                                                class="group/file w-full text-left p-1.5 rounded-lg border transition-all cursor-pointer shadow-2xs hover:shadow-sm
                                                    {{ in_array($ext, ['PDF']) ? 'bg-red-50/80 hover:bg-red-100/70 border-red-200 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:border-red-500/30' : '' }}
                                                    {{ in_array($ext, ['PNG', 'JPG', 'JPEG', 'WEBP', 'SVG']) ? 'bg-blue-50/80 hover:bg-blue-100/70 border-blue-200 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:border-blue-500/30' : '' }}
                                                    {{ !in_array($ext, ['PDF', 'PNG', 'JPG', 'JPEG', 'WEBP', 'SVG']) ? 'bg-emerald-50/80 hover:bg-emerald-100/70 border-emerald-200 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 dark:border-emerald-500/30' : '' }}">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="inline-flex items-center gap-1 font-mono text-[10px] font-bold uppercase
                                                    {{ in_array($ext, ['PDF']) ? 'text-red-700 dark:text-red-400' : '' }}
                                                    {{ in_array($ext, ['PNG', 'JPG', 'JPEG', 'WEBP', 'SVG']) ? 'text-blue-700 dark:text-blue-400' : '' }}
                                                    {{ !in_array($ext, ['PDF', 'PNG', 'JPG', 'JPEG', 'WEBP', 'SVG']) ? 'text-emerald-700 dark:text-emerald-400' : '' }}">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>{{ $ext ?: 'FILE' }}</span>
                                                </span>
                                                <span class="text-[9.5px] font-medium text-gray-400 dark:text-gray-500 group-hover/file:text-gray-700 dark:group-hover/file:text-gray-300 flex items-center gap-0.5">
                                                    <span>Preview</span>
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </span>
                                            </div>
                                            <p class="text-[11px] font-mono text-gray-700 dark:text-gray-300 truncate mt-0.5 group-hover/file:text-emerald-600 dark:group-hover/file:text-emerald-300 font-medium" title="{{ $fileName }}">
                                                {{ $fileName }}
                                            </p>
                                            @if($fCount > 1)
                                                <span class="text-[9.5px] font-mono text-gray-400 dark:text-gray-500 font-semibold mt-0.5 block">({{ $fCount }} total berkas)</span>
                                            @endif
                                        </button>
                                    @else
                                        <a href="{{ route('admin.soal.download-submissions', $sub->id) }}"
                                           title="Klik untuk unduh berkas {{ $fileName }}"
                                           class="group/file w-full text-left p-1.5 rounded-lg border transition-all cursor-pointer shadow-2xs hover:shadow-sm block
                                                {{ in_array($ext, ['ZIP', 'RAR', '7Z']) ? 'bg-purple-50/80 hover:bg-purple-100/70 border-purple-200 dark:bg-purple-500/10 dark:hover:bg-purple-500/20 dark:border-purple-500/30' : '' }}
                                                {{ in_array($ext, ['DOC', 'DOCX']) ? 'bg-indigo-50/80 hover:bg-indigo-100/70 border-indigo-200 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20 dark:border-indigo-500/30' : '' }}
                                                {{ !in_array($ext, ['ZIP', 'RAR', '7Z', 'DOC', 'DOCX']) ? 'bg-emerald-50/80 hover:bg-emerald-100/70 border-emerald-200 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 dark:border-emerald-500/30' : '' }}">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="inline-flex items-center gap-1 font-mono text-[10px] font-bold uppercase
                                                    {{ in_array($ext, ['ZIP', 'RAR', '7Z']) ? 'text-purple-700 dark:text-purple-400' : '' }}
                                                    {{ in_array($ext, ['DOC', 'DOCX']) ? 'text-indigo-700 dark:text-indigo-400' : '' }}
                                                    {{ !in_array($ext, ['ZIP', 'RAR', '7Z', 'DOC', 'DOCX']) ? 'text-emerald-700 dark:text-emerald-400' : '' }}">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>{{ $ext ?: 'FILE' }}</span>
                                                </span>
                                                <span class="text-[9.5px] font-medium text-gray-400 dark:text-gray-500 group-hover/file:text-gray-700 dark:group-hover/file:text-gray-300 flex items-center gap-0.5">
                                                    <span>Unduh</span>
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </span>
                                            </div>
                                            <p class="text-[11px] font-mono text-gray-700 dark:text-gray-300 truncate mt-0.5 font-medium" title="{{ $fileName }}">
                                                {{ $fileName }}
                                            </p>
                                            @if($fCount > 1)
                                                <span class="text-[9.5px] font-mono text-gray-400 dark:text-gray-500 font-semibold mt-0.5 block">({{ $fCount }} total berkas)</span>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            @endif

                            {{-- 2. Komponen Tautan Link (Dapat diklik langsung untuk menuju ke tautan) --}}
                            @if($sub->hasLink())
                                <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer"
                                   title="Buka tautan: {{ $sub->link_url }}"
                                   class="group/link inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-teal-50 hover:bg-teal-100/80 dark:bg-teal-500/10 dark:hover:bg-teal-500/20 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-500/30 transition-all shadow-2xs hover:shadow-xs max-w-[220px]">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span class="truncate">Buka Tautan</span>
                                    <svg class="w-3 h-3 flex-shrink-0 ml-auto opacity-70 group-hover/link:opacity-100 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endif

                            {{-- 3. Komponen Jawaban Teks (Dapat diklik langsung untuk membaca teks lengkap) --}}
                            @if($sub->hasText())
                                <button type="button"
                                        data-nama="{{ $sub->nama }}"
                                        data-nim="{{ $sub->nim }}"
                                        data-content="{{ $sub->text_content }}"
                                        onclick="showTextModal(this.dataset.nama, this.dataset.nim, this.dataset.content)"
                                        title="Klik untuk membaca jawaban teks"
                                        class="group/text inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 hover:bg-amber-100/80 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 transition-all shadow-2xs hover:shadow-xs cursor-pointer max-w-[220px]">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="truncate">Baca Teks</span>
                                    <svg class="w-3 h-3 flex-shrink-0 ml-auto opacity-70 group-hover/text:opacity-100 group-hover/text:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @endif

                            @if(!$sub->hasFile() && !$sub->hasLink() && !$sub->hasText())
                                <span class="text-gray-400 text-xs italic">-</span>
                            @endif
                        </div>
                    </td>

                    {{-- Waktu --}}
                    <td class="px-4 py-3.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $sub->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 font-mono">{{ $sub->created_at->diffForHumans() }}</p>
                    </td>

                    {{-- Aksi (Menyediakan tombol akses untuk setiap komponen aktif) --}}
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1 flex-wrap">
                            {{-- Aksi File --}}
                            @if($sub->hasFile())
                                @php
                                    $isImgOrPdf = in_array(strtolower($sub->file_extension ?? ''), ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'svg']);
                                @endphp
                                @if($isImgOrPdf)
                                    <button type="button"
                                            onclick="showFilePreviewModal('{{ route('admin.soal.submission.preview', $sub->id) }}', '{{ addslashes($sub->file_original_name ?: 'tugas') }}', '{{ addslashes($sub->nama) }} ({{ $sub->nim }})', '{{ strtolower($sub->file_extension ?? '') }}')"
                                            class="p-2 text-teal-600 dark:text-teal-400 hover:bg-teal-500/10 rounded-xl transition-colors inline-block cursor-pointer" title="Lihat Berkas Langsung">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                @endif

                                <a href="{{ route('admin.soal.download-submissions', $sub->id) }}"
                                   class="p-2 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-colors inline-block" title="Unduh Arsip Berkas Mahasiswa (.ZIP)">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            @endif

                            {{-- Aksi Link --}}
                            @if($sub->hasLink())
                                <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer"
                                   class="p-2 text-teal-600 dark:text-teal-400 hover:bg-teal-500/10 rounded-xl transition-colors inline-block" title="Buka Tautan: {{ $sub->link_url }}">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @endif

                            {{-- Aksi Teks --}}
                            @if($sub->hasText())
                                <button type="button"
                                        data-nama="{{ $sub->nama }}"
                                        data-nim="{{ $sub->nim }}"
                                        data-content="{{ $sub->text_content }}"
                                        onclick="showTextModal(this.dataset.nama, this.dataset.nim, this.dataset.content)"
                                        class="p-2 text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 rounded-xl transition-colors inline-block cursor-pointer" title="Baca Teks Lengkap">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </button>
                            @endif

                            {{-- Hapus / Reset Pengumpulan --}}
                            <form method="POST" action="{{ route('admin.soal.submission.destroy', $sub->id) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus pengumpulan tugas mahasiswa {{ addslashes($sub->nama) }} ({{ $sub->nim }})? Berkas dan tautan akan dihapus permanen dan mahasiswa dapat mengumpulkan ulang.', 'Ya, Hapus Pengumpulan', true)">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-colors inline-block cursor-pointer" title="Hapus / Reset Pengumpulan">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

