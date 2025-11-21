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
use Illuminate\Support\Carbon;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

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

      $validator = Validator::make(
        $this->form,
        [
          'email' => 'required|email|exists:users,email',
          'password' => 'required|string',
          '2fa' => 'sometimes|nullable|string',
          'backup' => 'sometimes|nullable|boolean',
        ],
        [
          'email.required' => 'Please enter your email address.',
          'email.email' => 'Please enter a valid email address.',
          'email.exists' => 'Account with this email was not found.',
          'password.required' => 'Please enter your password.',
        ]
      );

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      $user = $this->getUser()?->fresh();

      if ($user && !$user->active && $this->canRestoreFromDeletion($user)) {
        $user->forceFill([
          'active' => 1,
          'deletion_requested_at' => null,
          'deletion_scheduled_for' => null,
        ])->save();

        $user->refresh();
      }

      if (!$user || !$user->active) {
        $validator->errors()->add('email', 'Your account is temporarily locked. Please try again later or contact support.');
        throw new ValidationException($validator);
      }

      if ($user->twofa) {
        $this->verifyTwofa($user, $validator, $valid);
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
      return redirect()->away(
        Socialite::driver('x')
          ->scopes(['tweet.read', 'users.read', 'offline.access']) // Request necessary scopes
          ->redirect()
          ->getTargetUrl()
      );
    }
    
    public function getUser(): ?User
    {
      return $this->user_id ? User::find(Crypt::decrypt($this->user_id)) : null;
    }

  protected function canRestoreFromDeletion(User $user): bool
  {
    if (!$user->deletion_scheduled_for) {
      return false;
    }

    return Carbon::parse($user->deletion_scheduled_for)->isFuture();
  }

    protected function verifyTwofa(User $user, $validator, array $valid): void
    {
      $code = isset($valid['2fa']) ? trim((string) $valid['2fa']) : '';
      $useBackup = (bool) ($valid['backup'] ?? false);

      if ($useBackup) {
        if ($code === '') {
          $validator->errors()->add('2fa', 'Please enter your backup code.');
          throw new ValidationException($validator);
        }

        $backup = $user->backup()->where('code', $code)->first();

        if (!$backup) {
          $validator->errors()->add('2fa', 'Invalid backup code.');
          throw new ValidationException($validator);
        }

        $backup->delete();

        return;
      }

      if ($code === '') {
        $validator->errors()->add('2fa', 'Enter the code from your authenticator app.');
        throw new ValidationException($validator);
      }

      if (empty($user->google2fa_secret)) {
        $validator->errors()->add('2fa', 'Two-factor authentication is not configured. Please contact support.');
        throw new ValidationException($validator);
      }

      try {
        $secret = Crypt::decryptString($user->google2fa_secret);
      } catch (\Throwable $e) {
        $validator->errors()->add('2fa', 'Unable to verify the authentication code. Please try again later.');
        throw new ValidationException($validator);
      }

      if (!Google2FA::verifyKey($secret, preg_replace('/\s+/', '', $code), 4)) {
        $validator->errors()->add('2fa', 'Invalid authenticator app code.');
        throw new ValidationException($validator);
      }
    }

    public function render()
    {
      return view('livewire.modals.auth');
    }
}
