<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    public function getSpace()
    {
      return $this->space >= 1 ? round($this->space, 2) . ' GB' : ($this->space * 1000) . ' MB';
    }
}
