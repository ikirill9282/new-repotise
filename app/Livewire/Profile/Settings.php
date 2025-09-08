<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{

    public array $form = [
      'payment_method' => 'payment_method_1',
      'payout_method' => 'payout_method_1',
      'return_policy' => null,
    ];

    public function render()
    {
        return view('livewire.profile.settings', [
          'user' => Auth::user(),
        ]);
    }
}
