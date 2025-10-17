<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Helpers\Slug;
use App\Traits\HasStatus;
use Mews\Purifier\Facades\Purifier;

class Category extends Model
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
    $array = $this->only('id', 'title', 'slug', 'parent_id');

    return $array;
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id', 'id', 'id');
  }

  public function parent()
  {
    return $this->belongsTo(Category::class, 'parent_id');
  }
}
