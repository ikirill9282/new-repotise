<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Contracts\Support\Arrayable;

class Insights extends Component
{
    use WithPagination, WithoutUrlPagination; 

    public Arrayable|array $variables;

    public function mount($variables)
    {
      $this->variables = $variables;
    }


    public function render()
    {
        return view('livewire.insights', [
          'articles' => Article::query()
            ->whereHas('author', function($query) {
              $query->whereHas('roles', fn($subquery) => $subquery->whereIn('name', ['customer', 'creator']));
            })
            ->orderByDesc('id')
            ->paginate(9),
        ]);
    }
}
