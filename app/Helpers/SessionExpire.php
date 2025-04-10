<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class SessionExpire
{
  public static function check(string $session_key, ?callable $callback_true = null, ?callable $callback_false = null)
  {
    if (Session::exists($session_key)) {
      $expires = Carbon::parse(Session::get($session_key)['expires']);
      if (Carbon::now()->greaterThanOrEqualTo($expires)) {
        Session::forget($session_key);
      } else {
        return $callback_false ? $callback_false($session_key) : null;
      }
    }

    if (!Session::exists($session_key)) {
      Session::put($session_key, ['value' => true, 'expires' => Carbon::now()->addDay()]);
      return $callback_true ? $callback_true($session_key) : null;
    }

    return null;
  }
}