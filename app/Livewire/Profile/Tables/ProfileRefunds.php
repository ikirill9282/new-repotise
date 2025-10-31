<?php

namespace App\Livewire\Profile\Tables;

use App\Models\RefundRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileRefunds extends Component
{
    public string $sorting = 'newest';

    public int $perPage = 10;

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    protected function applySorting(Builder $query): Builder
    {
        return match ($this->sorting) {
            'oldest' => $query->orderBy('created_at'),
            'status' => $query->orderBy('status')->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };
    }

    protected function formatRefund(RefundRequest $refund): array
    {
        $product = $refund->orderProduct?->product;
        $policyDays = $product?->refund_policy ?? null;

        $deadline = $policyDays
            ? $refund->created_at?->copy()->addDays($policyDays)
            : null;

        $now = Carbon::now();

        $timeLeft = '—';
        if ($deadline) {
            $daysLeftFloat = $now->floatDiffInDays($deadline, false);
            $daysLeft = (int) round($daysLeftFloat);

            $timeLeft = match (true) {
                $daysLeft < 0 => 'Expired',
                $daysLeft === 0 => 'Due today',
                default => $daysLeft . ' day' . ($daysLeft === 1 ? '' : 's') . ' left',
            };
        }

        $reason = $refund->reason;
        if ($reason) {
            $reason = ucfirst(str_replace('_', ' ', $reason));
        } else {
            $reason = trim(strip_tags($refund->details ?? ''));
        }

        return [
            'model' => $refund,
            'buyer' => $refund->buyer,
            'product' => $product,
            'preview' => $product?->preview?->image,
            'reason' => $reason,
            'time_left' => $timeLeft,
            'status' => $refund->status,
        ];
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.profile-refunds', [
                'refunds' => collect(),
                'hasMore' => false,
            ]);
        }

        $baseQuery = RefundRequest::query()
            ->where('seller_id', $userId)
            ->with([
                'buyer',
                'orderProduct.product.preview',
            ]);

        $total = (clone $baseQuery)->count();

        $refunds = $this->applySorting(clone $baseQuery)
            ->limit($this->perPage)
            ->get()
            ->map(fn ($refund) => $this->formatRefund($refund));

        $hasMore = $total > $refunds->count();

        return view('livewire.profile.tables.profile-refunds', [
            'refunds' => $refunds,
            'hasMore' => $hasMore,
        ]);
    }
}
