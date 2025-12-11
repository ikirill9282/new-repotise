<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use App\Jobs\ReferalPromocode;
use App\Services\RecaptchaService;
use App\Services\IpRateLimitService;

class Register extends Component
{
  use HasForm;

  public array $form = [
    'email' => null,
    'password' => null,
    'repeat_password' => null,
    'as_seller' => false,
    'recaptcha_token' => null,
  ];
  
  public ?string $recaptcha_token = null;
  public bool $showRecaptchaV2 = false;
  
  protected function getRecaptchaService(): RecaptchaService
  {
    return app(RecaptchaService::class);
  }
  
  protected function getRateLimitService(): IpRateLimitService
  {
    return app(IpRateLimitService::class);
  }

  public function mount(?string $email = null)
  {
    if ($email) {
      $this->form['email'] = $email;
    }
  }

  public function attempt()
  {
    Log::info('Registration attempt started', [
      'email' => $this->form['email'] ?? null,
      'as_seller' => $this->form['as_seller'] ?? false
    ]);
    
    $ipAddress = request()->ip();
    $rateLimitService = $this->getRateLimitService();
    
    // Check IP limit for registration (5 per 24 hours)
    if (!$rateLimitService->isAllowed($ipAddress, 'register', 5, 60 * 24)) {
      $validator = Validator::make([], []);
      $validator->errors()->add('email', 'Too many registration attempts from this IP address. Please try again later.');
      throw new ValidationException($validator);
    }
    
    try {
      $validator = Validator::make($this->form, [
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:8|regex:/[a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\-]/',
        'repeat_password' => 'required|same:password',
        'as_seller' => 'boolean',
        'recaptcha_token' => $this->showRecaptchaV2 ? 'required|string' : 'sometimes|nullable|string',
      ], [
        'recaptcha_token.required' => 'Please complete the reCAPTCHA verification.',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->valid();
      
      // Verify reCAPTCHA
      $recaptchaService = $this->getRecaptchaService();
      if ($this->showRecaptchaV2) {
        $recaptchaResult = $recaptchaService->verifyV2($valid['recaptcha_token'] ?? null);
        if (!$recaptchaResult['success']) {
          $validator->errors()->add('form.recaptcha_token', 'reCAPTCHA verification failed. Please try again.');
          throw new ValidationException($validator);
        }
      } else {
        // Verify reCAPTCHA v3
        $recaptchaResult = $recaptchaService->verifyV3($this->recaptcha_token, 'register');
        if (!$recaptchaResult['success']) {
          $validator->errors()->add('recaptcha_token', 'reCAPTCHA verification failed. Please try again.');
          throw new ValidationException($validator);
        }
        
        // Show v2 if score is too low
        if ($recaptchaResult['low_score'] ?? false) {
          $this->showRecaptchaV2 = true;
          $validator->errors()->add('recaptcha_token', 'Please complete the additional verification.');
          throw new ValidationException($validator);
        }
      }

      // Validate password strength
      if (!User::validatePassword($valid['password'])) {
        $strength = User::getPasswordStrength($valid['password']);
        $message = $strength === 'weak' 
          ? 'The password is too weak. Please use a medium or strong password with at least 8 characters, including uppercase and lowercase letters, numbers, and symbols.'
          : 'The password does not meet the security requirements. Please use a medium or strong password.';
        $validator->errors()->add('password', $message);
        throw new ValidationException($validator);
      }

      if ($valid['password'] !== $valid['repeat_password']) {
        $validator->errors()->add('repeat_password', 'Passwords do not match. Please re-enter.');
        throw new ValidationException($validator);
      }
      
      // Create user
      $user = User::create([
        'email' => $valid['email'],
        'password' => $valid['password'],
      ]);

      // If registering as seller: assign creator role
      if ($valid['as_seller']) {
        try {
          $creatorRole = Role::findByName('creator');
          if ($creatorRole) {
            $user->assignRole($creatorRole);
          }
          // For seller: name will remain as username (set in User::creating)
          // Method getName() will use full_name from options if set, otherwise username
        } catch (\Exception $e) {
          // Log error but don't block registration
          Log::warning('Failed to assign creator role during registration', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
          ]);
        }
      }

      History::userCreated($user);
      
      // Record successful registration attempt
      $rateLimitService->recordAttempt($ipAddress, 'register', true, $user->id);

      $user->sendVerificationCode(seller: $valid['as_seller']);
      
      // Авторизовать пользователя автоматически
      Auth::login($user);
      Session::regenerate();
      ReferalPromocode::dispatch($user);
      
      // Редирект на страницу dashboard
      return redirect('/profile/dashboard');
      
    } catch (\Exception $e) {
      Log::error('Registration failed', [
        'email' => $this->form['email'] ?? null,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      
      // Re-throw ValidationException so Livewire can display errors
      if ($e instanceof ValidationException) {
        throw $e;
      }
      
      // For other exceptions, show a generic error
      session()->flash('error', 'Registration failed. Please try again.');
      throw $e;
    }
  }

  public function googleAuth()
  {
    return redirect()->away(
      Socialite::driver('google')
        ->with(['prompt' => 'select_account'])
        ->redirect()
        ->getTargetUrl()
    );
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

  public function render()
  {
    return view('livewire.modals.register');
  }
}
