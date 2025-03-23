<?php

namespace Database\Seeders;

use App\Models\Article;
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
      for ($i = 1; $i < 6; $i++) {
        Tag::firstOrCreate([
          'title' => "Tag $i",
        ]);
      }

      for($i = 1; $i <= 3; $i++) {
        $tag_ids = [];
        for ($x = 1; $x <= 3; $x++) {
          $tag_ids[] = 5 - $x;
        }
        Article::find($i)->tags()->sync($tag_ids);
      }
    }
}
