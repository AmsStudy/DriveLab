<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Soal extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'content_text',
        'unique_token',
        'kode_unik',
        'password',
        'category_id',
        'user_id',
        'file_path',
        'file_original_name',
        'file_type',
        'file_size',
        'access_type',
        'active_from',
        'active_until',
        'is_active',
        'is_downloadable',
        'access_count',
        'is_submission_enabled',
        'submission_types',
        'deadline_at',
        'submission_instructions',
        'allowed_extensions',
    ];

    protected $casts = [
        'active_from'  => 'datetime',
        'active_until' => 'datetime',
        'is_active'    => 'boolean',
        'is_downloadable' => 'boolean',
        'is_submission_enabled' => 'boolean',
        'submission_types' => 'array',
        'deadline_at'  => 'datetime',
        'allowed_extensions' => 'array',
    ];

    public function isSubmissionTypeAllowed(string $type): bool
    {
        $types = $this->submission_types ?: ['file'];
        return in_array($type, $types);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'is_active', 'access_type', 'active_from', 'active_until'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Soal \"{$this->title}\" telah di-{$eventName}");
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SoalAttachment::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Cek apakah soal ini bisa diakses saat ini.
     */
    public function isAccessible(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->access_type === 'lifetime') {
            return true;
        }

        $now = Carbon::now();

        if ($this->active_from && $now->lt($this->active_from)) {
            return false;
        }

        if ($this->active_until && $now->gt($this->active_until)) {
            return false;
        }

        return true;
    }

    /**
     * Status akses untuk ditampilkan di UI.
     */
    public function getAccessStatusAttribute(): string
    {
        if (!$this->is_active) return 'inactive';
        if ($this->access_type === 'lifetime') return 'active';

        $now = Carbon::now();

        if ($this->active_from && $now->lt($this->active_from)) return 'scheduled';
        if ($this->active_until && $now->gt($this->active_until)) return 'expired';

        return 'active';
    }


}
