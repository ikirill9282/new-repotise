<?php

use App\Http\Controllers\UserController as BaseUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Middleware\StripeWebhook;
use App\Jobs\CalcReward;
use App\Jobs\PayReward;
use Illuminate\Http\Request;
use App\Mail\ConfirmRegitster;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Jobs\ProcessOrder;
use App\Models\Order;

require __DIR__ . '/api.php';
// Route::controller(SiteController::class)->group(function() {
//   Route::get('/')->name('main');
//   Route::get('/{slug}');
// });

Route::get('/mail/{slug}', function(Request $request, $slug) {
  return view("emails.$slug", ['recipient' => User::find(7), 'sender' => User::find(1), 'msg' => 'test msg', 'credentials' => ['password' => '123']]);
});

Route::prefix('/auth')
  ->controller(AuthController::class)
  ->group(function() {
    Route::post('signin', 'signin')->name('signin');
    Route::match(['get', 'post'], 'signout', 'signout')->name('signout');

    Route::get('/email/verify', 'verifyEmail');
  });

Route::middleware('auth:web')->group(function() {
  Route::get('/profile', [CabinetController::class, 'profile'])->name('profile');
  Route::get('/profile/verify', [CabinetController::class, 'verify'])->name('verify');
  Route::post('/profile/verify', [CabinetController::class, 'verificate']);
  Route::get('/profile/verify/complete', [CabinetController::class, 'verifyComplete']);
  Route::get('/profile/verify/cancel', [CabinetController::class, 'verifyCancel']);
  Route::get('/profile/purchases', [CabinetController::class, 'purchases'])->name('profile.purchases');
  Route::get('/profile/referal', [CabinetController::class, 'referal'])->name('profile.referal');
  Route::get('/profile/settings', [CabinetController::class, 'settings'])->name('profile.settings');
  Route::get('/profile/checkout', [CabinetController::class, 'checkout'])->name('profile.checkout');


  Route::get('/profile/@{slug}', [CabinetController::class, 'public_profile'])->name('view.profile');
});

Route::post('/hook/stripe', function(Request $request) {
  Log::channel('stripe_events')->debug('Stripe Event', ['data' => $request->attributes->get('stripe_event')]);
  return response('ok');
})
  ->middleware(StripeWebhook::class)
  ->withoutMiddleware([VerifyCsrfToken::class]);;

// Route::get('/', SiteController::class)->name('home');
// Route::get('/{slug}', SiteController::class);
// Route::get('/insights/{slug}/{article}', SiteController::class);


Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment-success');
Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment-error');

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/help-center', [SiteController::class, 'helpCenter'])->name('help-center');
Route::get('/favorites', [SiteController::class, 'favorites'])->name('favorites');
Route::get('/search', [SiteController::class, 'search'])->name('search');

Route::get('/insights', [SiteController::class, 'insights'])->name('insights');
Route::get('/insights/{article}', [SiteController::class, 'feed'])->name('feed');

Route::get('/policies', [SiteController::class, 'policies'])->name('policies');
Route::get('/policies/{slug}', [SiteController::class, 'policies']);

Route::get('/products', [SiteController::class, 'products'])->name('products');
Route::get('/products/{country}', [SiteController::class, 'products'])->name('products.country');
Route::get('/products/{country}/{product}', [SiteController::class, 'product'])->name('products.country.product');

Route::get('/payment', [SiteController::class, 'payment'])->name('payment');
Route::get('/invite', [SiteController::class, 'invite'])->name('invite');
Route::get('/referal', [SiteController::class, 'referal'])->name('referal');
Route::get('/gift', [SiteController::class, 'gift'])->name('gift');
// Products
// Route::get('/{slug}/{country}', SiteController::class);
// Route::get('/{slug}/{country}/{product}', SiteController::class);

Route::get('/test', function() {
  // $job = new PayReward(Order::find(100201));
  // $job->handle();
});

Route::post('stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

Route::fallback(FallbackController::class);