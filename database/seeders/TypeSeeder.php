<?php

namespace Database\Seeders;

use App\Helpers\Slug;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $types = [
      "Adventure Guide",
      "Cultural Guide",
      "Historical Guide",
      "Nature Guide",
      "Beach Guide",
      "Food & Drink Guide",
      "Wildlife Guide",
      "City Guide",
      "Hiking Guide",
      "Luxury Travel Guide",
      "Budget Travel Guide",
      "Family Travel Guide",
      "Romantic Getaway Guide",
      "Nightlife Guide",
      "Shopping Guide",
      "Festival Guide",
      "Wellness & Spa Guide",
      "Photography Guide",
      "Road Trip Guide",
      "Eco Tourism Guide",
      "Backpacking Guide",
      "Cruise Guide",
      "Ski & Snowboard Guide",
      "Desert Safari Guide",
      "Island Guide",
      "Mountain Guide",
      "Religious Pilgrimage Guide",
      "Volunteer Travel Guide",
      "Cycling Guide",
      "Wildlife Safari Guide"
    ];

    foreach ($types as $type) {
      Type::firstOrCreate(['title' => $type], ['title' => $type]);
    }
  }
}
