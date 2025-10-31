<?php

namespace App\Livewire\Profile\Tables;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Insights extends Component
{
    public bool $showAll = false;

    public function loadAll(): void
    {
        $this->showAll = true;
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.insights', [
                'rows' => collect(),
                'summary' => [
                    'views' => 0,
                    'engagement_rate' => 0,
                ],
                'hasMore' => false,
            ]);
        }

        $articleBaseQuery = Article::query()
            ->where('user_id', $userId);

        $totalViews = (int) (clone $articleBaseQuery)->sum('views');

        $articleIds = (clone $articleBaseQuery)->pluck('id');

        $likesCount = DB::table('likes')
            ->where('type', 'article')
            ->whereIn('model_id', $articleIds)
            ->count();

        $commentsCount = DB::table('comments')
            ->whereIn('article_id', $articleIds)
            ->count();

        $engagementRate = 0.0;
        $interactions = $likesCount + $commentsCount;
        if ($totalViews > 0 && $interactions > 0) {
            $engagementRate = round($interactions / $totalViews * 100, 2);
        }

        $summary = [
            'views' => $totalViews,
            'engagement_rate' => $engagementRate,
        ];

        $likesSubQuery = DB::table('likes')
            ->select('model_id', DB::raw('COUNT(*) as likes_count'))
            ->where('type', 'article')
            ->groupBy('model_id');

        $rowsQuery = Article::query()
            ->where('articles.user_id', $userId)
            ->leftJoinSub($likesSubQuery, 'likes_stats', function ($join) {
                $join->on('articles.id', '=', 'likes_stats.model_id');
            })
            ->select('articles.*')
            ->selectRaw('COALESCE(likes_stats.likes_count, 0) as likes_count')
            ->orderByDesc('articles.views')
            ->orderByDesc('articles.published_at')
            ->orderByDesc('articles.created_at');

        if (!$this->showAll) {
            $rowsQuery->limit(10);
        }

        $rows = $rowsQuery->get();

        $articlesCount = (clone $articleBaseQuery)->count();

        $hasMore = !$this->showAll && $articlesCount > $rows->count();

        return view('livewire.profile.tables.insights', [
            'rows' => $rows,
            'summary' => $summary,
            'hasMore' => $hasMore,
        ]);
    }
}
