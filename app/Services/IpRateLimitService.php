<?php

namespace App\Services;

use App\Models\IpAttempt;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IpRateLimitService
{
    /**
     * Check if IP is blocked for a specific action
     */
    public function isBlocked(string $ipAddress, string $action): bool
    {
        $blockKey = "ip_blocked:{$ipAddress}:{$action}";
        return Cache::has($blockKey);
    }

    /**
     * Get remaining attempts for IP
     */
    public function getRemainingAttempts(string $ipAddress, string $action, int $maxAttempts, int $timeWindowMinutes): int
    {
        $since = Carbon::now()->subMinutes($timeWindowMinutes);
        
        $attempts = IpAttempt::where('ip_address', $ipAddress)
            ->where('action', $action)
            ->where('attempted_at', '>=', $since)
            ->where('success', false)
            ->count();

        return max(0, $maxAttempts - $attempts);
    }

    /**
     * Check if action is allowed (not rate limited)
     */
    public function isAllowed(string $ipAddress, string $action, int $maxAttempts, int $timeWindowMinutes): bool
    {
        if ($this->isBlocked($ipAddress, $action)) {
            return false;
        }

        return $this->getRemainingAttempts($ipAddress, $action, $maxAttempts, $timeWindowMinutes) > 0;
    }

    /**
     * Record an attempt
     */
    public function recordAttempt(string $ipAddress, string $action, bool $success = false, ?int $userId = null, ?int $relatedId = null): void
    {
        IpAttempt::create([
            'ip_address' => $ipAddress,
            'action' => $action,
            'user_id' => $userId,
            'related_id' => $relatedId,
            'attempted_at' => Carbon::now(),
            'success' => $success,
        ]);

        // If failed, check if we need to block
        if (!$success) {
            $this->checkAndBlock($ipAddress, $action);
        } else {
            // Clear block on success
            $this->clearBlock($ipAddress, $action);
        }
    }

    /**
     * Check and block IP if needed
     */
    protected function checkAndBlock(string $ipAddress, string $action): void
    {
        $config = $this->getActionConfig($action);
        
        if (!$config) {
            return;
        }

        $since = Carbon::now()->subMinutes($config['time_window']);
        $failedAttempts = IpAttempt::where('ip_address', $ipAddress)
            ->where('action', $action)
            ->where('attempted_at', '>=', $since)
            ->where('success', false)
            ->count();

        if ($failedAttempts >= $config['max_attempts']) {
            $this->blockIp($ipAddress, $action, $config['block_duration']);
            
            // Send email notification for login blocks
            if ($action === 'login' && $config['notify_on_block']) {
                $this->notifyIpBlocked($ipAddress, $action, $failedAttempts);
            }
        }
    }

    /**
     * Block IP address
     */
    public function blockIp(string $ipAddress, string $action, int $durationMinutes): void
    {
        $blockKey = "ip_blocked:{$ipAddress}:{$action}";
        Cache::put($blockKey, true, Carbon::now()->addMinutes($durationMinutes));
        
        Log::warning('IP address blocked', [
            'ip' => $ipAddress,
            'action' => $action,
            'duration_minutes' => $durationMinutes,
        ]);
    }

    /**
     * Clear IP block
     */
    public function clearBlock(string $ipAddress, string $action): void
    {
        $blockKey = "ip_blocked:{$ipAddress}:{$action}";
        Cache::forget($blockKey);
    }

    /**
     * Get action configuration
     */
    protected function getActionConfig(string $action): ?array
    {
        return match ($action) {
            'login' => [
                'max_attempts' => 10,
                'time_window' => 60, // 1 hour
                'block_duration' => 60, // 1 hour
                'notify_on_block' => true,
            ],
            'register' => [
                'max_attempts' => 5,
                'time_window' => 60 * 24, // 24 hours
                'block_duration' => 60 * 24, // 24 hours
                'notify_on_block' => false,
            ],
            'reset_password' => [
                'max_attempts' => 3,
                'time_window' => 60, // 1 hour
                'block_duration' => 60, // 1 hour
                'notify_on_block' => false,
            ],
            'reset_2fa' => [
                'max_attempts' => 5,
                'time_window' => 60, // 1 hour
                'block_duration' => 60, // 1 hour
                'notify_on_block' => false,
            ],
            'show_contacts' => [
                'max_attempts' => 10,
                'time_window' => 60 * 24, // 24 hours
                'block_duration' => 60 * 24, // 24 hours
                'notify_on_block' => false,
            ],
            'report_comment' => [
                'max_attempts' => 10,
                'time_window' => 60, // 1 hour
                'block_duration' => 60, // 1 hour
                'notify_on_block' => false,
            ],
            'report_article' => [
                'max_attempts' => 5,
                'time_window' => 60, // 1 hour
                'block_duration' => 60, // 1 hour
                'notify_on_block' => false,
            ],
            default => null,
        };
    }

    /**
     * Notify about IP block
     */
    protected function notifyIpBlocked(string $ipAddress, string $action, int $attempts): void
    {
        try {
            $adminEmail = config('mail.from.address');
            if (!$adminEmail) {
                return;
            }

            \Illuminate\Support\Facades\Mail::to($adminEmail)
                ->send(new \App\Mail\IpBlockedNotification($ipAddress, $action, $attempts));
        } catch (\Exception $e) {
            Log::error('Failed to send IP block notification', [
                'error' => $e->getMessage(),
                'ip' => $ipAddress,
            ]);
        }
    }

    /**
     * Check user-specific limits (for show_contacts)
     */
    public function checkUserLimit(int $userId, string $action, int $maxAttempts, int $timeWindowMinutes): bool
    {
        $since = Carbon::now()->subMinutes($timeWindowMinutes);
        
        $attempts = IpAttempt::where('user_id', $userId)
            ->where('action', $action)
            ->where('attempted_at', '>=', $since)
            ->where('success', true)
            ->count();

        return $attempts < $maxAttempts;
    }

    /**
     * Check if user already reported specific item
     */
    public function hasUserReported(int $userId, string $action, int $relatedId): bool
    {
        return IpAttempt::where('user_id', $userId)
            ->where('action', $action)
            ->where('related_id', $relatedId)
            ->where('success', true)
            ->exists();
    }
}
