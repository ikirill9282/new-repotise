<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payout extends Model
{
    protected $fillable = [
        'user_id',
        'payout_id',
        'amount',
        'fees',
        'total_deducted',
        'currency',
        'status',
        'processed_at',
        'stripe_payout_id',
        'payout_method',
        'failure_message',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'total_deducted' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_PAID = 'paid';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELED = 'canceled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_id)) {
                $payout->payout_id = static::generatePayoutId();
            }
            
            // Calculate total_deducted if not set
            if (is_null($payout->total_deducted)) {
                $payout->total_deducted = $payout->amount + ($payout->fees ?? 0);
            }
        });
    }

    public static function generatePayoutId(): string
    {
        do {
            $id = 'POUT-' . strtoupper(Str::random(7));
        } while (static::where('payout_id', $id)->exists());

        return $id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function revenueShares(): HasMany
    {
        return $this->hasMany(RevenueShare::class);
    }

    /**
     * Get formatted payout method display
     */
    public function getPayoutMethodDisplayAttribute(): ?string
    {
        if (empty($this->payout_method)) {
            return null;
        }

        // Try to get payment method from Stripe if possible
        try {
            $user = $this->user;
            if ($user && $user->stripe_id) {
                $methods = $user->paymentMethods();
                $method = $methods->firstWhere('id', $this->payout_method);
                
                if ($method && isset($method->card)) {
                    $brand = ucfirst($method->card->brand ?? 'Card');
                    $last4 = $method->card->last4 ?? '0000';
                    return "{$brand} ••••{$last4}";
                }
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        return $this->payout_method;
    }
}
