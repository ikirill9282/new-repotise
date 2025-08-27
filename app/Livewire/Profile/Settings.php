<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('livewire.profile.settings', [
          'user' => Auth::user(),
        ]);
    }
}
