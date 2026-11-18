<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoalAttachment extends Model
{
    protected $fillable = [
        'soal_id',
        'type',
        'file_path',
        'file_original_name',
        'file_type',
        'file_size',
        'link_url',
        'content_text',
        'is_downloadable',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'file_size'       => 'integer',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    public function isFile(): bool
    {
        return ($this->type ?? 'file') === 'file';
    }

    public function isLink(): bool
    {
        return $this->type === 'link';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function canDownload(): bool
    {
        return $this->isFile() && (bool) $this->is_downloadable;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 2) . ' MB';
    }
}
