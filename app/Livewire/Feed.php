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

    public Collection|array $variables = [];
    public int $perPage = 5;
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
      if ($this->perPage >= $this->totalRecords) {
        // $this->perPage = 2;
        // $this->dispatch('refresh-page');
      }
      $this->perPage += 5;
    }

    public function render()
    {
      if (Request::has('aid') && filter_var(Request::get('aid'), FILTER_VALIDATE_INT)) {
        $id = intval(Request::get('aid'));
        $first_article = Article::find($id);
      }
      return view('livewire.feed', ['first_article' => $first_article ?? null])->with(
        'articles', 
        Article::query()
          ->when(isset($id), fn($query) => $query->where('id', '!=', $id))
          ->orderByDesc('id')
          ->paginate($this->perPage)
      );
    }
}
