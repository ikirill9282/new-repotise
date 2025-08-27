<?php

namespace App\Livewire\Profile;

use Livewire\Attributes\On;
use Livewire\Component;

class Analytics extends Component
{
    public $activeTable = 'donation-analytics';

    #[On('tableChanged')]
    public function onTableChanged(string $name)
    {
      $this->activeTable = $name;
    }

    public function render()
    {
        return view('livewire.profile.analytics', ['table' => $this->activeTable]);
    }
}
