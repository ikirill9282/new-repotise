<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
      $admin = Role::firstOrCreate(['name' => 'admin'], ['name' => 'admin', 'title' => 'Admin']);
      $buyer = Role::firstOrCreate(['name' => 'customer'], ['name' => 'customer', 'title' => 'Customer']);
      $seller = Role::firstOrCreate(['name' => 'creator'], ['name' => 'creator', 'title' => 'Creator']);

      
      $write_comment_premission = Permission::firstOrCreate(
        ['name' => 'write_comment'],
        ['name' => 'write_comment', 'title' => 'Write Comments'],
      );

      $create_products = Permission::firstOrCreate(
        ['name' => 'create-products'],
        ['name' => 'create-products', 'title' => 'Create Products'],
      );

      $admin->givePermissionTo($write_comment_premission);
      $buyer->givePermissionTo($write_comment_premission);
      $seller->givePermissionTo($write_comment_premission);

      $admin->givePermissionTo($create_products);
      $seller->givePermissionTo($create_products);

      if (!empty(env('ADMIN_MAIL')) && !empty(env('ADMIN_PASS'))) {
        $admin = User::firstOrCreate(
          ['email' => env('ADMIN_MAIL')],
          [
            'name' => 'Admin',
            'email' => env('ADMIN_MAIL'), 
            'email_verified_at' => Carbon::now(),
            'password' => env('ADMIN_PASS'),
            // 'avatar' => '/storage/images/man.png',
          ],
        );
        if (!$admin->hasRole('admin')) $admin->assignRole(Role::findByName('admin'));
      }

      $seller = User::firstOrCreate(
        ['email' => 'seller@gmail.com'],
        [
          'name' => 'Seller',
          'email' => 'seller@gmail.com',
          'email_verified_at' => Carbon::now(),
          'password' => 'yX2zYInvor',
          // 'avatar' => '/storage/images/man.png',
        ],
      );
      $seller2 = User::firstOrCreate(
        ['email' => 'seller2@gmail.com'],
        [
          'name' => 'Seller2',
          'email' => 'seller2@gmail.com',
          'email_verified_at' => Carbon::now(),
          'password' => 'yX2zYInvor',
          // 'avatar' => '/storage/images/man.png',
        ],
      );

      $buyer = User::firstOrCreate(
        ['email' => 'buyer@gmail.com'],
        [
          'name' => 'Buyer',
          'email' => 'buyer@gmail.com',
          'email_verified_at' => Carbon::now(),
          'password' => '5MsIqDLxpR',
          // 'avatar' => '/storage/images/man.png',
        ],
      );

      $buyer2 = User::firstOrCreate(
        ['email' => 'buyer2@gmail.com'],
        [
          'name' => 'Buyer2',
          'email' => 'buyer2@gmail.com',
          'email_verified_at' => Carbon::now(),
          'password' => 'k48dvR6aT3',
          // 'avatar' => '/storage/images/man.png',
        ],
      );

      if (!User::where('id', 0)->exists()) {
        $system = User::create([
          'email' => env('MAIL_FROM_ADDRESS', 'system@trekguider.com'),
          'email_verified_at' => Carbon::now(),
          'password' => 'k48dvR6aT3',
        ]);
        $system->backup()->delete();
        $system->notifications()->delete();
        $system->options()->delete();
        $system->update(['id' => 0]);
        $system->assignRole(Role::findByName('system'));
      }

      if (!$buyer->hasRole('customer')) $buyer->assignRole(Role::findByName('customer'));
      if (!$buyer2->hasRole('customer')) $buyer2->assignRole(Role::findByName('customer'));
      if (!$seller->hasRole('creator')) $seller->assignRole(Role::findByName('creator'));
      if (!$seller2->hasRole('creator')) $seller2->assignRole(Role::findByName('creator'));
    }
}
