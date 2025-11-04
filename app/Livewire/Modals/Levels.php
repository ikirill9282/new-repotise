<?php

namespace App\Livewire\Modals;

use App\Models\Level;
use Livewire\Component;

class Levels extends Component
{
    public $levels = [];

    public function mount()
    {
      $this->levels = Level::query()
        ->orderBy('id')
        ->limit(4)
        ->get();
    }

    public function render()
    {
      return view('livewire.modals.levels');
    }
}
