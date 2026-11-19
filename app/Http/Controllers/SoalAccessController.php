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
            ->orWhere('unique_token', $inputCode)
            ->orWhere('unique_token', strtoupper($inputCode))
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
            if ($isPreviewable && !$attachment->canDownload()) {
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
            return response()->json([
                'access_status'   => 'deleted',
                'is_downloadable' => false,
                'title'           => 'Dokumen Tidak Ditemukan',
                'message'         => 'Dokumen soal ini telah dihapus atau tidak tersedia lagi.',
            ]);
        }

        $accessStatus = $soal->access_status;
        $isDeadlinePassed = (bool) ($soal->deadline_at && now()->greaterThan($soal->deadline_at));

        $title = match ($accessStatus) {
            'inactive'  => 'Akses Dihentikan Pengawas',
            'expired'   => 'Waktu Akses Telah Berakhir',
            'scheduled' => 'Akses Belum Dimulai',
            default     => ($isDeadlinePassed ? 'Batas Waktu Berakhir' : 'Akses Aktif'),
        };

        $message = match ($accessStatus) {
            'inactive'  => 'Pengawas atau instruktur laboratorium telah menghentikan akses ke dokumen soal ini.',
            'expired'   => 'Waktu akses dokumen soal ini telah berakhir sesuai jadwal laboratorium.',
            'scheduled' => 'Jadwal akses dokumen soal ini belum dimulai.',
            default     => ($isDeadlinePassed ? 'Batas waktu pengumpulan tugas telah berakhir.' : 'Akses dokumen soal sedang aktif.'),
        };

        return response()->json([
            'access_status'      => $accessStatus,
            'is_downloadable'    => (bool) $soal->is_downloadable,
            'is_deadline_passed' => $isDeadlinePassed,
            'title'              => $title,
            'message'            => $message,
            'active_until'       => $soal->active_until ? $soal->active_until->format('d M Y, H:i') : null,
            'deadline_at'        => $soal->deadline_at ? $soal->deadline_at->format('d M Y, H:i') : null,
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
        $allowedTypes   = $soal->submission_types ?: ['file'];

        // Validasi dasar identitas mahasiswa
        $request->validate([
            'nim'  => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/',
            'nama' => 'required|string|max:255',
        ]);

        $hasLinkAllowed = in_array('link', $allowedTypes, true);
        $hasTextAllowed = in_array('text', $allowedTypes, true);
        $hasFileAllowed = in_array('file', $allowedTypes, true);

        $hasLinkInput = $hasLinkAllowed && $request->filled('link_url');
        $hasTextInput = $hasTextAllowed && $request->filled('text_content');

        $uploadedFiles = [];
        if ($hasFileAllowed) {
            if ($request->hasFile('files')) {
                $rawFiles = $request->file('files');
                $uploadedFiles = is_array($rawFiles) ? $rawFiles : [$rawFiles];
            } elseif ($request->hasFile('file')) {
                $uploadedFiles = [$request->file('file')];
            }
        }
        $hasFileInput = !empty($uploadedFiles);

        // Cari data pengumpulan mahasiswa sebelumnya untuk soal ini
        $existing = Submission::with('files')
            ->where('soal_id', $soal->id)
            ->where('nim', $request->nim)
            ->first();
        $isUpdate = $existing !== null;

        // Pastikan setidaknya ada satu lampiran yang diisi (atau berkas yang sudah ada sebelumnya pada update)
        $hasExistingFiles = $existing && $existing->hasFile();
        if (!$hasFileInput && !$hasLinkInput && !$hasTextInput && !$hasExistingFiles) {
            return response()->json([
                'message' => 'Silakan lampirkan tugas Anda (unggah berkas, tautan link, atau jawaban teks).'
            ], 422);
        }

        // ── Validasi Komponen Link (jika diisi) ──
        if ($hasLinkInput) {
            $request->validate([
                'link_url' => 'required|url:http,https|max:2000',
            ]);
        }

        // ── Validasi Komponen Teks (jika diisi) ──
        if ($hasTextInput) {
            $request->validate([
                'text_content' => 'required|string|min:3|max:50000',
            ]);
        }

        // ── Validasi Komponen File (jika diunggah) ──
        $savedFilesData = [];
        if ($hasFileInput) {
            $maxFileSizeMb = $soal->getMaxFileSizeMb();
            $maxFileSizeKb = $maxFileSizeMb * 1024;
            $maxFiles = $soal->getMaxFiles();

            if (count($uploadedFiles) > $maxFiles) {
                return response()->json([
                    'message' => "Jumlah berkas melebihi batas maksimal ({$maxFiles} file). Anda memilih " . count($uploadedFiles) . " file."
                ], 422);
            }

            foreach ($uploadedFiles as $file) {
                if ($file->getSize() > ($maxFileSizeKb * 1024)) {
                    return response()->json(['message' => "Ukuran berkas \"{$file->getClientOriginalName()}\" melebihi {$maxFileSizeMb} MB."], 422);
                }
                [$isSafe, $errorMsg] = \App\Services\FileSecurityService::validate($file, $soal->allowed_extensions ?? []);
                if (!$isSafe) {
                    return response()->json(['message' => "Berkas \"{$file->getClientOriginalName()}\": {$errorMsg}"], 422);
                }
            }

            // Bersihkan berkas fisik lama karena digantikan oleh berkas baru
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

            foreach ($uploadedFiles as $idx => $file) {
                $suffix = count($uploadedFiles) > 1 ? ('_' . ($idx + 1)) : '';
                $fileName = $kelasPrefix . $nimClean . '_' . str_replace(' ', '_', $namaClean) . $suffix . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('submissions/' . $soal->id, $fileName, 'private');

                $fileHash = @hash_file('sha256', $file->getRealPath()) ?: null;
                $savedFilesData[] = [
                    'file_path'          => $path,
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_extension'     => $file->getClientOriginalExtension(),
                    'file_size'          => $file->getSize(),
                    'file_hash'          => $fileHash,
                ];
            }
        }

        // Tentukan data yang akan disimpan / diperbarui
        $subData = [
            'soal_id' => $soal->id,
            'nim'     => $request->nim,
            'nama'    => $request->nama,
            'kelas'   => $kelas,
        ];

        // Tautan Link
        if ($hasLinkAllowed) {
            $subData['link_url'] = $request->input('link_url') ? trim($request->input('link_url')) : null;
        }

        // Teks Jawaban
        if ($hasTextAllowed) {
            $subData['text_content'] = $request->input('text_content') ? trim($request->input('text_content')) : null;
        }

        // Berkas File
        if ($hasFileInput) {
            $firstFile = $savedFilesData[0];
            $subData['file_path']          = $firstFile['file_path'];
            $subData['file_original_name'] = $firstFile['file_original_name'];
            $subData['file_extension']     = $firstFile['file_extension'];
            $subData['file_size']          = $firstFile['file_size'];
            $subData['file_hash']          = $firstFile['file_hash'] ?? null;
        } elseif (!$existing) {
            $subData['file_path']          = null;
            $subData['file_original_name'] = null;
            $subData['file_extension']     = null;
            $subData['file_size']          = null;
            $subData['file_hash']          = null;
        }

        // Hitung komponen aktif untuk menentukan tipe pengumpulan (submission_type)
        $hasActiveFile = $hasFileInput || ($existing && $existing->hasFile());
        $hasActiveLink = !empty($subData['link_url']);
        $hasActiveText = !empty($subData['text_content']);

        $activePartsCount = ($hasActiveFile ? 1 : 0) + ($hasActiveLink ? 1 : 0) + ($hasActiveText ? 1 : 0);
        if ($activePartsCount > 1) {
            $subData['submission_type'] = 'composite';
        } elseif ($hasActiveLink) {
            $subData['submission_type'] = 'link';
        } elseif ($hasActiveText) {
            $subData['submission_type'] = 'text';
        } else {
            $subData['submission_type'] = 'file';
        }

        if ($existing) {
            $existing->update($subData);
            $submission = $existing;
        } else {
            $submission = Submission::create($subData);
        }

        // Simpan lampiran multi-berkas baru ke relasi submission_files
        if ($hasFileInput) {
            foreach ($savedFilesData as $fData) {
                $submission->files()->create($fData);
            }
        }

        // Susun pesan respons ramah pengguna
        $summaryParts = [];
        if ($hasActiveFile) {
            $fileCount = $submission->getFilesCount();
            $summaryParts[] = $fileCount > 1 ? "{$fileCount} berkas" : "1 berkas";
        }
        if ($hasActiveLink) {
            $summaryParts[] = "tautan link";
        }
        if ($hasActiveText) {
            $summaryParts[] = "jawaban teks";
        }

        $partsStr = implode(' + ', $summaryParts);
        $message = $isUpdate
            ? "Tugas berhasil diperbarui (" . ($partsStr ?: 'data tersimpan') . ")."
            : "Tugas berhasil dikumpulkan (" . ($partsStr ?: 'data tersimpan') . ").";

        return response()->json([
            'message'   => $message,
            'is_update' => $isUpdate,
        ]);
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
            $nama = trim((string) $request->query('nama', ''));
            if ($nama && mb_strlen($nama) >= 3) {
                // Deteksi kemungkinan kesalahan ketik NIM: cari apakah nama identik sudah terdaftar dengan NIM berbeda
                $sameName = Submission::where('soal_id', $soal->id)
                    ->where('nim', '!=', $nim)
                    ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower($nama)])
                    ->first();

                if ($sameName) {
                    return response()->json([
                        'has_submitted'        => false,
                        'nim_mismatch_warning' => true,
                        'registered_nim'       => $sameName->nim,
                        'registered_nama'      => $sameName->nama,
                    ]);
                }
            }

            return response()->json(['has_submitted' => false]);
        }

        $idParts = [];
        if ($existing->hasFile()) {
            $idParts[] = $existing->getFilesCount() > 1
                ? ($existing->getFilesCount() . ' berkas (' . $existing->file_original_name . ', dll)')
                : ($existing->file_original_name ?: '1 berkas');
        }
        if ($existing->hasLink()) {
            $idParts[] = 'Tautan: ' . \Illuminate\Support\Str::limit($existing->link_url, 30);
        }
        if ($existing->hasText()) {
            $idParts[] = 'Teks: ' . \Illuminate\Support\Str::limit($existing->text_content, 25);
        }
        $identifier = !empty($idParts) ? implode(' + ', $idParts) : ($existing->file_original_name ?? 'Tugas Terkumpul');

        $filesData = [];
        if ($existing->hasFile()) {
            foreach ($existing->getFilesList() as $f) {
                $fId = $f->id ?? 0;
                $filesData[] = [
                    'id'           => $fId,
                    'name'         => $f->file_original_name ?: basename($f->file_path),
                    'size'         => $f->file_size ? number_format($f->file_size / 1024, 1) . ' KB' : '0 KB',
                    'download_url' => route('soal.downloadOwnSubmission', [
                        'token'  => $soal->slug ?: $soal->unique_token,
                        'fileId' => $fId,
                        'nim'    => $existing->nim,
                    ]),
                ];
            }
        }

        return response()->json([
            'has_submitted'   => true,
            'nim'             => $existing->nim,
            'nama'            => $existing->nama,
            'kelas'           => $existing->kelas,
            'submission_type' => $existing->submission_type,
            'files_count'     => $existing->getFilesCount(),
            'identifier'      => $identifier,
            'submitted_at'    => $existing->updated_at ? $existing->updated_at->format('d M Y, H:i') : $existing->created_at->format('d M Y, H:i'),
            'files'           => $filesData,
            'link_url'        => $existing->link_url,
            'text_content'    => $existing->text_content,
        ]);
    }

    /**
     * Mahasiswa mengunduh berkas tugas miliknya sendiri untuk verifikasi hasil pengumpulan.
     */
    public function downloadOwnSubmission(Request $request, string $token, $fileId)
    {
        $soal = $this->findSoalByIdentifier($token);

        if (!$this->hasAuthorizedAccess($soal)) {
            abort(403, 'Akses tidak diizinkan. Silakan verifikasi password dokumen terlebih dahulu.');
        }

        $nim = trim((string) $request->query('nim', ''));
        if (!$nim) {
            abort(403, 'Identitas NIM diperlukan untuk memverifikasi berkas.');
        }

        $submission = Submission::where('soal_id', $soal->id)
            ->where('nim', $nim)
            ->firstOrFail();

        $fileIdInt = (int) $fileId;
        if ($fileIdInt > 0) {
            $subFile = $submission->files()->findOrFail($fileIdInt);
            $realFile = $this->resolveStorageFile($subFile->file_path);
            $filename = basename($subFile->file_original_name ?: basename($subFile->file_path));
            return response()->download($realFile, $filename);
        }

        if (!$submission->file_path) {
            abort(404, 'Berkas pengumpulan tidak ditemukan.');
        }

        $realFile = $this->resolveStorageFile($submission->file_path);
        $filename = basename($submission->file_original_name ?: basename($submission->file_path));
        return response()->download($realFile, $filename);
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
