<?php

namespace App\Services\Analytics;

use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\Subscriptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesRevenueService
{
    public function getTotalGMV(Carbon $startDate, Carbon $endDate): float
    {
        // GMV = сумма всех успешных заказов (cost) + успешные подписки
        $ordersGMV = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2) // PAID и выше
            ->sum('cost');

        $subscriptionsGMV = Subscriptions::whereBetween('created_at', [$startDate, $endDate])
            ->where('stripe_status', 'active')
            ->sum('stripe_price');

        return round($ordersGMV + ($subscriptionsGMV / 100), 2);
    }

    public function getNetPlatformRevenue(Carbon $startDate, Carbon $endDate): float
    {
        // Доход платформы = сумма platform_reward из заказов + комиссии с подписок
        $ordersRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2)
            ->sum('platform_reward');

        // TODO: Добавить расчет комиссий с подписок если есть отдельная таблица
        return round($ordersRevenue, 2);
    }

    public function getProductSalesGMV(Carbon $startDate, Carbon $endDate): float
    {
        return round(Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2)
            ->sum('cost'), 2);
    }

    public function getSubscriptionGMV(Carbon $startDate, Carbon $endDate): float
    {
        // GMV от подписок на продукты и донаты
        return round(Subscriptions::whereBetween('created_at', [$startDate, $endDate])
            ->where('stripe_status', 'active')
            ->sum('stripe_price') / 100, 2);
    }

    public function getDonationGMV(Carbon $startDate, Carbon $endDate): float
    {
        // TODO: Реализовать когда будет понятна структура донатов
        // Пока возвращаем 0
        return 0.0;
    }

    public function getTotalOrders(Carbon $startDate, Carbon $endDate): int
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2)
            ->count();
    }

    public function getAOV(Carbon $startDate, Carbon $endDate): float
    {
        $totalGMV = $this->getProductSalesGMV($startDate, $endDate);
        $totalOrders = $this->getTotalOrders($startDate, $endDate);

        if ($totalOrders == 0) {
            return 0.0;
        }

        return round($totalGMV / $totalOrders, 2);
    }

    public function getReferralRevenue(Carbon $startDate, Carbon $endDate): float
    {
        // GMV от покупок по реферальным ссылкам
        return round(Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2)
            ->whereHas('discount', function($query) {
                $query->where('group', 'referal');
            })
            ->sum('cost'), 2);
    }

    public function getRevenueTrendData(Carbon $startDate, Carbon $endDate): array
    {
        $daysDiff = $startDate->diffInDays($endDate);
        $groupBy = $daysDiff <= 7 ? 'day' : ($daysDiff <= 90 ? 'week' : 'month');

        $query = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(cost) as total_gmv'),
                DB::raw('SUM(platform_reward) as platform_revenue')
            )
            ->groupBy('date')
            ->orderBy('date');

        $data = $query->get()->map(function($item) {
            return [
                'date' => $item->date,
                'total_gmv' => (float) $item->total_gmv,
                'platform_revenue' => (float) $item->platform_revenue,
            ];
        });

        return $data->toArray();
    }

    public function getPreviousPeriodValue(Carbon $startDate, Carbon $endDate, callable $callback): float
    {
        $daysDiff = $startDate->diffInDays($endDate);
        $prevStart = $startDate->copy()->subDays($daysDiff + 1);
        $prevEnd = $startDate->copy()->subDay();

        return $callback($prevStart, $prevEnd);
    }
}

