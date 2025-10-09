<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected static function boot()
    {
      parent::boot();

      self::creating(function (Model $model) {
        if (is_null($model->total_without_discount)) {
          $model->total_without_discount = $model->price;
        }
      });
    }

    public function order()
    {
      return $this->belongsTo(Order::class);
    }

    public function product()
    {
      return $this->belongsTo(Product::class);
    }

    public function order_product()
    {
      return $this->belongsTo(OrderProducts::class);
    }

    public function getPrice()
    {
      return $this->price - $this->sale_price;
    }

    public function getPriceWithoutDiscount()
    {
      if ($this->getPrice() == $this->price) return null;

      return $this->price;
    }
    public function getTotal(): int
    {
      return round($this->getPrice() * $this->count ?? 1, 2);
    }
}
