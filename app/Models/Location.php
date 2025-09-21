<?php

namespace App\Models;

use App\Helpers\Slug;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Mews\Purifier\Facades\Purifier;

class Location extends Model
{
  use Searchable, HasStatus;

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {

      $model->title = Purifier::clean($model->title);

      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });

    self::updating(function ($model) {
      
      $model->title = Purifier::clean($model->title);

      if ($model->isDirty('title')) {
        $model->generateSlug();
      }
    });
  }

  public function toSearchableArray(): array
  {
    $array = $this->only('id', 'title', 'slug');
    return $array;
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, ProductLocations::class, 'location_id', 'product_id', 'id', 'id');
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
