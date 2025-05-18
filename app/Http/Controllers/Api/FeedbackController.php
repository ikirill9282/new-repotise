<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Likes;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\UserFavorite;

class FeedbackController extends Controller
{
    public function views(Request $request)
    {
      dd($request->all());
    }

    public function favorite(Request $request)
    {
      $valid = $request->validate([
        'hash' => 'required|string',
      ]);

      $data = CustomEncrypt::decodeUrlHash($valid['hash']);
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
        'count' => Auth::user()->favorite_count,
        'type' => $data['type'],
        'model_id' => $model->item_id,
      ]);
    }

    public function likes(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
      ]);

      $result = null;
      $data = CustomEncrypt::decodeUrlHash($valid['item']);
      
      if (Likes::where($data)->exists())  {
        Likes::where($data)->delete();
        $result = false;
      } else {
        Likes::create($data);
        $result = true;
      }
      
      return response()->json(['status' => $result]);
    }

    public function comment(Request $request)
    {
      $valid = $request->validate([
        'article' => 'required|string',
        'text' => 'required|string',
        'reply' => 'nullable|string',
      ]);

      try {
        $valid['article_id'] = CustomEncrypt::getId($valid['article']);
        $valid['user_id'] = Auth::user()->id;
        $valid['text'] = clean($valid['text'], 'user_comment');
        
        if (isset($valid['reply']) && $valid['reply']) {
          $valid['parent_id'] = CustomEncrypt::getId($valid['reply']);
        }

        unset($valid['article'], $valid['reply']);
        
        $comment = Comment::create($valid);
      } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }

      return response()->json(['status' => 'success', 'comment' => $comment]);
    }
}
