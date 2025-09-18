<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Page extends Component
{

  public User $user;
  public string $container;

  public function mount(User $user, string $container = '')
  {
    $this->user = $user;
    $this->container = $container;
  }

  public function render()
  {
    return view('livewire.profile.page', [
      'user' => $this->user,
    ]);
  }
}
