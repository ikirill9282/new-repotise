<?php

namespace App\Filament\Pages\Analytics;

use Filament\Pages\Page;
use App\Filament\Pages\Analytics\Concerns\HasDateRange;

class ContentPerformance extends Page
{
    use HasDateRange;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.analytics.content-performance';

    protected static ?string $navigationGroup = 'analytics';

    protected static ?string $navigationLabel = 'Content Analytics';

    protected static ?int $navigationSort = 3;

    public function getTitle(): string
    {
        return 'Content Performance';
    }

    protected \App\Services\Analytics\ContentPerformanceService $contentService;

    public function mount(): void
    {
        $this->contentService = new \App\Services\Analytics\ContentPerformanceService();
    }

    // Content Metrics Methods
    public function getTotalContentViews(): int
    {
        return $this->contentService->getTotalContentViews($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalContentViewsChange(): ?float
    {
        $current = $this->getTotalContentViews();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->contentService->getTotalContentViews($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getUniqueContentViews(): int
    {
        return $this->contentService->getUniqueContentViews($this->getStartDate(), $this->getEndDate());
    }

    public function getUniqueContentViewsChange(): ?float
    {
        $current = $this->getUniqueContentViews();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->contentService->getUniqueContentViews($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getAvgTimeOnContent(): string
    {
        return $this->contentService->getAvgTimeOnContent($this->getStartDate(), $this->getEndDate());
    }

    public function getAvgTimeOnContentChange(): ?float
    {
        return null; // Сложно сравнивать время
    }

    public function getNewContentPublished(): int
    {
        return $this->contentService->getNewContentPublished($this->getStartDate(), $this->getEndDate());
    }

    public function getNewContentPublishedChange(): ?float
    {
        $current = $this->getNewContentPublished();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->contentService->getNewContentPublished($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getTotalApprovedComments(): int
    {
        return $this->contentService->getTotalApprovedComments($this->getStartDate(), $this->getEndDate());
    }

    public function getTotalApprovedCommentsChange(): ?float
    {
        $current = $this->getTotalApprovedComments();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->contentService->getTotalApprovedComments($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }

    public function getCommentEngagementRate(): float
    {
        return $this->contentService->getCommentEngagementRate($this->getStartDate(), $this->getEndDate());
    }

    public function getCommentEngagementRateChange(): ?float
    {
        $current = $this->getCommentEngagementRate();
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $previous = $this->contentService->getCommentEngagementRate($prevStart, $prevEnd);
        return $this->calculateChange($current, $previous);
    }
}

