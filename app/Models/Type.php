<?php

namespace App\Models;

use App\Helpers\Slug;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class Type extends Model
{

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
      if ($model->isDirty('title')) {
        $model->generateSlug();
      }
    });
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, ProductTypes::class, 'type_id', 'product_id', 'id', 'id');
  }
}
