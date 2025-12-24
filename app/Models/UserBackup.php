<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserBackup extends Model
{
    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public static function generate(): array
    {
      $result = [];
      for ($i = 0; $i < 1; $i++) {
        $code = static::makeCode();
        while(static::where('code', $code)->exists()) {
          $code = static::makeCode();
        }
        array_push($result, $code);
      }
      return $result;
    }

    protected static function makeCode(): string
    {
      // Use a longer alphanumeric code (12 chars) for better security.
      return Str::random(12);
    }
}
