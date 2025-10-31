<?php

namespace App\Livewire\Profile;

use App\Enums\Order as OrderStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Likes;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Models\RefundRequest;
use App\Models\RevenueShare;
use App\Models\Review;
use App\Models\User;
use App\Models\UserFunds;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Analytics extends Component
{
    public $activeTable = 'donation-analytics';

    public string $period = '30';

    #[On('tableChanged')]
    public function onTableChanged(string $name)
    {
      $this->activeTable = $name;
    }

    protected function getPeriodDays(): int
    {
        return match ($this->period) {
            '7' => 7,
            '60' => 60,
            default => 30,
        };
    }

    protected function dateFrom(): Carbon
    {
        return Carbon::now()->subDays($this->getPeriodDays());
    }

    protected function emptySummary(): array
    {
        return [
            'total_revenue' => 0.0,
            'product_revenue' => 0.0,
            'donation_revenue' => 0.0,
            'insights_views' => 0,
            'creator_page_views' => 0,
        ];
    }

    protected function emptySalesStats(): array
    {
        return [
            'total_orders' => 0,
            'recurring_revenue' => 0.0,
            'refund_rate' => 0.0,
            'average_order_value' => 0.0,
            'conversion_rate' => 0.0,
            'referral_income' => 0.0,
        ];
    }

    protected function emptyProductStats(): array
    {
        return [
            'page_views' => 0,
            'average_rating' => 0.0,
            'top_product' => null,
            'average_order_value' => 0.0,
            'conversion_rate' => 0.0,
            'referral_income' => 0.0,
        ];
    }

    protected function emptyArticleStats(): array
    {
        return [
            'average_views' => 0.0,
            'likes' => 0,
            'comments' => 0,
            'top_article' => null,
        ];
    }

    protected function emptyDonationStats(): array
    {
        return [
            'recurring_donations' => 0,
            'average_donation' => 0.0,
            'top_donor' => null,
        ];
    }

    protected function buildSummary(User $user, Carbon $from): array
    {
        $revenueQuery = RevenueShare::query()
            ->where('author_id', $user->id)
            ->where('created_at', '>=', $from);

        $totalRevenue = (clone $revenueQuery)->sum('amount_paid');
        $productRevenue = (clone $revenueQuery)->whereNotNull('product_id')->sum('author_amount');
        $donationRevenue = (clone $revenueQuery)->whereNull('product_id')->sum('author_amount');

        $insightsViews = (int) Article::query()
            ->where('user_id', $user->id)
            ->sum('views');

        $creatorPageViews = (int) Product::query()
            ->where('user_id', $user->id)
            ->sum('views');

        return [
            'total_revenue' => (float) $totalRevenue,
            'product_revenue' => (float) $productRevenue,
            'donation_revenue' => (float) $donationRevenue,
            'insights_views' => $insightsViews,
            'creator_page_views' => $creatorPageViews,
        ];
    }

    protected function buildSalesStats(User $user, Carbon $from): array
    {
        $orderProductsQuery = OrderProducts::query()
            ->whereHas('product', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('order', fn ($q) => $q
                ->where('created_at', '>=', $from)
                ->where('status_id', '>=', OrderStatus::PAID)
            );

        $totalOrders = (clone $orderProductsQuery)
            ->select('order_id')
            ->distinct()
            ->count('order_id');

        $unitsSold = (int) (clone $orderProductsQuery)->sum('count');
        $orderRevenue = (float) (clone $orderProductsQuery)->sum('total');

        $recurringRevenue = (float) RevenueShare::query()
            ->where('author_id', $user->id)
            ->where('created_at', '>=', $from)
            ->whereNotNull('subscription_id')
            ->sum('author_amount');

        $refundCount = RefundRequest::query()
            ->where('seller_id', $user->id)
            ->where('created_at', '>=', $from)
            ->count();

        $productViews = (int) Product::query()
            ->where('user_id', $user->id)
            ->sum('views');

        $averageOrderValue = $totalOrders > 0 ? round($orderRevenue / $totalOrders, 2) : 0.0;
        $conversionRate = $productViews > 0 && $unitsSold > 0
            ? round(($unitsSold / $productViews) * 100, 2)
            : 0.0;
        $refundRate = $totalOrders > 0
            ? round(($refundCount / $totalOrders) * 100, 2)
            : 0.0;

        $referralIncome = (float) UserFunds::query()
            ->where('user_id', $user->id)
            ->where('group', 'referal')
            ->where('created_at', '>=', $from)
            ->sum('sum');

        return [
            'total_orders' => $totalOrders,
            'recurring_revenue' => round($recurringRevenue, 2),
            'refund_rate' => $refundRate,
            'average_order_value' => $averageOrderValue,
            'conversion_rate' => $conversionRate,
            'referral_income' => round($referralIncome, 2),
            'product_views' => $productViews,
            'units_sold' => $unitsSold,
            'order_revenue' => $orderRevenue,
        ];
    }

    protected function buildProductStats(User $user, Carbon $from, array $salesStats): array
    {
        $pageViews = $salesStats['product_views'] ?? 0;
        $conversionRate = $salesStats['conversion_rate'] ?? 0.0;
        $averageOrderValue = $salesStats['average_order_value'] ?? 0.0;
        $referralIncome = $salesStats['referral_income'] ?? 0.0;

        $averageRating = (float) Review::query()
            ->whereNull('parent_id')
            ->whereHas('product', fn ($q) => $q->where('user_id', $user->id))
            ->avg('rating');

        $topProductShare = RevenueShare::query()
            ->where('author_id', $user->id)
            ->where('created_at', '>=', $from)
            ->whereNotNull('product_id')
            ->select('product_id', DB::raw('SUM(author_amount) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->with('product')
            ->first();

        return [
            'page_views' => $pageViews,
            'average_rating' => round($averageRating, 2),
            'top_product' => $topProductShare?->product,
            'average_order_value' => $averageOrderValue,
            'conversion_rate' => $conversionRate,
            'referral_income' => round($referralIncome, 2),
        ];
    }

    protected function buildArticleStats(User $user): array
    {
        $articlesQuery = Article::query()->where('user_id', $user->id);
        $articleCount = (clone $articlesQuery)->count();

        $totalArticleViews = (clone $articlesQuery)->sum('views');
        $averageViews = $articleCount > 0 ? round($totalArticleViews / $articleCount, 2) : 0.0;

        $articleIds = (clone $articlesQuery)->pluck('id');

        $likes = Likes::query()
            ->where('type', 'article')
            ->whereIn('model_id', $articleIds)
            ->count();

        $comments = Comment::query()
            ->whereIn('article_id', $articleIds)
            ->count();

        $topArticle = (clone $articlesQuery)
            ->orderByDesc('views')
            ->first();

        return [
            'average_views' => $averageViews,
            'likes' => $likes,
            'comments' => $comments,
            'top_article' => $topArticle,
        ];
    }

    protected function buildDonationStats(User $user, Carbon $from): array
    {
        $donationQuery = RevenueShare::query()
            ->where('author_id', $user->id)
            ->where('created_at', '>=', $from)
            ->whereNull('product_id');

        $recurringDonations = (clone $donationQuery)
            ->whereNotNull('subscription_id')
            ->count();

        $averageDonation = (clone $donationQuery)->avg('author_amount');

        $topDonorShare = (clone $donationQuery)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('SUM(author_amount) as total_amount'))
            ->groupBy('user_id')
            ->orderByDesc('total_amount')
            ->with('user')
            ->first();

        return [
            'recurring_donations' => $recurringDonations,
            'average_donation' => $averageDonation ? round($averageDonation, 2) : 0.0,
            'top_donor' => $topDonorShare?->user,
        ];
    }

    public function render()
    {
        $user = Auth::user();

        if (!$user) {
            return view('livewire.profile.analytics', [
                'table' => $this->activeTable,
                'summary' => $this->emptySummary(),
                'salesStats' => $this->emptySalesStats(),
                'productStats' => $this->emptyProductStats(),
                'articleStats' => $this->emptyArticleStats(),
                'donationStats' => $this->emptyDonationStats(),
            ]);
        }

        $from = $this->dateFrom();

        $summary = $this->buildSummary($user, $from);
        $salesStats = $this->buildSalesStats($user, $from);
        $productStats = $this->buildProductStats($user, $from, $salesStats);
        $articleStats = $this->buildArticleStats($user);
        $donationStats = $this->buildDonationStats($user, $from);

        return view('livewire.profile.analytics', [
            'table' => $this->activeTable,
            'summary' => $summary,
            'salesStats' => $salesStats,
            'productStats' => $productStats,
            'articleStats' => $articleStats,
            'donationStats' => $donationStats,
        ]);
    }
}
