<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
          [
            'title' => 'Beginner',
            'description' => 'Starting Level',
            'fee' => 10,
            'space' => 0.3,
            'sales_treshold' => 100,
            'icon' => 'icons.star',
          ],
          [
            'title' => 'Growth',
            'description' => 'Reach $100 in sales to unlock',
            'fee' => 8,
            'space' => 0.5,
            'sales_treshold' => 300,
            'icon' => 'icons.graph-rise',
          ],
          [
            'title' => 'Pro',
            'description' => 'Reach $300 in sales to unlock',
            'fee' => 5,
            'space' => 1,
            'sales_treshold' => null,
            'icon' => 'icons.thumb',
          ],
          [
            'title' => 'Exclusive',
            'description' => null,
            'fee' => 0,
            'space' => 0,
            'sales_treshold' => null,
            'icon' => 'icons.thumb',
          ],
        ];
        foreach ($data as $item) {
          Level::create($item);
        }
    }
}
