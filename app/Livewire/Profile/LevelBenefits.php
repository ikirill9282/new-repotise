<?php

namespace App\Livewire\Profile;

use App\Models\Gallery;
use App\Models\Level;
use App\Models\RevenueShare;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class LevelBenefits extends Component
{
    public string $cardClass = 'h-full';

    public function mount(string $class = ''): void
    {
        $this->cardClass = trim('h-full ' . $class);
    }

    public function render()
    {
        $user = Auth::user();

        if (!$user) {
            return view('livewire.profile.level-benefits', [
                'state' => null,
            ]);
        }

        $user->loadMissing('options.level');

        $options = $user->options;

        $levels = Level::query()->orderBy('id')->get();

        if ($levels->isEmpty()) {
            return view('livewire.profile.level-benefits', [
                'state' => null,
            ]);
        }

        $currentLevel = $options?->level ?? $levels->first();

        $totalSales = (float) RevenueShare::query()
            ->where('author_id', $user->id)
            ->whereNull('refunded_at')
            ->sum('amount_paid');

        $storageUsedMb = (float) Gallery::query()
            ->where('user_id', $user->id)
            ->sum('size');

        $storageLimitGb = (float) ($options?->getStorageSpace() ?? $currentLevel?->space ?? 0);
        $storageLimitMb = $storageLimitGb > 0 ? $storageLimitGb * 1024 : 0;

        $nextLevel = $levels->firstWhere('id', '>', $currentLevel?->id);

        $nextThreshold = (float) ($nextLevel?->sales_treshold ?? 0);
        $currentLevelLabel = $currentLevel
            ? sprintf('Level %d: %s', $currentLevel->id, $currentLevel->title)
            : 'Level 1: Beginner';

        $currentIcon = $currentLevel?->icon ?? 'icons.thumb';

        $fee = $options?->getFee() ?? $currentLevel?->fee;
        $commissionRateLabel = $fee === null
            ? '—'
            : ($fee <= 0 ? 'Exclusive Rate' : $this->formatPercent($fee));

        $storageLimitLabel = $storageLimitGb <= 0
            ? 'Unlimited'
            : $this->formatStorage($storageLimitMb);

        $storageUsedLabel = $storageUsedMb > 0
            ? $this->formatStorage($storageUsedMb)
            : '0 MB';

        $storageDisplay = $storageLimitGb <= 0
            ? sprintf('%s / Unlimited', $storageUsedLabel)
            : sprintf('%s / %s', $storageUsedLabel, $storageLimitLabel);

        $storageUsageRatio = ($storageLimitMb > 0)
            ? min(1, $storageUsedMb / $storageLimitMb)
            : 0;
        $storagePoints = $storageLimitGb <= 0
            ? 10
            : max(0, (int) round($storageUsageRatio * 10));

        $progressPoints = 0;
        $progressLabel = 'N/A';
        $remainingAmount = null;

        if ($nextLevel && $nextThreshold > 0) {
            $progressValue = min($totalSales, $nextThreshold);
            $progressLabel = sprintf('%s / %s', $this->formatCurrency($progressValue), $this->formatCurrency($nextThreshold));
            $progressRatio = min(1, $totalSales / $nextThreshold);
            $progressPoints = max(0, (int) round($progressRatio * 10));
            $remainingAmount = max($nextThreshold - $totalSales, 0);
        } elseif ($nextLevel && $nextThreshold === 0.0) {
            $progressLabel = $this->formatCurrency($totalSales);
            $progressPoints = 10;
            $remainingAmount = 0;
        }

        $bonusEnd = $user->created_at?->copy()->addDays(30);
        $bonusDaysLeft = null;

        if ($bonusEnd) {
            $bonusDaysLeft = now()->lessThan($bonusEnd)
                ? now()->diffInDays($bonusEnd)
                : 0;
        }
        $bonusLabel = is_null($bonusDaysLeft)
            ? '—'
            : ($bonusDaysLeft > 0
                ? sprintf('%d day%s left', $bonusDaysLeft, $bonusDaysLeft === 1 ? '' : 's')
                : 'Expired');

        $benefits = $this->makeBenefitsDescription($nextLevel);

        $message = $this->makeMessage($nextLevel, $remainingAmount, $benefits);

        return view('livewire.profile.level-benefits', [
            'state' => [
                'current_level_label' => $currentLevelLabel,
                'current_level_icon' => $currentIcon,
                'commission_label' => $commissionRateLabel,
                'storage_label' => $storageDisplay,
                'storage_points' => $storagePoints,
                'bonus_label' => $bonusLabel,
                'progress_label' => $progressLabel,
                'progress_points' => $progressPoints,
                'message' => $message,
            ],
        ]);
    }

    private function formatPercent(float $value): string
    {
        $formatted = number_format($value, $value == (int) $value ? 0 : 2);

        return $formatted . '%';
    }

    private function formatCurrency(float $value): string
    {
        $formatted = number_format($value, $value == floor($value) ? 0 : 2);

        return '$' . $formatted;
    }

    private function formatStorage(float $megaBytes): string
    {
        if ($megaBytes <= 0) {
            return '0 MB';
        }

        $units = ['MB', 'GB', 'TB', 'PB'];
        $power = (int) floor(log($megaBytes, 1024));
        $power = max(0, min($power, count($units) - 1));

        $value = $megaBytes / pow(1024, $power);

        $precision = $value >= 10 ? 0 : 2;

        return number_format($value, $precision) . ' ' . $units[$power];
    }

    private function makeBenefitsDescription(?Level $nextLevel): ?string
    {
        if (!$nextLevel) {
            return null;
        }

        $parts = [];

        if (!is_null($nextLevel->fee)) {
            $parts[] = $nextLevel->fee <= 0
                ? 'exclusive commission terms'
                : $this->formatPercent($nextLevel->fee) . ' commission';
        }

        if (!is_null($nextLevel->space)) {
            $parts[] = $nextLevel->space <= 0
                ? 'unlimited storage'
                : $this->formatStorage($nextLevel->space * 1024) . ' storage';
        }

        if (empty($parts)) {
            return null;
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        $last = array_pop($parts);

        return implode(', ', $parts) . ' and ' . $last;
    }

    private function makeMessage(?Level $nextLevel, ?float $remainingAmount, ?string $benefits): string
    {
        if (!$nextLevel) {
            return 'You have reached the highest creator level.';
        }

        if ($remainingAmount === null) {
            return sprintf('You are already eligible for Level %d: %s.', $nextLevel->id, $nextLevel->title);
        }

        if ($remainingAmount <= 0) {
            return sprintf('You have unlocked Level %d: %s benefits.', $nextLevel->id, $nextLevel->title);
        }

        $parts = [
            sprintf('You need %s more in sales to reach Level %d: %s', $this->formatCurrency($remainingAmount), $nextLevel->id, $nextLevel->title),
        ];

        if ($benefits) {
            $parts[] = sprintf('and unlock %s', Str::lower($benefits));
        }

        return implode(' ', $parts) . '.';
    }
}

