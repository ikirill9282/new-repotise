<?php

namespace App\Helpers;

class CustomEncrypt
{
  // private static string $key = 'captainjoe';
  // private static string $cipher = 'aes-256-cbc';

  // /**
  //  *
  //  * @param string $data
  //  * @return string
  //  */
  // public static function encrypt(array $data): string
  // {
  //   $data = json_encode($data);
  //   $ivLength = openssl_cipher_iv_length(static::$cipher);
  //   $iv = openssl_random_pseudo_bytes($ivLength);

  //   $encrypted = openssl_encrypt($data, static::$cipher, static::getKey(), OPENSSL_RAW_DATA, $iv);

  //   if ($encrypted === false) {
  //     throw new \Exception("Error encryption.");
  //   }

  //   return base64_encode($iv . $encrypted);
  // }

  // /**
  //  *
  //  * @param string $encrypted
  //  * @return string
  //  */
  // public static function decrypt(string $encrypted): array
  // {
  //   $decodedData = base64_decode($encrypted);

  //   $ivLength = openssl_cipher_iv_length(static::$cipher);
  //   $iv = substr($decodedData, 0, $ivLength);
  //   $encrypted = substr($decodedData, $ivLength);

  //   $decrypted = openssl_decrypt($encrypted, static::$cipher, static::getKey(), OPENSSL_RAW_DATA, $iv);

  //   if ($decrypted === false) {
  //     throw new \Exception("Decode error");
  //   }

  //   return json_decode($decrypted, true);
  // }

  // protected static function getKey()
  // {
  //   return hash('sha256', static::$key, true);
  // }

  public static function getId(string $encrypted): ?int
  {
    $data = static::decodeUrlHash($encrypted);
    return $data['id'] ?? null;
  }

  public static function generateUrlHash(array $data): string
  {
    ksort($data);
    $queryString = static::generateRandomString(5) . http_build_query($data);
    
    return rtrim(strtr(base64_encode($queryString), '+/', '-_'), '=');
  }

  public static function generateStaticUrlHas(array $data): string
  {
    return rtrim(strtr(base64_encode(http_build_query($data)), '+/', '-_'), '=');
  }

  
  public static function decodeUrlHash(string $hash): array
  {
    $base64 = strtr($hash, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($hash)) % 4);
    $queryString = substr(base64_decode($base64), 5);
    $result = [];
    parse_str($queryString, $result);
    return $result;
  }

  protected static function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }
}