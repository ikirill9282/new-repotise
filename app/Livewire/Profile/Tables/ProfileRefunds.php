<?php

namespace App\Livewire\Profile\Tables;

use App\Models\RefundRequest;
use App\Services\RefundProcessingException;
use App\Services\StripeRefundProcessor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProfileRefunds extends Component
{
    private const REVIEW_WINDOW_DAYS = 30;

    public string $sorting = 'newest';

    public int $perPage = 10;

    public ?string $statusMessage = null;

    public ?string $statusError = null;

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
        $policyDays = self::REVIEW_WINDOW_DAYS;

        $deadline = $refund->created_at
            ? $refund->created_at->copy()->addDays($policyDays)
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
        }

        $details = trim(strip_tags($refund->details ?? ''));

        if ($refund->status !== 'pending') {
            $timeLeft = null;
        }

        return [
            'model' => $refund,
            'buyer' => $refund->buyer,
            'product' => $product,
            'preview' => $product?->preview?->image,
            'reason' => $reason,
            'details' => $details,
            'time_left' => $timeLeft,
            'status' => $refund->status,
            'status_label' => $this->formatStatusLabel($refund->status),
            'resolved_at' => $refund->resolved_at,
        ];
    }

    public function approveRefund(int $refundId): void
    {
        $this->resetStatusMessages();

        $refund = $this->findRefundForSeller($refundId);

        if (!$refund) {
            return;
        }

        if ($refund->status !== 'pending') {
            $this->statusError = 'Only pending refund requests can be approved.';
            return;
        }

        try {
            app(StripeRefundProcessor::class)->process($refund);
            $this->statusMessage = 'Refund approved and sent to Stripe.';
        } catch (RefundProcessingException $e) {
            $this->statusError = $e->getMessage();
        } catch (\Throwable $e) {
            Log::error('Stripe refund processing failed', [
                'refund_id' => $refundId,
                'seller_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            $this->statusError = 'Unable to complete the refund right now. Please try again.';
        }
    }

    public function rejectRefund(int $refundId): void
    {
        $this->resetStatusMessages();

        $refund = $this->findRefundForSeller($refundId);

        if (!$refund) {
            return;
        }

        if ($refund->status !== 'pending') {
            $this->statusError = 'Only pending refund requests can be updated.';
            return;
        }

        $refund->status = 'rejected';
        $refund->resolved_at = Carbon::now();
        $refund->save();

        $this->statusMessage = 'Refund rejected.';
    }

    protected function findRefundForSeller(int $refundId): ?RefundRequest
    {
        $userId = Auth::id();

        if (!$userId) {
            $this->statusError = 'You need to be signed in to manage refunds.';
            return null;
        }

        $refund = RefundRequest::query()
            ->where('seller_id', $userId)
            ->with(['orderProduct'])
            ->find($refundId);

        if (!$refund) {
            $this->statusError = 'Refund request not found.';
        }

        return $refund;
    }

    protected function resetStatusMessages(): void
    {
        $this->statusMessage = null;
        $this->statusError = null;
    }

    protected function formatStatusLabel(?string $status): string
    {
        return match ($status) {
            'approved' => 'Returned',
            'rejected' => 'Return Denied',
            'pending' => 'Return Requested',
            default => $status
                ? ucfirst(str_replace('_', ' ', $status))
                : 'Return Requested',
        };
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
            'statusMessage' => $this->statusMessage,
            'statusError' => $this->statusError,
        ]);
    }
}
