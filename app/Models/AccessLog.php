<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'soal_id',
        'ip_address',
        'user_agent',
        'status',
        'reason',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }
}
