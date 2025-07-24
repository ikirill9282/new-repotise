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
        if (is_null($model->price_without_discount)) {
          $model->price_without_discount = $model->price;
        }
      });
    }

    public function product()
    {
      return $this->belongsTo(Product::class);
    }

    public function order_product()
    {
      return $this->belongsTo(OrderProducts::class);
    }

    public function getTotal(): int
    {
      return round($this->price * $this->count);
    }
}
