<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Article;
use App\Traits\HasLastNews;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\On; 


class Feed extends Component
{
    use HasLastNews;

    public Collection|array $articles = [];
    public Collection|array $variables = [];
    public bool $end = false;
    public array $article_ids = [];

    public function mount(Arrayable|array $variables): void
    {
      $this->variables = $variables;

      if (Request::has('aid') && filter_var(Request::get('aid'), FILTER_VALIDATE_INT)) {
        $id = intval(Request::get('aid'));
        $this->appendArticle([$id]);
      } else {
        $this->appendArticle();
      }

      $this->appendLastNews();
    }

    public function loadArticles()
    {
      $this->articles = [];
      $articles = Article::query()
        ->whereIn('id', $this->article_ids)
        ->with('author', 'tags', 'preview')
        ->withCount('likes')
        ->get()
        ->map(fn($article) => $article->getFullComments()->getAnalogs()->getLikes());

      foreach ($this->article_ids as $id) {
        $this->articles[] = $articles->firstWhere('id', $id);
      }
    }

    public function appendArticle(array $additional = [])
    {

      $this->article_ids = array_merge($this->article_ids, $additional);

      $id = Article::select(['id'])
        ->whereNotIn('id', $this->article_ids)
        ->orderByDesc('id')
        ->first();

      if ($id) $this->article_ids[] = $id->id;
    }

    #[On('load-next-article')] 
    public function loadNextArticle()
    {
      if (count($this->articles) == Article::count()) {
        $this->end = true;
      }
      if ($this->end) return true;
    
      $this->appendArticle();
    }

    public function getArticlesIds()
    {
      $arr = ($this->articles instanceof Collection) ? $this->articles->toArray(): $this->articles;
      return array_column($arr, 'id');
    }

    public function render()
    {
      $this->loadArticles();
      return view('livewire.feed');
    }
}
