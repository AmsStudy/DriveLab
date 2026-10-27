<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\Category;
use App\Models\Soal;
use App\Models\Submission;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $soals      = $query->with(['category', 'admin'])->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.soal.index', compact('soals', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.soal.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'password'     => 'required|string|min:4',
            'category_id'  => 'nullable|exists:categories,id',
            'access_type'  => 'required|in:lifetime,scheduled',
            'active_from'  => 'nullable|required_if:access_type,scheduled|date',
            'active_until' => 'nullable|required_if:access_type,scheduled|date|after:active_from',
            'files'        => 'nullable|array|max:5',
            'files.*'      => 'file|max:5120', // 5MB per file
            'is_downloadable' => 'boolean',
            'kode_unik'    => 'required|string|max:20|unique:soals,kode_unik',
            'is_submission_enabled' => 'boolean',
            'deadline_at' => 'nullable|required_if:is_submission_enabled,1|date',
            'submission_instructions' => 'nullable|string',
            'allowed_extensions' => 'nullable|required_if:is_submission_enabled,1|array',
            'allowed_extensions.*' => 'string|in:pdf,word,excel,ppt,image,archive,pkt',
        ]);

        do {
            $token = Str::random(6);
        } while (Soal::where('unique_token', $token)->exists());

        $soal = Soal::create([
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'kode_unik'           => $validated['kode_unik'],
            'unique_token'        => $token,
            'password'            => Hash::make($validated['password']),
            'category_id'         => $validated['category_id'] ?? null,
            'user_id'             => auth()->id(),
            'access_type'         => $validated['access_type'],
            'active_from'         => $validated['active_from'] ?? null,
            'active_until'        => $validated['active_until'] ?? null,
            'is_active'           => true,
            'is_downloadable'     => $request->boolean('is_downloadable'),
            'is_submission_enabled' => $request->boolean('is_submission_enabled'),
            'deadline_at'         => $validated['deadline_at'] ?? null,
            'submission_instructions' => $validated['submission_instructions'] ?? null,
            'allowed_extensions'  => $validated['allowed_extensions'] ?? [],
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $soal->attachments()->create([
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_type'          => $file->getClientOriginalExtension(),
                    'file_size'          => $file->getSize(),
                    'file_path'          => $file->store('soals', 'private'),
                ]);
            }
        }

        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function show(Soal $soal)
    {
        $this->authorizeAccess($soal);
        $logs = AccessLog::where('soal_id', $soal->id)->latest('accessed_at')->paginate(20);
        $submissions = Submission::where('soal_id', $soal->id)->orderBy('created_at', 'desc')->get();
        return view('admin.soal.show', compact('soal', 'logs', 'submissions'));
    }

    public function edit(Soal $soal)
    {
        $this->authorizeAccess($soal);
        $categories = Category::all();
        return view('admin.soal.edit', compact('soal', 'categories'));
    }

    public function update(Request $request, Soal $soal)
    {
        $this->authorizeAccess($soal);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'password'     => 'nullable|string|min:4',
            'category_id'  => 'nullable|exists:categories,id',
            'access_type'  => 'required|in:lifetime,scheduled',
            'active_from'  => 'nullable|required_if:access_type,scheduled|date',
            'active_until' => 'nullable|required_if:access_type,scheduled|date|after:active_from',
            'is_active'    => 'boolean',
            'is_downloadable' => 'boolean',
            'kode_unik'    => 'required|string|max:20|unique:soals,kode_unik,' . $soal->id,
            'is_submission_enabled' => 'boolean',
            'deadline_at' => 'nullable|required_if:is_submission_enabled,1|date',
            'submission_instructions' => 'nullable|string',
            'allowed_extensions' => 'nullable|required_if:is_submission_enabled,1|array',
            'allowed_extensions.*' => 'string|in:pdf,word,excel,ppt,image,archive,pkt',
            'files'        => 'nullable|array|max:5',
            'files.*'      => 'file|max:5120',
        ]);

        $data = [
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'kode_unik'    => $validated['kode_unik'],
            'category_id'  => $validated['category_id'] ?? null,
            'access_type'  => $validated['access_type'],
            'active_from'  => $validated['active_from'] ?? null,
            'active_until' => $validated['active_until'] ?? null,
            'is_active'    => $request->boolean('is_active'),
            'is_downloadable' => $request->boolean('is_downloadable'),
            'is_submission_enabled' => $request->boolean('is_submission_enabled'),
            'deadline_at'         => $validated['deadline_at'] ?? null,
            'submission_instructions' => $validated['submission_instructions'] ?? null,
            'allowed_extensions'  => $validated['allowed_extensions'] ?? [],
        ];

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $soal->update($data);

        // Hapus file spesifik jika diminta
        if ($request->has('delete_attachments') && is_array($request->delete_attachments)) {
            $attachmentsToDelete = $soal->attachments()->whereIn('id', $request->delete_attachments)->get();
            foreach ($attachmentsToDelete as $att) {
                Storage::disk('private')->delete($att->file_path);
                $att->delete();
            }
        }

        // Tambahkan file baru
        if ($request->hasFile('files')) {
            $remainingCount = $soal->attachments()->count();
            $newFilesCount = count($request->file('files'));
            
            if ($remainingCount + $newFilesCount > 5) {
                return redirect()->back()->withErrors(['files' => 'Total file tidak boleh melebihi 5.'])->withInput();
            }

            foreach ($request->file('files') as $file) {
                $soal->attachments()->create([
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_type'          => $file->getClientOriginalExtension(),
                    'file_size'          => $file->getSize(),
                    'file_path'          => $file->store('soals', 'private'),
                ]);
            }
        }

        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil diperbarui!');
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
            Storage::disk('private')->delete($attachment->file_path);
            $attachment->delete();
        }

        Storage::disk('private')->deleteDirectory('submissions/' . $soal->id);
    }

    public function exportSubmissions(Soal $soal)
    {
        $this->authorizeAccess($soal);

        $submissions = Submission::where('soal_id', $soal->id)->orderBy('created_at', 'desc')->get();

        $csvFileName = 'rekap_tugas_' . Str::slug($soal->title) . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($submissions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIM', 'Nama', 'File Asli', 'Waktu Pengumpulan']);

            foreach ($submissions as $index => $sub) {
                fputcsv($file, [
                    $index + 1,
                    $sub->nim,
                    $sub->nama,
                    $sub->file_original_name,
                    $sub->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadSubmissionsZip(Soal $soal)
    {
        $this->authorizeAccess($soal);

        // Hindari timeout dan kehabisan memori untuk arsip tugas berukuran besar
        @set_time_limit(0);
        $currentMemory = (int) ini_get('memory_limit');
        if ($currentMemory > 0 && $currentMemory < 512) {
            @ini_set('memory_limit', '512M');
        }

        $submissions = Submission::where('soal_id', $soal->id)->orderBy('nim')->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Belum ada tugas yang dikumpulkan.');
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
            $usedNames = [];
            $added     = 0;

            foreach ($submissions as $submission) {
                $realFile = $this->resolvePrivateFile($submission->file_path, $realBase);

                if ($realFile === null) {
                    continue;
                }

                $zip->addFile($realFile, $this->submissionZipEntryName($submission, $usedNames));
                $added++;
            }

            $zip->close();

            if ($added === 0) {
                @unlink($tempPath);
                return back()->with('error', 'Tidak ada file fisik tugas yang dapat diunduh.');
            }

            $zipFileName = 'tugas_' . Str::slug($soal->title) . '_' . now()->format('Ymd_His') . '.zip';

            return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            @unlink($tempPath);
            return back()->with('error', 'Terjadi kesalahan saat memproses file ZIP: ' . $e->getMessage());
        }
    }

    public function downloadSubmission($id)
    {
        $submission = Submission::findOrFail($id);
        $this->authorizeAccess($submission->soal);

        $realBase = realpath(Storage::disk('private')->path(''));
        $realFile = $this->resolvePrivateFile($submission->file_path, $realBase);

        if ($realFile === null) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($realFile, basename($submission->file_original_name));
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

    private function submissionZipEntryName(Submission $submission, array &$usedNames): string
    {
        $nim      = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $submission->nim);
        $nama     = preg_replace('/[^a-zA-Z0-9\-_\s]/', '_', str_replace(' ', '_', $submission->nama));
        $original = $submission->file_original_name ?: basename($submission->file_path);
        $original = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $original);

        $name = "{$nim}_{$nama}_{$original}";
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
