<?php

namespace App\Livewire\Modals;

use App\Helpers\SessionExpire;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use App\Services\RecaptchaService;
use App\Services\IpRateLimitService;

class Backup extends Component
{
    use HasForm;

    public array $form = [
      'code' => null,
      'recaptcha_token' => null,
    ];
    
    protected function getRecaptchaService(): RecaptchaService
    {
        return app(RecaptchaService::class);
    }
    
    protected function getRateLimitService(): IpRateLimitService
    {
        return app(IpRateLimitService::class);
    }

    public ?string $user_id;

    public bool $shouldRender = true;

    public function mount(?string $user_id = null)
    {
      $this->user_id = $user_id;

      if (!$this->user_id || !$this->getUser() || !$this->canAttempt()) {
        $this->shouldRender = false;
      }
    }

    public function submit()
    {
      $this->attempt();
    }

    public function attempt()
    {
      if (!$this->canAttempt()) {
        $this->dispatch('toastError', ['message' => 'The login attempts have been exhausted. Please try again in one hour.']);
        $this->dispatch('openModal', 'auth');
        return ;
      }

      $ipAddress = request()->ip();
      $rateLimitService = $this->getRateLimitService();
      
      // Check IP limit for 2FA reset (5 per hour)
      if (!$rateLimitService->isAllowed($ipAddress, 'reset_2fa', 5, 60)) {
        $validator = Validator::make([], []);
        $validator->errors()->add('code', 'Too many 2FA reset attempts from this IP address. Please try again in 1 hour.');
        throw new ValidationException($validator);
      }

      $available_attempts = SessionExpire::get('backup') ?? 0;
      $validator = Validator::make($this->form, [
        // Allow old 6-char codes for existing users, and new safer 8-15 char codes.
        'code' => 'required|string|regex:/^(?:[A-Za-z0-9]{6}|[A-Za-z0-9]{8,15})$/',
        'recaptcha_token' => 'required|string',
      ], [
        'code.regex' => 'Invalid backup code format.',
        'recaptcha_token.required' => 'Please complete the reCAPTCHA verification.',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      // Verify reCAPTCHA v2
      $recaptchaService = $this->getRecaptchaService();
      $recaptchaResult = $recaptchaService->verifyV2($this->form['recaptcha_token'] ?? null);
      if (!$recaptchaResult['success']) {
        $validator->errors()->add('form.recaptcha_token', 'reCAPTCHA verification failed. Please try again.');
        throw new ValidationException($validator);
      }

      $user = $this->getUser();
      $valid = $validator->validated();
      $backups = $user->backup->pluck('code')->values()->toArray();
      
      if (!in_array($valid['code'], $backups)) {
        $rateLimitService->recordAttempt($ipAddress, 'reset_2fa', false, $user->id);
        $validator->errors()->add('code', 'Invalid backup code.');
        SessionExpire::set('backup', ($available_attempts+1), Carbon::now()->modify('+1 hour'));

        throw new ValidationException($validator);
      }

      $user->update(['twofa' => 0]);
      $user->backup()->where('code', $valid['code'])->delete();
      
      if ($user->backup()->get()->isEmpty()) {
        $user->resetBackup();
      }
      
      // Record successful attempt
      $rateLimitService->recordAttempt($ipAddress, 'reset_2fa', true, $user->id);
      
      $this->dispatch('openModal', 'backup-accept');
    }

    public function getUser(): ?User
    {
      return User::find(Crypt::decrypt($this->user_id));
    }

    protected function canAttempt(): bool
    {
      return $this->getUser()->canBackup();
    }

    public function render()
    {
      return view('livewire.modals.backup');
    }
}
