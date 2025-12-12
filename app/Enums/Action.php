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
  public const PROMOCODE_CREATED = 'Promocode Created';
  public const REFERAL_PROMOCODE_CREATED = 'Referal Promocode Created';
  public const GIFT_SEND = 'Gift sended.';
  public const GIFT_BUYER_REGISTERED = 'Gift Buyer Registered';
  public const GIFT_BUYER_GUEST = 'Gift Buyer Guest';
  public const GIFT_RECIPIENT = 'Gift Recipient';
  public const GIFT_CLAIMED = 'Gift Claimed';
  public const ACCOUNT_DELETION_CODE = 'Account Deletion Verification';
  public const DONATION_RECEIVED = 'Donation Received';
  public const DONATION_SENT = 'Donation Sent';

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
      self::PROMOCODE_CREATED,
      self::REFERAL_PROMOCODE_CREATED,
      self::GIFT_SEND,
      self::GIFT_BUYER_REGISTERED,
      self::GIFT_BUYER_GUEST,
      self::GIFT_RECIPIENT,
      self::GIFT_CLAIMED,
      self::ACCOUNT_DELETION_CODE,
      self::DONATION_RECEIVED,
      self::DONATION_SENT,
    ];
  }
}