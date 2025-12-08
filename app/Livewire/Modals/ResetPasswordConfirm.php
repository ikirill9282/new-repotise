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
use Illuminate\Support\Facades\Log;
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
            'form.code.required' => 'Please enter the verification code.',
            'form.code.exists' => "That code doesn't look right. Please double-check the code from your email and try again.",
            'form.code.regex' => "That code doesn't look right. Please double-check the code from your email and try again.",
            'form.code.min' => "That code doesn't look right. Please double-check the code from your email and try again.",
            'form.code.max' => "That code doesn't look right. Please double-check the code from your email and try again.",
            'form.password.required' => 'The password field is required.',
            'form.password.min' => 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.',
            'form.password.regex' => 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.',
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

    #[On('reset-password-submit')]
    public function handleSubmitEvent($payload = [])
    {
      // Only handle if this event is for this component
      if (isset($payload['componentId']) && $payload['componentId'] !== $this->getId()) {
        return;
      }
      $this->submit();
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
      \Log::info('ResetPasswordConfirm::submit called', [
        'component_id' => $this->getId(),
        'form_data' => $this->form,
        'email' => $this->email,
      ]);

      $messages = [
        'code.required' => 'Please enter the verification code.',
        'code.exists' => "That code doesn't look right. Please double-check the code from your email and try again.",
        'code.regex' => "That code doesn't look right. Please double-check the code from your email and try again.",
        'code.min' => "That code doesn't look right. Please double-check the code from your email and try again.",
        'code.max' => "That code doesn't look right. Please double-check the code from your email and try again.",
        'password.required' => 'The password field is required.',
        'password.min' => 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.',
        'password.regex' => 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.',
        'password_confirmation.required' => 'The password confirmation field is required.',
        'password_confirmation.same' => 'Passwords do not match. Please re-enter.',
      ];

      $validator = Validator::make($this->form, [
        'code' => 'required|string|min:6|max:6|regex:/^[0-9]+$/|exists:user_verifies,code',
        'password' => 'required|min:8|regex:/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\-]/',
        'password_confirmation' => 'required|same:password',
      ], $messages);

      \Log::info('ResetPasswordConfirm::submit validation', [
        'fails' => $validator->fails(),
        'errors' => $validator->errors()->toArray(),
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();

      if (!User::validatePassword($valid['password'])) {
        $strength = User::getPasswordStrength($valid['password']);
        $message = 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.';
        $validator->errors()->add('password', $message);
        throw new ValidationException($validator);
      }

      if ($valid['password'] !== $valid['password_confirmation']) {
        $validator->errors()->add('password_confirmation', 'Passwords do not match. Please re-enter.');
        throw new ValidationException($validator);
      }

      $user = User::whereHas('verify', fn($query) => $query->where([
        'code' => $valid['code'],
        'type' => 'reset',
      ]))
        ->with('verify')
        ->first();


      if (!$user) {
        $validator->errors()->add('code', "That code doesn't look right. Please double-check the code from your email and try again.");
        ResetFailed::dispatch(null, $valid['code'], 'code');
        throw new ValidationException($validator);
      }

      if ($user->verify->where('type', 'reset')->first()?->code !== $valid['code']) {
        $validator->errors()->add('code', "That code doesn't look right. Please double-check the code from your email and try again.");
        ResetFailed::dispatch($user, $valid['code'], 'invalid');
        throw new ValidationException($validator);
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
      
      \Log::info('ResetPasswordConfirm::submit success', [
        'user_id' => $user->id,
        'email' => $user->email,
      ]);

      $this->dispatch('openModal', 'reset-password-success');
    }

    public function render()
    {
        return view('livewire.modals.reset-password-confirm');
    }
}
