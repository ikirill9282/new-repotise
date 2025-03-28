<?php

namespace App\Livewire;

use App\Models\Article;
use App\Traits\HasLastNews;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Contracts\Support\Arrayable;

class Insights extends Component
{
    use WithPagination, WithoutUrlPagination, HasLastNews; 

    public Arrayable|array $variables;

    public function mount($variables)
    {
      $this->variables = $variables;
      $this->appendLastNews();
    }


    public function render()
    {
        return view('livewire.insights', [
          'articles' => Article::paginate(9),
        ]);
    }
}
