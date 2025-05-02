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
      $categories = [
        "Adventure",
        "Cultural",
        "Historical",
        "Nature",
        "Beaches",
        "Food & Drink",
        "Wildlife",
        "City Tours",
        "Hiking & Trekking",
        "Luxury",
        "Budget Travel",
        "Family Friendly",
        "Romantic Getaways",
        "Nightlife",
        "Shopping",
        "Festivals & Events",
        "Wellness & Spa",
        "Photography",
        "Road Trips",
        "Eco Tourism"
    ];

    foreach ($categories as $key => $category) {
      $category = Category::firstOrCreate(['title' => $category], ['title' => $category]);
    }
      // Category::firstOrCreate(
      //   ['title' => 'Hyde'],
      //   [
      //     'title' => 'Hyde',
      //     'slug' => Slug::makeEn('Hyde'),
      //   ]
      // );
      // Category::firstOrCreate(
      //   ['title' => 'Bus Hyde'],
      //   [
      //     'title' => 'Bus Hyde',
      //     'parent_id' => 1,
      //     'slug' => Slug::makeEn('Bus Hyde'),
      //   ]
      // );
      // Category::firstOrCreate(
      //   ['title' => 'Bike Hyde'],
      //   [
      //     'title' => 'Bike Hyde',
      //     'parent_id' => 1,
      //     'slug' => Slug::makeEn('Bike Hyde'),
      //   ]
      // );
      // Category::firstOrCreate(
      //   ['title' => 'Walking Hyde'],
      //   [
      //     'title' => 'Walking Hyde',
      //     'parent_id' => 1,
      //     'slug' => Slug::makeEn('Walking Hyde'),
      //   ]
      // );
      // Category::firstOrCreate(
      //   ['title' => 'Advanture'],
      //   [
      //     'title' => 'Advanture',
      //   ]
      // );
      // Category::firstOrCreate(
      //   ['title' => 'Travel'],
      //   [
      //     'title' => 'Travel',
      //   ]
      // );
    }
}
