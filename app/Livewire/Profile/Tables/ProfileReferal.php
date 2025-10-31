<?php

namespace App\Livewire\Profile\Tables;

use App\Models\UserFunds;
use App\Models\UserReferal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileReferal extends Component
{
    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.profile-referal', [
                'rows' => collect(),
                'summary' => [
                    'total_referrals' => 0,
                    'active_referrals' => 0,
                    'referral_income' => 0.0,
                ],
                'hasMore' => false,
            ]);
        }

        $referralQuery = UserReferal::query()
            ->where('owner_id', $userId)
            ->with([
                'referal' => function ($query) {
                    $query->with([
                        'orders' => fn($q) => $q->select('orders.id', 'orders.user_id', 'orders.status_id', 'orders.created_at'),
                        'discounts' => fn($q) => $q->select('discounts.id', 'discounts.user_id', 'discounts.code', 'discounts.group', 'discounts.type'),
                        'roles',
                    ]);
                },
            ]);

        $referrals = $referralQuery->get();

        $referralIncome = UserFunds::query()
            ->where('user_id', $userId)
            ->where('group', 'referal')
            ->sum('sum');

        $referralFunds = UserFunds::query()
            ->where('user_id', $userId)
            ->where('group', 'referal')
            ->get()
            ->groupBy(fn($fund) => $fund->model_id ?? 0);

        $incomeByReferal = $referralFunds->map(fn($items) => (float) $items->sum('sum'));

        $rowsData = $referrals->map(function ($entry) use ($incomeByReferal) {
            $referal = $entry->referal;

            $orders = $referal?->orders ?? collect();
            $completedOrders = $orders->filter(fn($order) => $order->status_id >= 2);
            $latestOrder = $completedOrders->sortByDesc('created_at')->first();

            $discounts = $referal?->discounts ?? collect();
            $promoCodes = $discounts
                ->filter(fn($discount) => $discount->group === 'referal' && $discount->type === 'promocode')
                ->pluck('code')
                ->toArray();

            $incomeKey = $referal->id ?? 0;
            $commission = $incomeByReferal->get($incomeKey, 0.0);

            return [
                'user' => $referal,
                'registered_at' => $referal?->created_at,
                'type' => $referal?->hasRole('creator') ? 'Seller' : 'Buyer',
                'is_active' => $latestOrder !== null,
                'promo_codes' => $promoCodes,
                'commission' => $commission,
            ];
        })->filter(fn($row) => !is_null($row['user']));

        $rowsData = $rowsData->sortByDesc('registered_at')->values();
        $rows = $rowsData->take(10)->values();
        $hasMore = $rowsData->count() > $rows->count();

        $activeReferrals = $rowsData->filter(fn($row) => $row['is_active'])->count();

        return view('livewire.profile.tables.profile-referal', [
            'rows' => $rows,
            'summary' => [
                'total_referrals' => $rowsData->count(),
                'active_referrals' => $activeReferrals,
                'referral_income' => (float) $referralIncome,
            ],
            'hasMore' => $hasMore,
        ]);
    }
}
