<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use App\Models\User;
use App\Models\UserMessages;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Contact extends Component
{

    public string $user_id;

    public function mount(string $user_id)
    {
      $this->user_id = $user_id;
    }

    public function getUser(): ?User
    {
      return User::where('id', Crypt::decrypt($this->user_id))->with('options')->first();
    }

    public function getContacts(): array
    {
      $user = $this->getUser();
      $result = [];
      
      if ($user->options->contact) {
        $result[] = $user->options->contact;
      }
      
      if ($user->options->contact2) {
        $result[] = $user->options->contact2;
      }

      return $result;
    }

    public function render()
    {
      return view('livewire.modals.contact', [
        'contacts' => $this->getContacts(),
        'user' => $this->getUser(),
      ]);
    }
}
