<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\Slug;
use Illuminate\Support\Carbon;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      // Get existing user IDs or create users if needed
      $userIds = User::pluck('id')->toArray();
      if (empty($userIds)) {
        // Create at least 5 users if none exist
        for ($i = 0; $i < 5; $i++) {
          $userIds[] = User::factory()->create()->id;
        }
      }
      
      // Ensure we have at least 5 user IDs (create additional users if needed)
      while (count($userIds) < 5) {
        $userIds[] = User::factory()->create()->id;
      }
      
      $cnt = 1;
      for ($x = 0; $x < 6; $x++) {
        for ($i = 0; $i <= 4; $i++) {
          // Use existing user ID from the array
          $userId = $userIds[$i] ?? $userIds[0];
          
          // try {
          $title = ($i == 0) ? "Why do you need a Baby Monitor? We'll tell you in our article $cnt" : "Article Title $cnt";
          $article = Article::firstOrCreate(
            ['user_id' => $userId, 'title' => $title],
            [
              'user_id' => $userId,
              'title' => $title,
              'views' => 0,
              'text' => '<h3>Among the manufacturers of prestigious Swiss watches</h3>
                            <h4>Among the manufacturers of prestigious Swiss watches, there are recognized leaders - Breguet</h4>
                            <p>Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.</p>
                            <h4>Heading 2</h4>
                            <p>Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.</p>
                            <img src="/storage/images/img_articles.png" alt="" class="img_articles">
                            <h4>Heading 3</h4>
                            <p>Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin.Among the manufacturers of prestigious Swiss watches there are recognized leaders - these are Breguet, Patek Philippe, Audemars Piguet, Vacheron Constantin '. $cnt .'.</p>'
            ]);
            $cnt++;

          // } catch (\Exception $e) {
          //   dd($e->getMessage());
          // }
        }
      }
  }
}
