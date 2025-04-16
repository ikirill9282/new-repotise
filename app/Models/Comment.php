<?php

namespace App\Models;

use App\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\Collapse;

class Comment extends Model
{
  use HasAuthor;

  public function parent()
  {
    return $this->belongsTo(Comment::class, 'parent_id');
  }

  public function children()
  {
    return $this->hasMany(Comment::class, 'parent_id');
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
    $this->load('children', 'likes.author', 'author');
    $this->loadCount('likes', 'children');

    foreach ($this->children as &$child) {
      // if ($child->children()->exists()) {
      //   $child->getChildren();
      // } else {
        $child->load('likes.author', 'author');
        $child->loadCount('likes', 'children');
      // }
    }
  }
}
