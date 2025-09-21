<?php


namespace App\Helpers;

class Collapse
{
  public static function make(int $value)
  {
    if ($value > 1000000000) {
      return round($value / 1000000000, 2) . 'kkk';
    } elseif ($value > 1000000) {
      return round($value / 1000000, 2) . 'kk';
    } elseif ($value > 1000) {
      return $value / 1000 . 'k';
    } else {
      return $value;
    }
  }

  public static function subtractPercent($number, $percent) {
    return $number - ($number * ($percent / 100));
  }

  public static function bytesToMegabytes(int $bytes): float 
  {
    return $bytes / 1024 / 1024;
  }
}