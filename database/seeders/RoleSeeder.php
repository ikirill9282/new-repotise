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
      $author = Role::firstOrCreate(['name' => 'author'], ['name' => 'author', 'title' => 'Author']);
      $buyer = Role::firstOrCreate(['name' => 'buyer'], ['name' => 'buyer', 'title' => 'Buyer']);
      $seller = Role::firstOrCreate(['name' => 'seller'], ['name' => 'seller', 'title' => 'Seller']);
    }
}
