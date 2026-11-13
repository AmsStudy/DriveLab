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
        'formatted_text',
    ];

    protected static function booted(): void
    {
        static::deleting(function (QuizQuestion $question) {
            if ($question->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($question->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($question->image_path);
            }

            // Hapus gambar pada opsi-opsinya juga
            foreach ($question->options as $option) {
                if ($option->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($option->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($option->image_path);
                }
            }
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return '/storage/' . ltrim($this->image_path, '/');
    }

    public function getFormattedTextAttribute(): string
    {
        return \App\Helpers\RichTextHelper::format($this->question_text);
    }

    public function isMultiSelect(): bool
    {
        if ($this->question_type === 'fill_blank') {
            return false;
        }

        return $this->question_type === 'multi_select'
            || ($this->relationLoaded('options') && $this->options->where('is_correct', true)->count() > 1);
    }

    public function isFillBlank(): bool
    {
        return $this->question_type === 'fill_blank';
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
