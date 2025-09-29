<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Invest extends Component
{

    public array $fields = [
      'name' => null,
      'topic' => null,
      'text' => null,
    ];
    
    public function submit()
    {
      $validator = Validator::make($this->fields, [
        'name' => 'required|string',
        'topic' => 'required|string',
        'text' => 'required|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      Form::create([
        'source' => 'Investment',
        'user_id' => Auth::check() ? Auth::user()->id : 0,
        'data' => json_encode($valid),
      ]);

      $this->dispatch('toastSuccess', [
        'message' => 'The form has been successfully submitted and will be forwarded to the administration for review. Thank you for your cooperation!'
      ]);
      $this->fields = [
        'name' => null,
        'topic' => null,
        'text' => null,
      ];
      $this->dispatch('resetForm');
    }

    public function render()
    {
        return view('livewire.forms.invest');
    }
}
