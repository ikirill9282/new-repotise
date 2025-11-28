<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Order;
use App\Models\User;
use App\Models\Article;
use App\Models\ModerationQueue;
use App\Models\Payout;
use App\Enums\Order as EnumsOrder;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\ModerationQueueResource;
use App\Filament\Resources\PayoutResource;
use App\Filament\Resources\ArticleResource;
use Illuminate\Support\Carbon;

class RecentActivityWidget extends Widget
{
    use HasDashboardDateRange;
    
    protected static string $view = 'filament.widgets.recent-activity-widget';

    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = 'full';

    public function mount(): void
    {
        $this->mountHasDashboardDateRange();
    }

    public function getRecentOrders()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        
        return Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'order_products.product'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($order) {
                $firstProduct = $order->order_products->first()?->product;
                return [
                    'id' => $order->id,
                    'product_name' => $firstProduct?->title ?? 'N/A',
                    'buyer' => $order->user->name ?? 'N/A',
                    'amount' => $order->cost,
                    'time' => $order->created_at->diffForHumans(),
                ];
            });
    }

    public function getRecentRegistrations()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        
        return User::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('roles')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'username' => $user->username ?? 'N/A',
                    'role' => $user->getDisplayRoleName(),
                    'time' => $user->created_at->diffForHumans(),
                ];
            });
    }

    public function getRecentModerationItems()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        
        return ModerationQueue::query()
            ->whereIn('priority', [ModerationQueue::PRIORITY_HIGH, ModerationQueue::PRIORITY_NORMAL])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $instance = $item->getModelInstance();
                $title = $instance?->title ?? $instance?->subject ?? 'N/A';
                return [
                    'type' => ucfirst($item->model ?? 'Unknown'),
                    'element' => $title,
                    'time' => $item->created_at->diffForHumans(),
                ];
            });
    }

    public function getRecentPayouts()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        
        return Payout::query()
            ->whereIn('status', [Payout::STATUS_COMPLETED, Payout::STATUS_FAILED, Payout::STATUS_PAID])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(function($payout) {
                return [
                    'id' => $payout->stripe_payout_id ?? $payout->payout_id ?? 'N/A',
                    'seller' => $payout->user?->name ?? $payout->user?->username ?? 'N/A',
                    'amount' => (float) $payout->amount,
                    'status' => ucfirst($payout->status),
                    'time' => $payout->updated_at?->diffForHumans() ?? $payout->created_at?->diffForHumans() ?? 'N/A',
                ];
            });
    }

    public function getRecentContent()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        
        return Article::query()
            ->where('status_id', 1) // Published
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('author')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($article) {
                return [
                    'title' => $article->title ?? 'N/A',
                    'author' => $article->author?->name ?? $article->author?->username ?? 'N/A',
                    'type' => 'Article',
                    'time' => $article->created_at?->diffForHumans() ?? 'N/A',
                ];
            });
    }
}

