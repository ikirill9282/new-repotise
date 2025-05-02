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

      $proucts = [
        'popular_proucts.png',
        'product_1.jpg',
        'product_2.jpg',
        'product_3.jpg',
        'product_4.webp',
        'product_5.webp',
        'product_6.jpg',
        'product_7.jpg',
        'product_8.webp',
        'product_9.webp',
        'product_19.webp',
      ];
      foreach (Product::all() as $product) {
        shuffle($proucts);
        $product->gallery()->firstOrCreate([
          'image' => "/storage/images/{$proucts[0]}",
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
