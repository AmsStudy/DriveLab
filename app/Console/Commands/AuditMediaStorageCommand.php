<?php

namespace App\Console\Commands;

use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\SoalAttachment;
use App\Models\SubmissionFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AuditMediaStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:audit {--fix-symlink : Hapus symlink lama dan buat ulang symlink public/storage secara presisi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit kesehatan seluruh file gambar soal, opsi kuis, lampiran, dan symlink storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=================================================");
        $this->info("     AUDIT KESEHATAN MEDIA & STORAGE SERVER      ");
        $this->info("=================================================");

        // 1. Audit Symlink public/storage
        $this->auditStorageSymlink();

        // 2. Audit Gambar Soal Kuis
        $questionStats = $this->auditQuizQuestionImages();

        // 3. Audit Gambar Opsi Kuis
        $optionStats = $this->auditQuizOptionImages();

        // 4. Audit Lampiran Soal Lab
        $soalAttachmentStats = $this->auditSoalAttachments();

        // 5. Audit File Pengumpulan Mahasiswa (Submissions)
        $submissionStats = $this->auditSubmissionFiles();

        // Rekapitulasi
        $this->newLine();
        $this->info("-------------------------------------------------");
        $this->info("                RINGKASAN AUDIT                  ");
        $this->info("-------------------------------------------------");

        $rows = [
            ['Gambar Soal Kuis', $questionStats['total'], $questionStats['found'], $questionStats['missing']],
            ['Gambar Opsi Kuis', $optionStats['total'], $optionStats['found'], $optionStats['missing']],
            ['Lampiran Soal Lab', $soalAttachmentStats['total'], $soalAttachmentStats['found'], $soalAttachmentStats['missing']],
            ['File Pengumpulan Mahasiswa', $submissionStats['total'], $submissionStats['found'], $submissionStats['missing']],
        ];

        $this->table(['Kategori Media', 'Total di Database', 'Tersedia Fisik', 'Hilang dari Disk'], $rows);

        $totalMissing = $questionStats['missing'] + $optionStats['missing'] + $soalAttachmentStats['missing'] + $submissionStats['missing'];

        if ($totalMissing > 0) {
            $this->warn("⚠️ Ditemukan {$totalMissing} file fisik yang tidak ada di storage disk server.");
            return Command::FAILURE;
        }

        $this->info("✅ Semua file media dan storage terverifikasi 100% sehat!");
        return Command::SUCCESS;
    }

    protected function auditStorageSymlink(): void
    {
        $this->line("1. Memeriksa Symlink 'public/storage'...");
        $publicStorage = public_path('storage');
        $targetStorage = storage_path('app/public');

        $isLink = is_link($publicStorage);
        $linkTarget = $isLink ? @readlink($publicStorage) : null;
        $isValid = $isLink && file_exists($publicStorage);

        if (!$isLink && !file_exists($publicStorage)) {
            $this->warn("   ❌ Symlink public/storage BELUM ADA.");
        } elseif (!$isValid) {
            $this->error("   ❌ Symlink public/storage PATAH / BROKEN!");
            $this->line("      Target saat ini: " . ($linkTarget ?: 'Tidak diketahui'));
            $this->line("      Seharusnya menuju: {$targetStorage}");
        } else {
            $this->info("   ✅ Symlink public/storage AKTIF & VALID (menuju: {$linkTarget})");
        }

        if ($this->option('fix-symlink') || (!$isValid && $this->confirm('Apakah Anda ingin memperbaiki symlink storage sekarang?', true))) {
            $this->info("   Memperbaiki symlink...");
            if (file_exists($publicStorage) || is_link($publicStorage)) {
                @unlink($publicStorage);
            }
            Artisan::call('storage:link');
            $this->info("   ✅ Symlink berhasil dibuat ulang: " . Artisan::output());
        }
    }

    protected function auditQuizQuestionImages(): array
    {
        $this->newLine();
        $this->line("2. Memeriksa Gambar Soal Kuis (quiz_questions)...");
        $questions = QuizQuestion::whereNotNull('image_path')->where('image_path', '!=', '')->get();

        $found = 0;
        $missing = 0;
        $missingList = [];

        foreach ($questions as $q) {
            $cleanPath = ltrim(str_replace('storage/', '', $q->image_path), '/');
            if (Storage::disk('public')->exists($cleanPath)) {
                $found++;
            } else {
                $missing++;
                $missingList[] = [
                    'id'       => $q->id,
                    'quiz_id'  => $q->quiz_id,
                    'order'    => $q->order_num,
                    'path'     => $q->image_path,
                    'snippet'  => mb_substr(strip_tags($q->question_text), 0, 40) . '...',
                ];
            }
        }

        if ($missing > 0) {
            $this->error("   ❌ {$missing} dari {$questions->count()} gambar soal kuis tidak ditemukan!");
            $this->table(['ID Soal', 'Quiz ID', 'No', 'Path Gambar', 'Penggalan Soal'], $missingList);
        } else {
            $this->info("   ✅ Seluruh gambar soal ({$questions->count()}) tersedia di storage public.");
        }

        return ['total' => $questions->count(), 'found' => $found, 'missing' => $missing];
    }

    protected function auditQuizOptionImages(): array
    {
        $this->newLine();
        $this->line("3. Memeriksa Gambar Opsi Kuis (quiz_options)...");
        $options = QuizOption::whereNotNull('image_path')->where('image_path', '!=', '')->get();

        $found = 0;
        $missing = 0;
        $missingList = [];

        foreach ($options as $opt) {
            $cleanPath = ltrim(str_replace('storage/', '', $opt->image_path), '/');
            if (Storage::disk('public')->exists($cleanPath)) {
                $found++;
            } else {
                $missing++;
                $missingList[] = [
                    'id'          => $opt->id,
                    'question_id' => $opt->quiz_question_id,
                    'path'        => $opt->image_path,
                ];
            }
        }

        if ($missing > 0) {
            $this->warn("   ⚠️ {$missing} dari {$options->count()} gambar opsi kuis tidak ditemukan di disk.");
            if (count($missingList) <= 10) {
                $this->table(['ID Opsi', 'ID Soal', 'Path Gambar'], $missingList);
            }
        } else {
            $this->info("   ✅ Seluruh gambar opsi ({$options->count()}) tersedia di storage public.");
        }

        return ['total' => $options->count(), 'found' => $found, 'missing' => $missing];
    }

    protected function auditSoalAttachments(): array
    {
        $this->newLine();
        $this->line("4. Memeriksa Lampiran Soal Lab (soal_attachments)...");
        $attachments = SoalAttachment::where('type', 'file')
            ->orWhereNull('type')
            ->whereNotNull('file_path')
            ->get();

        $found = 0;
        $missing = 0;

        foreach ($attachments as $att) {
            $exists = Storage::disk('private')->exists($att->file_path)
                   || Storage::disk('public')->exists($att->file_path)
                   || Storage::exists($att->file_path);

            if ($exists) {
                $found++;
            } else {
                $missing++;
            }
        }

        if ($missing > 0) {
            $this->warn("   ⚠️ {$missing} dari {$attachments->count()} file lampiran soal tidak ditemukan.");
        } else {
            $this->info("   ✅ Seluruh lampiran soal ({$attachments->count()}) terverifikasi ada.");
        }

        return ['total' => $attachments->count(), 'found' => $found, 'missing' => $missing];
    }

    protected function auditSubmissionFiles(): array
    {
        $this->newLine();
        $this->line("5. Memeriksa File Tugas Mahasiswa (submission_files)...");
        $files = SubmissionFile::all();

        $found = 0;
        $missing = 0;

        foreach ($files as $file) {
            $exists = Storage::disk('private')->exists($file->file_path)
                   || Storage::exists($file->file_path);

            if ($exists) {
                $found++;
            } else {
                $missing++;
            }
        }

        if ($missing > 0) {
            $this->warn("   ⚠️ {$missing} dari {$files->count()} file mahasiswa tidak ditemukan.");
        } else {
            $this->info("   ✅ Seluruh file pengumpulan mahasiswa ({$files->count()}) aman di storage private.");
        }

        return ['total' => $files->count(), 'found' => $found, 'missing' => $missing];
    }
}
