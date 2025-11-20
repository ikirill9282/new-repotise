<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'email',
        'failure_reason',
    ];

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    /**
     * Get user relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log successful login
     */
    public static function logSuccess(?User $user, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return static::create([
            'user_id' => $user?->id,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'status' => self::STATUS_SUCCESS,
            'email' => $user?->email,
        ]);
    }

    /**
     * Log failed login
     */
    public static function logFailed(?string $email = null, ?string $reason = null, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return static::create([
            'user_id' => null,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'status' => self::STATUS_FAILED,
            'email' => $email,
            'failure_reason' => $reason,
        ]);
    }
}
