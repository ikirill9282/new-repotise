<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      if (!empty(env('ADMIN_LOGIN')) && !empty(env('ADMIN_PASS'))) {
        $admin = User::firstOrCreate(
          ['username' => env('ADMIN_LOGIN')],
          [
            'username' => env('ADMIN_LOGIN'), 
            'password' => env('ADMIN_PASS'),
            'avatar' => '/storage/images/avatar.svg',
          ],
        );
        if (!$admin->hasRole('admin')) $admin->assignRole(Role::findByName('admin'));
      }

      $author = User::firstOrCreate(
        ['username' => 'Author'],
        [
          'username' => 'Author', 
          'password' => '5MsIqDLxpR',
          'avatar' => '/storage/images/avatar.svg',
        ],
      );

      $buyer = User::firstOrCreate(
        ['username' => 'Buyer'],
        [
          'username' => 'Buyer', 
          'password' => 'k48dvR6aT3',
          'avatar' => '/storage/images/avatar.svg',
        ],
      );

      $seller = User::firstOrCreate(
        ['username' => 'Seller'],
        [
          'username' => 'Seller',
          'password' => 'yX2zYInvor',
          'avatar' => '/storage/images/avatar.svg',
        ],
      );

      if (!$author->hasRole('author')) $author->assignRole(Role::findByName('author'));
      if (!$buyer->hasRole('buyer')) $buyer->assignRole(Role::findByName('buyer'));
      if (!$seller->hasRole('seller')) $seller->assignRole(Role::findByName('seller'));
    }
}
