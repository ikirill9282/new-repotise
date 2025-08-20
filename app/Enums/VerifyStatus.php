<?php

namespace App\Enums;

use Filament\Support\Colors\Color;

class VerifyStatus
{
  public const NEW = 1;
  public const PENDING = 2;
  public const FULL = 3;
  public const COMPLETE = 4;

  public static function toArray(): array
  {
    return [
      self::NEW => static::label(self::NEW),
      self::PENDING => static::label(self::PENDING),
      self::FULL => static::label(self::FULL),
      self::COMPLETE => static::label(self::COMPLETE),
    ];
  }

  public static function label(int $val)
  {
    return match($val) {
      1 => 'New',
      2 => 'Pending Manual Review',
      3 => 'Pending Full Verification',
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