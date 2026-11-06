<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_question_id',
        'option_text',
        'image_path',
        'is_correct',
        'order_num',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order_num'  => 'integer',
    ];

    protected $appends = [
        'image_url',
        'formatted_text',
    ];

    protected static function booted(): void
    {
        static::deleting(function (QuizOption $option) {
            if ($option->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($option->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($option->image_path);
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
        return \App\Helpers\RichTextHelper::format($this->option_text);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
