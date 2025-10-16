<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class Payments extends Model
{
    public function paymentable()
    {
      return $this->morphTo();
    }

    public function asStripePaymentIntent(bool $charges = false)
    {
      $params = [];
      if ($charges) {
        $params['expand'] = ['charges.data.balance_transaction'];
      }
      
      return Cashier::stripe()->paymentIntents->retrieve($this->stripe_id, $params);
    }
}
