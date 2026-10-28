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
        'unique_token',
        'time_limit_minutes',
        'time_per_question',
        'shuffle_questions',
        'shuffle_options',
        'is_active',
        'show_leaderboard',
        'show_review',
        'max_attempts',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'time_per_question'  => 'integer',
        'shuffle_questions'  => 'boolean',
        'shuffle_options'    => 'boolean',
        'is_active'          => 'boolean',
        'show_leaderboard'   => 'boolean',
        'show_review'        => 'boolean',
        'max_attempts'       => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (Quiz $quiz) {
            if (empty($quiz->slug)) {
                $baseSlug = Str::slug($quiz->title) ?: 'kuis-' . Str::lower(Str::random(5));
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }
                $quiz->slug = $slug;
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
        return $this->hasMany(QuizQuestion::class)->orderBy('order_num');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class)->latest('started_at');
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }
}
