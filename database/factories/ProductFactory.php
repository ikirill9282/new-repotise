<?php

namespace Database\Factories;

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
    $users = User::whereHas('roles', fn($q) => $q->where('name', 'creator'))
      ->get()
      ->pluck('id')
      ->shuffle()
      ->toArray();

    $types = Type::all()
      ->pluck('id')
      ->shuffle()
      ->toArray();

    $locations = Location::all()
      ->pluck('id')
      ->shuffle()
      ->toArray();

    return [
      'user_id' => $users[0],
      'title' => collect(fake()->words(3))->map(fn($word) => ucfirst($word))->join(' '),
      'price' => fake()->numberBetween(1000, 20000),
      'old_price' => fake()->numberBetween(100000, 200000),
      'type_id' => $types[0],
      'location_id' => $locations[0],
      'rating' => fake()->numberBetween(0, 5),
      'text' => collect(fake()->paragraphs(3))->map(fn($paragraph) => ucfirst($paragraph))->join("\n"),
    ];
  }


  public function configure()
  {
    $categories = Category::all()->pluck('id')->shuffle()->toArray();
    return $this->afterCreating(function (Model $product) use ($categories) {
      $count = fake()->numberBetween(1, 5);
      $cat = array_slice($categories, 0, $count);
      $product->categories()->sync($cat);
    });
  }
}
