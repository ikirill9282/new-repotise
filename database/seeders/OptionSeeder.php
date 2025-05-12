<?php

namespace Database\Seeders;

use App\Models\Options;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserOptions;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $options = [
        [
          'name' => 'avatar',
          'label' => 'Avatar',
          'type' => 'image',
          'default' => 1,
        ],
        [
          'name' => 'facebook',
          'label' => 'Facebook',
          'type' => 'social',
          'default' => 0,
        ],
        [
          'name' => 'twitter',
          'label' => 'X',
          'type' => 'social',
          'default' => 0,
        ],
        [
          'name' => 'description',
          'label' => 'Description',
          'type' => 'text',
          'default' => 0,
        ],
      ];
      $options = array_map(fn($option) => Options::firstOrCreate($option), $options);
      
      foreach (User::all() as $user) {
        foreach ($options as $option) {
          $user_option = UserOptions::firstOrCreate(
            ['user_id' => $user->id, 'option_id' => $option->id],
            [
              'user_id' => $user->id,
              'option_id' => $option->id,
              'value' => ($option->type === 'image') ? '/storage/images/default_avatar.png' : null,
            ]
          );

          if ($option->name == 'description') {
            $user_option->update(['value' => '<p>Love To Dream Sleepsuit for All Seasons <span>Love To Dream</span> Sleepsuit for
                                              All <span>Seasons Love To Dream</span> Sleepsuit for All Seasons</p>']);
          }
        }
      }
    }
}
