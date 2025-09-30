<?php

namespace App\Livewire\Profile;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class Page extends Component
{
  public string $user_id;
  public string $container;

  public function mount(string $user_id, string $container = '')
  {
    $this->user_id = $user_id;
    $this->container = $container;
  }

  #[On('resetPage')]
  public function render()
  {
    return view('livewire.profile.page');
  }
}
