<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class PaymentController extends Controller
{
  public function intent(Request $request)
  {
    // return Cashier::stripe()->paymentIntents->create([
    //   'amount' => 2000,
    //   'currency' => 'usd',
    //   'automatic_payment_methods' => ['enabled' => true],
    // ]);
    return Cashier::stripe()->paymentIntents->retrieve('pi_3RYPkiFkz2A7XNTi0nepUAQ5');
  }
}
