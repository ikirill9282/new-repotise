<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleLikes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ReviewLikes;
use App\Models\Comment;
use App\Models\Likes;
use App\Models\Review;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      foreach (Article::all() as $article) {
        foreach (User::all() as $user) {
          Likes::firstOrCreate([
            'model_id' => $article->id,
            'user_id' => $user->id,
            'type' => 'article',
          ]);
        }
      }

      $users = User::all();
      foreach (Comment::all() as $comment) {
        $range = rand(1, 4);
        $user_order = $users->shuffle()->values();
        for ($i = 1; $i <= $range; $i++) {
          $user = $user_order->shift();
          Likes::firstOrCreate(
            [
              'user_id' => $user->id,
              'type' => 'comment',
              'model_id' => $comment->id,
            ],
            [
              'user_id' => $user->id,
              'type' => 'comment',
              'model_id' => $comment->id,
            ],
          );
        }
      }

      $users = User::all();
      foreach (Review::all() as $review) {
        $range = rand(1, 4);
        $user_order = $users->shuffle()->values();
        
        for ($i = 1; $i <= $range; $i++) {
          $user = $user_order->shift();
          Likes::firstOrCreate(
            [
              'user_id' => $user->id,
              'type' => 'review',
              'model_id' => $review->id,
            ],
            [
              'user_id' => $user->id,
              'type' => 'review',
              'model_id' => $review->id,
            ],
          );
        }
      }
    }
}
