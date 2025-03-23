<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Models\Likes;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
  protected Collection $articles;
  protected Collection $users;

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $data = [
      [
        'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
        'user_id' => 1,
        'children' => [
          [
            'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
            'user_id' => 2,
          ]
        ]
      ],
      [
        'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
        'user_id' => 3,
        'children' => [
          [
            'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
            'user_id' => 4,
            'children' => [
              [
                'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
                'user_id' => 2,
              ],
              [
                'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
                'user_id' => 3,
                'children' => [
                  [
                    'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
                    'user_id' => 1,
                  ],
                  [
                    'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
                    'user_id' => 4,
                  ]
                ]
              ],
            ],
          ]
        ]
      ],
      [
        'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
        'user_id' => 2,
      ],
      [
        'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!',
        'user_id' => 3,
      ],
    ];

    DB::beginTransaction();
    try {
      $articles = Article::select(['id'])->get()->pluck('id');
      foreach ($articles as $article_id) {
        $this->process($data, $article_id);
      }
    } catch (\Exception|\Error $e) {
      DB::rollBack();
      throw $e;
    }
    DB::commit();
  }

  protected function process(array $data, int $article_id, ?int $parent_id = null)
  {
    foreach ($data as $item) {
      $comment = Comment::create([
        'article_id' => $article_id,
        'user_id' => $item['user_id'],
        'parent_id' => $parent_id,
        'text' => $item['text'],
      ]);
      if (
        isset($item['children']) &&
        is_array($item['children']) &&
        !empty($item['children'])
      )
      {
        $this->process($item['children'], $article_id, $comment->id);
      }
    }
  }
}
