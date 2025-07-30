<?php

namespace App\Models;

use App\Helpers\Slug;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Location extends Model
{
  use Searchable, HasStatus;

  public function toSearchableArray(): array
  {
    $array = $this->only('id', 'title', 'slug');
    return $array;
  }

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {

      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
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

  public function products()
  {
    return $this->hasMany(Product::class);
  }

  public function hasPoster(): bool
  {
    return !is_null($this->poster) && strlen($this->poster);
  }

  public function makeUrl(): string
  {
    return url("/products/$this->slug");
  }
}
