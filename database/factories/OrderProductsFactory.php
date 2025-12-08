<?php

namespace Database\Factories;

use App\Models\OrderProducts;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderProductsFactory extends Factory
{
    protected $model = OrderProducts::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'count' => fake()->numberBetween(1, 5),
            'price' => fake()->numberBetween(10, 200),
            'sale_price' => fake()->numberBetween(0, 50),
            'total' => fake()->numberBetween(10, 200),
            'total_without_discount' => fake()->numberBetween(10, 200),
        ];
    }
}
