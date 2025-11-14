<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;

use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public static function genCode()
    {
      $uuid = Uuid::uuid4()->toString();
      if (static::where('code', $uuid)->exists()) {
        return static::genCode();
      }

      return $uuid;
    }
}
