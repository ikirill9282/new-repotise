<?php
namespace App\Livewire\Profile\Tables;

use App\Models\RevenueShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DonationAnalytics extends Component
{
    public string $period = '30';

    public ?string $active = null;

    public ?string $donationType = null;

    public function mount(?string $active = null, ?string $period = null): void
    {
        $this->active = $active;
        if (!empty($period)) {
            $this->period = $period;
        }
    }

    protected function getPeriodDays(): int
    {
        return match ($this->period) {
            '7' => 7,
            '60' => 60,
            default => 30,
        };
    }

    protected function dateFrom(): Carbon
    {
        return Carbon::now()->subDays($this->getPeriodDays());
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.donation-analytics', [
                'rows' => collect(),
            ]);
        }

        $from = $this->dateFrom();

        $rows = RevenueShare::query()
            ->where('author_id', $userId)
            ->whereNull('product_id')
            ->where('created_at', '>=', $from)
            ->whereNull('refunded_at')
            ->with('user')
            ->when(
                $this->donationType === 'recurring',
                fn($query) => $query->whereNotNull('subscription_id')
            )
            ->when(
                $this->donationType === 'one_time',
                fn($query) => $query->whereNull('subscription_id')
            )
            ->orderByDesc('created_at')
            ->limit(25)
            ->get()
            ->map(function (RevenueShare $share) {
                $commissions = ($share->stripe_fee ?? 0) + ($share->service_amount ?? 0);

                return [
                    'date' => $share->created_at?->timezone(config('app.timezone')),
                    'transaction_id' => $share->order_id ?? $share->subscription_id ?? '—',
                    'donor' => $share->user,
                    'gross' => (float) ($share->amount_paid ?? 0),
                    'commission' => (float) $commissions,
                    'net' => (float) ($share->author_amount ?? 0),
                    'message' => null,
                    'type' => $share->subscription_id ? 'Recurring' : 'One-time',
                ];
            });

        return view('livewire.profile.tables.donation-analytics', [
            'rows' => $rows,
            'donationTypes' => [
                'one_time' => 'One-time',
                'recurring' => 'Recurring',
            ],
        ]);
    }
}
