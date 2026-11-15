<?php

namespace App\Console\Commands;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoCloseExpiredQuizzesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quizzes:auto-close {--force-submit-abandoned : Otomatis finalisasi attempt yang ditinggalkan peserta melebihi batas waktu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menutup sesi kuis yang jadwalnya telah berakhir atau sudah tidak ada aktivitas pengerjaan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Memeriksa kuis yang kedaluwarsa atau selesai...");

        $now = Carbon::now();
        $closedCount = 0;

        // 1. Kuis yang jadwal ends_at nya sudah lewat tapi status sesi belum 'ended'
        $expiredScheduleQuizzes = Quiz::where('session_status', '!=', 'ended')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->get();

        foreach ($expiredScheduleQuizzes as $quiz) {
            $quiz->update([
                'session_status' => 'ended',
                'is_active'      => false,
            ]);
            $this->line(" - Kuis [ID: {$quiz->id}] '{$quiz->title}' ditutup karena jadwal ends_at telah lewat ({$quiz->ends_at}).");
            $closedCount++;
        }

        // 2. Kuis yang dinonaktifkan (is_active = false) tapi status sesi masih 'waiting' atau 'started'
        $inactiveQuizzes = Quiz::where('is_active', false)
            ->where('session_status', '!=', 'ended')
            ->get();

        foreach ($inactiveQuizzes as $quiz) {
            $quiz->update(['session_status' => 'ended']);
            $this->line(" - Kuis [ID: {$quiz->id}] '{$quiz->title}' disetel ke 'ended' karena kuis dinonaktifkan.");
            $closedCount++;
        }

        // 3. Kuis lama yang seluruh pesertanya sudah selesai mengerjakan (completed_at NOT NULL)
        $completedQuizzes = Quiz::where('session_status', '!=', 'ended')
            ->whereHas('attempts')
            ->whereDoesntHave('attempts', function ($query) {
                $query->whereNull('completed_at');
            })
            ->where('updated_at', '<=', $now->copy()->subHours(2))
            ->get();

        foreach ($completedQuizzes as $quiz) {
            $quiz->update(['session_status' => 'ended']);
            $this->line(" - Kuis [ID: {$quiz->id}] '{$quiz->title}' disetel ke 'ended' karena seluruh peserta ({$quiz->attempts()->count()}) sudah selesai.");
            $closedCount++;
        }

        // 4. (Opsional/Background) Finalisasi attempt yang ditinggalkan tanpa submit resmi
        $abandonedCount = 0;
        $staleAttempts = QuizAttempt::whereNull('completed_at')
            ->where(function ($query) use ($now) {
                // Heartbeat mati lebih dari 3 jam atau started_at lebih dari 6 jam lalu
                $query->where('last_heartbeat_at', '<=', $now->copy()->subHours(3))
                      ->orWhere('started_at', '<=', $now->copy()->subHours(6));
            })
            ->get();

        foreach ($staleAttempts as $att) {
            $duration = $att->started_at ? max(0, $now->diffInSeconds($att->started_at)) : 0;
            $att->update([
                'completed_at'       => $now,
                'duration_seconds'   => $duration,
                'is_force_submitted' => true,
            ]);
            $abandonedCount++;
        }

        if ($abandonedCount > 0) {
            $this->info("Berhasil memfinalisasi {$abandonedCount} attempt yang ditinggalkan (stale/abandoned).");
        }

        $this->info("Pemeriksaan selesai. Total kuis ditutup otomatis: {$closedCount}");
        return Command::SUCCESS;
    }
}
