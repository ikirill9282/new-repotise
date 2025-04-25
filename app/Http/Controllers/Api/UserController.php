<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserFavorite;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function favorite(Request $request)
    {
      $valid = $request->validate([
        'hash' => 'required|string',
      ]);

      $data = CustomEncrypt::decrypt($valid['hash']);
      $attributes = array_merge($data, ['user_id' => Auth::user()->id]);
      $model = UserFavorite::where($attributes)->first();

      if ($model) {
        $model->delete();
        $value = false;
      } else {
        $model = UserFavorite::create($attributes);
        $value = true;
      }

      return response()->json([
        'message' => 'Success!', 
        'status' => true, 
        'value' => $value,
        'count' => Auth::user()->favorite_count
      ]);
    }
}
