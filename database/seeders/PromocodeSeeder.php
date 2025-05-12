<?php

namespace Database\Seeders;

use App\Models\Promocode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromocodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Promocode::firstOrCreate([
          'user_id' => 1,
          'code' => 'SALE500',
          'percent' => 10,
          'active' => 1,
          'end' => '2025-05-31 00:00:00',
        ]);
    }
}
