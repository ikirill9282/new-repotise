<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'config',
        'last_updated_at',
    ];

    protected $casts = [
        'config' => 'array',
        'last_updated_at' => 'datetime',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_NOT_CONFIGURED = 'not_configured';

    public const TYPE_PAYMENT = 'payment';
    public const TYPE_EMAIL = 'email';
    public const TYPE_ANALYTICS = 'analytics';
    public const TYPE_OTHER = 'other';

    /**
     * Get config value by key
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set config value
     */
    public function setConfig(string $key, $value): void
    {
        $config = $this->config ?? [];
        $config[$key] = $value;
        $this->config = $config;
    }

    /**
     * Check if integration is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if integration is configured
     */
    public function isConfigured(): bool
    {
        return $this->status !== self::STATUS_NOT_CONFIGURED;
    }

    /**
     * Update last updated timestamp
     */
    public function touchLastUpdated(): void
    {
        $this->update(['last_updated_at' => now()]);
    }
}
