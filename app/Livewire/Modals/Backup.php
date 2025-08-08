<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;

class Backup extends Component
{
    use HasForm;

    public array $form = [
        'code' => null,
    ];

    public function getRules()
    {
        return [
            'form.code' => 'required|string|min:6|max:6|regex:/^[a-zA-Z0-9]+$/',
        ];
    }

    public function getMessages()
    {
        return [
            'form.code.required' => 'The backup code field is required.',
            'form.code.min' => 'The backup code must be exactly 6 characters long.',
            'form.code.max' => 'The backup code must be exactly 6 characters long.',
            'form.code.regex' => 'The backup code must be a 6-digit number.',
        ];
    }

    public function render()
    {
        return view('livewire.modals.backup');
    }
}
