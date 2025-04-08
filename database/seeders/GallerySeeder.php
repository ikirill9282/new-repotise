<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Product;
use App\Models\News;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      foreach (Article::all() as $article) {
        $article->gallery()->firstOrCreate([
          'image' => '/storage/images/img_articles.png',
          'preview' => 1,
          'type' => 'articles'
        ]);
      }

      foreach (Product::all() as $product) {
        $product->gallery()->firstOrCreate([
          'image' => '/storage/images/popular_proucts.png',
          'preview' => 1,
          'type' => 'products',
        ]);
      }

      // foreach (News::all() as $news_item) {
      //   $news_item->gallery()->firstOrCreate([
      //     'image' => '/storage/images/img_articles.png',
      //     'preview' => 1,
      //     'type' => 'news',
      //   ]);
      // }
    }
}
