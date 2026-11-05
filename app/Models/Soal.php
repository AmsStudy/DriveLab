<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Soal extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content_text',
        'unique_token',
        'kode_unik',
        'password',
        'plain_password',
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
        'is_submission_enabled',
        'submission_types',
        'deadline_at',
        'submission_instructions',
        'allowed_extensions',
        'classes',
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
        'classes'      => 'array',
    ];

    protected static function booted()
    {
        static::creating(function (Soal $soal) {
            if (empty($soal->slug)) {
                $soal->slug = static::generateUniqueSlug($soal->title ?? '');
            }

            if (empty($soal->unique_token)) {
                do {
                    $token = Str::random(6);
                } while (static::where('unique_token', $token)->exists());
                $soal->unique_token = $token;
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'dokumen-' . Str::lower(Str::random(5));
        
        $reserved = ['tracktask', 'submittask', 'create', 'edit', 'status', 'view', 'file', 'download', 'preview-wrapper'];
        if (in_array(strtolower($base), $reserved, true)) {
            $base = "{$base}-dokumen";
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

    public function hasClasses(): bool
    {
        return !empty($this->classes) && is_array($this->classes) && count($this->classes) > 0;
    }

    public function getClassesList(): array
    {
        return is_array($this->classes) ? $this->classes : [];
    }

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
     * Cek apakah soal ini diproteksi password.
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
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
