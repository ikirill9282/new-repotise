<?php

namespace App\Enums;


enum Order
{
  public const NEW = 1;
  public const PAID = 2;

  public function toArray(): array
  {
    return [
      self::NEW,
      self::PAID,
    ];
  }

  public static function label(int $val)
  {
    return match($val) {
      1 => 'New',
      2 => 'Paid',
      default => 'Unknown',
    };
  }
}