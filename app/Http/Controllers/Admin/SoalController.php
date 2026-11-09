<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Soal;
use App\Models\Submission;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = $user->isSuperAdmin() ? Soal::query() : Soal::where('user_id', $user->id);

        // Filter
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('access_type', 'scheduled')->where('active_until', '<', now());
            }
        }

        $soals      = $query->with(['category', 'admin', 'attachments'])->withCount('submissions')->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.soal.index', compact('soals', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.soal.create', compact('categories'));
    }

    public function checkKodeUnik(Request $request)
    {
        $code = trim((string) $request->query('kode_unik', ''));
        $ignoreId = $request->query('ignore_id');

        if ($code === '') {
            return response()->json([
                'available' => false,
                'message'   => 'Kode unik tidak boleh kosong.',
            ]);
        }

        if (mb_strlen($code) > 20) {
            return response()->json([
                'available' => false,
                'message'   => 'Kode unik maksimal 20 karakter.',
            ]);
        }

        $exists = Soal::withTrashed()
            ->where('kode_unik', $code)
            ->when($ignoreId, function ($query, $ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'available' => false,
                'message'   => 'Kode unik sudah digunakan. Silakan pilih kode lain.',
            ]);
        }

        return response()->json([
            'available' => true,
            'message'   => 'Kode unik tersedia.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'content_text' => 'nullable|string',
            'password'     => 'required|string|min:4',
            'category_id'  => 'nullable|exists:categories,id',
            'access_type'  => 'required|in:lifetime,scheduled',
            'active_from'  => 'nullable|required_if:access_type,scheduled|date',
            'active_until' => 'nullable|required_if:access_type,scheduled|date|after:active_from',
            'files'        => 'nullable|array|max:15',
            'files.*'      => 'file|max:5120', // 5MB per file
            'file_downloadable' => 'nullable|array',
            'is_downloadable' => 'boolean',
            'kode_unik'    => ['required', 'string', 'max:20', Rule::unique('soals', 'kode_unik')->whereNull('deleted_at')],
            'is_submission_enabled' => 'boolean',
            'submission_types' => 'nullable|array',
            'submission_types.*' => 'string|in:file,link,text',
            'deadline_at' => 'nullable|required_if:is_submission_enabled,1|date',
            'submission_instructions' => 'nullable|string',
            'max_files'    => 'nullable|integer|min:1|max:20',
            'allowed_extensions' => 'nullable|required_if:is_submission_enabled,1|array',
            'allowed_extensions.*' => 'string|in:all,pdf,word,excel,ppt,image,archive,pkt',
            'classes'      => 'nullable|string|max:500',
            'links'        => 'nullable|array|max:15',
            'links.*.title'=> 'nullable|string|max:255',
            'links.*.url'  => 'nullable|url:http,https|max:2000',
        ]);

        // Validasi keamanan file lampiran (anti-malware, anti-shell, anti-executable)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                [$isSafe, $errorMsg] = \App\Services\FileSecurityService::validate($file);
                if (!$isSafe) {
                    return back()->withInput()->withErrors([
                        'files' => "File lampiran \"{$file->getClientOriginalName()}\" ditolak demi keamanan: {$errorMsg}"
                    ]);
                }
            }
        }

        do {
            $token = Str::random(6);
        } while (Soal::where('unique_token', $token)->exists());

        $submissionTypes = $request->input('submission_types');
        if ($request->boolean('is_submission_enabled') && empty($submissionTypes)) {
            $submissionTypes = ['file'];
        }

        $classes = null;
        if ($request->boolean('is_submission_enabled') && $request->filled('classes')) {
            $raw = is_array($request->classes) ? implode(',', $request->classes) : $request->classes;
            $parsed = array_values(array_unique(array_filter(array_map(fn($c) => strtoupper(trim($c)), explode(',', $raw)))));
            if (!empty($parsed)) {
                $classes = $parsed;
            }
        }

        $soalData = [
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'content_text'        => $validated['content_text'] ?? null,
            'kode_unik'           => $validated['kode_unik'],
            'unique_token'        => $token,
            'password'            => Hash::make($validated['password']),
            'plain_password'      => $validated['password'],
            'category_id'         => $validated['category_id'] ?? null,
            'user_id'             => auth()->id(),
            'access_type'         => $validated['access_type'],
            'active_from'         => $validated['active_from'] ?? null,
            'active_until'        => $validated['active_until'] ?? null,
            'is_active'           => true,
            'is_downloadable'     => $request->boolean('is_downloadable'),
            'is_submission_enabled' => $request->boolean('is_submission_enabled'),
            'submission_types'    => $submissionTypes,
            'max_files'           => $request->boolean('is_submission_enabled') ? max(1, (int)$request->input('max_files', 1)) : 1,
            'deadline_at'         => $validated['deadline_at'] ?? null,
            'submission_instructions' => $validated['submission_instructions'] ?? null,
            'allowed_extensions'  => $validated['allowed_extensions'] ?? [],
            'classes'             => $classes,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('soals', 'slug')) {
            $soalData['slug'] = Soal::generateUniqueSlug($validated['title']);
        }

        $soal = Soal::create($soalData);

        // 1. Simpan lampiran file fisik (maksimal 15 file)
        if ($request->hasFile('files')) {
            $fileDl = $request->input('file_downloadable', []);
            $masterDl = $request->boolean('is_downloadable');

            foreach ($request->file('files') as $idx => $file) {
                $canDownload = isset($fileDl[$idx]) ? (bool)$fileDl[$idx] : $masterDl;

                $soal->attachments()->create([
                    'type'               => 'file',
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_type'          => $file->getClientOriginalExtension(),
                    'file_size'          => $file->getSize(),
                    'file_path'          => $file->store('soals', 'private'),
                    'is_downloadable'    => $canDownload,
                ]);
            }
        }

        // 2. Simpan lampiran link eksternal (jika ada)
        if ($request->filled('links') && is_array($request->links)) {
            foreach ($request->links as $link) {
                if (!empty($link['url'])) {
                    $soal->attachments()->create([
                        'type'               => 'link',
                        'file_original_name' => !empty($link['title']) ? $link['title'] : $link['url'],
                        'file_type'          => 'link',
                        'link_url'           => $link['url'],
                        'is_downloadable'    => false,
                    ]);
                }
            }
        }

        return redirect()->route('admin.soal.index')
            ->with('success', 'Dokumen soal berhasil ditambahkan!');
    }

    public function show(Soal $soal)
    {
        $this->authorizeAccess($soal);
        $submissions = Submission::with('files')->where('soal_id', $soal->id)->orderBy('created_at', 'desc')->get();
        $categories  = Category::all();
        return view('admin.soal.show', compact('soal', 'submissions', 'categories'));
    }

    public function submissionsData(Request $request, Soal $soal)
    {
        $this->authorizeAccess($soal);

        $submissions = Submission::with('files')->where('soal_id', $soal->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $count = $submissions->count();
        $maxUpdated = $submissions->max('updated_at')?->timestamp ?? 0;
        $maxId = $submissions->max('id') ?? 0;
        $checksum = md5("{$soal->id}_{$count}_{$maxUpdated}_{$maxId}");

        $clientChecksum = $request->query('checksum');
        $force = $request->boolean('force');

        if (!$force && $clientChecksum === $checksum) {
            return response()->json([
                'success'  => true,
                'changed'  => false,
                'checksum' => $checksum,
            ]);
        }

        $classCounts = [];
        if ($soal->hasClasses()) {
            foreach ($soal->getClassesList() as $cls) {
                $classCounts[$cls] = $submissions->where('kelas', $cls)->count();
            }
        }

        $counts = [
            'total'   => $count,
            'file'    => $submissions->filter(fn($s) => ($s->submission_type ?? 'file') === 'file')->count(),
            'link'    => $submissions->where('submission_type', 'link')->count(),
            'text'    => $submissions->where('submission_type', 'text')->count(),
            'classes' => $classCounts,
        ];

        $html = view('admin.soal.partials.submissions-table', compact('soal', 'submissions'))->render();

        return response()->json([
            'success'  => true,
            'changed'  => true,
            'checksum' => $checksum,
            'counts'   => $counts,
            'html'     => $html,
        ]);
    }

    public function edit(Soal $soal)
    {
        $this->authorizeAccess($soal);
        return redirect()->route('admin.soal.show', $soal)->with('open_edit_modal', true);
    }

    public function update(Request $request, Soal $soal)
    {
        $this->authorizeAccess($soal);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'content_text' => 'nullable|string',
            'password'     => 'nullable|string|min:4',
            'category_id'  => 'nullable|exists:categories,id',
            'access_type'  => 'required|in:lifetime,scheduled',
            'active_from'  => 'nullable|required_if:access_type,scheduled|date',
            'active_until' => 'nullable|required_if:access_type,scheduled|date|after:active_from',
            'is_active'    => 'boolean',
            'is_downloadable' => 'boolean',
            'kode_unik'    => ['required', 'string', 'max:20', Rule::unique('soals', 'kode_unik')->ignore($soal->id)->whereNull('deleted_at')],
            'is_submission_enabled' => 'boolean',
            'submission_types' => 'nullable|array',
            'submission_types.*' => 'string|in:file,link,text',
            'deadline_at' => 'nullable|required_if:is_submission_enabled,1|date',
            'submission_instructions' => 'nullable|string',
            'max_files'    => 'nullable|integer|min:1|max:20',
            'allowed_extensions' => 'nullable|required_if:is_submission_enabled,1|array',
            'allowed_extensions.*' => 'string|in:all,pdf,word,excel,ppt,image,archive,pkt',
            'classes'      => 'nullable|string|max:500',
            'files'        => 'nullable|array|max:15',
            'files.*'      => 'file|max:5120',
            'file_downloadable' => 'nullable|array',
            'attachment_downloadable' => 'nullable|array',
            'links'        => 'nullable|array|max:15',
            'links.*.title'=> 'nullable|string|max:255',
            'links.*.url'  => 'nullable|url:http,https|max:2000',
        ]);

        // Validasi keamanan file lampiran baru (anti-malware, anti-shell, anti-executable)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                [$isSafe, $errorMsg] = \App\Services\FileSecurityService::validate($file);
                if (!$isSafe) {
                    return back()->withInput()->withErrors([
                        'files' => "File lampiran baru \"{$file->getClientOriginalName()}\" ditolak demi keamanan: {$errorMsg}"
                    ]);
                }
            }
        }

        $submissionTypes = $request->input('submission_types');
        if ($request->boolean('is_submission_enabled') && empty($submissionTypes)) {
            $submissionTypes = ['file'];
        }

        $classes = null;
        if ($request->boolean('is_submission_enabled') && $request->filled('classes')) {
            $raw = is_array($request->classes) ? implode(',', $request->classes) : $request->classes;
            $parsed = array_values(array_unique(array_filter(array_map(fn($c) => strtoupper(trim($c)), explode(',', $raw)))));
            if (!empty($parsed)) {
                $classes = $parsed;
            }
        }

        $data = [
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'content_text' => $validated['content_text'] ?? null,
            'kode_unik'    => $validated['kode_unik'],
            'category_id'  => $validated['category_id'] ?? null,
            'access_type'  => $validated['access_type'],
            'active_from'  => $validated['active_from'] ?? null,
            'active_until' => $validated['active_until'] ?? null,
            'is_active'    => $request->boolean('is_active'),
            'is_downloadable' => $request->boolean('is_downloadable'),
            'is_submission_enabled' => $request->boolean('is_submission_enabled'),
            'submission_types'    => $submissionTypes,
            'max_files'           => $request->boolean('is_submission_enabled') ? max(1, (int)$request->input('max_files', 1)) : 1,
            'deadline_at'         => $validated['deadline_at'] ?? null,
            'submission_instructions' => $validated['submission_instructions'] ?? null,
            'allowed_extensions'  => $validated['allowed_extensions'] ?? [],
            'classes'             => $classes,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('soals', 'slug')) {
            $data['slug'] = ($soal->title !== $validated['title'] || empty($soal->slug))
                ? Soal::generateUniqueSlug($validated['title'], $soal->id)
                : $soal->slug;
        }

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $data['password']       = Hash::make($validated['password']);
            $data['plain_password'] = $validated['password'];
        }

        $soal->update($data);

        // Hapus file/lampiran spesifik jika diminta
        if ($request->has('delete_attachments') && is_array($request->delete_attachments)) {
            $attachmentsToDelete = $soal->attachments()->whereIn('id', $request->delete_attachments)->get();
            foreach ($attachmentsToDelete as $att) {
                if ($att->isFile() && $att->file_path) {
                    Storage::disk('private')->delete($att->file_path);
                }
                $att->delete();
            }
        }

        // Update hak download lampiran file yang sudah ada
        if ($request->has('attachment_downloadable') && is_array($request->attachment_downloadable)) {
            foreach ($soal->attachments()->where('type', 'file')->get() as $existingAtt) {
                $canDl = !empty($request->attachment_downloadable[$existingAtt->id]);
                $existingAtt->update(['is_downloadable' => $canDl]);
            }
        } elseif ($request->has('is_downloadable_bulk')) {
            // Jika admin melakukan mass toggle
            $soal->attachments()->where('type', 'file')->update(['is_downloadable' => $request->boolean('is_downloadable')]);
        }

        // Tambahkan file baru (maks total 15 file)
        if ($request->hasFile('files')) {
            $remainingCount = $soal->attachments()->where('type', 'file')->count();
            $newFilesCount = count($request->file('files'));
            
            if ($remainingCount + $newFilesCount > 15) {
                return redirect()->back()->withErrors(['files' => 'Total file tidak boleh melebihi 15.'])->withInput();
            }

            $fileDl = $request->input('file_downloadable', []);
            $masterDl = $request->boolean('is_downloadable');

            foreach ($request->file('files') as $idx => $file) {
                $canDownload = isset($fileDl[$idx]) ? (bool)$fileDl[$idx] : $masterDl;

                $soal->attachments()->create([
                    'type'               => 'file',
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_type'          => $file->getClientOriginalExtension(),
                    'file_size'          => $file->getSize(),
                    'file_path'          => $file->store('soals', 'private'),
                    'is_downloadable'    => $canDownload,
                ]);
            }
        }

        // Tambahkan link baru (jika ada)
        if ($request->filled('links') && is_array($request->links)) {
            foreach ($request->links as $link) {
                if (!empty($link['url'])) {
                    $soal->attachments()->create([
                        'type'               => 'link',
                        'file_original_name' => !empty($link['title']) ? $link['title'] : $link['url'],
                        'file_type'          => 'link',
                        'link_url'           => $link['url'],
                        'is_downloadable'    => false,
                    ]);
                }
            }
        }

        if ($request->input('redirect_to') === 'index') {
            return redirect()->route('admin.soal.index')
                ->with('success', 'Dokumen soal berhasil diperbarui!');
        }

        return redirect()->route('admin.soal.show', $soal)
            ->with('success', 'Dokumen soal berhasil diperbarui!');
    }

    public function destroy(Soal $soal)
    {
        $this->authorizeAccess($soal);

        $soal->delete();

        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    public function toggleActive(Soal $soal)
    {
        $this->authorizeAccess($soal);
        $soal->update(['is_active' => !$soal->is_active]);

        $msg = $soal->is_active ? 'Soal diaktifkan.' : 'Soal dinonaktifkan.';
        return back()->with('success', $msg);
    }

    public function preview(Soal $soal)
    {
        $this->authorizeAccess($soal);
        $soal->load(['category', 'admin', 'attachments']);
        $isAdminPreview = true;
        $isDeadlinePassed = $soal->deadline_at && now()->greaterThan($soal->deadline_at);

        return view('soal.view', compact('soal', 'isAdminPreview', 'isDeadlinePassed'));
    }

    public function downloadAttachment(\App\Models\SoalAttachment $attachment)
    {
        $soal = $attachment->soal;
        $this->authorizeAccess($soal);

        if (!$attachment->isFile() || empty($attachment->file_path)) {
            abort(404, 'Lampiran ini bukan file yang dapat diunduh.');
        }

        $realBase = realpath(Storage::disk('private')->path(''));
        $realFile = $this->resolvePrivateFile($attachment->file_path, $realBase);

        if ($realFile === null) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($realFile, basename($attachment->file_original_name));
    }

    /**
     * Pastikan admin hanya bisa akses soal miliknya.
     */
    private function authorizeAccess(Soal $soal): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $soal->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke soal ini.');
        }
    }

    public function trash(Request $request)
    {
        $user  = auth()->user();
        $query = $user->isSuperAdmin() ? Soal::onlyTrashed() : Soal::onlyTrashed()->where('user_id', $user->id);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $soals = $query->with(['category', 'admin'])->latest('deleted_at')->paginate(15);
        
        return view('admin.soal.trash', compact('soals'));
    }

    public function restore($id)
    {
        $user = auth()->user();
        $soal = $user->isSuperAdmin() ? Soal::onlyTrashed()->findOrFail($id) : Soal::onlyTrashed()->where('user_id', $user->id)->findOrFail($id);
        
        $soal->restore();

        return redirect()->route('admin.soal.trash')
            ->with('success', 'Soal berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $user = auth()->user();
        $soal = $user->isSuperAdmin() ? Soal::onlyTrashed()->findOrFail($id) : Soal::onlyTrashed()->where('user_id', $user->id)->findOrFail($id);
        
        $this->purgeSoalStorage($soal);

        $soal->forceDelete();

        return redirect()->route('admin.soal.trash')
            ->with('success', 'Soal, lampiran, dan file tugas berhasil dihapus permanen.');
    }

    /**
     * Hapus semua file fisik soal (lampiran + pengumpulan tugas).
     */
    private function purgeSoalStorage(Soal $soal): void
    {
        foreach ($soal->attachments as $attachment) {
            if ($attachment->isFile() && $attachment->file_path) {
                Storage::disk('private')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        Storage::disk('private')->deleteDirectory('submissions/' . $soal->id);
    }

    public function exportSubmissions(Request $request, Soal $soal)
    {
        $this->authorizeAccess($soal);

        $query = Submission::where('soal_id', $soal->id);
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        $submissions = $query->orderBy('kelas')->orderBy('nim')->get();

        $kelasSuffix = $request->filled('kelas') ? '_kelas_' . Str::slug($request->kelas) : '';
        $csvFileName = 'rekap_tugas_' . Str::slug($soal->title) . $kelasSuffix . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($submissions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM untuk Excel
            fputs($file, "sep=;\r\n"); // Instruksikan Excel agar otomatis memisahkan kolom dengan delimiter titik-koma (;)
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Kelas', 'Tipe Pengumpulan', 'File / Tautan / Teks', 'Waktu Pengumpulan'], ';');

            foreach ($submissions as $index => $sub) {
                $konten = match ($sub->submission_type) {
                    'link'  => $sub->link_url,
                    'text'  => $sub->text_content,
                    default => $sub->getFilesCount() > 1
                        ? $sub->getFilesList()->pluck('file_original_name')->filter()->implode(', ')
                        : ($sub->file_original_name ?: ($sub->file_path ? basename($sub->file_path) : '-')),
                };

                // Sanitasi setiap kolom terhadap Formula Injection (CWE-1236)
                fputcsv($file, [
                    $index + 1,
                    $this->sanitizeCsvCell($sub->nim),
                    $this->sanitizeCsvCell($sub->nama),
                    $this->sanitizeCsvCell($sub->kelas ?? '-'),
                    strtoupper($sub->submission_type ?? 'FILE'),
                    $this->sanitizeCsvCell($konten),
                    $sub->created_at->format('Y-m-d H:i:s')
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sanitasi sel CSV untuk mencegah Formula Injection (CWE-1236).
     */
    private function sanitizeCsvCell(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Karakter berbahaya pemicu eksekusi formula di Excel / Calc
        $triggers = ['=', '+', '-', '@', "\t", "\r"];
        if (in_array($value[0], $triggers, true)) {
            return "'" . $value;
        }

        return $value;
    }

    public function downloadSubmissionsZip(Request $request, Soal $soal)
    {
        $this->authorizeAccess($soal);

        // Hindari timeout dan kehabisan memori untuk arsip tugas berukuran besar
        @set_time_limit(0);
        $currentMemory = (int) ini_get('memory_limit');
        if ($currentMemory > 0 && $currentMemory < 512) {
            @ini_set('memory_limit', '512M');
        }

        $query = Submission::where('soal_id', $soal->id);
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        $submissions = $query->orderBy('kelas')->orderBy('nim')->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Belum ada tugas yang dikumpulkan' . ($request->filled('kelas') ? ' untuk kelas ' . $request->kelas : '') . '.');
        }

        // Hitung total ukuran akumulasi file untuk mencegah memory exhaustion & zip bomb
        $totalBytes = 0;
        foreach ($submissions as $sub) {
            if ($sub->isFile() && $sub->file_size) {
                $totalBytes += (int) $sub->file_size;
            }
        }

        // Batas maksimal arsip zip 1GB (1024 * 1024 * 1024 bytes)
        $maxBytes = 1024 * 1024 * 1024;
        if ($totalBytes > $maxBytes) {
            return back()->with('error', 'Ukuran total file tugas (' . round($totalBytes / (1024 * 1024 * 1024), 2) . ' GB) melebihi batas maksimal arsip ZIP (1 GB). Silakan unduh file tugas secara individual.');
        }

        if (!class_exists(ZipArchive::class)) {
            abort(500, 'Ekstensi ZIP tidak tersedia di server.');
        }

        $realBase = realpath(Storage::disk('private')->path(''));
        $tempDir  = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        } else {
            // Bersihkan file arsip sementara yang sudah berusia lebih dari 1 jam
            foreach (glob($tempDir . '/*.zip') as $oldZip) {
                if (is_file($oldZip) && (time() - filemtime($oldZip)) > 3600) {
                    @unlink($oldZip);
                }
            }
        }

        $tempPath = $tempDir . '/' . Str::uuid() . '.zip';
        $zip      = new ZipArchive();

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat arsip ZIP pada server.');
        }

        try {
            $usedNames   = [];
            $added       = 0;
            $filterKelas = $request->input('kelas');

            foreach ($submissions as $submission) {
                // Tentukan folder mahasiswa: {NIM}_{Nama}
                $safeNim           = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $submission->nim);
                $safeNama          = preg_replace('/[^a-zA-Z0-9\-_\s]/', '_', str_replace(' ', '_', $submission->nama));
                $studentFolderName = "{$safeNim}_{$safeNama}";

                $folderPrefix = '';
                if (empty($filterKelas) && ($soal->hasClasses() || !empty($submission->kelas))) {
                    $safeKelas    = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $submission->kelas ?: 'Lainnya');
                    $folderPrefix = "Kelas_{$safeKelas}/{$studentFolderName}/";
                } else {
                    $folderPrefix = "{$studentFolderName}/";
                }

                if ($submission->isLink()) {
                    $entryName = $folderPrefix . 'tautan_tugas.txt';
                    $content   = "PENGUMPULAN TUGAS BERUPA TAUTAN / LINK\n"
                               . "NIM        : " . $submission->nim . "\n"
                               . "Nama       : " . $submission->nama . "\n"
                               . ($submission->kelas ? ("Kelas      : " . $submission->kelas . "\n") : "")
                               . "Tautan     : " . $submission->link_url . "\n"
                               . "Waktu      : " . $submission->created_at->format('Y-m-d H:i:s') . "\n";
                    $zip->addFromString($entryName, $content);
                    $added++;
                    continue;
                }

                if ($submission->isText()) {
                    $entryName = $folderPrefix . 'jawaban_teks.txt';
                    $content   = "PENGUMPULAN TUGAS BERUPA TEKS\n"
                               . "NIM        : " . $submission->nim . "\n"
                               . "Nama       : " . $submission->nama . "\n"
                               . ($submission->kelas ? ("Kelas      : " . $submission->kelas . "\n") : "")
                               . "Waktu      : " . $submission->created_at->format('Y-m-d H:i:s') . "\n"
                               . "--------------------------------------------------------\n\n"
                               . $submission->text_content . "\n";
                    $zip->addFromString($entryName, $content);
                    $added++;
                    continue;
                }

                // Berkas fisik tugas mahasiswa (mendukung 1 atau banyak berkas)
                $files = $submission->getFilesList();
                foreach ($files as $file) {
                    if (empty($file->file_path)) continue;
                    $realFile = $this->resolvePrivateFile($file->file_path, $realBase);
                    if ($realFile !== null) {
                        $cleanOriginal = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $file->file_original_name ?: basename($file->file_path));
                        $entryName = $folderPrefix . $cleanOriginal;
                        $entryBase = $cleanOriginal;
                        $c = 1;
                        while (isset($usedNames[$entryName])) {
                            $ext = pathinfo($entryBase, PATHINFO_EXTENSION);
                            $stem = pathinfo($entryBase, PATHINFO_FILENAME);
                            $cleanOriginal = $ext ? "{$stem}_{$c}.{$ext}" : "{$stem}_{$c}";
                            $entryName = $folderPrefix . $cleanOriginal;
                            $c++;
                        }
                        $usedNames[$entryName] = true;
                        $zip->addFile($realFile, $entryName);
                        $added++;
                    }
                }
            }

            $zip->close();

            if ($added === 0) {
                @unlink($tempPath);
                return back()->with('error', 'Tidak ada file atau data tugas yang dapat dimasukkan ke dalam arsip ZIP.');
            }

            $kelasSuffix = $filterKelas ? ('_kelas_' . Str::slug($filterKelas)) : ($soal->hasClasses() ? '_semua_kelas' : '');
            $zipFileName = 'tugas_' . Str::slug($soal->title) . $kelasSuffix . '_' . now()->format('Ymd_His') . '.zip';

            return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            @unlink($tempPath);
            return back()->with('error', 'Terjadi kesalahan saat memproses file ZIP: ' . $e->getMessage());
        }
    }

    public function downloadSubmission($id)
    {
        $submission = Submission::with(['soal', 'files'])->findOrFail($id);
        $this->authorizeAccess($submission->soal);

        if (!class_exists(ZipArchive::class)) {
            abort(500, 'Ekstensi ZIP tidak tersedia di server.');
        }

        $realBase = realpath(Storage::disk('private')->path(''));
        $tempDir  = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . Str::uuid() . '.zip';
        $zip      = new ZipArchive();

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file ZIP pada server.');
        }

        $added = 0;
        $usedFileNames = [];

        if ($submission->isLink()) {
            $content = "PENGUMPULAN TUGAS BERUPA TAUTAN / LINK\n"
                     . "NIM        : " . $submission->nim . "\n"
                     . "Nama       : " . $submission->nama . "\n"
                     . ($submission->kelas ? ("Kelas      : " . $submission->kelas . "\n") : "")
                     . "Tautan     : " . $submission->link_url . "\n"
                     . "Waktu      : " . $submission->created_at->format('Y-m-d H:i:s') . "\n";
            $zip->addFromString('tautan_tugas.txt', $content);
            $added++;
        } elseif ($submission->isText()) {
            $content = "PENGUMPULAN TUGAS BERUPA TEKS\n"
                     . "NIM        : " . $submission->nim . "\n"
                     . "Nama       : " . $submission->nama . "\n"
                     . ($submission->kelas ? ("Kelas      : " . $submission->kelas . "\n") : "")
                     . "Waktu      : " . $submission->created_at->format('Y-m-d H:i:s') . "\n"
                     . "--------------------------------------------------------\n\n"
                     . $submission->text_content . "\n";
            $zip->addFromString('jawaban_teks.txt', $content);
            $added++;
        } else {
            $files = $submission->getFilesList();
            foreach ($files as $file) {
                if (empty($file->file_path)) continue;
                $realFile = $this->resolvePrivateFile($file->file_path, $realBase);
                if ($realFile !== null) {
                    $cleanName = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $file->file_original_name ?: basename($file->file_path));
                    $name = $cleanName;
                    $k = 1;
                    while (isset($usedFileNames[$name])) {
                        $ext = pathinfo($cleanName, PATHINFO_EXTENSION);
                        $stem = pathinfo($cleanName, PATHINFO_FILENAME);
                        $name = $ext ? "{$stem}_{$k}.{$ext}" : "{$stem}_{$k}";
                        $k++;
                    }
                    $usedFileNames[$name] = true;
                    $zip->addFile($realFile, $name);
                    $added++;
                }
            }
        }

        $zip->close();

        if ($added === 0) {
            @unlink($tempPath);
            return back()->with('error', 'Tidak ada berkas yang dapat diunduh untuk mahasiswa ini.');
        }

        $safeNim  = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $submission->nim);
        $safeNama = preg_replace('/[^a-zA-Z0-9\-_]/', '_', str_replace(' ', '_', $submission->nama));
        $zipFileName = 'tugas_' . $safeNim . '_' . $safeNama . '.zip';

        return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function previewSubmission(Request $request, $id)
    {
        $submission = Submission::with(['soal', 'files'])->findOrFail($id);
        $this->authorizeAccess($submission->soal);

        if ($submission->isLink()) {
            return redirect()->away($submission->link_url);
        }

        if ($submission->isText()) {
            return response()->json([
                'type'    => 'text',
                'nama'    => $submission->nama,
                'nim'     => $submission->nim,
                'kelas'   => $submission->kelas,
                'content' => $submission->text_content,
            ]);
        }

        $fileObj = null;
        if ($request->filled('file_id')) {
            $fileObj = $submission->files()->find($request->file_id);
        }
        if (!$fileObj) {
            $fileObj = $submission->getFilesList()->first();
        }

        if (!$fileObj || empty($fileObj->file_path)) {
            abort(404, 'Pengumpulan ini bukan file yang dapat dilihat.');
        }

        $realBase = realpath(Storage::disk('private')->path(''));
        $realFile = $this->resolvePrivateFile($fileObj->file_path, $realBase);

        if ($realFile === null) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        $mimeType = mime_content_type($realFile) ?: 'application/octet-stream';
        $ext = strtolower(pathinfo($fileObj->file_original_name ?: $fileObj->file_path, PATHINFO_EXTENSION));
        $inlineExts = ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'txt'];
        $disposition = in_array($ext, $inlineExts, true) ? 'inline' : 'attachment';

        return response()->file($realFile, [
            'Content-Type'           => $mimeType,
            'Content-Disposition'    => $disposition . '; filename="' . basename($fileObj->file_original_name ?: 'tugas') . '"',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function destroySubmission($id)
    {
        $submission = Submission::with('soal')->findOrFail($id);
        $soal = $submission->soal;
        $this->authorizeAccess($soal);

        // Hapus file fisik jika ada
        if ($submission->isFile() && $submission->file_path) {
            Storage::disk('private')->delete($submission->file_path);
        }

        $nim = $submission->nim;
        $nama = $submission->nama;
        $submission->delete();

        return back()->with('success', "Data pengumpulan tugas mahasiswa {$nama} ({$nim}) berhasil dihapus. Mahasiswa bersangkutan dapat mengumpulkan ulang.");
    }

    private function resolvePrivateFile(string $filePath, string $realBase): ?string
    {
        $safePath = Storage::disk('private')->path($filePath);
        $realFile = realpath($safePath);

        if ($realFile === false || !str_starts_with($realFile, $realBase) || !file_exists($realFile)) {
            return null;
        }

        return $realFile;
    }

    private function submissionZipEntryName(Submission $submission, array &$usedNames, ?string $customExt = null): string
    {
        $nim      = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $submission->nim);
        $nama     = preg_replace('/[^a-zA-Z0-9\-_\s]/', '_', str_replace(' ', '_', $submission->nama));
        $kelas    = $submission->kelas ? (preg_replace('/[^a-zA-Z0-9\-_]/', '_', $submission->kelas) . '_') : '';

        if ($customExt !== null) {
            $original = "jawaban.{$customExt}";
        } else {
            $original = $submission->file_original_name ?: basename($submission->file_path ?? 'tugas');
            $original = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $original);
        }

        $name = "{$kelas}{$nim}_{$nama}_{$original}";
        $base = $name;
        $i    = 1;

        while (isset($usedNames[$name])) {
            $ext  = pathinfo($base, PATHINFO_EXTENSION);
            $stem = pathinfo($base, PATHINFO_FILENAME);
            $name = $ext ? "{$stem}_{$i}.{$ext}" : "{$stem}_{$i}";
            $i++;
        }

        $usedNames[$name] = true;

        return $name;
    }
}
