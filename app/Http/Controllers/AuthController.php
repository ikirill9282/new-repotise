<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
      $request->session()->regenerate();
      
      return redirect('/');
    }
}
