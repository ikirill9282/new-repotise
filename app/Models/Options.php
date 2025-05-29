<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    public function users()
    {
      return $this->belongsToMany(User::class, 'user_options', 'option_id', 'user_id', 'id', 'id')
        ->withPivot(['value']);
    }

    public function getDefaultValue(): mixed
    {
      if ($this->name === 'avatar') return '/storage/images/default_avatar.svg';

      return null;
    }
}
