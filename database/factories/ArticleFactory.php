<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'text' => fake()->paragraphs(5, true),
            'slug' => fake()->slug(),
            'status_id' => \App\Enums\Status::ACTIVE,
            'views' => fake()->numberBetween(0, 1000),
        ];
    }
}
