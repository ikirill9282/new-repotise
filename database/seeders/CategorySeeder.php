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
    }
}
