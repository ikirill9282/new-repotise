<?php

namespace App\Livewire\Profile\Tables;

use Livewire\Component;

class ProfileProduct extends Component
{

  public $all_checked = false;

  public function render()
  {
    return view('livewire.profile.tables.profile-product');
  }
}
