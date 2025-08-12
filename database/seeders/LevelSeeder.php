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
            'fee' => 10,
            'space' => 0.3,
            'sales_treshold' => 100,
          ],
          [
            'title' => 'Growth',
            'fee' => 8,
            'space' => 0.5,
            'sales_treshold' => 300,
          ],
          [
            'title' => 'Pro',
            'fee' => 5,
            'space' => 0.5,
            'sales_treshold' => null,
          ],
        ];
        foreach ($data as $item) {
          Level::create($item);
        }
    }
}
