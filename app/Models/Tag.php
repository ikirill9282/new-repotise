<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
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
    
    public function articles()
    {
      return $this->belongsToMany(Article::class, 'article_tags', 'tag_id', 'article_id', 'id', 'id');
    }
}
