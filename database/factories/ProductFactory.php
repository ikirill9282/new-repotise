<?php

namespace Database\Factories;

use App\Enums\ProductModel;
use App\Models\Category;
use App\Models\Location;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
{
  /**
   * The current password being used by the factory.
   */
  protected static ?string $password;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {

    $users = User::whereIn('id', [0,2,3])->get();
    $types_count = Type::count();
    $locations_count = Location::count();
    $sub = fake()->numberBetween(0, 1);

    return [
      'user_id' => $users->shuffle()->first()->id,
      'title' => collect(fake()->words(3))->map(fn($word) => ucfirst($word))->join(' '),
      'price' => fake()->numberBetween(10, 200),
      'subscription' => $sub,
      'old_price' => fake()->numberBetween(300, 2000),
      'type_id' => fake()->numberBetween(1, $types_count),
      'location_id' => fake()->numberBetween(1, $locations_count),
      'rating' => fake()->numberBetween(0, 5),
      'text' => collect(fake()->paragraphs(20))->map(fn($paragraph) => ucfirst($paragraph))->join("\n"),
    ];
  }


  public function configure()
  {
    $categories = Category::all()->pluck('id')->shuffle()->toArray();
    return $this->afterCreating(function (Model $product) use ($categories) {
      $count = fake()->numberBetween(1, 5);
      $cat = array_slice($categories, 0, $count);
      $product->categories()->sync($cat);
      if ($product->subscription) {
        $product->subprice()->create([
          'month' => $m = fake()->numberBetween(20, 40),
          'quarter' => $q = fake()->numberBetween(14, ($m-1)),
          'year' => fake()->numberBetween(10, ($q-1)),
        ]);
      }
    });
  }
}
