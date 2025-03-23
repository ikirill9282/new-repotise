<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Review::firstOrCreate(
        ['user_id' => 3, 'parent_id' => null],
        [
          'user_id' => 3,
          'product_id' => 1,
          'parent_id' => null,
          'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!'
        ]
      );
      Review::firstOrCreate(
        ['user_id' => 3, 'parent_id' => 1],
        [
          'user_id' => 3,
          'product_id' => 1,
          'parent_id' => 1,
          'text' => 'Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!'
        ]
      );
    }
}
