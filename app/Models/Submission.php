<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (Submission $submission) {
            if ($submission->file_path) {
                Storage::disk('private')->delete($submission->file_path);
            }
        });
    }

    protected $fillable = [
        'soal_id',
        'nim',
        'nama',
        'submission_type',
        'link_url',
        'text_content',
        'file_path',
        'file_original_name',
        'file_extension',
        'file_size',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    public function isFile(): bool
    {
        return ($this->submission_type ?? 'file') === 'file';
    }

    public function isLink(): bool
    {
        return $this->submission_type === 'link';
    }

    public function isText(): bool
    {
        return $this->submission_type === 'text';
    }
}
