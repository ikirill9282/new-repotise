<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPrice
{
  public function price(): Attribute
  {
    return Attribute::make(
      get: fn($value) => number_format($value, 0, '.', ' '),
    );
  }

  public function oldPrice(): Attribute
  {
    return Attribute::make(
      get: fn($value) => number_format($value, 0, '.', ' '),
    );
  }
}