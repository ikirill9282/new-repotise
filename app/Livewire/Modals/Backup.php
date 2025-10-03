<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Backup extends Component
{
    use HasForm;

    public array $form = [
        'code' => null,
    ];

    public function attempt()
    {
      $validatior = Validator::make($this->form, [
        'code' => 'required|string|min:6|max:6|regex:/^[a-zA-Z0-9]+$/',
      ]);
    }

    public function render()
    {
        return view('livewire.modals.backup');
    }
}
