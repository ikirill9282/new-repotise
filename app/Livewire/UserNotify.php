<?php

namespace App\Livewire;

use App\Models\UserNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserNotify extends Component
{

    public Collection $notifications;

    public function mount()
    {
      $this->resetNotifications();
    }

    public function resetNotifications()
    {
        $this->notifications = \App\Models\UserNotification::query()
          ->where([
            'user_id' => Auth::user()?->id,
            'show' => 1,
          ])
          ->get()
            ?: collect();
    }

    public function markAsRead(UserNotification $model)
    {
      $model->update(['show' => 0]);
      $this->resetNotifications();
    }

    public function render()
    {
        return view('livewire.user-notify');
    }
}
