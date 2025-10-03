<?php

namespace App\Livewire\Modals;

use Livewire\Attributes\On;
use App\Helpers\SessionExpire;
use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use App\Events\ResetFailed;
use App\Models\History;
use App\Enums\Action;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ResetPasswordConfirm extends Component
{
    use HasForm;

    public array $form = [
      'code' => null,
      'password' => null,
      'password_confirmation' => null,
    ];

    public $email;
    public $resend = null;

    public function getRules()
    {
        return [
          'form.code' => 'required|string|min:6|max:6|regex:/^[0-9]+$/|exists:user_verifies,code',
          'form.password' => 'required|min:8|regex:/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\-]/',
          'form.password_confirmation' => 'required|same:form.password',
        ];
    }

    public function getMessages()
    {
        return [
            'form.code.required' => 'The code field is required.',
            'form.code.exists' => 'The code does not exist or is expired.',
            'form.code.regex' => 'The code must be a 6-digit number.',
            'form.code.min' => 'The code must be exactly 6 characters long.',
            'form.code.max' => 'The code must be exactly 6 characters long.',
            'form.password.required' => 'The password field is required.',
            'form.password.min' => 'The password must be at least 8 characters long.',
            'form.password.regex' => 'The password must include a combination of letters, numbers, and symbols.',
            'form.password_confirmation.required' => 'The password confirmation field is required.',
            'form.password_confirmation.same' => 'Passwords do not match. Please re-enter.',
        ];
    }

    public function mount($args = [])
    {
      $this->email = $args['email'] ?? null;
      $this->fillFromSession();

      if (!$this->email) {
        $this->dispatch('openModal', 'reset-password');
        return;
      }
    }


    #[On('clearTimer')]
    public function onClearTimer()
    {
      $this->resend = null;
      SessionExpire::expire('reset_password_code');
    }

    public function fillFromSession()
    {
      if (SessionExpire::exists('reset_password_email')) {
        $this->email = SessionExpire::get('reset_password_email');
      } 

      if (SessionExpire::exists('reset_password_code')) {
        $this->resend = SessionExpire::getExpire('reset_password_code')?->timestamp * 1000 ?? null;
      }
    }

    public function resendCode()
    {
      $user = User::firstWhere('email', $this->email);
      $user->sendResetCode();
      $this->fillFromSession();
    }

    public function getResendExpire()
    {
      if (Carbon::fromTimestamp($this->resend)->addMinutes(3)->isFuture()) {
        return Carbon::fromTimestamp($this->resend)->addMinutes(3)->timestamp * 1000;
      }
      $this->resend = null;
      return null;
    }

    public function submit()
    {
      $validator = Validator::make($this->form, [
        'code' => 'required|string|min:6|max:6|regex:/^[0-9]+$/|exists:user_verifies,code',
        'password' => 'required|min:8|regex:/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\-]/',
        'password_confirmation' => 'required|same:password',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();

      if (!User::validatePassword($valid['password'])) {
        $validator->errors()->add('password', 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.');
        return ;
      }

      if ($valid['password'] !== $valid['password_confirmation']) {
        $validator->errors()->add('password_confirmation', 'Passwords do not match. Please re-enter.');
        return ;
      }

      $user = User::whereHas('verify', fn($query) => $query->where([
        'code' => $valid['code'],
        'type' => 'reset',
      ]))
        ->with('verify')
        ->first();


      if (!$user) {
        $validator->errors()->add('code', 'Code is expired');
        ResetFailed::dispatch(null, $valid['code'], 'code');
        return ;
      }

      if ($user->verify->where('type', 'reset')->first()?->code !== $valid['code']) {
        $validator->errors()->add('code', 'Invalid code');
        ResetFailed::dispatch($user, $valid['code'], 'invalid');
        return ;
      }

      $op = $user->password;
      $user->update(['password' => $valid['password']]);
      $user->refresh();
      $user->verify()->where('type', 'reset')->delete();

      History::success()
        ->action(Action::RESET_PASSWORD)
        ->userId($user->id)
        ->values($op, $user->password)
        ->message('User password changed')
        ->write()
        ;
      
      $this->dispatch('openModal', 'reset-password-success');
    }

    public function render()
    {
        return view('livewire.modals.reset-password-confirm');
    }
}
