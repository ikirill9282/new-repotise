<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Article;
use App\Traits\HasLastNews;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\On; 
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Feed extends Component
{
    use HasLastNews;
    use WithPagination;

    // public Collection|array $articles = [];
    public Collection|array $variables = [];
    // public bool $end = false;
    // public array $article_ids = [];
    // public int $visible = 3;
    public int $perPage = 2;
    public int $totalRecords;

    public function mount(Arrayable|array $variables): void
    {
      $this->variables = $variables;
      $this->totalRecords = Article::count();
      $this->appendLastNews();
    }

    #[On('load-next-article')] 
    public function loadNextArticle()
    {
      $this->perPage += 2;
    }

    public function render()
    {
      if (Request::has('aid') && filter_var(Request::get('aid'), FILTER_VALIDATE_INT)) {
        $id = intval(Request::get('aid'));
        $first_article = Article::where('id', $id);
      }
      return view('livewire.feed')->with(
        'articles', 
        Article::query()
          ->orderByDesc('id')
          ->paginate($this->perPage)
      );
    }
}
