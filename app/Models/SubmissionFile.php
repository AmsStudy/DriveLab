<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubmissionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'file_path',
        'file_original_name',
        'file_extension',
        'file_size',
    ];

    protected static function booted(): void
    {
        static::deleting(function (SubmissionFile $file) {
            if ($file->file_path) {
                Storage::disk('private')->delete($file->file_path);
            }
        });
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}

