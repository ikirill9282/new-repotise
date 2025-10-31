<?php
namespace App\Livewire\Profile\Tables;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Likes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ArticleAnalytics extends Component
{
    public ?string $active = null;

    public ?string $period = null;

    public function mount(?string $active = null, ?string $period = null): void
    {
        $this->active = $active;
        $this->period = $period;
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.article-analytics', [
                'rows' => collect(),
            ]);
        }

        $articles = Article::query()
            ->where('user_id', $userId)
            ->with('preview')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        $articleIds = $articles->pluck('id');

        $likes = Likes::query()
            ->select('model_id', DB::raw('COUNT(*) as likes_count'))
            ->where('type', 'article')
            ->whereIn('model_id', $articleIds)
            ->groupBy('model_id')
            ->pluck('likes_count', 'model_id');

        $comments = Comment::query()
            ->select('article_id', DB::raw('COUNT(*) as comments_count'))
            ->whereIn('article_id', $articleIds)
            ->groupBy('article_id')
            ->pluck('comments_count', 'article_id');

        $rows = $articles->map(function (Article $article) use ($likes, $comments) {
            $views = (int) ($article->views ?? 0);
            $likesCount = (int) ($likes[$article->id] ?? 0);
            $commentsCount = (int) ($comments[$article->id] ?? 0);
            $engagementRate = $views > 0
                ? round((($likesCount + $commentsCount) / $views) * 100, 2)
                : 0.0;

            return [
                'article' => $article,
                'views' => $views,
                'likes' => $likesCount,
                'comments' => $commentsCount,
                'engagement_rate' => $engagementRate,
                'avg_read_time' => null,
            ];
        });

        return view('livewire.profile.tables.article-analytics', [
            'rows' => $rows,
        ]);
    }
}
