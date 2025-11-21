<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class Register extends Component
{
  use HasForm;

  public array $form = [
    'email' => null,
    'password' => null,
    'repeat_password' => null,
    'as_seller' => false,
  ];

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
    
    try {
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

      // Validate password strength
      if (!User::validatePassword($valid['password'])) {
        $validator->errors()->add('password', 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.');
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

      $user->sendVerificationCode(seller: $valid['as_seller']);
      
      // Reset form
      $this->resetForm();
      
      // Dispatch event to open success modal
      $this->dispatch('openModal', 'register-success');
      
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

  public function render()
  {
    return view('livewire.modals.register');
  }
}
