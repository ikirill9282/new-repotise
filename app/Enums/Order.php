<?php

namespace App\Enums;

use Filament\Support\Colors\Color;

enum Order
{
  public const NEW = 1;
  public const PAID = 2;
  public const REWARDING = 3;
  public const COMPLETE = 4;

  public static function toArray(): array
  {
    return [
      self::NEW => static::label(self::NEW),
      self::PAID => static::label(self::PAID),
      self::REWARDING => static::label(self::REWARDING),
      self::COMPLETE => static::label(self::COMPLETE),
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

  public static function color(int $val)
  {
    return match($val) {
      1 => Color::Gray,
      2 => Color::Blue,
      3 => Color::Indigo,
      4 => Color::Emerald,
      default => Color::Gray,
    };
  }
}