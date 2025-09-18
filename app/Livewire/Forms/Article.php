<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\Article as ModelArticle;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Schema;

class Article extends Component
{

    use HasForm;

    public $fields = [];
    protected $hidden = [];
  
    public function mount(ModelArticle $article)
    {
      $fields = $this->getFormFields($article, ['created_at', 'updated_at', 'published_at']);
      $this->hidden['id'] = $fields['id'];
      $this->hidden['user_id'] = $fields['user_id'];
      unset($fields['user_id'], $fields['id']);

      $this->fields = $fields;
    }

    public function getHidden(string $key): mixed
    {
      return $this->hidden[$key] ?? null;
    }

    public function submit()
    {
      dd($this->fields);
    }

    public function render()
    {
      return view('livewire.forms.article');
    }
}
