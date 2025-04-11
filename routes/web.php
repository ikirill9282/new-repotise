<?php

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

Route::get('/', SiteController::class)->name('home');
Route::get('/{slug}', SiteController::class);
Route::get('/insights/{slug}', SiteController::class);
Route::get('/insights/{slug}/{article}', SiteController::class);

Route::fallback(FallbackController::class);