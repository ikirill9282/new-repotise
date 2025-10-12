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

  public function stripeData(): Attribute
  {
    return Attribute::make(
      get: fn(?string $val) => json_decode($val, true),
      set: fn(array $val) => json_encode($val),
    );
  }

  public function month(): float
  {
    $res = $this->month > 0
      ? $this->subtractPercent($this->getPrice(), $this->month)
      : $this->getPrice();

    return round($res, 2);
  }

  public function monthWithoutDiscount()
  {
    $res = $this->month > 0
      ? $this->subtractPercent($this->getPriceWithoutDiscount(), $this->month)
      : $this->getPrice();

    return round($res, 2);
  }

  public function quarter(): float
  {
    $res = $this->subprice?->quarter > 0
      ? $this->subtractPercent($this->getPrice(), $this->quarter)
      : $this->getPrice();

    return round($res, 2);
  }

  public function quarterWithoutDiscount()
  {
    $res = $this->subprice?->quarter > 0
      ? $this->subtractPercent($this->getPriceWithoutDiscount(), $this->quarter)
      : $this->getPrice();

    return round($res, 2);
  }

  public function year(): float
  {
    $res = $this->subprice?->year > 0
      ? $this->subtractPercent($this->getPrice(), $this->year)
      : $this->getPrice();

    return round($res, 2);
  }

  public function yearWithoutDiscount(): float
  {
    $res = $this->subprice?->year > 0
      ? $this->subtractPercent($this->getPriceWithoutDiscount(), $this->year)
      : $this->getPrice();

    return round($res, 2);
  }

  public function getMonthSum(): float
  {
    return $this->month();
  }

  public function getMonthSumWithoutDiscount(): float
  {
    return $this->monthWithoutDiscount();
  }

  public function getQuarterSum(): float
  {
    return round($this->quarter() * 3, 2);
  }

  public function getQuarterSumWithoutDiscount(): float
  {
    return round($this->quarterWithoutDiscount() * 3, 2);
  }

  public function getYearSum(): float
  {
    return round($this->year() * 12, 2);
  }

  public function getYearSumWithoutDiscount(): float
  {
    return round($this->yearWithoutDiscount() * 12, 2);
  }

  public function getMonthId(): string
  {
    return $this->stripe_data['month'];
  }

  public function getQuarterId(): string
  {
    return $this->stripe_data['quarter'];
  }

  public function getYearId(): string
  {
    return $this->stripe_data['year'];
  }

  public function getPeriodId(string $period): ?string
  {
    return match($period) {
      'month' => $this->getMonthId(),
      'quarter' => $this->getQuarterId(),
      'year' => $this->getYearId(),
      default => null,
    };
  }

  public function getPeriodPrice(string $period)
  {
    return match($period) {
      'month' => $this->getMonthSum(),
      'quarter' => $this->getQuarterSum(),
      'year' => $this->getYearSum(),
      default => null,
    };
  }

  public function getPeriodPriceWithoutDiscount(string $period)
  {
    return match($period) {
      'month' => $this->getMonthSumWithoutDiscount(),
      'quarter' => $this->getQuarterSumWithoutDiscount(),
      'year' => $this->getYearSumWithoutDiscount(),
      default => null,
    };
  }

  public function getPrice(): float
  {
    return $this->product->getPrice();
  }

  public function getPriceWithoutDiscount(): float
  {
    return $this->product->getPriceWithoutDiscount();
  }

  public function subtractPercent($number, $percent): float
  {
    return $number - ($number * ($percent / 100));
  }
}
