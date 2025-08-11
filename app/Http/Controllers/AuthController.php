<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use App\Jobs\ReferalPromocode;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
      $valid = $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
        'remember' => 'sometimes|nullable|accepted',
      ]);

      if (Auth::attempt(['email' => $valid['email'], 'password' => $valid['password']], $valid['remember'] ?? false)) {
        $request->session()->regenerate();
        return redirect()->back();
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

        $url = boolval($valid['seller']) 
          ? $user->makeProfileVerificationUrl()
          : $user->makeProfileUrl();
        
        return redirect($url);

      } catch (\Exception $e) {
        History::emailVerifyException($e);
      }
      
      return redirect('/');
    }

    
}
