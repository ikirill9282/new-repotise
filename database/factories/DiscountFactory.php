<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'user_id' => null,
            'visibility' => 'public',
            'group' => null,
            'type' => 'promocode',
            'target' => 'cart',
            'target_id' => null,
            'target_author' => 'all',
            'code' => '#' . strtoupper(fake()->unique()->lexify('????')),
            'percent' => 10,
            'sum' => null,
            'min' => null,
            'max' => 50,
            'limit' => 1,
            'attempts' => 0,
            'active' => 1,
            'end' => Carbon::now()->addMonth(),
        ];
    }
}
