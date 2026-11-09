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
                <tr class="hover:bg-emerald-500/5 dark:hover:bg-emerald-950/10 transition-colors submission-row border-b border-gray-100 dark:border-[#1A2820]" data-type="{{ $sub->submission_type ?: 'file' }}" data-kelas="{{ $sub->kelas ?? '' }}">
                    {{-- Mahasiswa Identity --}}
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                {{ substr($sub->nama, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate sub-nama">{{ $sub->nama }}</p>
                                <p class="text-xs font-mono text-gray-500 sub-nim">{{ $sub->nim }}</p>
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

                    {{-- Submission Type Badge --}}
                    <td class="px-4 py-3.5 text-xs">
                        @if($sub->isLink())
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/30">Link</span>
                        @elseif($sub->isText())
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">Teks</span>
                        @else
                            @php $fCount = $sub->getFilesCount(); @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30" title="{{ $fCount }} berkas diunggah">
                                File {{ $fCount > 1 ? "({$fCount})" : '' }}
                            </span>
                        @endif
                    </td>

                    {{-- Waktu --}}
                    <td class="px-4 py-3.5 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $sub->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 font-mono">{{ $sub->created_at->diffForHumans() }}</p>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            @if($sub->isLink())
                                <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer"
                                   class="p-2 text-teal-600 dark:text-teal-400 hover:bg-teal-500/10 rounded-xl transition-colors inline-block" title="Buka Link Tautan">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @elseif($sub->isText())
                                <button type="button"
                                        data-nama="{{ $sub->nama }}"
                                        data-nim="{{ $sub->nim }}"
                                        data-content="{{ $sub->text_content }}"
                                        onclick="showTextModal(this.dataset.nama, this.dataset.nim, this.dataset.content)"
                                        class="p-2 text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 rounded-xl transition-colors inline-block cursor-pointer" title="Baca Teks Lengkap">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            @else
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

                            {{-- Hapus / Reset Pengumpulan --}}
                            <form method="POST" action="{{ route('admin.soal.submission.destroy', $sub->id) }}" class="inline"
                                  onsubmit="confirmAction(event, 'Hapus pengumpulan tugas mahasiswa {{ addslashes($sub->nama) }} ({{ $sub->nim }})? Berkas akan dihapus permanen dan mahasiswa dapat mengumpulkan ulang.', 'Ya, Hapus Pengumpulan', true)">
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

