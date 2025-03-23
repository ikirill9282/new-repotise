<?php

namespace Database\Seeders;

use App\Helpers\Slug;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $product = Product::firstOrCreate(
        [
          'user_id' => 4,
          'title' => 'Get to know Japan',
        ],
        [
          'user_id' => 4,
          'title' => 'Get to know Japan',
          'slug' => Slug::makeEn('Get to know Japan') . '-' . Carbon::now()->format('YmdHis'),
          'price' => 20000,
          'old_price' => 3000000,
          'type_id' => 1,
          'location_id' => 1,
          'rating' => 3.5,
          'reviews' => 13,
          'text' => "This luxury watch combines Swiss quality, stylish design and excellent performance. Classics. Original wristwatch. The mechanism has 8 stones. The round steel case is decorated with diamond pavé. Mother-of-pearl dial. No second hand. Markers in the form of Roman numerals. The date window is located at the 6 o'clock position. <br><br> This luxury watch combines Swiss quality, stylish design and excellent performance. Classics. Original wristwatch. The mechanism has 8 stones. The round steel case is decorated with diamond pavé. Mother-of-pearl dial. No second hand. Markers in the form of Roman numerals. The date window is located at the 6 o'clock position. <br><br> This luxury watch combines Swiss quality, stylish design and excellent performance. Classics. Original wristwatch. The mechanism has 8 stones. The round steel case is decorated with diamond pavé. Mother-of-pearl dial. No second hand. Markers in the form of Roman numerals. The date window is located at the 6 o'clock position.",
        ]
      );

      $product->categories()->sync([1]);

      $product->gallery()->firstOrCreate(
        ['image' => '/storage/images/popular_proucts.png'],
        [
          'image' => '/storage/images/popular_proucts.png',
          'preview' => 1,
        ],
      );
    }
}
