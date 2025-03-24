<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Article;
use App\Traits\HasLastNews;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\On; 


class Insights extends Component
{
    use HasLastNews;

    public Collection|array $articles = [];
    public Collection|array $variables = [];
    public bool $end = false;
    public $article_ids = [];

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

    public function appendArticle(array $additional = [])
    {
      if (!empty($additional)) {
       $this->articles = Article::query()
          ->whereIn('id', $additional)
          ->with('author', 'tags', 'preview')
          ->withCount('likes')
          ->get()
          ->map(function($article) {
            $article = $article
              ->getFullComments()
              ->getAnalogs()
              ->getLikes();

            return $article;
          });
      }

      $id = Article::select(['id'])
        ->whereNotIn('id', $this->getArticlesIds())
        ->orderByDesc('id')
        ->first();

      if ($id) {
        $this->articles[] = Article::query()
          ->where('id', $id->id)
          ->with('author', 'tags', 'preview')
          ->withCount('likes')
          ->first();
      } else {
        $this->end = true;
        return;
      }

    }

    #[On('load-next-article')] 
    public function loadNextArticle()
    {
      if (count($this->articles) == Article::count()) {
        $this->end = true;
      }
      if ($this->end) return;
    
      $this->appendArticle();
    }

    public function getArticlesIds()
    {
      $arr = ($this->articles instanceof Collection) ? $this->articles->toArray(): $this->articles;
      return array_column($arr, 'id');
    }

    public function render()
    {
      return view('livewire.insights');
    }
}
