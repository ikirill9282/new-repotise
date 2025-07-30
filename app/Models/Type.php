<?php

namespace App\Models;

use App\Helpers\Slug;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{

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

  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
