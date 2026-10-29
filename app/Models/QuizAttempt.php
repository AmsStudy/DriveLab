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
    ];

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
}
