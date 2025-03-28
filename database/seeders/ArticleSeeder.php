<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\Slug;
use Illuminate\Support\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $cnt = 1;
      for ($x = 0; $x < 5; $x++) {
        for ($i = 1; $i <= 5; $i++) {
          $article = Article::firstOrCreate(
            ['user_id' => $i, 'title' => 'Article Title'],
            [
              'user_id' => $i,
              'title' => "Article Title $cnt",
              'subtitle' => null,
              'slug' => Slug::make("Article Title"),
              'views' => 0,
              'text' => '<h3>Among the manufacturers of prestigious Swiss watches</h3>
                            <h4>Among the manufacturers of prestigious Swiss watches, there are recognized leaders - Breguet</h4>
                            <p>Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.</p>
                            <h4>Heading 2</h4>
                            <p>Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.</p>
                            <img src="/storage/images/img_articles.png" alt="" class="img_articles">
                            <h4>Heading 3</h4>
                            <p>Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.</p>'
            ]);
            $cnt++;
        }
      }
  }
}
