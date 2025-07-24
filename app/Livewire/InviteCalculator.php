<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class InviteCalculator extends Component
{
    public $amount = 10000;

    public $platform_fee = 5;

    public array $numbers = [
      'avg' => 50,
      'sales' => 100,
    ];

    public function calcAmount()
    {
      $validator = Validator::make($this->numbers, [
        'avg' => 'required|integer',
        'sales' => 'required|integer',
      ]);

      if ($validator->fails()) {
        return false;
      }

      $valid = $validator->validated();
      $payouts = 12;
      $gross = $valid['avg'] * $valid['sales'] * $payouts;
      $platform_fee = $gross / 100 * $this->platform_fee;
      $payment_fee = ($gross / 100 * 2.9) + (($valid['sales'] * $payouts) * 0.30);
      $subtotal = $gross - $platform_fee - $payment_fee;
      $payout_fee = ($subtotal / 100 * 0.25) + ($payouts * 0.25);
      $this->amount = ceil($subtotal - $payout_fee);
    }

    public function render()
    {
      $this->calcAmount();
      return view('livewire.invite-calculator');
    }
}
