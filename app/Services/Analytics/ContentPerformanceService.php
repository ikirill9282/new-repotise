<?php

namespace App\Services\Analytics;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContentPerformanceService
{
    protected ?\App\Services\Analytics\GA4Service $ga4Service = null;

    public function __construct()
    {
        try {
            $this->ga4Service = new \App\Services\Analytics\GA4Service();
        } catch (\Exception $e) {
            \Log::warning('GA4Service not available: ' . $e->getMessage());
        }
    }

    public function getTotalContentViews(Carbon $startDate, Carbon $endDate): int
    {
        if ($this->ga4Service) {
            return $this->ga4Service->getContentViews($startDate, $endDate, '/articles/*');
        }
        // Fallback: возвращаем количество просмотров из БД
        return Article::whereBetween('created_at', [$startDate, $endDate])
            ->sum('views');
    }

    public function getUniqueContentViews(Carbon $startDate, Carbon $endDate): int
    {
        // GA4 не предоставляет уникальные просмотры для конкретных путей напрямую
        // Можно использовать фильтры в runReport, но это сложнее
        // Пока возвращаем 0, можно улучшить позже
        return 0;
    }

    public function getAvgTimeOnContent(Carbon $startDate, Carbon $endDate): string
    {
        // GA4 не предоставляет среднее время на странице для конкретных путей напрямую
        // Можно использовать фильтры в runReport
        // Пока возвращаем '0:00', можно улучшить позже
        return '0:00';
    }

    public function getNewContentPublished(Carbon $startDate, Carbon $endDate): int
    {
        return Article::whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    public function getTotalApprovedComments(Carbon $startDate, Carbon $endDate): int
    {
        // TODO: Проверить структуру Comment модели для статуса "Published"
        return Comment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '!=', 0) // Предполагаем, что 0 = deleted
            ->count();
    }

    public function getCommentEngagementRate(Carbon $startDate, Carbon $endDate): float
    {
        $totalViews = $this->getTotalContentViews($startDate, $endDate);
        $totalComments = $this->getTotalApprovedComments($startDate, $endDate);

        if ($totalViews == 0) {
            return 0.0;
        }

        return round(($totalComments / $totalViews) * 100, 2);
    }

    public function getTopPerformingContent(Carbon $startDate, Carbon $endDate, int $limit = 20): array
    {
        return Article::whereBetween('created_at', [$startDate, $endDate])
            ->with('author')
            ->withCount(['messages as comments_count' => function($query) {
                $query->where('status_id', '!=', 0);
            }])
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'author_id' => $article->user_id,
                    'author_name' => $article->author->name ?? 'Unknown',
                    'author_username' => $article->author->username ?? 'unknown',
                    'type' => 'Article',
                    'category' => null, // TODO: Добавить если есть категории
                    'publish_date' => $article->created_at->format('Y-m-d'),
                    'views' => (int) ($article->views ?? 0),
                    'unique_views' => 0, // TODO: Из GA4
                    'avg_time_on_page' => '0:00', // TODO: Из GA4
                    'approved_comments' => (int) ($article->comments_count ?? 0),
                ];
            })
            ->toArray();
    }

    public function getAuthorStatistics(Carbon $startDate, Carbon $endDate): array
    {
        return Article::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'user_id',
                DB::raw('COUNT(*) as articles_count'),
                DB::raw('SUM(views) as total_views'),
                DB::raw('AVG(views) as avg_views')
            )
            ->groupBy('user_id')
            ->with('author')
            ->orderByDesc('total_views')
            ->get()
            ->map(function($item) {
                return [
                    'author_id' => $item->user_id,
                    'author_name' => $item->author->name ?? 'Unknown',
                    'author_username' => $item->author->username ?? 'unknown',
                    'articles_count' => (int) $item->articles_count,
                    'total_views' => (int) $item->total_views,
                    'avg_views' => round((float) $item->avg_views, 2),
                    'avg_read_time' => '0:00', // TODO: Рассчитать
                    'avg_engagement_rate' => 0.0, // TODO: Рассчитать
                ];
            })
            ->toArray();
    }
}

