<?php

namespace App\Livewire\Modals;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditContacts extends Component
{

    public array $form = [
      'contact' => null,
      'contact2' => null,
    ];

    public function mount()
    {
      $this->form['contact'] = Auth::user()->options->contact;
      $this->form['contact2'] = Auth::user()->options->contact2;
    }

    public function submit()
    {
      $validator = Validator::make($this->form, [
        'contact' => 'sometimes|nullable|string',
        'contact2' => 'sometimes|nullable|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      
      Auth::user()->options()->update($valid);
      
      $this->dispatch('toastSuccess', ['message' => 'Contact information updated successful!']);
      $this->dispatch('closeModal');
    }
    
    public function render()
    {
        return view('livewire.modals.edit-contacts');
    }
}
