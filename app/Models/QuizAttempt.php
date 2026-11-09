<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'nim',
        'student_name',
        'kelas',
        'score',
        'total_points_earned',
        'total_points_possible',
        'correct_answers_count',
        'wrong_answers_count',
        'started_at',
        'completed_at',
        'duration_seconds',
        'tab_switch_count',
        'is_locked',
        'lock_reason',
        'penalty_end_time',
        'violations_log',
        'draft_answers',
        'extra_time_minutes',
        'last_heartbeat_at',
        'is_force_submitted',
    ];

    protected $casts = [
        'score'                 => 'decimal:2',
        'total_points_earned'   => 'integer',
        'total_points_possible' => 'integer',
        'correct_answers_count' => 'integer',
        'wrong_answers_count'   => 'integer',
        'started_at'            => 'datetime',
        'completed_at'          => 'datetime',
        'duration_seconds'      => 'integer',
        'tab_switch_count'      => 'integer',
        'is_locked'             => 'boolean',
        'penalty_end_time'      => 'datetime',
        'violations_log'        => 'array',
        'draft_answers'         => 'array',
        'extra_time_minutes'    => 'integer',
        'last_heartbeat_at'     => 'datetime',
        'is_force_submitted'    => 'boolean',
    ];

    protected $appends = [
        'formatted_duration',
    ];

    public function getFormattedDurationAttribute(): string
    {
        return $this->formattedDuration();
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function formattedDuration(): string
    {
        if (!$this->duration_seconds) {
            return '-';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds} detik";
    }

    public function isOnline(): bool
    {
        if ($this->isCompleted()) {
            return false;
        }
        return $this->last_heartbeat_at && now()->diffInSeconds($this->last_heartbeat_at) < 35;
    }

    public function answeredQuestionsCount(): int
    {
        if ($this->isCompleted()) {
            // Use eager-loaded answers_count if available to eliminate N+1 query
            return $this->answers_count ?? $this->answers()->count();
        }
        $draft = $this->draft_answers ?? [];
        unset($draft['__flagged']);
        return count(array_filter($draft, fn($v) => !is_null($v) && $v !== '' && $v !== []));
    }

    public function flaggedQuestionsCount(): int
    {
        $draft = $this->draft_answers ?? [];
        return isset($draft['__flagged']) && is_array($draft['__flagged']) ? count($draft['__flagged']) : 0;
    }

    public function timeRemainingSeconds(?Quiz $quiz = null): ?int
    {
        if ($this->isCompleted()) {
            return 0;
        }
        $quiz = $quiz ?? $this->quiz;
        if (!$quiz || $quiz->time_limit_minutes <= 0) {
            return null; // Unlimited
        }
        $totalMinutes = $quiz->time_limit_minutes + ($this->extra_time_minutes ?? 0);
        $allowed = $totalMinutes * 60;

        // Jika kuis masih di status Ruang Tunggu (waiting) atau belum dimulai oleh pengawas:
        // Waktu hitung mundur belum berjalan sama sekali, sisa waktu masih utuh penuh.
        if ($quiz->isSessionWaiting() || !$quiz->isSessionStarted() || !$this->started_at) {
            return $allowed;
        }

        if ($quiz->isSessionPaused() && $quiz->paused_at) {
            $elapsed = max(0, (int) ($quiz->paused_at->timestamp - $this->started_at->timestamp));
        } else {
            $elapsed = max(0, (int) (now()->timestamp - $this->started_at->timestamp));
        }
        return max(0, $allowed - $elapsed);
    }
}
