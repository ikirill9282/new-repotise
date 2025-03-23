<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use App\Models\News;

trait HasLastNews
{
  public Collection|Arrayable|array $last_news = [];

  protected $maximum_models = 4;

  public function appendLastNews(): void
  {
    $this->last_news = News::orderByDesc('id')->limit($this->maximum_models)->get();
    if ($this->last_news->isNotEmpty() && $this->last_news->count() < 4) {
      for (
        $i = $this->last_news->count();
        $i < $this->maximum_models; 
        $i++
      ) {
        $this->last_news = collect(array_merge($this->last_news->all(), $this->last_news->all()));
      }
    }
    
    $this->last_news = $this->last_news->slice($this->maximum_models);
  }
}