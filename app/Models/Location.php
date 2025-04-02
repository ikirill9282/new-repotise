<?php

namespace App\Models;

use App\Helpers\Slug;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Location extends Model
{
    use Searchable;

    public function toSearchableArray(): array
    {
      $array = $this->only('id', 'title');
      return $array;
    }

    public static function boot()
    {
      parent::boot();

      self::creating(function($model) {
        if (!isset($model->slug) || empty($model->slug)) {
          $model->slug = Slug::makeEn($model->title);
        }
      });
    }
}
