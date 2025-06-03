<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CabinetController extends Controller
{
    public function verify(Request $request)
    {
      return view('site.pages.profileVerify', [
        'user' => Auth::user(),
      ]);
    }

    public function profile(Request $request, ?string $slug = null)
    {
      $user = is_null($slug) ? Auth::user() : (User::where('username', str_ireplace('@', '', $slug))->first() ?? Auth::user());
      return view('site.pages.profile', [
        'user' => $user,
      ]);
    }
}
