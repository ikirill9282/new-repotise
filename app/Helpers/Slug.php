<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class Slug
{
  static function make(string $str)
    {
      $converter = array(
          'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
          'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
          'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
          'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
          'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
          'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
          'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
      );
      $value = mb_strtolower($str);
      $value = strtr($value, $converter);
      $value = preg_replace('/[^-0-9a-z]/is', '-', $value);
      $value = preg_replace('/[-]+/is', '-', $value);
      
      $value = trim($value, '-');
      
      return $value;
    }

  public static function makeEn(string $str)
  {
    $str = preg_replace('/[\s]/is', '-', strtolower($str));
    $str = preg_replace('/[^-0-9a-z]/is', '-', $str);
    $str = preg_replace('/[-]+/is', '-', $str);

    return $str;
  }
}