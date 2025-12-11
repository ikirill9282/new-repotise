<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\HtmlString;
use App\Services\RecaptchaService;
use App\Services\IpRateLimitService;

class ResetPassword extends Component
{
    use HasForm;

    public array $form = [
      'email' => null,
      'recaptcha_token' => null,
    ];
    
    public bool $showRecaptchaV2 = false;
    
    protected function getRecaptchaService(): RecaptchaService
    {
        return app(RecaptchaService::class);
    }
    
    protected function getRateLimitService(): IpRateLimitService
    {
        return app(IpRateLimitService::class);
    }
    
    public function getRules()
    {
        return [
            'form.email' => 'required|email|exists:users,email',
        ];
    }

    public function getMessages()
    {
        return [
            'form.email.required' => 'Please enter your email address.',
            'form.email.email' => 'Please enter a valid email address.',
            'form.email.exists' => new HtmlString('Email address not found. Please check your email or <a href="#" onclick="Livewire.dispatch(\'openModal\', { modalName: \'auth\' }); return false;" class="text-active underline sign-in-res">Sign Up</a> instead?'),
        ];
    }

    public function submit()
    {
        $ipAddress = request()->ip();
        $rateLimitService = $this->getRateLimitService();
        
        // Check IP limit for password reset (3 per hour)
        if (!$rateLimitService->isAllowed($ipAddress, 'reset_password', 3, 60)) {
            $validator = Validator::make([], []);
            $validator->errors()->add('form.email', 'Too many password reset attempts from this IP address. Please try again in 1 hour.');
            throw new ValidationException($validator);
        }
        
        // Check if this is second attempt to show reCAPTCHA v2
        $remainingAttempts = $rateLimitService->getRemainingAttempts($ipAddress, 'reset_password', 3, 60);
        $this->showRecaptchaV2 = (3 - $remainingAttempts) >= 1;
        
        $messages = [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => new HtmlString('Email address not found. Please check your email or <a href="#" onclick="Livewire.dispatch(\'openModal\', { modalName: \'auth\' }); return false;" class="text-active underline sign-in-res">Sign Up</a> instead?'),
            'recaptcha_token.required' => 'Please complete the reCAPTCHA verification.',
        ];

        $validator = Validator::make(
            $this->form,
            [
                'email' => 'required|email|exists:users,email',
                'recaptcha_token' => $this->showRecaptchaV2 ? 'required|string' : 'sometimes|nullable|string',
            ],
            $messages
        );
        if ($validator->fails()) {
          throw new ValidationException($validator);
        }
        $valid = $validator->validated();
        
        // Verify reCAPTCHA v2 if shown
        if ($this->showRecaptchaV2) {
            $recaptchaService = $this->getRecaptchaService();
            $recaptchaResult = $recaptchaService->verifyV2($valid['recaptcha_token'] ?? null);
            if (!$recaptchaResult['success']) {
                $validator->errors()->add('form.recaptcha_token', 'reCAPTCHA verification failed. Please try again.');
                throw new ValidationException($validator);
            }
        }

        $user = User::firstWhere('email', $valid['email']);
        $user->sendResetCode();
        
        // Record successful attempt
        $rateLimitService->recordAttempt($ipAddress, 'reset_password', true, $user->id);
        
        $this->dispatch('openModal', 'reset-password-confirm', ['email' => $user->email]);
    }

    public function render()
    {
        return view('livewire.modals.reset-password');
    }
}
