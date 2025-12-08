<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'image' => fake()->imageUrl(),
            'type' => 'product',
        ];
    }
}
