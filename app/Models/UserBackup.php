<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBackup extends Model
{
    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public static function generate(): array
    {
      $result = [];
      for ($i = 0; $i < 10; $i++) {
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
      return substr(trim(preg_replace('/[^a-zA-Z0-9]/is', '', base64_encode(random_bytes(10))), '='), -6, 6);
    }
}
