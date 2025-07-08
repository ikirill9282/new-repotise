<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class InviteCalculator extends Component
{
    public $amount = 10000;

    public array $numbers = [
      'avg' => null,
      'sales' => null,
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
      $this->amount = $valid['avg'] * $valid['sales'];
    }

    public function render()
    {
      $this->calcAmount();
      return view('livewire.invite-calculator');
    }
}
