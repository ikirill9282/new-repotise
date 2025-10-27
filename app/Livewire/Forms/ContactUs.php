<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;

class ContactUs extends Component
{
    use WithFileUploads;

    public array $fields = [
      'name' => null,
      'email' => null,
      'subject' => null,
      'text' => null,
      'file' => null,
    ];


    public function submit(Request $request)
    {
      $validator = Validator::make($this->fields, [
        'name' => 'required|string',
        'email' => 'required|email',
        'subject' => 'required|string',
        'text' => 'required|string',
        'file' => 'sometimes|nullable|file|max:25600',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      if (!empty($valid['file'])) {
        $valid['file'] = $valid['file']->store('forms');
      }

      Form::create([
        'source' => 'ContactUs',
        'user_id' => Auth::check() ? Auth::user()->id : 0,
        'data' => json_encode($valid),
      ]);

      $this->dispatch('toastSuccess', [
        'message' => 'The form has been successfully submitted and will be forwarded to the administration for review. Thank you for your cooperation!',
      ]);
      
      $this->fields = [
        'name' => null,
        'email' => null,
        'subject' => null,
        'text' => null,
        'file' => null,
      ];

      $this->dispatch('resetForm');
    }

    public function render()
    {
        return view('livewire.forms.contact-us');
    }
}
