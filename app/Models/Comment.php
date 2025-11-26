<?php

namespace App\Models;

use App\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\Collapse;
use App\Traits\HasMessages;
use App\Traits\HasReport;
use App\Traits\HasStatus;

class Comment extends Model
{
  use HasAuthor, HasStatus, HasMessages, HasReport;

  public function messages()
  {
    return $this->hasMany(static::class, 'parent_id')->orderByDesc('id');
  }
  
  public function article()
  {
    return $this->belongsTo(Article::class);
  }

  public function parent()
  {
    return $this->belongsTo(Comment::class, 'parent_id');
  }

  public function children()
  {
    $query = $this->hasMany(Comment::class, 'parent_id');
    
    // Показываем активные комментарии для всех
    // И PENDING комментарии только для их автора
    $query->where(function($q) {
      $q->where('status_id', \App\Enums\Status::ACTIVE);
      if (auth()->check()) {
        $q->orWhere(function($subQ) {
          $subQ->where('status_id', \App\Enums\Status::PENDING)
            ->where('user_id', auth()->id());
        });
      }
    });
    
    return $query->orderByDesc('created_at');
  }

  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'comment'); 
  }

  public function likesCount(): Attribute
  {
    return Attribute::make(
      get: fn($value) => Collapse::make($value),
    );
  }

  public function getChildren()
  {
    $this->load('children', 'likes.author.options', 'author.options');
    $this->loadCount('likes', 'children');

    foreach ($this->children as &$child) {
      // if ($child->children()->exists()) {
      //   $child->getChildren();
      // } else {
        $child->load('likes.author.options', 'author.options');
        $child->loadCount('likes', 'children');
      // }
    }
  }
}
