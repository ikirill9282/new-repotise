<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;

class Subscriptions extends Subscription
{
    public function payments()
    {
      return $this->morphMany(Payments::class, 'paymentable');
    }

    public function getForeignKey()
    {
      return 'subscription_id';
    }

    public function getForeignKeyName()
    {
      return $this->getForeignKey();
    }
}
