<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  use HasAuthor, HasStatus;

  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'review');
  }

  public function children()
  {
    return $this->hasMany(static::class, 'parent_id');
  }
}