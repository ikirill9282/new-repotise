<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\UserController as BaseUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Models\Admin\Page;


require __DIR__ . '/api.php';
// Route::controller(SiteController::class)->group(function() {
//   Route::get('/')->name('main');
//   Route::get('/{slug}');
// });

Route::prefix('/auth')
  ->controller(AuthController::class)
  ->group(function() {
    Route::post('signin', 'signin')->name('signin');
    Route::match(['get', 'post'], 'signout', 'signout')->name('signout');
  });

Route::middleware('auth:web')->group(function() {
  Route::get('/cart', [SiteController::class, 'cart']);
  Route::get('/cart/order', [SiteController::class, 'order']);
  Route::get('/cart/{status}', [SiteController::class, 'payment'])->name('payment.status');
});

// Route::get('/', SiteController::class)->name('home');
// Route::get('/{slug}', SiteController::class);
// Route::get('/insights/{slug}/{article}', SiteController::class);

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