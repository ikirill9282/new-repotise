<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\Article as ModelArticle;

class Article extends Component
{

    public $fields = [];
  
    public function mount(?ModelArticle $article = null)
    {
      $this->fields = is_null($article) 
        ? (new ModelArticle())->toArray() 
        : $article->toArray();
    }

    public function render()
    {
        return view('livewire.forms.article');
    }
}
