<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'soal_id',
        'title',
        'slug',
        'description',
        'access_code',
        'access_password',
        'unique_token',
        'time_limit_minutes',
        'starts_at',
        'ends_at',
        'time_per_question',
        'shuffle_questions',
        'shuffle_options',
        'is_active',
        'session_status',
        'paused_at',
        'show_leaderboard',
        'show_review',
        'show_correct_answer',
        'allow_open_book',
        'admin_unlock_password',
        'max_attempts',
        'classes',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'starts_at'          => 'datetime',
        'ends_at'            => 'datetime',
        'paused_at'          => 'datetime',
        'time_per_question'  => 'integer',
        'shuffle_questions'  => 'boolean',
        'shuffle_options'    => 'boolean',
        'is_active'          => 'boolean',
        'show_leaderboard'   => 'boolean',
        'show_review'        => 'boolean',
        'show_correct_answer' => 'boolean',
        'allow_open_book'    => 'boolean',
        'max_attempts'       => 'integer',
        'classes'            => 'array',
    ];

    protected static function booted()
    {
        static::creating(function (Quiz $quiz) {
            if (empty($quiz->slug)) {
                $quiz->slug = static::generateUniqueSlug($quiz->title ?? '');
            }

            if (empty($quiz->access_code)) {
                do {
                    $code = 'LAB-' . strtoupper(Str::random(5));
                } while (static::where('access_code', $code)->exists());
                $quiz->access_code = $code;
            }

            if (empty($quiz->unique_token)) {
                do {
                    $token = Str::lower(Str::random(8));
                } while (static::where('unique_token', $token)->exists());
                $quiz->unique_token = $token;
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'kuis-' . Str::lower(Str::random(5));

        $reserved = ['join', 'lobby', 'play', 'results', 'autosave', 'submit', 'attempt', 'start'];
        if (in_array(strtolower($base), $reserved, true)) {
            $base = "{$base}-kuis";
        }

        $slug = $base;
        $counter = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function getSlugOrTokenAttribute(): string
    {
        return $this->slug ?: $this->unique_token;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_num')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(QuizAnnouncement::class)->latest();
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }

    public function isScheduleUpcoming(): bool
    {
        return $this->starts_at !== null && now()->lt($this->starts_at);
    }

    public function isScheduleExpired(): bool
    {
        return $this->ends_at !== null && now()->gt($this->ends_at);
    }

    public function isScheduleOpen(): bool
    {
        if ($this->isScheduleUpcoming()) {
            return false;
        }
        if ($this->isScheduleExpired()) {
            return false;
        }
        return true;
    }

    public function hasAccessPassword(): bool
    {
        return !empty($this->access_password);
    }

    public function hasClasses(): bool
    {
        return !empty($this->classes) && is_array($this->classes) && count($this->classes) > 0;
    }

    public function getClassesList(): array
    {
        if (!$this->hasClasses()) {
            return [];
        }
        return array_values(array_filter(array_map('trim', $this->classes)));
    }

    public function isSessionWaiting(): bool
    {
        return ($this->session_status ?? 'waiting') === 'waiting';
    }

    public function isSessionStarted(): bool
    {
        return ($this->session_status ?? 'waiting') === 'started';
    }

    public function isSessionPaused(): bool
    {
        return ($this->session_status ?? 'waiting') === 'paused';
    }

    public function isSessionEnded(): bool
    {
        return ($this->session_status ?? 'waiting') === 'ended';
    }

    public function canShowCorrectAnswer(): bool
    {
        return (bool) $this->show_correct_answer;
    }
}
