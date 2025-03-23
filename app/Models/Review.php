<?php

namespace App\Models;

use App\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  use HasAuthor;

  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'review');
  }
}