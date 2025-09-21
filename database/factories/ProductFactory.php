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
    return [
      'user_id' => fake()->randomElement([0,2,3]),
      'title' => collect(fake()->words(3))->map(fn($word) => ucfirst($word))->join(' '),
      'price' => fake()->numberBetween(10, 200),
      'subscription' => fake()->boolean(),
      'old_price' => fake()->numberBetween(300, 2000),
      'rating' => fake()->numberBetween(0, 5),
      'text' => collect(fake()->paragraphs(20))->map(fn($paragraph) => ucfirst($paragraph))->join("\n"),
    ];
  }


  public function configure()
  {
    $categories = Category::all()->pluck('id');
    $types = Type::all()->pluck('id');
    $locations = Location::all()->pluck('id');

    return $this->afterCreating(function (Model $product) use($categories, $types, $locations) {
      $categories = $categories->shuffle();
      $types = $types->shuffle();
      $locations = $locations->shuffle();

      $cat = $categories->slice(0, fake()->numberBetween(1, 5));
      $product->categories()->sync($cat);

      $typ = $types->slice(0, fake()->numberBetween(1, 5));
      $product->types()->sync($typ);

      $loc = $locations->slice(0, fake()->numberBetween(1, 5));
      $product->locations()->sync($loc);

      if ($product->subscription) {
        $product->subprice()->update([
          'month' => $m = fake()->numberBetween(0, 10),
          'quarter' => $q = fake()->numberBetween($m, ($m+5)),
          'year' => fake()->numberBetween($q, ($q+5)),
        ]);
      }
    });
  }
}
