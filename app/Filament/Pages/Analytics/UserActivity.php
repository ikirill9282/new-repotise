<?php

namespace App\Filament\Pages\Analytics;

use Filament\Pages\Page;
use App\Filament\Pages\Analytics\Concerns\HasDateRange;

class UserActivity extends Page
{
    use HasDateRange;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.pages.analytics.user-activity';

    protected static ?string $navigationGroup = 'analytics';

    protected static ?string $navigationLabel = 'User Analytics';

    protected static ?int $navigationSort = 4;

    public function getTitle(): string
    {
        return 'User Activity';
    }

    protected \App\Services\Analytics\UserActivityService $userService;

    public function mount(): void
    {
        $this->userService = new \App\Services\Analytics\UserActivityService();
    }

    // User Metrics Methods
    public function getTotalActiveUsers(): int
    {
        return $this->userService->getTotalActiveUsers($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalActiveUsersChange(): ?float
    {
        $current = $this->getTotalActiveUsers();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->userService->getTotalActiveUsers($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getNewRegistrations(): int
    {
        return $this->userService->getNewRegistrations($this->getStartDate(), $this->getEndDate());
    }

    public function getNewRegistrationsChange(): ?float
    {
        $current = $this->getNewRegistrations();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->userService->getNewRegistrations($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getTotalBuyers(): int
    {
        return $this->userService->getTotalBuyers($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalBuyersChange(): ?float
    {
        $current = $this->getTotalBuyers();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->userService->getTotalBuyers($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getTotalActiveSellers(): int
    {
        return $this->userService->getTotalActiveSellers($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalActiveSellersChange(): ?float
    {
        $current = $this->getTotalActiveSellers();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->userService->getTotalActiveSellers($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getStripeActiveSellers(): int
    {
        // Не зависит от Date Range - текущий месяц
        return $this->userService->getStripeActiveSellers();
    }

    public function getSellersPendingVerification(): int
    {
        // Не зависит от Date Range
        return $this->userService->getSellersPendingVerification();
    }

    public function getUserRetentionRate(): float
    {
        return $this->userService->getUserRetentionRate($this->getStartDate(), $this->getEndDate());
    }

    public function getUserRetentionRateChange(): ?float
    {
        $current = $this->getUserRetentionRate();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->userService->getUserRetentionRate($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getRegistrationTrendData(): array
    {
        return $this->userService->getRegistrationTrendData($this->getStartDate(), $this->getEndDate());
    }
}

