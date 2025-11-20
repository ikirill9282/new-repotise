<?php

namespace App\Filament\Pages\Analytics;

use Filament\Pages\Page;
use App\Filament\Pages\Analytics\Concerns\HasDateRange;

class TrafficEngagement extends Page
{
    use HasDateRange;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.analytics.traffic-engagement';

    protected static ?string $navigationGroup = 'analytics';

    protected static ?string $navigationLabel = 'Traffic Analytics';

    protected static ?int $navigationSort = 1;

    protected \App\Services\Analytics\TrafficEngagementService $trafficService;

    public function mount(): void
    {
        $this->trafficService = new \App\Services\Analytics\TrafficEngagementService();
    }

    public function getTitle(): string
    {
        return 'Traffic & Engagement';
    }

    // Key Metrics Methods
    public function getTotalVisits(): int
    {
        return $this->trafficService->getTotalVisits($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalVisitsChange(): ?float
    {
        $current = $this->getTotalVisits();
        $previous = $this->trafficService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->trafficService->getTotalVisits($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getUniqueVisitors(): int
    {
        return $this->trafficService->getUniqueVisitors($this->getStartDate(), $this->getEndDate());
    }

    public function getUniqueVisitorsChange(): ?float
    {
        $current = $this->getUniqueVisitors();
        $previous = $this->trafficService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->trafficService->getUniqueVisitors($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getPageviews(): int
    {
        return $this->trafficService->getPageviews($this->getStartDate(), $this->getEndDate());
    }

    public function getPageviewsChange(): ?float
    {
        $current = $this->getPageviews();
        $previous = $this->trafficService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->trafficService->getPageviews($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    public function getAvgSessionDuration(): string
    {
        return $this->trafficService->getAvgSessionDuration($this->getStartDate(), $this->getEndDate());
    }

    public function getAvgSessionDurationChange(): ?float
    {
        return null; // Сложно сравнивать время
    }

    public function getBounceRate(): float
    {
        return $this->trafficService->getBounceRate($this->getStartDate(), $this->getEndDate());
    }

    public function getBounceRateChange(): ?float
    {
        $current = $this->getBounceRate();
        $previous = $this->trafficService->getPreviousPeriodValue(
            $this->getStartDate(),
            $this->getEndDate(),
            fn($start, $end) => $this->trafficService->getBounceRate($start, $end)
        );
        return $this->calculateChange($current, $previous);
    }

    // Chart Data Methods
    public function getVisitsTrendData(): array
    {
        return $this->trafficService->getVisitsTrendData($this->getStartDate(), $this->getEndDate());
    }
}

