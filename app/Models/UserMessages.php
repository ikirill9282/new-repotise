<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class UserMessages extends Model
{
    protected static function boot()
    {
      parent::boot();

      // PURIFY
      static::creating(function($model) {
        $model->text = Purifier::clean($model->text);
      });

      // PURIFY
      static::updating(function($model) {
        $model->text = Purifier::clean($model->text);
      });
    }
}
