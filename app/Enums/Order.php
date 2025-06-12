<?php

namespace App\Enums;


enum Order
{
  public const NEW = 'new';

  public function toArray(): array
  {
    return [
      self::NEW,
    ];
  }

  public static function get($val)
  {
    return match($val) {
      static::NEW => 1,
    };
  }
}