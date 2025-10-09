<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class Register extends Component
{
  use HasForm;

  public array $form = [
    'email' => null,
    'password' => null,
    'repeat_password' => null,
    'as_seller' => false,
  ];

  public function attempt()
  {
    $validator = Validator::make($this->form, [
      'email' => 'required|email|max:255|unique:users,email',
      'password' => 'required|min:8|regex:/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\-]/',
      'repeat_password' => 'required|same:password',
      'as_seller' => 'boolean',
    ]);

    if ($validator->fails()) {
      throw new ValidationException($validator);
    }

    $valid = $validator->valid();

    if (!User::validatePassword($valid['password'])) {
      $validator->errors()->add('password', 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.');
      return ;
    }

    if ($valid['password'] !== $valid['repeat_password']) {
      $validator->errors()->add('repeat_password', 'Passwords do not match. Please re-enter.');
      return ;
    }
    $user = User::create([
      'email' => $valid['email'],
      'password' => $valid['password'],
    ]);

    History::userCreated($user);

    $user->sendVerificationCode(seller: $valid['as_seller']);
    $this->dispatch('openModal', 'register-success');
  }

  public function googleAuth()
  {
    return redirect()->away(Socialite::driver('google')->redirect()->getTargetUrl());
  }

  public function fbAuth()
  {
    return redirect()->away(Socialite::driver('facebook')->redirect()->getTargetUrl());
  }

  public function xAuth()
  {
    return redirect()->away(Socialite::driver('x')->redirect()->getTargetUrl());
  }

  public function render()
  {
    return view('livewire.modals.register');
  }
}
