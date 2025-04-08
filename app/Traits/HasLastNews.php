<?php

namespace App\Traits;

use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use App\Models\News;

trait HasLastNews
{
  public Collection|Arrayable|array $last_news = [];

  protected $maximum_models = 4;

  public function appendLastNews(): void
  {
    $this->last_news = Article::getLastNews($this->maximum_models);
  }
}