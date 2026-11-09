<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class SoalAccessController extends Controller
{
    public function accessByCode(Request $request)
    {
        // Rate limiting: maks 10 pencarian kode per menit per sesi klien (aman untuk lab/NAT bersama)
        $clientIdentifier = $this->getClientRateLimitIdentifier($request);
        $codeThrottleKey = 'access_code_' . $clientIdentifier;
        if (RateLimiter::tooManyAttempts($codeThrottleKey, 10)) {
            $seconds = RateLimiter::availableIn($codeThrottleKey);
            return back()->with('error', 'Terlalu banyak percobaan pencarian kode. Coba lagi dalam ' . ceil($seconds) . ' detik.');
        }

        if ($request->has('kode_unik')) {
            $request->merge(['kode_unik' => trim($request->kode_unik)]);
        }

        $request->validate([
            'kode_unik' => 'required|string|max:50'
        ]);

        $inputCode = trim($request->kode_unik);
        $soal = Soal::where('kode_unik', $inputCode)
            ->orWhere('kode_unik', strtoupper($inputCode))
            ->first();

        if (!$soal) {
            RateLimiter::hit($codeThrottleKey, 60);
            return back()->with('error', 'Kode unik dokumen tidak ditemukan.');
        }

        RateLimiter::clear($codeThrottleKey);

        return redirect()->route('soal.access', ['token' => $soal->slug ?: $soal->unique_token]);
    }

    /**
     * Cari soal berdasarkan slug atau unique_token.
     */
    private function findSoalByIdentifier(string $identifier, array $with = []): Soal
    {
        $query = Soal::query();
        if (!empty($with)) {
            $query->with($with);
        }

        return $query->where(function ($q) use ($identifier) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('soals', 'slug')) {
                $q->where('slug', $identifier)
                  ->orWhere('unique_token', $identifier);
            } else {
                $q->where('unique_token', $identifier);
            }
        })->firstOrFail();
    }

    /**
     * Cek apakah sesi saat ini memiliki izin akses ke soal.
     */
    private function hasAuthorizedAccess(Soal $soal): bool
    {
        return (bool) (
            session("soal_access_{$soal->unique_token}")
            || ($soal->slug && session("soal_access_{$soal->slug}"))
            || session("soal_access_id_{$soal->id}")
        );
    }

    /**
     * Tampilkan halaman login soal (form password).
     */
    public function show(string $token)
    {
        $soal = $this->findSoalByIdentifier($token);

        return view('soal.access', compact('soal'));
    }

    /**
     * Proses verifikasi password soal.
     */
    public function verify(Request $request, string $token)
    {
        $request->validate(['password' => 'required|string']);

        $soal = $this->findSoalByIdentifier($token, ['category', 'admin']);

        // Kunci rate limit berbasis kombinasi ID soal dan sesi browser klien (terisolasi per komputer lab, anti-lockout massal)
        $clientIdentifier = $this->getClientRateLimitIdentifier($request);
        $throttleKey = 'soal_access_' . $soal->id . '_' . $clientIdentifier;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $waitText = $seconds > 60 ? ceil($seconds / 60) . ' menit' : $seconds . ' detik';
            return back()->withErrors(['password' => 'Terlalu banyak percobaan salah. Silakan coba lagi dalam ' . $waitText . '.']);
        }

        // Cek apakah soal bisa diakses
        $status = $soal->access_status;
        if ($status !== 'active') {
            $msg = match ($status) {
                'inactive'  => 'Soal ini sedang tidak aktif.',
                'scheduled' => 'Waktu akses soal belum dimulai. Silakan tunggu.',
                'expired'   => 'Waktu akses soal ini sudah berakhir.',
                default     => 'Soal tidak dapat diakses saat ini.'
            };

            return back()->withErrors(['password' => $msg]);
        }

        // Cek password
        $isMatch = Hash::check($request->password, $soal->password)
            || ($soal->plain_password && $request->password === $soal->plain_password);

        if (!$isMatch) {
            RateLimiter::hit($throttleKey, 60); // Cooldown 60 detik jika gagal 5x (ramah untuk praktikum lab)

            return back()->withErrors(['password' => 'Password salah. Silakan coba lagi.']);
        }

        // Akses berhasil
        RateLimiter::clear($throttleKey);

        // Simpan token akses di session agar tidak perlu input password lagi
        session([
            "soal_access_{$soal->unique_token}" => true,
            "soal_access_{$soal->slug}"        => true,
            "soal_access_id_{$soal->id}"       => true,
        ]);

        return redirect()->route('soal.view', ['token' => $token]);
    }

    /**
     * Tampilkan konten soal (setelah password benar).
     */
    public function view(Request $request, string $token)
    {
        $soal = $this->findSoalByIdentifier($token, ['category', 'admin', 'attachments']);

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            // Cek session akses
            if (!$this->hasAuthorizedAccess($soal)) {
                return redirect()->route('soal.access', ['token' => $token]);
            }

            // Cek masih bisa diakses
            $status = $soal->access_status;
            if ($status !== 'active') {
                session()->forget([
                    "soal_access_{$soal->unique_token}",
                    "soal_access_{$soal->slug}",
                    "soal_access_id_{$soal->id}",
                ]);
                return redirect()->route('soal.access', ['token' => $token])->withErrors([
                    'password' => 'Akses soal ini sudah berakhir.',
                ]);
            }
        }

        // Hitung apakah deadline sudah lewat
        $isDeadlinePassed = $soal->deadline_at && now()->greaterThan($soal->deadline_at);

        return view('soal.view', compact('soal', 'isAdminPreview', 'isDeadlinePassed'));
    }

    public function previewWrapper(Request $request, string $token, $attachmentId)
    {
        $soal = $this->findSoalByIdentifier($token);
        $attachment = $soal->attachments()->findOrFail($attachmentId);

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            if (!$this->hasAuthorizedAccess($soal)) abort(403, 'Akses tidak diizinkan.');
            if ($soal->access_status !== 'active') abort(403, 'Waktu akses soal ini sudah berakhir.');
        }

        $previewToken = hash_hmac('sha256', "preview-{$soal->unique_token}-{$attachment->id}", config('app.key'));

        return view('soal.preview-wrapper', compact('soal', 'attachment', 'isAdminPreview', 'previewToken'));
    }

    /**
     * Stream file PDF/lainnya langsung di browser (inline preview).
     */
    public function previewFile(Request $request, string $token, $attachmentId)
    {
        $soal = $this->findSoalByIdentifier($token);
        $attachment = $soal->attachments()->findOrFail($attachmentId);

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            if (!$this->hasAuthorizedAccess($soal)) {
                abort(403, 'Akses tidak diizinkan.');
            }

            if ($soal->access_status !== 'active') {
                abort(403, 'Waktu akses soal ini sudah berakhir.');
            }

            $ext = strtolower($attachment->file_type ?? pathinfo($attachment->file_original_name, PATHINFO_EXTENSION));
            $isPreviewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);

            // Jika file previewable dan unduhan dinonaktifkan:
            if ($isPreviewable && !$soal->is_downloadable) {
                $secFetchDest = strtolower($request->header('Sec-Fetch-Dest', ''));
                $secFetchMode = strtolower($request->header('Sec-Fetch-Mode', ''));

                // 1. Tangani jika dibuka langsung via address bar atau tab baru
                if ($secFetchDest === 'document' || $secFetchMode === 'navigate') {
                    return redirect()->route('soal.preview_wrapper', [
                        'token'      => $soal->slug ?: $soal->unique_token,
                        'attachment' => $attachmentId,
                    ]);
                }

                // 2. Wajibkan header autentikasi preview aman
                $expectedToken = hash_hmac('sha256', "preview-{$soal->unique_token}-{$attachment->id}", config('app.key'));
                $providedToken = $request->header('X-Lab-Preview');

                if (!$providedToken || !hash_equals($expectedToken, $providedToken)) {
                    abort(403, 'Akses langsung tidak diizinkan. Silakan buka file melalui preview resmi.');
                }
            }
        }

        $realFile = $this->resolveStorageFile($attachment->file_path);

        $mimeType = mime_content_type($realFile);

        return response()->file($realFile, [
            'Content-Type'              => $mimeType,
            'Content-Disposition'       => 'inline; filename="' . basename($attachment->file_original_name) . '"',
            'X-Frame-Options'           => 'SAMEORIGIN',
            'X-Content-Type-Options'    => 'nosniff',
            'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'                    => 'no-cache',
        ]);
    }

    public function downloadFile(Request $request, string $token, $attachmentId)
    {
        $soal = $this->findSoalByIdentifier($token);
        $attachment = $soal->attachments()->findOrFail($attachmentId);

        if (!$attachment->isFile() || empty($attachment->file_path)) {
            abort(404, 'Lampiran ini bukan file yang dapat diunduh.');
        }

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            if (!$this->hasAuthorizedAccess($soal)) {
                abort(403, 'Akses tidak diizinkan.');
            }

            if ($soal->access_status !== 'active') {
                abort(403, 'Waktu akses soal ini sudah berakhir.');
            }

            if (!$attachment->canDownload()) {
                abort(403, 'File soal ini tidak diizinkan untuk diunduh.');
            }
        }

        $realFile = $this->resolveStorageFile($attachment->file_path);

        return response()->download($realFile, basename($attachment->file_original_name));
    }

    public function status(string $token)
    {
        $soal = $this->findSoalByIdentifier($token);
        if (!$soal) {
            return response()->json(['access_status' => 'deleted']);
        }
        return response()->json([
            'access_status'   => $soal->access_status,
            'is_downloadable' => $soal->is_downloadable,
        ]);
    }

    public function submitTask(Request $request, string $token)
    {
        // Rate limiting: maks 15 pengiriman per sesi per menit
        $rateLimitKey = 'submit_task_' . $request->session()->getId();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 15)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json(['message' => 'Terlalu banyak permintaan pengumpulan. Coba lagi dalam ' . ceil($seconds) . ' detik.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $soal = $this->findSoalByIdentifier($token);

        // Cek fitur submission aktif
        if (!$soal->is_submission_enabled) {
            return response()->json(['message' => 'Fitur pengumpulan tugas tidak diaktifkan untuk soal ini.'], 403);
        }

        // Cek deadline
        if ($soal->deadline_at && now()->greaterThan($soal->deadline_at)) {
            return response()->json(['message' => 'Batas waktu pengumpulan tugas telah berakhir.'], 403);
        }

        // Cek akses soal masih aktif
        if ($soal->access_status !== 'active') {
            return response()->json(['message' => 'Tugas tidak dapat dikumpulkan, waktu akses telah berakhir.'], 403);
        }

        if ($this->isAuthorizedAdminPreview($request, $soal)) {
            return response()->json(['message' => 'Pengumpulan tugas tidak tersedia dalam mode preview admin.'], 403);
        }

        if (!$this->hasAuthorizedAccess($soal)) {
            return response()->json(['message' => 'Sesi akses tidak valid. Silakan masukkan password soal terlebih dahulu.'], 403);
        }

        if ($request->has('nim')) {
            $request->merge(['nim' => trim($request->nim)]);
        }
        if ($request->has('nama')) {
            $request->merge(['nama' => trim($request->nama)]);
        }
        if ($request->has('kelas')) {
            $request->merge(['kelas' => strtoupper(trim($request->kelas))]);
        }

        // Validasi pemilihan kelas jika soal mewajibkan kelas
        if ($soal->hasClasses()) {
            $request->validate([
                'kelas' => ['required', 'string', \Illuminate\Validation\Rule::in($soal->getClassesList())],
            ], [
                'kelas.required' => 'Silakan pilih kelas Anda terlebih dahulu.',
                'kelas.in'       => 'Kelas yang dipilih tidak terdaftar untuk tugas ini.',
            ]);
        }
        $kelas = $soal->hasClasses() ? $request->input('kelas') : ($request->input('kelas') ?: null);

        $submissionType = $request->input('submission_type', 'file');
        $allowedTypes   = $soal->submission_types ?: ['file'];

        if (!in_array($submissionType, $allowedTypes, true)) {
            return response()->json(['message' => 'Tipe pengumpulan (' . $submissionType . ') tidak diizinkan untuk tugas ini.'], 422);
        }

        // ─── Tipe 1: Link URL ─────────────────────────────────────────────
        if ($submissionType === 'link') {
            $request->validate([
                'nim'      => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/',
                'nama'     => 'required|string|max:255',
                'link_url' => 'required|url:http,https|max:2000',
            ]);

            $existing = Submission::where('soal_id', $soal->id)->where('nim', $request->nim)->first();
            $isUpdate = $existing !== null;

            if ($existing && $existing->file_path) {
                Storage::disk('private')->delete($existing->file_path);
            }

            $subData = [
                'soal_id'            => $soal->id,
                'nim'                => $request->nim,
                'nama'               => $request->nama,
                'kelas'              => $kelas,
                'submission_type'    => 'link',
                'link_url'           => $request->link_url,
                'text_content'       => null,
                'file_path'          => null,
                'file_original_name' => null,
                'file_extension'     => null,
                'file_size'          => null,
            ];

            if ($existing) {
                foreach ($existing->files as $f) {
                    if ($f->file_path) Storage::disk('private')->delete($f->file_path);
                }
                $existing->files()->delete();
                if ($existing->file_path) {
                    Storage::disk('private')->delete($existing->file_path);
                }
                $existing->update($subData);
            } else {
                Submission::create($subData);
            }

            return response()->json([
                'message'   => $isUpdate ? 'Tautan tugas berhasil diperbarui.' : 'Tautan tugas berhasil dikumpulkan.',
                'is_update' => $isUpdate,
            ]);
        }

        // ─── Tipe 2: Teks Langsung ─────────────────────────────────────────
        if ($submissionType === 'text') {
            $request->validate([
                'nim'          => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/',
                'nama'         => 'required|string|max:255',
                'text_content' => 'required|string|min:3|max:50000',
            ]);

            $existing = Submission::where('soal_id', $soal->id)->where('nim', $request->nim)->first();
            $isUpdate = $existing !== null;

            if ($existing && $existing->file_path) {
                Storage::disk('private')->delete($existing->file_path);
            }

            $subData = [
                'soal_id'            => $soal->id,
                'nim'                => $request->nim,
                'nama'               => $request->nama,
                'kelas'              => $kelas,
                'submission_type'    => 'text',
                'link_url'           => null,
                'text_content'       => $request->text_content,
                'file_path'          => null,
                'file_original_name' => null,
                'file_extension'     => null,
                'file_size'          => null,
            ];

            if ($existing) {
                foreach ($existing->files as $f) {
                    if ($f->file_path) Storage::disk('private')->delete($f->file_path);
                }
                $existing->files()->delete();
                if ($existing->file_path) {
                    Storage::disk('private')->delete($existing->file_path);
                }
                $existing->update($subData);
            } else {
                Submission::create($subData);
            }

            return response()->json([
                'message'   => $isUpdate ? 'Jawaban teks berhasil diperbarui.' : 'Jawaban teks berhasil dikumpulkan.',
                'is_update' => $isUpdate,
            ]);
        }

        // ─── Tipe 3: Upload File dengan Security Inspection (Dukung Multi-File) ─
        $request->validate([
            'nim'     => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/',
            'nama'    => 'required|string|max:255',
            'file'    => 'nullable|file|max:10240',
            'files'   => 'nullable|array',
            'files.*' => 'file|max:10240',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $rawFiles = $request->file('files');
            $uploadedFiles = is_array($rawFiles) ? $rawFiles : [$rawFiles];
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            return response()->json(['message' => 'Silakan pilih berkas tugas terlebih dahulu.'], 422);
        }

        $maxFiles = $soal->getMaxFiles();
        if (count($uploadedFiles) > $maxFiles) {
            return response()->json([
                'message' => "Jumlah berkas melebihi batas maksimal ({$maxFiles} file). Anda memilih " . count($uploadedFiles) . " file."
            ], 422);
        }

        // Validasi keamanan setiap file (anti-shell, anti-executable, sniffing, double-extension)
        foreach ($uploadedFiles as $file) {
            [$isSafe, $errorMsg] = \App\Services\FileSecurityService::validate($file, $soal->allowed_extensions ?? []);
            if (!$isSafe) {
                return response()->json(['message' => "Berkas \"{$file->getClientOriginalName()}\": {$errorMsg}"], 422);
            }
        }

        $existing = Submission::with('files')
            ->where('soal_id', $soal->id)
            ->where('nim', $request->nim)
            ->first();

        $isUpdate = $existing !== null;

        // Bersihkan berkas fisik lama jika mahasiswa mengumpulkan ulang
        if ($existing) {
            foreach ($existing->files as $oldFile) {
                if ($oldFile->file_path) {
                    Storage::disk('private')->delete($oldFile->file_path);
                }
            }
            if ($existing->file_path) {
                Storage::disk('private')->delete($existing->file_path);
            }
            $existing->files()->delete();
        }

        $kelasPrefix = $kelas ? (preg_replace('/[^a-zA-Z0-9\-_]/', '_', $kelas) . '_') : '';
        $nimClean    = preg_replace('/[^a-zA-Z0-9\-]/', '_', $request->nim);
        $namaClean   = preg_replace('/[^a-zA-Z0-9\s\-]/', '_', $request->nama);

        $savedFilesData = [];
        foreach ($uploadedFiles as $idx => $file) {
            $suffix = count($uploadedFiles) > 1 ? ('_' . ($idx + 1)) : '';
            $fileName = $kelasPrefix . $nimClean . '_' . str_replace(' ', '_', $namaClean) . $suffix . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('submissions/' . $soal->id, $fileName, 'private');

            $savedFilesData[] = [
                'file_path'          => $path,
                'file_original_name' => $file->getClientOriginalName(),
                'file_extension'     => $file->getClientOriginalExtension(),
                'file_size'          => $file->getSize(),
            ];
        }

        $firstFile = $savedFilesData[0];
        $subData = [
            'soal_id'            => $soal->id,
            'nim'                => $request->nim,
            'nama'               => $request->nama,
            'kelas'              => $kelas,
            'submission_type'    => 'file',
            'link_url'           => null,
            'text_content'       => null,
            'file_path'          => $firstFile['file_path'],
            'file_original_name' => $firstFile['file_original_name'],
            'file_extension'     => $firstFile['file_extension'],
            'file_size'          => $firstFile['file_size'],
        ];

        if ($existing) {
            $existing->update($subData);
            $submission = $existing;
        } else {
            $submission = Submission::create($subData);
        }

        // Simpan ke relasi submission_files
        foreach ($savedFilesData as $fData) {
            $submission->files()->create($fData);
        }

        $count = count($savedFilesData);
        $message = $isUpdate
            ? "Tugas berhasil diperbarui ({$count} berkas). Berkas pengumpulan sebelumnya telah diganti."
            : "Tugas berhasil dikumpulkan ({$count} berkas).";

        return response()->json(['message' => $message, 'is_update' => $isUpdate]);
    }

    public function checkSubmission(Request $request, string $token)
    {
        $soal = $this->findSoalByIdentifier($token);
        if (!$this->hasAuthorizedAccess($soal)) {
            return response()->json(['has_submitted' => false], 403);
        }

        $nim = trim((string) $request->query('nim', ''));
        if (!$nim) {
            return response()->json(['has_submitted' => false]);
        }

        $existing = Submission::where('soal_id', $soal->id)->where('nim', $nim)->first();
        if (!$existing) {
            return response()->json(['has_submitted' => false]);
        }

        $identifier = match ($existing->submission_type) {
            'link' => $existing->link_url,
            'text' => \Illuminate\Support\Str::limit($existing->text_content, 35),
            default => $existing->getFilesCount() > 1
                ? ($existing->getFilesCount() . ' berkas (' . $existing->file_original_name . ', dll)')
                : $existing->file_original_name,
        };

        return response()->json([
            'has_submitted'   => true,
            'nim'             => $existing->nim,
            'nama'            => $existing->nama,
            'kelas'           => $existing->kelas,
            'submission_type' => $existing->submission_type,
            'files_count'     => $existing->getFilesCount(),
            'identifier'      => $identifier,
            'submitted_at'    => $existing->updated_at ? $existing->updated_at->format('d M Y, H:i') : $existing->created_at->format('d M Y, H:i'),
        ]);
    }

    public function trackTask(Request $request)
    {
        // Rate limiting: maks 15 permintaan pelacakan per menit terisolasi per sesi klien
        $rateLimitKey = 'track_task_' . $this->getClientRateLimitIdentifier($request);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 15)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json(['message' => 'Terlalu banyak permintaan pelacakan. Coba lagi dalam ' . ceil($seconds) . ' detik.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        if ($request->has('nim')) {
            $request->merge(['nim' => trim($request->nim)]);
        }

        $request->validate([
            'nim' => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/'
        ]);

        $submissions = \App\Models\Submission::where('nim', $request->nim)
            ->with('soal:id,title')
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $submissions->map(function ($sub) {
            $identifier = match ($sub->submission_type) {
                'link' => $sub->link_url,
                'text' => \Illuminate\Support\Str::limit($sub->text_content, 40),
                default => $sub->file_original_name,
            };

            return [
                'id'              => $sub->id,
                'soal_title'      => $sub->soal->title ?? 'Dokumen Dihapus',
                'nama'            => $sub->nama,
                'kelas'           => $sub->kelas,
                'submission_type' => $sub->submission_type ?? 'file',
                'file_name'       => $identifier,
                'link_url'        => $sub->link_url,
                'created_at'      => $sub->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json(['data' => $formatted]);
    }

    /**
     * Validasi dan kembalikan path absolut file dari private disk.
     * Abort 403 jika path traversal terdeteksi, abort 404 jika file tidak ada.
     */
    private function resolveStorageFile(string $filePath): string
    {
        $safePath = Storage::disk('private')->path($filePath);
        $realBase = realpath(Storage::disk('private')->path(''));
        $realFile = realpath($safePath);

        if ($realFile === false || !str_starts_with($realFile, $realBase)) {
            abort(403, 'Akses file tidak diizinkan.');
        }

        if (!file_exists($realFile)) {
            abort(404, 'File tidak ditemukan.');
        }

        return $realFile;
    }

    /**
     * Preview admin: superadmin semua soal; admin hanya soal miliknya.
     */
    private function isAuthorizedAdminPreview(Request $request, Soal $soal): bool
    {
        if (!$request->has('admin_preview') || !auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && (int) $soal->user_id === (int) $user->id;
    }

    /**
     * Dapatkan pengenal unik klien untuk rate limiting yang ramah lingkungan Lab/NAT.
     * Menggunakan session browser unik per komputer, dengan fallback IP jaringan.
     */
    protected function getClientRateLimitIdentifier(Request $request): string
    {
        if (app()->runningUnitTests()) {
            $testSession = $request->header('X-Test-Session-Id');
            if ($testSession) {
                return 'test_' . $testSession;
            }
            return 'test_' . $request->ip();
        }

        $sessionId = $request->session()->getId();
        if (!empty($sessionId)) {
            return 'sess_' . $sessionId;
        }

        return 'ip_' . $request->ip();
    }
}
