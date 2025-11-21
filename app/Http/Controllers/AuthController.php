<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\History;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use App\Jobs\ReferalPromocode;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
      $valid = $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
        'remember' => 'sometimes|nullable|accepted',
      ]);

      $email = $valid['email'];
      $user = User::where('email', $email)->first();
      
      // Check if user is locked
      if ($user && $user->login_locked_until && $user->login_locked_until > now()) {
        LoginHistory::logFailed($email, 'Account is locked due to multiple failed login attempts', $request->ip(), $request->userAgent());
        
        return back()->withErrors([
          'email' => 'Your account has been temporarily locked due to multiple failed login attempts. Please try again later or contact support.',
        ])->onlyInput('email');
      }

      // Reset lock if time has passed
      if ($user && $user->login_locked_until && $user->login_locked_until <= now()) {
        $user->login_locked_until = null;
        $user->failed_login_attempts = 0;
        $user->last_failed_login_at = null;
        $user->save();
      }

      if (Auth::attempt(['email' => $valid['email'], 'password' => $valid['password']], $valid['remember'] ?? false)) {
        $request->session()->regenerate();
        
        // Log successful login
        LoginHistory::logSuccess(Auth::user(), $request->ip(), $request->userAgent());
        
        // Reset failed attempts on successful login
        if ($user) {
          $user->failed_login_attempts = 0;
          $user->last_failed_login_at = null;
          $user->login_locked_until = null;
          $user->save();
        }
        
        return redirect()->back();
      }

      // Log failed login
      $failureReason = 'Invalid credentials';
      LoginHistory::logFailed($email, $failureReason, $request->ip(), $request->userAgent());

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

      return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
      ])->onlyInput('email');
    }

    public function signout(Request $request)
    {
      Auth::logout();
      Session::forget('cart');
      Session::regenerate(true);
      
      return redirect('/');
    }


    public function verifyEmail(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'confirm' => 'required|string',
        'seller' => 'sometimes|boolean',
      ]);

      if ($validator->fails()) {;
  
        History::emailVerifyValidationError($validator);
        
        return redirect('/');
      }

      try {
        $valid = $validator->validated();
        $data = Crypt::decrypt($valid['confirm']);

        if (!isset($data['code']) || empty($data['code'])) {
          History::emailVerifyError('Code does not exist in cipher', $data['code']);
          throw new Exception('Code does not exist in cipher');
        }

        $user = User::whereHas('verify', fn($query) => $query->where('code', $data['code']))
          ->first();

        if (!$user) {
          History::emailVerifyError('Code is expired', $data['code']);
          throw new Exception('Code is expired');
        }

        if ($user->email_verified_at) {
          return redirect($user->makeProfileUrl());
        }

        $user->update(['email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')]);
        $user->verify()->delete();
        
        History::emailVerifySuccess($user, $data['code']);

        Auth::login($user);
        Session::regenerate();
        ReferalPromocode::dispatch($user);

        // If seller, redirect to profile (not verification page) - new logic
        // Seller can use all tools, but payout will be disabled until Full Name is set
        if (boolval($valid['seller'])) {
          // Assign creator role if not already assigned (for backwards compatibility)
          if (!$user->hasRole('creator')) {
            $creatorRole = \Spatie\Permission\Models\Role::findByName('creator');
            if ($creatorRole) {
              $user->assignRole($creatorRole);
            }
          }
        }
        
        // Redirect seller to profile, not verification page
        $url = boolval($valid['seller']) 
          ? $user->makeProfileUrl()
          : $user->makeProfileUrl();
        
        return redirect($url);

      } catch (\Exception $e) {
        History::emailVerifyException($e);
      }
      
      return redirect('/');
    }

    public function googleCallback(Request $request)
    {
      $google_user = Socialite::driver('google')->user();
      $user = User::firstWhere('email', $google_user->email);
      
      if (!$user) {
        $user = User::create(['email' => $google_user->email]);
        $user->sendVerificationCode();
        History::userCreated($user);
      }

      Auth::login($user);
      Session::regenerate();

      return redirect()->route('home');
    }

    public function googleCallbackDelete(Request $request)
    {
      return response()->json('OK');
    }

    public function xCallback(Request $request)
    {
      try {
        $x_user = Socialite::driver('x')->user();
        
        // X (Twitter) may not provide email, so we need to handle this case
        // If email is not available, use the X ID as a fallback identifier
        $email = $x_user->email ?? $x_user->id . '@x.placeholder';
        
        // Check if user exists by email or by X ID stored somewhere
        // For now, we'll search by email
        $user = User::firstWhere('email', $email);
        
        if (!$user) {
          // Create new user
          $user = User::create([
            'email' => $email,
            // If email is not real, mark it as unverified
            'email_verified_at' => $x_user->email ? Carbon::now() : null,
          ]);
          
          // Send verification code if email is real
          if ($x_user->email) {
            $user->sendVerificationCode();
          }
          
          History::userCreated($user);
        }
        
        Auth::login($user);
        Session::regenerate();
        ReferalPromocode::dispatch($user);

        return redirect()->route('home');
        
      } catch (\Exception $e) {
        // Log the error
        \Illuminate\Support\Facades\Log::error('X OAuth callback error', [
          'error' => $e->getMessage(),
          'trace' => $e->getTraceAsString(),
        ]);
        
        // Redirect with error message
        return redirect()->route('home')->with('error', 'Failed to authenticate with X. Please try again.');
      }
    }
}
