<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Slug;

class Policies extends Model
{
  protected static function boot()
  {
    parent::boot();

    static::creating(function($model) {
      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });
  }


  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }
}
