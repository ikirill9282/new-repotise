<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $admin = Role::firstOrCreate(['name' => 'admin'], ['name' => 'admin', 'title' => 'Admin']);
      $buyer = Role::firstOrCreate(['name' => 'customer'], ['name' => 'customer', 'title' => 'Customer']);
      $seller = Role::firstOrCreate(['name' => 'creator'], ['name' => 'creator', 'title' => 'Creator']);
    }
}
