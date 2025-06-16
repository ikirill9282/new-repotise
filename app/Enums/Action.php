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
  public const VERIFY_CANCEL = 'User Cancel Verification';
  public const VERIFY_REQUIRES_INPUT = 'User Verification Requires Input';
  public const VERIFY_IN_PROGRESS = 'User Verification In Progress';
  public const INVITE_BY_PURCHASE = 'User Invited By Purchase';

  public function toArray(): array
  {
    return [
      self::RESET_PASSWORD,
      self::VERIFY_EMAIL,
      self::USER_CREATED,
      self::BACKUP_ACTIVATE,
      self::VERIFY_START,
      self::VERIFY_SUCCESS,
      self::VERIFY_CANCEL,
      self::VERIFY_REQUIRES_INPUT,
      self::VERIFY_IN_PROGRESS,
      self::INVITE_BY_PURCHASE,
    ];
  }
}