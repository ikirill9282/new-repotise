<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function clear($user_id, $group = null)
    {
        static::where('user_id', $user_id)
            ->where('group', $group)
            ->delete();
    }
}
