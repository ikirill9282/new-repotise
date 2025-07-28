<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Role::firstOrCreate(['name' => 'system'], ['name' => 'system', 'title' => 'System']);
      Role::firstOrCreate(['name' => 'refered-seller'], ['name' => 'refered-seller', 'title' => 'Refered Seller']);
    }
}
