<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use App\Helpers\Slug;
use Illuminate\Support\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news_item = News::firstOrCreate(
        ['user_id' => 2, 'title' => "Why do you need a Baby Monitor? We'll tell you in our article"],  
        [
          'user_id' => 2,
          'title' => "Why do you need a Baby Monitor? We'll tell you in our article",
          'slug' => Slug::make("Why do you need a Baby Monitor? We'll tell you in our article"),
          'text' => 'Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons',
        ]);
    }
}
