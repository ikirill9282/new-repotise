<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 


class Auth extends Component
{

    #[On('openAuthModal')]
    public function test()
    {

    }

    public function render()
    {
        return view('livewire.auth');
    }
}
