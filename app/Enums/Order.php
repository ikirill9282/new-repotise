<?php

namespace App\Enums;


enum Order
{
  public const NEW = 1;
  public const PAID = 2;
  public const REWARDING = 3;
  public const COMPLETE = 4;

  public function toArray(): array
  {
    return [
      self::NEW,
      self::PAID,
      self::REWARDING,
      self::COMPLETE,
    ];
  }

  public static function label(int $val)
  {
    return match($val) {
      1 => 'New',
      2 => 'Paid',
      3 => 'Rewarding',
      4 => 'Complete',
      default => 'Unknown',
    };
  }
}