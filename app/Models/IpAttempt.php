<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpAttempt extends Model
{
    protected $fillable = [
        'ip_address',
        'action',
        'user_id',
        'related_id',
        'attempted_at',
        'success',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'success' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
