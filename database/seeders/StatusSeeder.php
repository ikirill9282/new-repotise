<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
          [
            'title' => 'Active',
          ],
          [
            'title' => 'Draft',
          ],
          [
            'title' => 'Pending Review',
          ],
          [
            'title' => 'Needs Revision',
          ],
          [
            'title' => 'Reject',
          ],
          [
            'title' => 'Scheduled',
          ],
          [
            'title' => 'Deleted',
          ],
        ];
        
        foreach ($data as $item) {
          \App\Models\Status::firstOrCreate($item);
        }
    }
}
