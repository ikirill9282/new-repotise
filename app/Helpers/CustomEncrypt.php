<?php

namespace App\Helpers;

class CustomEncrypt
{
  private static string $key = 'captainjoe';
  private static string $cipher = 'aes-256-cbc';

  /**
   *
   * @param string $data
   * @return string
   */
  public static function encrypt(array $data): string
  {
    $data = json_encode($data);
    $ivLength = openssl_cipher_iv_length(static::$cipher); // Длина IV
    $iv = openssl_random_pseudo_bytes($ivLength); // Генерация случайного IV

    $encrypted = openssl_encrypt($data, static::$cipher, static::getKey(), OPENSSL_RAW_DATA, $iv); // Шифрование данных

    if ($encrypted === false) {
      throw new \Exception("Error encryption.");
    }

    // Возвращаем зашифрованные данные в виде base64 (совмещая IV и зашифрованные данные)
    return base64_encode($iv . $encrypted);
  }

  /**
   *
   * @param string $encrypted
   * @return string
   */
  public static function decrypt(string $encrypted): array
  {
    $decodedData = base64_decode($encrypted); // Декодируем из base64

    $ivLength = openssl_cipher_iv_length(static::$cipher); // Длина IV
    $iv = substr($decodedData, 0, $ivLength); // Извлекаем IV из зашифрованных данных
    $encrypted = substr($decodedData, $ivLength); // Получаем зашифрованную часть

    $decrypted = openssl_decrypt($encrypted, static::$cipher, static::getKey(), OPENSSL_RAW_DATA, $iv); // Расшифровка данных

    if ($decrypted === false) {
      throw new \Exception("Ошибка расшифровки данных.");
    }

    return json_decode($decrypted, true);
  }

  protected static function getKey()
  {
    return hash('sha256', static::$key, true);
  }

  public static function generateUrlHash(array $data): string
  {
    ksort($data);
    $queryString = http_build_query($data);
    
    return rtrim(strtr(base64_encode($queryString), '+/', '-_'), '=');
  }

  
  public static function decodeUrlHash(string $hash): array
  {
    $base64 = strtr($hash, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($hash)) % 4);
    $queryString = base64_decode($base64);
    $result = [];
    parse_str($queryString, $result);
    return $result;
  }
}

// Пример использования
// try {
//     $encryptor = new CustomEncrypt();
    
//     $originalText = "Привет, мир!";
//     echo "Оригинальный текст: " . $originalText . PHP_EOL;
    
//     $encryptedText = $encryptor->encrypt($originalText);
//     echo "Зашифрованный текст: " . $encryptedText . PHP_EOL;
    
//     $decryptedText = $encryptor->decrypt($encryptedText);
//     echo "Расшифрованный текст: " . $decryptedText . PHP_EOL;
// } catch (\Exception $e) {
//     echo "Ошибка: " . $e->getMessage();
// }