<?php
namespace App\Livewire\Profile\Tables;

use App\Enums\Order as OrderStatus;
use App\Models\RevenueShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SalesAnalytics extends Component
{
    public string $period = '30';

    public ?string $active = null;

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
            return view('livewire.profile.tables.sales-analytics', [
                'rows' => collect(),
            ]);
        }

        $from = $this->dateFrom();

        $rows = RevenueShare::query()
            ->where('author_id', $userId)
            ->whereNotNull('product_id')
            ->where('created_at', '>=', $from)
            ->with(['product', 'order'])
            ->orderByDesc('created_at')
            ->limit(25)
            ->get()
            ->map(function (RevenueShare $share) {
                $order = $share->order;
                $orderStatus = $order?->status_id
                    ? OrderStatus::label($order->status_id)
                    : '—';

                $commissions = ($share->stripe_fee ?? 0)
                    + ($share->service_amount ?? 0)
                    + ($share->referral_amount ?? 0);

                return [
                    'date' => $share->created_at?->timezone(config('app.timezone')),
                    'order_id' => $share->order_id,
                    'product' => $share->product,
                    'status' => $orderStatus,
                    'gross' => (float) ($share->amount_paid ?? 0),
                    'commissions' => (float) $commissions,
                    'net' => (float) ($share->author_amount ?? 0),
                ];
            });

        return view('livewire.profile.tables.sales-analytics', [
            'rows' => $rows,
        ]);
    }
}
