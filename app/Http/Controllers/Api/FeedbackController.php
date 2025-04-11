<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Likes;

class FeedbackController extends Controller
{
    public function views(Request $request)
    {
      dd($request->all());
    }

    public function likes(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
      ]);

      $result = null;
      $data = CustomEncrypt::decrypt($valid['item']);
      
      if (Likes::where($data)->exists())  {
        Likes::where($data)->delete();
        $result = false;
      } else {
        Likes::create($data);
        $result = true;
      }
      
      return response()->json(['status' => $result]);
    }
}
