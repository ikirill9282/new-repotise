<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\UserReferal;
use App\Helpers\CustomEncrypt;
use App\Models\History;

class Register extends Component
{
  use HasForm;

  public array $form = [
    'email' => null,
    'password' => null,
    'repeat_password' => null,
    'as_seller' => false,
  ];

  public function getRules()
  {
    return [
      'form.email' => 'required|email|max:255|unique:users,email',
      'form.password' => 'required|min:8|regex:/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\-]/',
      'form.repeat_password' => 'required|same:form.password',
      'form.as_seller' => 'boolean',
    ];
  }
  public function getMessages()
  {
    return [
      'form.email.required' => 'Email is required.',
      'form.email.email' => 'Please enter a valid email address.',
      'form.email.unique' => 'This email address is already registered. Sign In instead?',
      'form.password.required' => 'Password is required.',
      'form.password.min' => 'Password must be at least 8 characters long.',
      'form.password.regex' => 'The password must include a combination of letters, numbers, and symbols.',
      'form.repeat_password.required' => 'Please repeat your password.',
      'form.repeat_password.same' => 'Passwords do not match. Please re-enter.',
      'form.as_seller.boolean' => 'As seller must be true or false.',
    ];
  }
  

  public function submit()
  {
    $state = $this->getValidFormState();

    if (!User::validatePassword($state['password'])) {
      $this->addError('form.password', 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.');
      return ;
    }

    if ($state['password'] !== $state['repeat_password']) {
      $this->addError('form.repeat_password', 'Passwords do not match. Please re-enter.');
      return ;
    }

    $user = User::create([
      'email' => $state['email'],
      'password' => $state['password'],
    ]);
//     if ($this->password !== $this->repeat_password) {
//       $this->addErrorText('reg.password', 'Passwords do not match. Please re-enter.');
//       $this->addErrorText('reg.repeat_password', 'Passwords do not match. Please re-enter.');
//       return ;
//     }

//     $user = User::create([
//       'email' => $this->email,
//       'password' => $this->password,
//     ]);

    if (Session::exists('referal')) {
      DB::transaction(function() use ($user) {
        $id = CustomEncrypt::getId(Session::get('referal'));
        $owner = User::find($id);
        UserReferal::firstOrCreate(['owner_id' => $owner->id, 'referal_id' => $user->id]);
      });
      Session::forget('referal');
    }
    History::userCreated($user);
    $user->sendVerificationCode(seller: $state['as_seller']);
    $this->dispatch('openModal', 'register-success');
  }


  public function render()
  {
    return view('livewire.modals.register');
  }
}
