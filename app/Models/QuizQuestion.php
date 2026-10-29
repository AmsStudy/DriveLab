<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'image_path',
        'question_type',
        'points',
        'explanation',
        'order_num',
    ];

    protected $casts = [
        'points'    => 'integer',
        'order_num' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function isMultiSelect(): bool
    {
        return $this->question_type === 'multi_select'
            || ($this->relationLoaded('options') && $this->options->where('is_correct', true)->count() > 1);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('order_num');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function correctOptions(): HasMany
    {
        return $this->hasMany(QuizOption::class)->where('is_correct', true);
    }
}
