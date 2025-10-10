<?php

namespace Database\Seeders;

use App\Models\Policies;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Policies::create([
        'title' => 'Terms and Conditions',
      ]);
      Policies::create([
        'title' => 'Seller Agreement',
      ]);
      Policies::create([
        'title' => 'Privacy Policy',
      ]);
      Policies::create([
        'title' => 'Cookie Policy',
      ]);
      Policies::create([
        'title' => 'Data processing agreement',
      ]);
      Policies::create([
        'title' => 'Payment Policy',
      ]);
      Policies::create([
        'title' => 'Copyright Policy',
      ]);
      Policies::create([
        'title' => 'Disput Resolution Policy',
      ]);
      Policies::create([
        'title' => 'Referral Program Policy',
      ]);
    }
}
