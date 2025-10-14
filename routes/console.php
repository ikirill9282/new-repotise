<?php

use App\Helpers\CustomEncrypt;
use App\Helpers\Slug;
use App\Jobs\DeliveryGift;
use App\Jobs\PayReward;
use App\Jobs\ProcessOrder;
use App\Jobs\ReferalPromocode;
use App\Jobs\ReferalFreeProduct;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use App\Models\Category;
use App\Models\Article;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Meilisearch\Client;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Jobs\TestQueue;
use App\Mail\Gift;
use App\Mail\InviteByPurchase;
use App\Mail\Password;
use App\Models\Discount;
use App\Models\MailLog;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use App\Models\News;
use App\Models\Subscriptions;
use App\Services\StripeClient;
use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\Crypt;
use Laravel\Cashier\Subscription;
use Mews\Purifier\Facades\Purifier;
use Stripe\Collection;
use Stripe\Price;

Schedule::command('app:check-mailgun-log')->everyFifteenMinutes();
Schedule::command('app:clear-expires-images')->hourlyAt(5);
Schedule::command('queue-monitor:stale')->daily();

Artisan::command('tt', function(Request $request) {
  $order = Order::find(100200);
  // Product::find(99)->publishInStripe();
  // Product::find(2)->publishInStripe();
});

Artisan::command('ttm', function() {
  Mail::to(User::find(6)->email)->send(new InviteByPurchase(User::find(6), Order::find(1), User::makePassword()));
});

Artisan::command('ttq', function() {
  TestQueue::dispatch()->delay(60);
});

Artisan::command('rl_stripe', function() {
  $list = Cashier::stripe()->customers->all();
  foreach ($list->data as $item) {
    $item->delete();
  }
});

Artisan::command('rl_index', function() {
  Artisan::call('scout:flush', ['model' => Product::class]);
  Artisan::call('scout:flush', ['model' => Article::class]);
  Artisan::call('scout:flush', ['model' => User::class]);
  Artisan::call('scout:flush', ['model' => Category::class]);
  Artisan::call('scout:flush', ['model' => Location::class]);

  Artisan::call('scout:import', ['model' => Product::class]);
  Artisan::call('scout:import', ['model' => Article::class]);
  Artisan::call('scout:import', ['model' => User::class]);
  Artisan::call('scout:import', ['model' => Category::class]);
  Artisan::call('scout:import', ['model' => Location::class]);

  $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
  $index = $client->index('articles')->updateSortableAttributes(['created_at']);
});


Artisan::command('flush_stripe_products', function() {
  $products = Cashier::stripe()->products->all(['limit' => 100]);
  
  while(!$products->isEmpty()) {
    foreach ($products as $product) {
      try {
        Cashier::stripe()->products->delete($product->id);
      } catch (\Exception $e) {
        continue;
      }
    }
  }
});

Artisan::command('flush_stripe_customers', function() {
  $users = Cashier::stripe()->customers->all(['limit' => 100]);
  foreach ($users as $user) {
    Cashier::stripe()->customers->delete($user->id);
  }
});