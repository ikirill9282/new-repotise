<?php

namespace App\Livewire\Profile\Tables;

use App\Enums\Status;
use App\Models\Article;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ProfileArticle extends Component
{
  public int $status_id;

  public bool $all_checked = false;

  public string $sorting = 'newest';

  protected $listeners = ['articles-refresh' => '$refresh'];

  public function mount($active, ?string $sorting = null)
  {
    $this->status_id = match($active) {
      'articles-published' => Status::ACTIVE,
      'articles-scheduled' => Status::SCHEDULED,
      'articles-draft' => Status::DRAFT,
    };

    if (!empty($sorting)) {
      $this->sorting = $sorting;
    }
  }

  public function duplicate(string $articleId)
  {
    try {
      $article = Article::find(Crypt::decrypt($articleId));
      
      if (!$article || $article->user_id !== Auth::id()) {
        $this->dispatch('toastError', ['message' => 'Article not found or access denied.']);
        return;
      }

      $newArticle = $article->replicate(['status_id', 'published_at', 'slug']);
      $newArticle->status_id = Status::DRAFT;
      $newArticle->published_at = null;
      $newArticle->save();

      // Копируем теги
      if ($article->tags->isNotEmpty()) {
        $tagIds = $article->tags->pluck('id');
        $newArticle->tags()->sync($tagIds);
      }

      // Копируем галерею без оптимизации (файлы уже оптимизированы)
      $article->copyGallery($newArticle, 'articles');

      $this->dispatch('toastSuccess', ['message' => 'Article duplicated successfully!']);
      
      // Перенаправляем на страницу редактирования дублированной статьи
      return redirect($newArticle->makeEditUrl());
    } catch (\Exception $e) {
      Log::error('Error duplicating article', [
        'article_id' => $articleId,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      $this->dispatch('toastError', ['message' => 'Failed to duplicate article. Please try again.']);
    }
  }

  public function delete(string $articleId)
  {
    $this->dispatch('openModal', [
      'modalName' => 'delete-article',
      'args' => ['article_id' => $articleId]
    ]);
  }

  public function render()
  {
    $statuses = [$this->status_id];
    if ($this->status_id == Status::SCHEDULED) $statuses[] = Status::PENDING;

    return view('livewire.profile.tables.profile-article', [
      'articles' => Auth::user()->articles()
        ->with('preview')
        ->withCount('likes')
        ->whereIn('status_id', $statuses)
        ->when(
          $this->sorting === 'views',
          fn($query) => $query->orderByDesc('views')
        )
        ->when(
          $this->sorting === 'likes',
          fn($query) => $query->orderByDesc('likes_count')
        )
        ->when(
          $this->sorting === 'alphabetical',
          fn($query) => $query->orderBy('title')
        )
        ->when(
          $this->sorting === 'oldest',
          fn($query) => $query->orderBy(DB::raw('COALESCE(published_at, created_at, updated_at)'))
        )
        ->when(
          $this->sorting === 'newest',
          fn($query) => $query->orderByDesc(DB::raw('COALESCE(published_at, created_at, updated_at)'))
        )
        ->when(
          ! in_array($this->sorting, ['views', 'likes', 'alphabetical', 'oldest', 'newest'], true),
          fn($query) => $query->orderByDesc(DB::raw('COALESCE(published_at, created_at, updated_at)'))
        )
        ->orderByDesc('id')
        ->get()
        ->map(function (Article $article) {
          $article->views_total = (int) ($article->views ?? 0);
          return $article;
        })
    ]);
  }
}
