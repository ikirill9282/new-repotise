<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Scout\Searchable;

class News extends Model
{
  use HasAuthor, HasGallery, Searchable;


  public function toSearchableArray(): array
  {
      $array = $this->toArray();

      $array['author'] = $this->author->toArray();

      return $array;
  }

  public static function getLastNews(int $maximum_models = 4)
  {
    
    $last_news = News::orderByDesc('id')->limit($maximum_models)->get();
    while ($last_news->count() < $maximum_models) {
      $last_news = $last_news->collect()->merge($last_news)->slice(0, $maximum_models);
    }
    
    return $last_news;
  }
}
