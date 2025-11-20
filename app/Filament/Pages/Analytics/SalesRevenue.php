<?php

namespace App\Filament\Pages\Analytics;

use Filament\Pages\Page;
use App\Filament\Pages\Analytics\Concerns\HasDateRange;

class SalesRevenue extends Page
{
    use HasDateRange;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.analytics.sales-revenue';

    protected static ?string $navigationGroup = 'analytics';

    protected static ?string $navigationLabel = 'Sales Analytics';

    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return 'Sales & Revenue';
    }

    protected \App\Services\Analytics\SalesRevenueService $salesService;

    public function mount(): void
    {
        $this->salesService = new \App\Services\Analytics\SalesRevenueService();
    }

    // Revenue Metrics Methods
    public function getTotalGMV(): float
    {
        return $this->salesService->getTotalGMV($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalGMVChange(): ?float
    {
        $current = $this->getTotalGMV();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getTotalGMV($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getNetPlatformRevenue(): float
    {
        return $this->salesService->getNetPlatformRevenue($this->getStartDate(), $this->getEndDate());
    }

    public function getNetPlatformRevenueChange(): ?float
    {
        $current = $this->getNetPlatformRevenue();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getNetPlatformRevenue($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getProductSalesGMV(): float
    {
        return $this->salesService->getProductSalesGMV($this->getStartDate(), $this->getEndDate());
    }

    public function getProductSalesGMVChange(): ?float
    {
        $current = $this->getProductSalesGMV();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getProductSalesGMV($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getSubscriptionGMV(): float
    {
        return $this->salesService->getSubscriptionGMV($this->getStartDate(), $this->getEndDate());
    }

    public function getSubscriptionGMVChange(): ?float
    {
        $current = $this->getSubscriptionGMV();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getSubscriptionGMV($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getDonationGMV(): float
    {
        return $this->salesService->getDonationGMV($this->getStartDate(), $this->getEndDate());
    }

    public function getDonationGMVChange(): ?float
    {
        $current = $this->getDonationGMV();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getDonationGMV($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getTotalOrders(): int
    {
        return $this->salesService->getTotalOrders($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalOrdersChange(): ?float
    {
        $current = $this->getTotalOrders();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getTotalOrders($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getAOV(): float
    {
        return $this->salesService->getAOV($this->getStartDate(), $this->getEndDate());
    }

    public function getAOVChange(): ?float
    {
        $current = $this->getAOV();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getAOV($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getReferralRevenue(): float
    {
        return $this->salesService->getReferralRevenue($this->getStartDate(), $this->getEndDate());
    }

    public function getReferralRevenueChange(): ?float
    {
        $current = $this->getReferralRevenue();
        $previous = $this->salesService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->salesService->getReferralRevenue($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getRevenueTrendData(): array
    {
        return $this->salesService->getRevenueTrendData($this->getStartDate(), $this->getEndDate());
    }
}

