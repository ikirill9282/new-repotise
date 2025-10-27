<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;

class RevenueShare extends Model
{
  
  public function user()
  {
    return $this->belongsTo(User::class);
  }
  
  public function product()
  {
    return $this->belongsTo(Product::class);
  }
  
  public function author()
  {
    return $this->belongsTo(User::class, 'author_id');
  }
  
  public function order()
  {
    return $this->belongsTo(Order::class);
  }
  
  public function subscription() {
    return $this->belongsTo(Subscription::class);
  }
}
