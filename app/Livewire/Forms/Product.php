<?php

namespace App\Livewire\Forms;

use Livewire\Component;

class Product extends Component
{

    public int $step = 1;

    public function nextStep()
    {
      $this->step = 2;
      $this->dispatch('stepChanged');
    }

    public function prevStep()
    {
      $this->step = 1;
      $this->dispatch('stepChanged');
    }

    public function render()
    {
      return view('livewire.forms.product');
    }
}
