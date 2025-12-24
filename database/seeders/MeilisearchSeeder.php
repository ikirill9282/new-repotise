<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class MeilisearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Artisan::call('rl_index');
        } catch (\Exception $e) {
            // Skip Meilisearch indexing if Meilisearch is not available
            $this->command->warn('Meilisearch is not available. Skipping index creation.');
        }
    }
}
