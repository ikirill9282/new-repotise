<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleTags;
use App\Models\NewsTags;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\News;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $tags = [];
      for ($i = 1; $i < 6; $i++) {
        $tags[] = Tag::firstOrCreate([
          'title' => "Tag $i",
        ]);
      }

      foreach (Article::all() as $article) {
        foreach ($tags as $tag) {
          ArticleTags::firstOrCreate([
            'article_id' => $article->id,
            'tag_id' => $tag->id,
          ]);
        }
      }

      // foreach (News::all() as $news_item) {
      //   foreach ($tags as $tag) {
      //     NewsTags::firstOrCreate([
      //       'news_id' => $news_item->id,
      //       'tag_id' => $tag->id,
      //     ]);
      //   }
      // }
    }
}
