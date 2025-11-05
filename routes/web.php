<?php

use App\Http\Controllers\UserController as BaseUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Middleware\StripeWebhook;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\OrderProductFileController;

require __DIR__ . '/api.php';

Route::get('/mail/{slug}', function(Request $request, $slug) {
  return view("emails.$slug", [
    'recipient' => User::find(7), 
    'sender' => User::find(1), 
    'msg' => 'test msg', 
    'credentials' => ['password' => '123']
  ]);
});

Route::prefix('/auth')
  ->controller(AuthController::class)
  ->group(function() {
    Route::post('signin', 'signin')->name('signin');
    Route::match(['get', 'post'], 'signout', 'signout')->name('signout');

    Route::get('/email/verify', 'verifyEmail');

    Route::get('/google/callback', 'googleCallback');
    Route::get('/facebook/callback', 'facebookCallback');
    Route::get('/facebook/callback/delete', 'facebookCallbackDelete');
    Route::get('/x/callback', 'xCallback');
  });

Route::middleware('auth:web')->group(function() {

  Route::get('/products/subscribe', [PaymentController::class, 'subscribe']);

  Route::get('/profile', [CabinetController::class, 'profile'])->name('profile');
  Route::get('/profile/edit', [CabinetController::class, 'edit'])->name('profile.edit');
  Route::get('/profile/verify', [CabinetController::class, 'verify'])->name('verify');
  Route::post('/profile/verify', [CabinetController::class, 'verificate']);
  Route::get('/profile/verify/complete', [CabinetController::class, 'verifyComplete']);
  Route::get('/profile/verify/cancel', [CabinetController::class, 'verifyCancel']);
  Route::get('/profile/checkout', [CabinetController::class, 'checkout'])->name('profile.checkout');

  Route::get('/profile/purchases', [CabinetController::class, 'purchases'])->name('profile.purchases');
  Route::get('/profile/purchases/{type}', [CabinetController::class, 'purchases'])->name('profile.purchases.subscriptions');
  Route::get('/profile/settings', [CabinetController::class, 'settings'])->name('profile.settings');
  Route::get(
    '/profile/settings/email/verify/{token}',
    [CabinetController::class, 'confirmEmailChange']
  )->name('profile.settings.email.verify')->middleware('signed');
  Route::get('/profile/referal', [CabinetController::class, 'referal'])->name('profile.referal');
  
  Route::get('/profile/dashboard', [CabinetController::class, 'dashboard'])->name('profile.dashboard');
  Route::get('/profile/products', [CabinetController::class, 'products'])->name('profile.products');
  Route::get('/profile/articles', [CabinetController::class, 'articles'])->name('profile.articles');
  Route::get('/profile/reviews', [CabinetController::class, 'reviews'])->name('profile.reviews');
  Route::get('/profile/sales', [CabinetController::class, 'sales'])->name('profile.sales');

  Route::get('/profile/articles/create', [CabinetController::class, 'create_article'])->name('profile.articles.create');
  Route::get('/profile/products/create', [CabinetController::class, 'create_product'])->name('profile.products.create');
  Route::get('/profile/products/create/media', [CabinetController::class, 'create_product_media'])->name('profile.products.create.media');

  Route::get('/orders/files/{orderProduct}/{file}', OrderProductFileController::class)
    ->name('orders.files.download');
});

// Public Profile
Route::get('/profile/@{slug}', [CabinetController::class, 'public_profile'])->name('view.profile');

// Stripe Hooks
Route::post('/hook/stripe', [StripeController::class, 'hook'])
  ->middleware(StripeWebhook::class)
  ->withoutMiddleware([VerifyCsrfToken::class]);


Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::get('/payment/checkout-subscription', [PaymentController::class, 'checkoutSubscription'])->name('checkout.subscription');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/subscription-success', [PaymentController::class, 'success'])->name('subscription.success');
Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/help-center', [SiteController::class, 'helpCenter'])->name('help-center');
Route::get('/favorites', [SiteController::class, 'favorites'])->name('favorites');
Route::get('/search', [SiteController::class, 'search'])->name('search');

Route::get('/insights', [SiteController::class, 'insights'])->name('insights');
Route::get('/insights/{article}', [SiteController::class, 'feed'])->name('feed');

Route::get('/creators', [SiteController::class, 'creators'])->name('creators');

Route::get('/policies-all', [SiteController::class, 'allPolicies'])->name('policies');
Route::get('/policies/{slug}', [SiteController::class, 'policies']);

Route::get('/products', [SiteController::class, 'products'])->name('products');
// Route::get('/products/{country}', [SiteController::class, 'products'])->name('products.country');
Route::get('/products/{product}', [SiteController::class, 'product'])->name('products.country.product');

Route::get('/payment', [SiteController::class, 'payment'])->name('payment');
Route::get('/sellers', [SiteController::class, 'sellers'])->name('sellers');
Route::get('/referal', [SiteController::class, 'referal'])->name('referal');
Route::get('/gift', [SiteController::class, 'gift'])->name('gift');
Route::get('/investments', [SiteController::class, 'investments'])->name('investments');

// Products
// Route::get('/{slug}/{country}', SiteController::class);
// Route::get('/{slug}/{country}/{product}', SiteController::class);

Route::get('/test', function() {
  // $job = new PayReward(Order::find(100201));
  // $job->handle();
});

// Route::post('/hook/stripe', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook')
//   ->middleware(StripeWebhook::class)
//   ->withoutMiddleware([VerifyCsrfToken::class]);

Route::fallback(FallbackController::class);
