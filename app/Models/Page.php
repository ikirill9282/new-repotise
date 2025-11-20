<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Slug;
use App\Models\PageConfig;

class Page extends Model
{
  
  protected $append = ['url'];

  protected $fillable = [
    'title',
    'slug',
    'content',
    'status',
    'type',
    'seo_title',
    'seo_description',
    'seo_keywords',
  ];

  protected $casts = [
    'content' => 'string',
    'status' => 'string',
    'type' => 'string',
  ];

  public const STATUS_DRAFT = 'draft';
  public const STATUS_PUBLISHED = 'published';

  public const TYPE_SYSTEM = 'system';
  public const TYPE_CUSTOM = 'custom';

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });

    // self::updating(function ($model) {
    //     if ($model->isDirty('title')) {
    //         $model->generateSlug();
    //     }
    // });
  }

  public function config()
  {
    return $this->hasMany(PageConfig::class);
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  // public function sections()
  // {
  //   return $this->belongsToMany(Section::class, PageSection::class, 'page_id', 'section_id', 'id', 'id')->withPivot('order');
  // }

  public function url(): Attribute
  {
    return Attribute::make(
      get: fn() => url(($this->slug === 'home') ? '/' : $this->slug),
    );
  }

  public function variables(): Attribute
  {
    return Attribute::make(
      get: fn() => $this->config->keyBy('name'),
    );
  }
}
