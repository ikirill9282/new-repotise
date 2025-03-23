<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
  protected static function booted()
  {
      static::saving(function ($subscribe) {
        if ($subscribe->author_id == $subscribe->subscriber_id) {
            return false;
        }
      });
  }
}
