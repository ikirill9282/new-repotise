<?php

namespace App\Enums;

use Filament\Support\Colors\Color;

class VerifyStatus
{
  public const OPEN = 1;
  public const COMPLETE = 2;

  public static function toArray(): array
  {
    return [
      self::NEW => static::label(self::NEW),
      self::COMPLETE => static::label(self::COMPLETE),
    ];
  }

  public static function label(int $val)
  {
    return match($val) {
      1 => 'Open',
      2 => 'Complete',
      default => 'Unknown',
    };
  }

  public static function color(int $val)
  {
    return match($val) {
      1 => Color::Blue,
      2 => Color::Emerald,
      default => Color::Gray,
    };
  }
}