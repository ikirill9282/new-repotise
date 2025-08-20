<?php

namespace App\Livewire\Profile\Tables;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Referal extends Component
{
    public function render()
    {
        return view('livewire.profile.tables.referal', [
          'user' => Auth::user(),
        ]);
    }
}
