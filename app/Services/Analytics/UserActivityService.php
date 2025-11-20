<?php

namespace App\Services\Analytics;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;

class UserActivityService
{
    public function getTotalActiveUsers(Carbon $startDate, Carbon $endDate): int
    {
        // Уникальные пользователи, совершившие вход (из sessions таблицы)
        return DB::table('sessions')
            ->whereBetween('last_activity', [
                $startDate->timestamp,
                $endDate->timestamp
            ])
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count('user_id');
    }

    public function getNewRegistrations(Carbon $startDate, Carbon $endDate, bool $referralOnly = false): int
    {
        $query = User::whereBetween('created_at', [$startDate, $endDate]);

        if ($referralOnly) {
            // TODO: Добавить проверку на реферальные регистрации когда будет понятна структура
        }

        return $query->count();
    }

    public function getTotalBuyers(Carbon $startDate, Carbon $endDate): int
    {
        // Пользователи, совершившие хотя бы одну покупку за период
        return User::whereHas('orders', function($query) use ($startDate, $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status_id', '>=', 2);
        })->count();
    }

    public function getTotalActiveSellers(Carbon $startDate, Carbon $endDate): int
    {
        // Продавцы, совершившие вход за период
        $userIds = DB::table('sessions')
            ->whereBetween('last_activity', [
                $startDate->timestamp,
                $endDate->timestamp
            ])
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        if (empty($userIds)) {
            return 0;
        }

        return User::whereIn('id', $userIds)
            ->whereHas('roles', function($query) {
                $query->where('name', 'creator');
            })
            ->count();
    }

    protected ?\App\Services\Analytics\StripeAnalyticsService $stripeService = null;

    public function __construct()
    {
        try {
            $this->stripeService = new \App\Services\Analytics\StripeAnalyticsService();
        } catch (\Exception $e) {
            \Log::warning('StripeAnalyticsService not available: ' . $e->getMessage());
        }
    }

    public function getStripeActiveSellers(): int
    {
        if ($this->stripeService) {
            $currentMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            return $this->stripeService->getActiveSellersCount($currentMonth, $endOfMonth);
        }
        return 0;
    }

    public function getSellersPendingVerification(): int
    {
        if ($this->stripeService) {
            return $this->stripeService->getPendingVerificationCount();
        }
        return 0;
    }

    public function getUserRetentionRate(Carbon $startDate, Carbon $endDate): float
    {
        // Пользователи периода N-1, вернувшиеся в период N
        $daysDiff = $startDate->diffInDays($endDate);
        $prevStart = $startDate->copy()->subDays($daysDiff + 1);
        $prevEnd = $startDate->copy()->subDay();

        // Пользователи предыдущего периода
        $prevPeriodUsers = DB::table('sessions')
            ->whereBetween('last_activity', [
                $prevStart->timestamp,
                $prevEnd->timestamp
            ])
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        if (empty($prevPeriodUsers)) {
            return 0.0;
        }

        // Вернувшиеся пользователи
        $returnedUsers = DB::table('sessions')
            ->whereBetween('last_activity', [
                $startDate->timestamp,
                $endDate->timestamp
            ])
            ->whereIn('user_id', $prevPeriodUsers)
            ->distinct()
            ->count('user_id');

        return round(($returnedUsers / count($prevPeriodUsers)) * 100, 2);
    }

    public function getRegistrationTrendData(Carbon $startDate, Carbon $endDate, bool $referralOnly = false): array
    {
        $query = User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date');

        if ($referralOnly) {
            // TODO: Добавить фильтр по реферальным регистрациям
        }

        return $query->get()->map(function($item) {
            return [
                'date' => $item->date,
                'count' => (int) $item->count,
            ];
        })->toArray();
    }

    public function getTopSellers(Carbon $startDate, Carbon $endDate, int $limit = 10): array
    {
        $sellers = User::whereHas('roles', function($query) {
            $query->where('name', 'creator');
        })->get();

        $result = [];
        foreach ($sellers as $seller) {
            $gmv = DB::table('order_products')
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->where('products.user_id', $seller->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status_id', '>=', 2)
                ->sum('order_products.total');

            $salesCount = DB::table('order_products')
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->where('products.user_id', $seller->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status_id', '>=', 2)
                ->count();

            $platformCommission = DB::table('order_products')
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->where('products.user_id', $seller->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status_id', '>=', 2)
                ->sum('order_products.platform_reward');

            if ($gmv > 0) {
                $result[] = [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'username' => $seller->username,
                    'gmv' => (float) $gmv,
                    'sales_count' => (int) $salesCount,
                    'platform_commission' => (float) $platformCommission,
                ];
            }
        }

        // Сортируем по GMV и берем топ
        usort($result, fn($a, $b) => $b['gmv'] <=> $a['gmv']);
        
        return array_slice($result, 0, $limit);
    }
}

