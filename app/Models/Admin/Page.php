<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Slug;

class Page extends Model
{
  
  protected $append = ['url'];


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

  public function sections()
  {
    return $this->belongsToMany(Section::class, PageSection::class, 'page_id', 'section_id', 'id', 'id')->withPivot('order');
  }

  public function url(): Attribute
  {
    return Attribute::make(
      get: fn() => url($this->slug),
    );
  }
}
