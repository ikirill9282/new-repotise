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
            'avatar' => '/storage/images/man.png',
          ],
        );
        if (!$admin->hasRole('admin')) $admin->assignRole(Role::findByName('admin'));
      }

      $seller = User::firstOrCreate(
        ['username' => 'Seller'],
        [
          'username' => 'Seller',
          'password' => 'yX2zYInvor',
          'avatar' => '/storage/images/man.png',
        ],
      );
      $seller2 = User::firstOrCreate(
        ['username' => 'Seller2'],
        [
          'username' => 'Seller2',
          'password' => 'yX2zYInvor',
          'avatar' => '/storage/images/man.png',
        ],
      );

      $buyer = User::firstOrCreate(
        ['username' => 'Buyer'],
        [
          'username' => 'Buyer', 
          'password' => '5MsIqDLxpR',
          'avatar' => '/storage/images/man.png',
        ],
      );

      $buyer2 = User::firstOrCreate(
        ['username' => 'Buyer2'],
        [
          'username' => 'Buyer2', 
          'password' => 'k48dvR6aT3',
          'avatar' => '/storage/images/man.png',
        ],
      );

      if (!$buyer->hasRole('buyer')) $buyer->assignRole(Role::findByName('buyer'));
      if (!$buyer2->hasRole('buyer')) $buyer->assignRole(Role::findByName('buyer'));
      if (!$seller->hasRole('seller')) $seller->assignRole(Role::findByName('seller'));
      if (!$seller2->hasRole('seller')) $seller->assignRole(Role::findByName('seller'));
    }
}
