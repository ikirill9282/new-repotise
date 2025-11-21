<?php

namespace App\Livewire\Profile\Tables;

use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class PayoutAnalytics extends Component
{
    public ?string $statusFilter = null;
    public ?string $methodFilter = null;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        // No period needed for lifetime stats
    }

    public function updatedStatusFilter(): void
    {
        // Trigger re-render
    }

    public function updatedMethodFilter(): void
    {
        // Trigger re-render
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function openPayoutDetails(int $payoutId): void
    {
        $this->dispatch('openModal', 'payout-details', ['payout_id' => $payoutId]);
    }

    protected function buildLifetimeStats(User $user): array
    {
        $baseQuery = Payout::query()
            ->where('user_id', $user->id)
            ->where('status', Payout::STATUS_PAID); // Only count successful payouts for lifetime stats

        $totalWithdrawn = (float) (clone $baseQuery)->sum('amount');
        $totalPayoutsCount = (clone $baseQuery)->count();
        $averagePayoutAmount = $totalPayoutsCount > 0 ? $totalWithdrawn / $totalPayoutsCount : 0.0;

        // Check if user has automatic payouts enabled (would be stored in user_options)
        $nextScheduledPayout = null;
        // TODO: Implement logic to calculate next scheduled payout date and amount
        // This would depend on your automatic payout configuration

        return [
            'total_withdrawn' => round($totalWithdrawn, 2),
            'total_payouts_count' => $totalPayoutsCount,
            'average_payout_amount' => round($averagePayoutAmount, 2),
            'next_scheduled_payout' => $nextScheduledPayout,
        ];
    }

    protected function getPayoutMethods(User $user): array
    {
        $methods = [];
        
        try {
            $paymentMethods = $user->paymentMethods();
            foreach ($paymentMethods as $method) {
                if ($method->card) {
                    $brand = ucfirst($method->card->brand ?? 'Card');
                    $last4 = $method->card->last4 ?? '0000';
                    $methods[] = [
                        'id' => $method->id,
                        'label' => "{$brand} ••••{$last4}",
                    ];
                }
            }
        } catch (\Exception $e) {
            // Handle error
        }

        return $methods;
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.payout-analytics', [
                'payouts' => collect(),
                'lifetimeStats' => [
                    'total_withdrawn' => 0.0,
                    'total_payouts_count' => 0,
                    'average_payout_amount' => 0.0,
                    'next_scheduled_payout' => null,
                ],
                'payoutMethods' => [],
            ]);
        }

        $user = Auth::user();

        // Build lifetime stats
        $lifetimeStats = $this->buildLifetimeStats($user);

        // Get payout methods for filter
        $payoutMethods = $this->getPayoutMethods($user);

        // Build query with filters
        $baseQuery = Payout::query()
            ->where('user_id', $userId);

        // Apply status filter
        if (!empty($this->statusFilter) && $this->statusFilter !== 'all') {
            $baseQuery->where('status', $this->statusFilter);
        }

        // Apply method filter
        if (!empty($this->methodFilter) && $this->methodFilter !== 'all') {
            $baseQuery->where('payout_method', $this->methodFilter);
        }

        // Apply sorting
        $payouts = (clone $baseQuery)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return view('livewire.profile.tables.payout-analytics', [
            'payouts' => $payouts,
            'lifetimeStats' => $lifetimeStats,
            'payoutMethods' => $payoutMethods,
        ]);
    }
}

