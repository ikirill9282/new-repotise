<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOptions extends Model
{
    public function getCommissionPercent()
    {
      return match($this->level) {
        1 => 10,
        2 => 8,
        3 => 5,
      };
    }
}
