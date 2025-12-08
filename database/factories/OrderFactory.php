<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Enums\Order as OrderEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status_id' => OrderEnum::NEW,
            'cost' => fake()->numberBetween(10, 500),
            'tax' => 0,
            'cost_without_discount' => fake()->numberBetween(10, 500),
            'cost_without_tax' => fake()->numberBetween(10, 500),
        ];
    }
}
