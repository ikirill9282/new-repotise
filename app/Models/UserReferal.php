<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReferal extends Model
{
    public $timestamps = false;

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function referal()
    {
        return $this->belongsTo(User::class, 'referal_id');
    }
}
