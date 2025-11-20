<?php

namespace App\Filament\Auth;

use App\Models\LoginHistory;
use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
  protected function throwFailureValidationException(): never
  {
    $email = $this->data['email'] ?? null;
    $user = $email ? User::where('email', $email)->first() : null;
    
    // Check if user is locked
    if ($user && $user->login_locked_until && $user->login_locked_until > now()) {
      LoginHistory::logFailed($email, 'Account is locked due to multiple failed login attempts', request()->ip(), request()->userAgent());
      
      throw ValidationException::withMessages([
        'data.email' => __('Your account has been temporarily locked due to multiple failed login attempts. Please try again later or contact support.'),
      ]);
    }

    // Reset lock if time has passed
    if ($user && $user->login_locked_until && $user->login_locked_until <= now()) {
      $user->login_locked_until = null;
      $user->failed_login_attempts = 0;
      $user->last_failed_login_at = null;
      $user->save();
    }

    // Log failed login
    LoginHistory::logFailed($email, 'Invalid credentials', request()->ip(), request()->userAgent());

    // Increment failed attempts
    if ($user) {
      $now = now();
      $lastFailed = $user->last_failed_login_at;
      $fifteenMinutesAgo = $now->copy()->subMinutes(15);
      
      // Reset counter if last failed attempt was more than 15 minutes ago
      if (!$lastFailed || $lastFailed->lt($fifteenMinutesAgo)) {
        $user->failed_login_attempts = 1;
      } else {
        $user->failed_login_attempts++;
      }
      
      $user->last_failed_login_at = $now;
      
      // Lock account if 5 or more failed attempts within 15 minutes
      if ($user->failed_login_attempts >= 5) {
        $user->login_locked_until = $now->copy()->addMinutes(15);
      }
      
      $user->save();
    }

    throw ValidationException::withMessages([
      'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
    ]);
  }

  protected function afterLogin(): void
  {
    $user = auth()->user();
    
    // Log successful login
    LoginHistory::logSuccess($user, request()->ip(), request()->userAgent());
    
    // Reset failed attempts on successful login
    if ($user) {
      $user->failed_login_attempts = 0;
      $user->last_failed_login_at = null;
      $user->login_locked_until = null;
      $user->save();
    }
  }
}
