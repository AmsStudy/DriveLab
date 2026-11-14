<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--keep=14 : Jumlah file backup terakhir yang dipertahankan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat backup database MySQL dalam bentuk file .sql / .sql.gz terkompresi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai proses backup database...');

        $connection = config('database.default');
        if ($connection !== 'mysql') {
            $this->error("Backup command saat ini hanya mendukung koneksi MySQL. Koneksi saat ini: {$connection}");
            return Command::FAILURE;
        }

        $config = config("database.connections.{$connection}");
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';

        if (empty($database)) {
            $this->error('Nama database belum dikonfigurasi di file .env');
            return Command::FAILURE;
        }

        // Tentukan direktori penyimpanan
        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $timestamp = date('Y_m_d_His');
        $sqlFilename = "backup_{$database}_{$timestamp}.sql";
        $sqlPath = $backupDir . DIRECTORY_SEPARATOR . $sqlFilename;
        $gzPath = $sqlPath . '.gz';

        // 1. Eksekusi mysqldump
        $dumpCmd = [
            'mysqldump',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--password={$password}",
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            $database,
        ];

        $processResult = Process::timeout(300)->run($dumpCmd);

        if (!$processResult->successful()) {
            // Coba alternatif jika password kosong atau mysqldump flag password
            $err = $processResult->errorOutput();
            $this->warn("mysqldump standard gagal: {$err}");
            $this->info("Mencoba fallback eksekusi shell...");

            $passArg = $password !== '' ? "-p\"" . addcslashes($password, '"\\$') . "\"" : "";
            $cmdString = "mysqldump -h {$host} -P {$port} -u {$username} {$passArg} --single-transaction --quick {$database} > \"{$sqlPath}\"";
            
            $res = Process::timeout(300)->run($cmdString);
            if (!$res->successful() || !File::exists($sqlPath) || File::size($sqlPath) === 0) {
                $this->error("Gagal membuat backup database. Pastikan binary 'mysqldump' terinstall di server/container.");
                return Command::FAILURE;
            }
        } else {
            File::put($sqlPath, $processResult->output());
        }

        if (!File::exists($sqlPath) || File::size($sqlPath) === 0) {
            $this->error("File backup kosong atau gagal dibuat.");
            return Command::FAILURE;
        }

        $uncompressedSize = File::size($sqlPath);
        $this->info("File SQL berhasil dibuat: {$sqlFilename} (" . $this->formatBytes($uncompressedSize) . ")");

        // 2. Kompresi ke .gz menggunakan PHP native gzencode (hemat ruang hingga 80-90%)
        $this->info("Mengompresi file backup ke format .gz...");
        $sqlContent = File::get($sqlPath);
        $gzContent = gzencode($sqlContent, 9);
        
        if ($gzContent !== false) {
            File::put($gzPath, $gzContent);
            File::delete($sqlPath); // Hapus file .sql mentah setelah dikompresi
            $finalPath = $gzPath;
            $finalFilename = $sqlFilename . '.gz';
            $finalSize = File::size($gzPath);
            $this->info("Kompresi berhasil! File tersimpan: {$finalFilename} (" . $this->formatBytes($finalSize) . ")");
        } else {
            $finalPath = $sqlPath;
            $finalFilename = $sqlFilename;
            $finalSize = $uncompressedSize;
            $this->warn("Kompresi gzip gagal, menyimpan file mentah .sql: {$finalFilename}");
        }

        // 3. Rotasi Backup: Hapus file backup lama melebihi limit
        $keepLimit = (int) $this->option('keep');
        $this->rotateBackups($backupDir, $keepLimit);

        $this->newLine();
        $this->info("=== BACKUP DATABASE SUKSES ===");
        $this->line("Lokasi: {$finalPath}");
        $this->line("Ukuran: " . $this->formatBytes($finalSize));

        return Command::SUCCESS;
    }

    /**
     * Hapus backup tertua jika jumlah file melebihi limit.
     */
    protected function rotateBackups(string $dir, int $limit): void
    {
        $files = File::files($dir);
        $backupFiles = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (str_starts_with($filename, 'backup_') && (str_ends_with($filename, '.sql') || str_ends_with($filename, '.sql.gz'))) {
                $backupFiles[$file->getPathname()] = $file->getMTime();
            }
        }

        if (count($backupFiles) > $limit) {
            asort($backupFiles); // Urutkan dari yang tertua
            $toDeleteCount = count($backupFiles) - $limit;
            $deleted = 0;

            foreach ($backupFiles as $path => $mtime) {
                if ($deleted >= $toDeleteCount) {
                    break;
                }
                File::delete($path);
                $this->line("Menghapus backup lama: " . basename($path));
                $deleted++;
            }
        }
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

