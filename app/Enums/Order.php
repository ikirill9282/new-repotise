<?php

namespace App\Enums;


enum Order
{
  public const NEW = 1;
  public const PAID = 2;
  public const PAYMENT_PROCESSING = 3;

  public function toArray(): array
  {
    return [
      self::NEW,
      self::PAID,
      self::PAYMENT_PROCESSING,
    ];
  }

  public static function label(int $val)
  {
    return match($val) {
      1 => 'New',
      2 => 'Paid',
      3 => 'Payment Processing',
      default => 'Unknown',
    };
  }
}