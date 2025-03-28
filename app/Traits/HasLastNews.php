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
    $this->last_news = News::getLastNews($this->maximum_models);
  }
}