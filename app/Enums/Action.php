<?php

namespace App\Enums;


enum Action
{
  public const RESET_PASSWORD = 'Reset Password';
  public const VERIFY_EMAIL = 'Verify Email';
  public const USER_CREATED = 'User Created';
  public const BACKUP_ACTIVATE = 'Backup Code Activate';
  public const VERIFY_START = 'Sart Verification';

  public function toArray(): array
  {
    return [
      self::RESET_PASSWORD,
      self::VERIFY_EMAIL,
      self::USER_CREATED,
    ];
  }
}