<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleTags;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

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
    }
}
