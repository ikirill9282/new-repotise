<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Scout\Searchable;
use App\Helpers\Slug;

class News extends Model
{
  use HasAuthor, HasGallery, Searchable;
  
  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      $model->generateSlug();
    });

    self::updating(function ($model) {
        if ($model->isDirty('title')) {
            $model->generateSlug();
        }
    });
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  public function toSearchableArray(): array
  {
      $array = $this->toArray();
    
      $array['author'] = $this->author->only('profile', 'name', 'avatar', 'description');
      $array['tags'] = $this->tags->select('id', 'title')->toArray();

      return $array;
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class, NewsTags::class, 'news_id', 'tag_id', 'id', 'id');
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
