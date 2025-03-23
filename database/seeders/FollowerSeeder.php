<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Follower;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $authors = User::all();
      foreach ($authors as $author) {
        foreach ($authors as $subscriber) {
          Follower::firstOrCreate([
            'author_id' => $author->id,
            'subscriber_id' => $subscriber->id,
          ]);
        }
      }
    }
}
