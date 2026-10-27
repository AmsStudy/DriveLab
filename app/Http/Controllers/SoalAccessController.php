<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
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
        $request->validate([
            'kode_unik' => 'required|string'
        ]);

        $soal = Soal::where('kode_unik', $request->kode_unik)->first();

        if (!$soal) {
            return back()->with('error', 'Kode unik dokumen tidak ditemukan.');
        }

        return redirect()->route('soal.access', ['token' => $soal->unique_token]);
    }

    /**
     * Tampilkan halaman login soal (form password).
     */
    public function show(string $token)
    {
        $soal = Soal::where('unique_token', $token)->firstOrFail();

        return view('soal.access', compact('soal'));
    }

    /**
     * Proses verifikasi password soal.
     */
    public function verify(Request $request, string $token)
    {
        $request->validate(['password' => 'required|string']);

        $soal = Soal::with('category', 'admin')->where('unique_token', $token)->firstOrFail();

        $ip        = $request->ip();
        $userAgent = $request->userAgent();
        
        $throttleKey = 'soal_access_' . $soal->id . '_' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['password' => 'Terlalu banyak percobaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.']);
        }

        // Cek apakah soal bisa diakses
        $status = $soal->access_status;
        if ($status !== 'active') {
            AccessLog::create([
                'soal_id'     => $soal->id,
                'user_agent'  => $userAgent,
                'status'      => 'failed',
                'reason'      => $status,
                'accessed_at' => now(),
            ]);

            $msg = match ($status) {
                'inactive'  => 'Soal ini sedang tidak aktif.',
                'scheduled' => 'Waktu akses soal belum dimulai. Silakan tunggu.',
                'expired'   => 'Waktu akses soal ini sudah berakhir.',
                default     => 'Soal tidak dapat diakses saat ini.'
            };

            return back()->withErrors(['password' => $msg]);
        }

        // Cek password
        if (!Hash::check($request->password, $soal->password)) {
            RateLimiter::hit($throttleKey, 300); // Blokir 5 menit jika gagal 5x

            AccessLog::create([
                'soal_id'     => $soal->id,
                'user_agent'  => $userAgent,
                'status'      => 'failed',
                'reason'      => 'wrong_password',
                'accessed_at' => now(),
            ]);

            return back()->withErrors(['password' => 'Password salah. Silakan coba lagi.']);
        }

        // Akses berhasil
        RateLimiter::clear($throttleKey);
        $soal->increment('access_count');

        AccessLog::create([
            'soal_id'     => $soal->id,
            'user_agent'  => $userAgent,
            'status'      => 'success',
            'reason'      => null,
            'accessed_at' => now(),
        ]);

        // Simpan token akses di session agar tidak perlu input password lagi
        session(["soal_access_{$soal->unique_token}" => true]);

        return redirect()->route('soal.view', $token);
    }

    /**
     * Tampilkan konten soal (setelah password benar).
     */
    public function view(Request $request, string $token)
    {
        $soal = Soal::with(['category', 'admin', 'attachments'])->where('unique_token', $token)->firstOrFail();

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            // Cek session akses
            if (!session("soal_access_{$soal->unique_token}")) {
                return redirect()->route('soal.access', $token);
            }

            // Cek masih bisa diakses
            $status = $soal->access_status;
            if ($status !== 'active') {
                session()->forget("soal_access_{$soal->unique_token}");
                return redirect()->route('soal.access', $token)->withErrors([
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
        $soal = Soal::where('unique_token', $token)->firstOrFail();
        $attachment = $soal->attachments()->findOrFail($attachmentId);

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            if (!session("soal_access_{$soal->unique_token}")) abort(403, 'Akses tidak diizinkan.');
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
        $soal = Soal::where('unique_token', $token)->firstOrFail();
        $attachment = $soal->attachments()->findOrFail($attachmentId);

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            if (!session("soal_access_{$soal->unique_token}")) {
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
                        'token'      => $token,
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
        $soal = Soal::where('unique_token', $token)->firstOrFail();
        $attachment = $soal->attachments()->findOrFail($attachmentId);

        $isAdminPreview = $this->isAuthorizedAdminPreview($request, $soal);

        if (!$isAdminPreview) {
            if (!session("soal_access_{$soal->unique_token}")) {
                abort(403, 'Akses tidak diizinkan.');
            }

            if ($soal->access_status !== 'active') {
                abort(403, 'Waktu akses soal ini sudah berakhir.');
            }

            $ext = strtolower($attachment->file_type ?? '');
            $isPreviewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
            if ($isPreviewable && !$soal->is_downloadable) {
                abort(403, 'File soal ini tidak diizinkan untuk diunduh.');
            }
        }

        $realFile = $this->resolveStorageFile($attachment->file_path);

        return response()->download($realFile, basename($attachment->file_original_name));
    }

    public function status(string $token)
    {
        $soal = Soal::where('unique_token', $token)->first();
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
        // Rate limiting: maks 30 pengiriman per IP per menit
        $rateLimitKey = 'submit_task_' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi dalam ' . ceil($seconds) . ' detik.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $soal = Soal::where('unique_token', $token)->firstOrFail();

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

        if (!session("soal_access_{$soal->unique_token}")) {
            return response()->json(['message' => 'Sesi akses tidak valid. Silakan masukkan password soal terlebih dahulu.'], 403);
        }

        // Tentukan MIME type yang diizinkan berdasarkan allowed_extensions
        $allowed = $soal->allowed_extensions ?? [];
        $allowedMimes = [];
        if (empty($allowed)) {
            // Semua format diizinkan (whitelist aman)
            $allowedMimes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip', 'rar', 'pkt'];
        } else {
            if (in_array('pdf', $allowed))     $allowedMimes = array_merge($allowedMimes, ['pdf']);
            if (in_array('word', $allowed))    $allowedMimes = array_merge($allowedMimes, ['doc', 'docx']);
            if (in_array('excel', $allowed))   $allowedMimes = array_merge($allowedMimes, ['xls', 'xlsx']);
            if (in_array('ppt', $allowed))     $allowedMimes = array_merge($allowedMimes, ['ppt', 'pptx']);
            if (in_array('image', $allowed))   $allowedMimes = array_merge($allowedMimes, ['jpg', 'jpeg', 'png']);
            if (in_array('archive', $allowed)) $allowedMimes = array_merge($allowedMimes, ['zip', 'rar']);
            if (in_array('pkt', $allowed))     $allowedMimes = array_merge($allowedMimes, ['pkt']);
        }

        $extRule = 'extensions:' . implode(',', $allowedMimes);

        $request->validate([
            'nim'  => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/',
            'nama' => 'required|string|max:255',
            'file' => ['required', 'file', 'max:10240', $extRule],
        ]);

        $file = $request->file('file');

        // Verifikasi MIME type asli dari konten file (anti-spoof ekstensi)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        $safeMimes = [
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'image/jpeg', 'image/png',
            'application/zip', 'application/x-zip-compressed',
            'application/x-rar-compressed', 'application/vnd.rar',
        ];

        if (!in_array($realMime, $safeMimes) && $file->getClientOriginalExtension() !== 'pkt') {
            return response()->json(['message' => 'Tipe file tidak diizinkan.'], 422);
        }

        // Nama file bersih — hapus karakter berbahaya dari NIM/Nama
        $nimClean  = preg_replace('/[^a-zA-Z0-9\-]/', '_', $request->nim);
        $namaClean = preg_replace('/[^a-zA-Z0-9\s\-]/', '_', $request->nama);
        $fileName  = $nimClean . '_' . str_replace(' ', '_', $namaClean) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('submissions/' . $soal->id, $fileName, 'private');

        $existing = Submission::where('soal_id', $soal->id)
            ->where('nim', $request->nim)
            ->first();

        $isUpdate = $existing !== null;

        if ($existing) {
            if ($existing->file_path && $existing->file_path !== $path) {
                Storage::disk('private')->delete($existing->file_path);
            }

            $existing->update([
                'nama'               => $request->nama,
                'file_path'          => $path,
                'file_original_name' => $file->getClientOriginalName(),
                'file_extension'     => $file->getClientOriginalExtension(),
                'file_size'          => $file->getSize(),
            ]);
        } else {
            Submission::create([
                'soal_id'            => $soal->id,
                'nim'                => $request->nim,
                'nama'               => $request->nama,
                'file_path'          => $path,
                'file_original_name' => $file->getClientOriginalName(),
                'file_extension'     => $file->getClientOriginalExtension(),
                'file_size'          => $file->getSize(),
            ]);
        }

        $message = $isUpdate
            ? 'Tugas berhasil diperbarui. File pengumpulan sebelumnya telah diganti.'
            : 'Tugas berhasil dikumpulkan.';

        return response()->json(['message' => $message, 'is_update' => $isUpdate]);
    }

    public function trackTask(Request $request)
    {
        // Rate limiting: maks 30 permintaan per IP per menit
        $rateLimitKey = 'track_task_' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi dalam ' . ceil($seconds) . ' detik.'], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $request->validate([
            'nim' => 'required|string|max:50|regex:/^[a-zA-Z0-9\-]+$/'
        ]);

        $submissions = \App\Models\Submission::where('nim', $request->nim)
            ->with('soal:id,title')
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $submissions->map(function ($sub) {
            return [
                'id'         => $sub->id,
                'soal_title' => $sub->soal->title,
                'nama'       => $sub->nama,
                'file_name'  => $sub->file_original_name,
                'created_at' => $sub->created_at->format('Y-m-d H:i:s')
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
}
