<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Order;
use App\Enums\Order as EnumsOrder;
use App\Filament\Resources\TransactionResource;
use App\Filament\Pages\Analytics\SalesRevenue;
use App\Filament\Pages\CommissionsSettings;
use Illuminate\Support\Facades\Log;

class KeyMetricsWidget extends BaseWidget
{
    use HasDashboardDateRange;

    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = '30s'; // Poll every 30 seconds

    public function mount(): void
    {
        // #region agent log
        Log::info('DEBUG: KeyMetricsWidget mount() entry', ['hypothesisId' => 'E']);
        // #endregion
        try {
            $this->mountHasDashboardDateRange();
            // #region agent log
            Log::info('DEBUG: KeyMetricsWidget mount() success', ['hypothesisId' => 'E']);
            // #endregion
        } catch (\Exception $e) {
            // #region agent log
            Log::error('DEBUG: KeyMetricsWidget mount() error', ['hypothesisId' => 'E', 'error' => $e->getMessage()]);
            // #endregion
        }
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // #region agent log
        Log::info('DEBUG: KeyMetricsWidget getStats() entry', ['hypothesisId' => 'A']);
        // #endregion
        try {
            // ВАЖНО: Используем trigger для принудительного обновления - это заставляет Livewire пересчитать метод
            // Читаем свойство явно чтобы создать зависимость
            $trigger = $this->dateRangeUpdateTrigger ?? 0;
            // Принудительно читаем из session чтобы получить актуальные данные
            $this->dashboardStartDate = session('dashboard_start_date');
            $this->dashboardEndDate = session('dashboard_end_date');
            
            // #region agent log
            Log::info('DEBUG: session data read', ['hypothesisId' => 'F', 'startDate' => $this->dashboardStartDate, 'endDate' => $this->dashboardEndDate]);
            // #endregion
            
            // Читаем даты из session каждый раз при вызове метода
            $startDate = $this->getStartDate();
            $endDate = $this->getEndDate();
            
            // #region agent log
            Log::info('DEBUG: dates calculated', ['hypothesisId' => 'A', 'startDate' => $startDate->format('Y-m-d'), 'endDate' => $endDate->format('Y-m-d')]);
            // #endregion

        // GMV (Gross Merchandise Volume)
        $orders = Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $gmv = (float) ($orders->sum('cost') ?? 0);
        
        // Предыдущий период для сравнения
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $prevOrders = Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->get();
        $prevGmv = (float) ($prevOrders->sum('cost') ?? 0);
        $gmvChange = $this->calculateChange($gmv, $prevGmv);

        // Net Platform Revenue (application_fee_amount + platform fees)
        $netRevenue = (float) ($orders->sum('platform_reward') ?? 0);
        $prevNetRevenue = (float) ($prevOrders->sum('platform_reward') ?? 0);
        $netRevenueChange = $this->calculateChange($netRevenue, $prevNetRevenue);

        // New Orders
        $newOrders = $orders->count();
        $prevNewOrders = $prevOrders->count();
        $newOrdersChange = $this->calculateChange($newOrders, $prevNewOrders);

        // New Subscriptions (запрос по подпискам)
        // TODO: Реализовать подсчет новых подписок через Stripe API или внутреннюю БД
        $newSubscriptions = 0;
        $prevNewSubscriptions = 0;
        $newSubscriptionsChange = $this->calculateChange($newSubscriptions, $prevNewSubscriptions);

        $stats = [
            Stat::make('GMV', '$' . number_format($gmv, 2))
                ->description($gmvChange !== null 
                    ? ($gmvChange >= 0 ? '+' : '') . number_format($gmvChange, 1) . '% vs previous period'
                    : 'Gross Merchandise Volume'
                )
                ->descriptionIcon($gmvChange !== null && $gmvChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($gmvChange !== null && $gmvChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-currency-dollar')
                ->url(\App\Filament\Pages\Analytics\SalesRevenue::getUrl()),
            
            Stat::make('Net Platform Revenue', '$' . number_format($netRevenue, 2))
                ->description($netRevenueChange !== null 
                    ? ($netRevenueChange >= 0 ? '+' : '') . number_format($netRevenueChange, 1) . '% vs previous period'
                    : 'Platform revenue'
                )
                ->descriptionIcon($netRevenueChange !== null && $netRevenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netRevenueChange !== null && $netRevenueChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes')
                ->url(\App\Filament\Pages\CommissionsSettings::getUrl()),
            
            Stat::make('New Orders', number_format($newOrders))
                ->description($newOrdersChange !== null 
                    ? ($newOrdersChange >= 0 ? '+' : '') . number_format($newOrdersChange, 1) . '% vs previous period'
                    : 'Orders in period'
                )
                ->descriptionIcon($newOrdersChange !== null && $newOrdersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($newOrdersChange !== null && $newOrdersChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-shopping-cart')
                ->url(TransactionResource::getUrl('index')),
            
            Stat::make('New Subscriptions', number_format($newSubscriptions))
                ->description($newSubscriptionsChange !== null 
                    ? ($newSubscriptionsChange >= 0 ? '+' : '') . number_format($newSubscriptionsChange, 1) . '% vs previous period'
                    : 'Subscriptions created'
                )
                ->descriptionIcon($newSubscriptionsChange !== null && $newSubscriptionsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($newSubscriptionsChange !== null && $newSubscriptionsChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-arrow-path')
                ->url(\App\Filament\Pages\Analytics\SalesRevenue::getUrl()),
        ];
        // #region agent log
        Log::info('DEBUG: getStats() return', ['hypothesisId' => 'A', 'statsCount' => count($stats)]);
        // #endregion
        return $stats;
        } catch (\Exception $e) {
            // #region agent log
            Log::error('DEBUG: getStats() error', ['hypothesisId' => 'A', 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // #endregion
            throw $e;
        }
    }
}

