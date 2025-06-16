<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class PaymentIntents extends Model
{
    public function reloadStatus()
    {
      $data = Cashier::stripe()->paymentIntents->retrieve(
        $this->id,
        []
      );
      $this->status = $data->status;
    }
}
