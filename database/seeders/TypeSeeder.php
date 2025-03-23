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
      Type::firstOrCreate(
        ['title' => 'Travel Guides'],
        [
        'title' => 'Travel Guides',
        'slug' => Slug::makeEn('Travel Guides'),
        ]
      );
    }
}
