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
        ['user_id' => 1, 'title' => "Why do you need a Baby Monitor? We'll tell you in our article"],  
        [
          'user_id' => 1,
          'title' => "Why do you need a Baby Monitor? We'll tell you in our article",
          'text' => 'Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons',
        ]);
        $news_ite2 = News::firstOrCreate(
        ['user_id' => 2, 'title' => "New Horizons: How the Revival of International Air Travel is Shaping the 2024 Tourism Landscape"],  
        [
          'user_id' => 2,
          'title' => "New Horizons: How the Revival of International Air Travel is Shaping the 2024 Tourism Landscape",
          'text' => 'Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons',
        ]);
        $news_item3 = News::firstOrCreate(
        ['user_id' => 2, 'title' => "Exotic Destinations: Top 5 Unusual Countries to Visit This Year"],  
        [
          'user_id' => 2,
          'title' => "Exotic Destinations: Top 5 Unusual Countries to Visit This Year",
          'text' => 'Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All SeasonsLove To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons Love To Dream Sleepsuit for All Seasons',
        ]);
    }
}
