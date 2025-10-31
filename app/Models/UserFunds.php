<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFunds extends Model
{
    protected $casts = [
        'sum' => 'float',
    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
      return $this->morphTo(__FUNCTION__, 'model', 'model_id');
    }
}
