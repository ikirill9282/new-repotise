<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasMessages;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  use HasAuthor, HasStatus, HasMessages;
  
  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'review');
  }

  // public function children()
  // {
  //   return $this->hasMany(static::class, 'parent_id');
  // }

  public function messages()
  {
    return $this->hasMany(static::class, 'parent_id');
  }

  // public function getChildren()
  // {
  //   $this->loadChildren();
  //   if (!$this->children?->isEmpty()) {
  //     foreach($this->children as $child) {
  //       $child->load('likes.author.options', 'author.options');
  //       $child->loadCount('likes', 'children');
  //     }
  //   }
  // }
}