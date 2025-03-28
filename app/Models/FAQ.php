<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $table = 'faq';

    public function answer()
    {
      return $this->hasOne(FAQ::class, 'parent_id');
    }
}
