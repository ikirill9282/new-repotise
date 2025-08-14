<?php

namespace App\Helpers;

class CustomEncrypt
{
  public static function getId(string $encrypted, bool $salt = true): ?int
  {
    $data = static::decodeUrlHash($encrypted, $salt);
    return $data['id'] ?? null;
  }

  public static function generateUrlHash(array $data, bool $salt = true): string
  {
    ksort($data);
    $queryString = ($salt ? static::generateRandomString(5) : '') . http_build_query($data);
    
    return rtrim(strtr(base64_encode($queryString), '+/', '-_'), '=');
  }

  public static function generateStaticUrlHas(array $data): string
  {
    return rtrim(strtr(base64_encode(http_build_query($data)), '+/', '-_'), '=');
  }
  
  public static function decodeUrlHash(string $hash, bool $salt = true): array
  {
    $base64 = strtr($hash, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($hash)) % 4);
    $queryString = $salt ? substr(base64_decode($base64), 5) : base64_decode($base64);
    $result = [];
    parse_str($queryString, $result);
    return $result;
  }

  protected static function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }
}