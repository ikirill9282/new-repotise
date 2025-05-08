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
      "Travel Guides",
      "Creator Guides",
      "Itineraries",
      "Audio guides",
      "Video guides",
      "E-books",
      "Maps",
      "Courses",
      "Animations",
      "Templates",
      "Presets",
      "Worksheets",
      "Checklists",
      "Other",
    ];

    foreach ($types as $type) {
      Type::firstOrCreate(['title' => $type], ['title' => $type]);
    }
  }
}
