<?php

namespace Database\Seeders;

use App\Helpers\Slug;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LocationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $countries = [
      "Albania",
      "Andorra",
      "Austria",
      "Belarus",
      "Belgium",
      "Bosnia And Herzegovina",
      "Bulgaria",
      "Croatia",
      "Cyprus",
      "Czech Republic",
      "Denmark",
      "Estonia",
      "Finland",
      "France",
      "Germany",
      "Greece",
      "Hungary",
      "Iceland",
      "Ireland",
      "Italy",
      "Latvia",
      "Liechtenstein",
      "Lithuania",
      "Luxembourg",
      "Malta",
      "Moldova",
      "Monaco",
      "Montenegro",
      "Netherlands",
      "North Macedonia",
      "Norway",
      "Poland",
      "Portugal",
      "Romania",
      "Russia",
      "San Marino",
      "Serbia",
      "Slovakia",
      "Slovenia",
      "Spain",
      "Sweden",
      "Switzerland",
      "Turkey",
      "Ukraine",
      "United Kingdom",
      "Vatican City",
      "Afghanistan",
      "Armenia",
      "Azerbaijan",
      "Bahrain",
      "Bangladesh",
      "Bhutan",
      "Brunei",
      "Cambodia",
      "China",
      "East Timor",
      "Georgia",
      "India",
      "Indonesia",
      "Iran",
      "Iraq",
      "Israel",
      "Japan",
      "Jordan",
      "Kazakhstan",
      "Kuwait",
      "Kyrgyzstan",
      "Laos",
      "Lebanon",
      "Malaysia",
      "Maldives",
      "Mongolia",
      "Myanmar",
      "Nepal",
      "North Korea",
      "Oman",
      "Pakistan",
      "Palestine",
      "Philippines",
      "Qatar",
      "Saudi Arabia",
      "Singapore",
      "South Korea",
      "Sri Lanka",
      "Syria",
      "Taiwan",
      "Tajikistan",
      "Thailand",
      "Turkmenistan",
      "United Arab Emirates",
      "Uzbekistan",
      "Vietnam",
      "Yemen"
    ];

    foreach ($countries as $country) {
      Location::firstOrCreate(
        ['title' => $country], 
        [
          'title' => $country,
          'poster' => "/storage/images/home_filter.png",
        ]);
    }
    // Location::firstOrCreate(
    //   ['title' => 'Japan'],
    //   [
    //     'title' => 'Japan',
    //     'slug' => Slug::makeEn('Japan'),
    //   ],
    // );
    // Location::firstOrCreate(
    //   ['title' => 'Vietnam'],
    //   [
    //     'title' => 'Vietnam',
    //     'slug' => Slug::makeEn('Vietnam'),
    //   ],
    // );
    // Location::firstOrCreate(
    //   ['title' => 'China'],
    //   [
    //     'title' => 'China',
    //     'slug' => Slug::makeEn('China'),
    //   ],
    // );
  }
}
