<?php

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
use App\Models\Discount;
use App\Models\Order;

Schedule::command('app:check-mailgun-log')->everyFifteenMinutes();
Schedule::command('artisan queue-monitor:stale')->daily();

Artisan::command('tt', function(Request $request) {
  // Mail::to(User::find(7)->email)->send(new Gift(User::find(7), Order::find(100229), ['password' => '123']));
  
  $t = new PayReward(Order::find(100200));
  $t->handle();

  // Discount::createForUsers([7], [
  //   'visibility' => 'public',
  //   'type' => 'freeproduct',
  //   'target' => 'cart',
  //   // 'percent' => 15,
  //   'max' => 50,
  // ]);
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

  // "id" => "vs_1RWx7vFkz2A7XNTilUfYxhop"
  // "object" => "identity.verification_session"
  // "client_reference_id" => null
  // "client_secret" => "vs_1RWx7vFkz2A7XNTilUfYxhop_secret_test_YWNjdF8xUjRrU2NGa3oyQTdYTlRpLF9TUnFwOEdCTU9HZ2x2R2liSDJaWWtIM0pBcUtiQUxK0100JgDIth0G"
  // "created" => 1749204235
  // "last_error" => null
  // "last_verification_report" => null
  // "livemode" => false
  // "metadata" => array:1 [
  //   "user_id" => "1"
  // ]
  // "options" => []
  // "redaction" => null
  // "related_customer" => null
  // "status" => "requires_input"
  // "type" => "document"
  // "url" => "https://verify.stripe.com/start/test_YWNjdF8xUjRrU2NGa3oyQTdYTlRpLF9TUnFwOEdCTU9HZ2x2R2liSDJaWWtIM0pBcUtiQUxK0100JgDIth0G"


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