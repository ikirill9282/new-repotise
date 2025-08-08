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

  public static function expire(string $session_key): bool
  {
    if (Session::exists($session_key) && Carbon::now()->greaterThanOrEqualTo(Carbon::parse(Session::get($session_key)['expires']))) {
      Session::forget($session_key);
      return true;
    }

    return false;
  }

  public static function exists(string $session_key): bool
  {
    return Session::exists($session_key) && !Carbon::now()->greaterThanOrEqualTo(Carbon::parse(Session::get($session_key)['expires']));
  }

  public static function get(string $session_key)
  {
    if (!static::expire($session_key)) {
      $data = Session::get($session_key);
      if (isset($data['value'])) {
        return $data['value'];
      }
    }
    return null;
  }

  public static function getExpire(string $session_key)
  {
    if (!static::expire($session_key)) {
      $data = Session::get($session_key);
      if (isset($data['expires'])) {
        return $data['expires'];
      }
    }
    return null;
  }

  public static function set(string $session_key, $value, ?Carbon $expire = null): void
  {
    Session::put($session_key, ['value' => $value, 'expires' => $expire ? $expire : Carbon::now()->addDay()]);
  }

  


  // TODO: Rework on Session facade.

  public static function saveCart(string $session_key, array $data): void
  {
    $current = Session::get($session_key);
    $expires = isset($current['expires']) ? Carbon::now()->greaterThanOrEqualTo(Carbon::parse($current['expires'])) : true;
    if ($expires) {
      Session::forget($session_key);
    }

    Session::put($session_key, ['value' => json_encode($data), 'expires' => Carbon::now()->addWeek()]);
  }

  public static function getCart(string $session_key): array
  {
    $res = Session::exists($session_key) ? json_decode(Session::get($session_key)['value'], true) : [];
    return $res ?? [];
  }

  public static function setCartItemCount(string $session_key, int $id, int $count)
  {
    if (Session::exists($session_key)) {
      $session_data = static::getCart($session_key);
      if (isset($session_data['products'])) {

        foreach ($session_data['products'] as &$product) {
          if ($product['id'] == $id) {
            $product['count'] = $count;
          }
        }

        static::saveCart('cart', $session_data);
      }
    }
  }

  public static function addPromocode(string $session_key, int $id)
  {
    if (Session::exists($session_key)) {
      $session_data = static::getCart($session_key);
      $session_data['promocode'] = $id;
      static::saveCart($session_key, $session_data);
    }
  }
}
