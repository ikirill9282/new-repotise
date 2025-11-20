<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get setting value by key with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            if ($setting->type === 'json') {
                return json_decode($setting->value, true);
            }

            return $setting->value ?? $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, string $type = 'string'): self
    {
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
            ]
        );

        // Clear cache
        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
    }

    /**
     * Get all settings as array
     */
    public static function allAsArray(): array
    {
        return Cache::remember('settings.all', 3600, function () {
            $settings = static::all();
            $result = [];
            foreach ($settings as $setting) {
                if ($setting->type === 'json') {
                    $result[$setting->key] = json_decode($setting->value, true);
                } else {
                    $result[$setting->key] = $setting->value;
                }
            }
            return $result;
        });
    }
}
