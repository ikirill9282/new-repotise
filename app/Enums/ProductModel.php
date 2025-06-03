<?php

namespace App\Enums;

class ProductModel
{
  public const PRODUCT  = 'product';
  public const SUBSCRIPTION  = 'subscription';

  public function toArray(): array
  {
    return [
      self::PRODUCT,
      self::SUBSCRIPTION,
    ];
  }
}