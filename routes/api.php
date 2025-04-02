<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function() {
  Route::prefix('search')->controller(SearchController::class)->group(function() {
    Route::get('/', 'search')->name('search');
  });
});