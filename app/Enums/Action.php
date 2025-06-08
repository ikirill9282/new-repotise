<?php

namespace App\Enums;


enum Action
{
  public const RESET_PASSWORD = 'Reset Password';
  public const VERIFY_EMAIL = 'Verify Email';
  public const USER_CREATED = 'User Created';
  public const BACKUP_ACTIVATE = 'Backup Code Activate';
  public const VERIFY_START = 'Start Verification';
  public const VERIFY_SUCCESS = 'User Verified';

  public function toArray(): array
  {
    return [
      self::RESET_PASSWORD,
      self::VERIFY_EMAIL,
      self::USER_CREATED,
      self::BACKUP_ACTIVATE,
      self::VERIFY_START,
      self::VERIFY_SUCCESS,
    ];
  }
}