<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\User;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class Auth extends Component
{
    use HasForm;

    public array $form = [
        'email' => null,
        'password' => null,
        '2fa' => null,
        'backup' => null,
    ];

    public ?string $user_id = null;

    public int $step = 1;

    public function prepareEmail()
    {
      $validator = Validator::make(
        $this->form,
        [
          'email' => 'required|email',
        ],
        [
          'email.required' => 'Please enter your email address.',
          'email.email' => 'Please enter a valid email address.',
        ]
      );

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      $user = User::firstWhere('email', $valid['email']);

      if (!$user) {
        $this->resetValidation();
        $this->dispatch('openModal', 'register', ['email' => $valid['email']]);
        return;
      }

      $this->user_id = Crypt::encrypt($user->id);
      $this->step = 2;
    }

    public function attempt()
    {
      if ($this->step == 1) {
        return $this->prepareEmail();
      }

      $validator = Validator::make($this->form, [
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string',
        '2fa' => 'sometimes|nullable|string',
        'backup' => 'sometimes|nullable|boolean',
      ]);

      if ($validator->fails()) {
        dd($validator->errors());
        throw new ValidationException($validator);
      }

      // TODO: 2fa here
      $valid = $validator->validated();
      $user = $this->getUser();

      if (!$user || !$user->active) {
        $validator->errors()->add('email', 'Your account is temporarily locked. Please try again later or contact support.');
        throw new ValidationException($validator);
      }

      if ($user->twofa) {
        if (!isset($valid['2fa']) && !isset($valid['backup'])) {
          $validator->errors()->add('2fa', 'Please enter the two-factor authentication code to proceed.');
          throw new ValidationException($validator);
        }

        if (isset($valid['backup']) && $valid['backup']) {
          $this->dispatch('openModal', 'backup', ['user_id' => Crypt::encrypt($user->id)]);
          return ;
        }
      }

      if (AuthFacade::attempt(['email' => $valid['email'], 'password' => $valid['password']], true)) {
        Session::regenerate(true);
        $url = str_ireplace('&modal=auth', '', url()->previous());
        $url = str_ireplace('?modal=auth', '', $url);
        return redirect($url);
      }
      
      $validator->errors()->add('email', 'Invalid email or password. Please try again.');
      throw new ValidationException($validator);
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
    
    public function getUser(): ?User
    {
      return $this->user_id ? User::find(Crypt::decrypt($this->user_id)) : null;
    }

    public function render()
    {
      return view('livewire.modals.auth');
    }
}
