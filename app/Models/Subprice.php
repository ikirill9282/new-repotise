<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Subprice extends Model
{
    public function product()
    {
      return $this->belongsTo(Product::class);
    }

    public function getMonthId()
    {
      return $this->stripe_data['month'];
    }

    public function getQuarterId()
    {
      return $this->stripe_data['quarter'];
    }

    public function getYearId()
    {
      return $this->stripe_data['year'];
    }

    public function stripeData(): Attribute
    {
      return Attribute::make(
        get: fn(?string $val) => json_decode($val, true),
        set: fn(array $val) => json_encode($val),
      );
    }
}
