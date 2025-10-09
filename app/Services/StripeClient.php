<?php

namespace App\Services;

use App\Models\Product;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;
use Stripe\Stripe;

class StripeClient
{
  public function __construct()
  {
    Stripe::setApiKey(env('STRIPE_SECRET'));
  }

  public function createProduct(Product $model): StripeProduct
  {
    $stripe_product = StripeProduct::create([
      'name' => $model->title,
      'active' => true,
      'metadata' => [
        'product_id' => $model->id,
      ],
      'url' => $model->makeUrl(),
    ]);

    $model->update(['stripe_id' => $stripe_product->id]);

    return $stripe_product;
  }

  public function createPrices(Product $product, StripeProduct $stripe_product): void
  {
    $month = StripePrice::create([
      'product' => $stripe_product->id,
      'unit_amount' => $product->subprice->getMonthSum() * 100, // cents!
      'currency' => 'usd',
      'recurring' => ['interval' => 'month'],
    ]);

    $quarter = StripePrice::create([
      'product' => $stripe_product->id,
      'unit_amount' => $product->subprice->getQuarterSum() * 100, // cents!
      'currency' => 'usd',
      'recurring' => [
        'interval' => 'month',
        'interval_count' => 3,
      ]
    ]);

    $year = StripePrice::create([
      'product' => $stripe_product->id,
      'unit_amount' => $product->subprice->getYearSum() * 100, // cents!
      'currency' => 'usd',
      'recurring' => [
        'interval' => 'year',
      ]
    ]);

    $product->subprice->update(['stripe_data' => [
      'month' => $month->id,
      'quarter' => $quarter->id,
      'year' => $year->id,
    ]]);
  }
}