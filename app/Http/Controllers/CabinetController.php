<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller
{
    public function verify(Request $request)
    {
      return view('site.pages.profileVerify', [
        'user' => Auth::user(),
      ]);
    }

    public function profile(Request $request)
    {
      return view('site.pages.profile', [
        'user' => Auth::user(),
      ]);
    }
}
