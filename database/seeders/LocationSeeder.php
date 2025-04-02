<?php

namespace Database\Seeders;

use App\Helpers\Slug;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Location::firstOrCreate(
        ['title' => 'Japan'],
        [
          'title' => 'Japan',
          'slug' => Slug::makeEn('Japan'),
        ],
      );
      Location::firstOrCreate(
        ['title' => 'Vietnam'],
        [
          'title' => 'Vietnam',
          'slug' => Slug::makeEn('Vietnam'),
        ],
      );
      Location::firstOrCreate(
        ['title' => 'China'],
        [
          'title' => 'China',
          'slug' => Slug::makeEn('China'),
        ],
      );
    }
}
