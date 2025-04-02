<?php

namespace Database\Seeders;

use App\Helpers\Slug;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Category::firstOrCreate(
        ['title' => 'Hyde'],
        [
          'title' => 'Hyde',
          'slug' => Slug::makeEn('Hyde'),
        ]
      );
      Category::firstOrCreate(
        ['title' => 'Bus Hyde'],
        [
          'title' => 'Bus Hyde',
          'parent_id' => 1,
          'slug' => Slug::makeEn('Bus Hyde'),
        ]
      );
      Category::firstOrCreate(
        ['title' => 'Bike Hyde'],
        [
          'title' => 'Bike Hyde',
          'parent_id' => 1,
          'slug' => Slug::makeEn('Bike Hyde'),
        ]
      );
      Category::firstOrCreate(
        ['title' => 'Walking Hyde'],
        [
          'title' => 'Walking Hyde',
          'parent_id' => 1,
          'slug' => Slug::makeEn('Walking Hyde'),
        ]
      );
    }
}
