<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Slug;
use Mews\Purifier\Facades\Purifier;

class Tag extends Model
{
    
    protected static function boot()
    {
      parent::boot();

      self::creating(function ($model) {

        // PURIFY
        $model->title = Purifier::clean($model->title);
        $model->title = str_replace('&amp;', '&', $model->title);

        // SLUG
        if (!isset($model->slug) || empty($model->slug)) {
          $model->generateSlug();
        }
      });

      self::updating(function ($model) {
        // PURIFY
        $model->title = Purifier::clean($model->title);

        // SLUG
        if ($model->isDirty('title')) {
          $model->generateSlug();
        }
      });
    }
    
    public function articles()
    {
      return $this->belongsToMany(Article::class, 'article_tags', 'tag_id', 'article_id', 'id', 'id');
    }

    private function generateSlug()
    {
      $this->slug = Slug::makeEn($this->title);
    }
}
