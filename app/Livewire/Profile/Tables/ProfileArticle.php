<?php

namespace App\Livewire\Profile\Tables;

use App\Enums\Status;
use App\Models\Article;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileArticle extends Component
{
  public int $status_id;

  public bool $all_checked = false;

  public string $sorting = 'newest';

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
