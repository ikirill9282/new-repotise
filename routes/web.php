<?php

use App\Http\Controllers\UserController as BaseUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use Illuminate\Http\Request;
use App\Mail\ConfirmRegitster;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

require __DIR__ . '/api.php';
// Route::controller(SiteController::class)->group(function() {
//   Route::get('/')->name('main');
//   Route::get('/{slug}');
// });

Route::get('/mail/{slug}', function(Request $request, $slug) {
  return view("emails.$slug", ['user' => Auth::user()]);
});

Route::prefix('/auth')
  ->controller(AuthController::class)
  ->group(function() {
    Route::post('signin', 'signin')->name('signin');
    Route::match(['get', 'post'], 'signout', 'signout')->name('signout');

    Route::get('/email/verify', 'verifyEmail');
  });

Route::middleware('auth:web')->group(function() {
  Route::get('/profile', [CabinetController::class, 'profile']);
  Route::get('/profile/verify', [CabinetController::class, 'verify']);
  Route::post('/profile/verify', [CabinetController::class, 'verificate']);
  Route::get('/profile/verify/complete', [CabinetController::class, 'verifyComplete']);
  Route::get('/profile/verify/cancel', [CabinetController::class, 'verifyCancel']);
  Route::get('/profile/{slug}', [CabinetController::class, 'profile']);
});

Route::post('/hook/stripe', function(Request $request) {
  Log::debug('Stripe Event', ['data' => $request->json()]);
})
  ->withoutMiddleware([VerifyCsrfToken::class]);;

// Route::get('/', SiteController::class)->name('home');
// Route::get('/{slug}', SiteController::class);
// Route::get('/insights/{slug}/{article}', SiteController::class);


Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::get('/payment/order', [PaymentController::class, 'order']);
Route::get('/payment/order/complete', [PaymentController::class, 'orderComplete']);
Route::get('/payment/{status}', [PaymentController::class, 'payment'])->name('payment.status');


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

// Products
// Route::get('/{slug}/{country}', SiteController::class);
// Route::get('/{slug}/{country}/{product}', SiteController::class);

Route::fallback(FallbackController::class);