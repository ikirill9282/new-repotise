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
      $model->title = str_replace('&amp;', '&', $model->title);

      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
      
      // Check if location with same title already exists
      $existingByTitle = static::where('title', $model->title)->first();
      if ($existingByTitle) {
        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        throw new \Illuminate\Validation\ValidationException(
          $validator,
          ['title' => ["Location with title '{$model->title}' already exists. Please select the existing location or choose a different name."]]
        );
      }
      
      // Check if location with same slug already exists
      $existingBySlug = static::where('slug', $model->slug)->first();
      if ($existingBySlug) {
        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        throw new \Illuminate\Validation\ValidationException(
          $validator,
          ['slug' => ["Location with slug '{$model->slug}' already exists. Please select the existing location or choose a different name."]]
        );
      }
    });

    self::updating(function ($model) {
      
      $model->title = Purifier::clean($model->title);
      $model->title = str_replace('&amp;', '&', $model->title);

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
