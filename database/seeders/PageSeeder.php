<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $main_page = Page::firstOrCreate([
        'title' => 'Home',
        'slug' => 'home',
      ]);
      $articles_page = Page::firstOrCreate([
        'title' => 'Travel Insights',
        'slug' => 'articles',
      ]);
      $news_page = Page::firstOrCreate([
        'title' => 'news',
        'slug' => 'news',
      ]);
      $insights_page = Page::firstOrCreate([
        'title' => 'insights',
        'slug' => 'insights',
      ]);
    }
}
