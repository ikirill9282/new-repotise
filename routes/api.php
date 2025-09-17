<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UserController;
use App\Models\Admin\SectionVariables;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use App\Models\Article;
use App\Http\Controllers\PaymentController;

Route::prefix('api')->group(function() {
  Route::prefix('search')->controller(SearchController::class)->group(function() {
    Route::get('/', 'search')->name('search');
  });

  Route::prefix('/data')->controller(DataController::class)->group(function() {
    Route::get('/', function() {
      return response()->json([]);
    });
    Route::get('/feed/{id}', 'feed');
    Route::get('/tags', 'tags');
    Route::get('/types', 'types');
    Route::get('/locations', 'locations');
    Route::get('/categories', 'categories');
    Route::post('/messages', 'messages');
    Route::post('/favorite-author', 'favorite_author');
  });

  Route::prefix('/feedback')->middleware('auth:web')->controller(FeedbackController::class)->group(function() {
    Route::get('/views', 'views');
    Route::post('/likes', 'likes');
    Route::post('/comment', 'comment');
    Route::post('/review', 'review');
    Route::post('/favorite', 'favorite');
    Route::post('/follow', 'follow');
  });

  // Route::prefix('/user')->middleware('auth:web')->controller(UserController::class)->group(function() {
  // });


  Route::prefix('/cart')->controller(CartController::class)->group(function() {
    Route::post('/push', 'push');
    Route::post('/count', 'count');
    Route::post('/remove', 'remove');
    Route::post('/promocode', 'promocode');
  });


  Route::prefix('/payment')->controller(PaymentController::class)->group(function() {
    // Route::post('/intent', 'intent');
    Route::post('/confirm', 'confirm');
  });
});