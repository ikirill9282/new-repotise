<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;
use Stripe\Stripe;
use Laravel\Cashier\Cashier;

class StripeClient
{
  public function __construct()
  {
    // Use helper function to get secret from Integration or env
    $secret = stripe_secret();
    if ($secret) {
      Stripe::setApiKey($secret);
    } else {
      // Fallback to env for backwards compatibility
      Stripe::setApiKey(env('STRIPE_SECRET'));
    }
  }

  public function createPaymentIntent(Order $model)
  {
    $ephemeralKey = Cashier::stripe()->ephemeralKeys->create(
      ['customer' => $model->user->asStripeCustomer()->id],
      ['stripe_version' => '2022-11-15']
    );
    $transaction = Cashier::stripe()->paymentIntents->create([
      'amount' => ($model->cost * 100),
      'currency' => 'usd',
      'automatic_payment_methods' => ['enabled' => true],
      'customer' => $model->user->asStripeCustomer()->id, 
      'metadata' => [
        'initiator' => ($model->user?->id ?? 0 == 0) ? 'system' : 'customer',
        'inititator_id' => $model->user?->id ?? 0,
        'ephermal' => $ephemeralKey->secret,
        'type' => 'order',
      ],
    ]);
    $model->payment_id = $transaction->id;
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

  public function createPrice(Product $product, StripeProduct $stripe_product): void
  {
    $price = StripePrice::create([
      'product' => $stripe_product->id,
      'unit_amount' => $product->getPrice() * 100, // cents!
      'currency' => 'usd',
    ]);

    $product->update(['stripe_price_id' => $price->id]);
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