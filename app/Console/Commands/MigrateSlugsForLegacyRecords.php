<?php

namespace App\Console\Commands;

use App\Models\Quiz;
use App\Models\Soal;
use Illuminate\Console\Command;

class MigrateSlugsForLegacyRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO-friendly title slugs for existing legacy Soal and Quiz records while preserving tokens';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting slug migration for legacy Soal and Quiz records...');

        $updatedSoal = 0;
        Soal::withTrashed()->chunk(50, function ($soals) use (&$updatedSoal) {
            foreach ($soals as $soal) {
                if (empty($soal->slug)) {
                    $soal->slug = Soal::generateUniqueSlug($soal->title ?? 'dokumen', $soal->id);
                    $soal->saveQuietly();
                    $updatedSoal++;
                    $this->line("Updated Soal #{$soal->id}: {$soal->title} -> {$soal->slug}");
                }
            }
        });

        $updatedQuiz = 0;
        Quiz::chunk(50, function ($quizzes) use (&$updatedQuiz) {
            foreach ($quizzes as $quiz) {
                if (empty($quiz->slug)) {
                    $quiz->slug = Quiz::generateUniqueSlug($quiz->title ?? 'kuis', $quiz->id);
                    $quiz->saveQuietly();
                    $updatedQuiz++;
                    $this->line("Updated Quiz #{$quiz->id}: {$quiz->title} -> {$quiz->slug}");
                }
            }
        });

        $this->info("Migration completed! {$updatedSoal} Soal records and {$updatedQuiz} Quiz records verified/updated.");

        return Command::SUCCESS;
    }
}
