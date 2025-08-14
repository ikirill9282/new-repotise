<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Likes;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\UserFavorite;
use App\Models\Review;
use Illuminate\Support\Facades\Crypt;
use App\Models\Product;

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
        'model' => 'required|string',
        'text' => 'required|string|max:1000',
        'reply' => 'nullable|string',
        'edit' => 'nullable|string',
      ]);

      if (isset($valid['edit']) && $valid['edit']) {
        $comment_id = CustomEncrypt::getId($valid['edit']);
        $comment = Comment::find($comment_id);
        $comment->update(['text' => $valid['text'], 'edited' => 1]);
      } else {
        try {
          $valid['article_id'] = CustomEncrypt::getId($valid['model']);
          $valid['user_id'] = Auth::user()->id;
          $valid['text'] = clean($valid['text'], 'user_comment');
          
          if (isset($valid['reply']) && $valid['reply']) {
            $valid['parent_id'] = CustomEncrypt::getId($valid['reply']);
          }

          unset($valid['model'], $valid['reply'], $valid['edit']);
          
          $comment = Comment::create($valid);
        } catch (\Exception $e) {
          return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
      }

      return response()->json(['status' => 'success', 'comment' => $comment]);
    }

    public function review(Request $request)
    {
      $valid = $request->validate([
        'model' => 'required|string',
        'model' => function($attribute, $value, $parameters, $validator) {
          $model_id = CustomEncrypt::getId($value);
          $data = $validator->getData();
          $product = Product::find($model_id);
          
          if (!isset($data['reply']) && !isset($data['edit']) && !Auth::user()->canWriteReview($product)) {
            $text = 'You have already submitted a review for this product.';

            if (
              Auth::user()->hasRole(['admin', 'super-admin']) 
              && !Auth::user()->reviews()->whereNull('parent_id')->where('product_id', $model_id)->exists()
            ) {
              $text = 'Only customers who have purchased the product are eligible to write reviews.';
            }

            $validator->errors()->add('model', $text);
          }
        },
        'text' => 'required|string|max:1000',
        'reply' => 'nullable|string',
        'edit' => 'nullable|string',
        'rating' => 'required_without_all:reply,edit',
      ]);

      if (isset($valid['edit']) && $valid['edit']) {
        $review_id = CustomEncrypt::getId($valid['edit']);
        $review = Review::find($review_id);
        $review->update(['text' => $valid['text'], 'edited' => 1]);
      } else {
        try {
          $valid['product_id'] = CustomEncrypt::getId($valid['model']);
          $valid['user_id'] = Auth::user()->id;
          $valid['text'] = clean($valid['text'], 'user_comment');
          
          if (isset($valid['reply']) && $valid['reply']) {
            $valid['parent_id'] = CustomEncrypt::getId($valid['reply']);
          }

          unset($valid['model'], $valid['reply'], $valid['edit']);
          
          $review = Review::create($valid);
        } catch (\Exception $e) {
          return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
      }

      return response()->json(['status' => 'success', 'comment' => $review]);
    }

    public function follow(Request $request)
    {
      $valid = $request->validate(['resource' => 'required|string']);
      $id = Crypt::decrypt($valid['resource']);
      
      $condition = ['author_id' => $id, 'subscriber_id' => $request->user()->id];
      $exists = Follower::where($condition)->exists();

      if (Follower::where($condition)->exists()) {
        Follower::where($condition)->delete();
      } else {
        Follower::create($condition);
      }

      return response()->json(['sub' => intval(!$exists)]);
    }
}
