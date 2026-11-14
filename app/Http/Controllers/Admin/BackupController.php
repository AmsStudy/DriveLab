<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Tampilkan daftar file backup database.
     */
    public function index()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (File::isDirectory($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                if (str_starts_with($filename, 'backup_') && (str_ends_with($filename, '.sql') || str_ends_with($filename, '.sql.gz'))) {
                    $backups[] = [
                        'filename'      => $filename,
                        'size'          => $this->formatBytes($file->getSize()),
                        'raw_size'      => $file->getSize(),
                        'created_at'    => date('d M Y H:i:s', $file->getMTime()),
                        'timestamp'     => $file->getMTime(),
                    ];
                }
            }

            // Urutkan dari yang terbaru
            usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        }

        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Buat file backup database baru secara manual.
     */
    public function create()
    {
        try {
            $exitCode = Artisan::call('db:backup');
            if ($exitCode === 0) {
                return redirect()->route('admin.backups.index')->with('success', 'Backup database baru berhasil dibuat!');
            }
            return redirect()->route('admin.backups.index')->with('error', 'Gagal membuat backup database. Periksa log server.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Unduh file backup database.
     */
    public function download(string $filename): BinaryFileResponse
    {
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(403, 'Akses tidak sah');
        }

        $filePath = storage_path('app/backups/' . $filename);
        if (!File::exists($filePath)) {
            abort(404, 'File backup tidak ditemukan');
        }

        return response()->download($filePath);
    }

    /**
     * Hapus file backup database.
     */
    public function destroy(string $filename)
    {
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(403, 'Akses tidak sah');
        }

        $filePath = storage_path('app/backups/' . $filename);
        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('admin.backups.index')->with('success', "File backup {$filename} berhasil dihapus.");
        }

        return redirect()->route('admin.backups.index')->with('error', 'File backup tidak ditemukan.');
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

