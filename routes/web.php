<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;


// Route::controller(SiteController::class)->group(function() {
//   Route::get('/')->name('main');
//   Route::get('/{slug}');
// });

Route::get('/', SiteController::class)->name('home');
Route::get('/{slug}', SiteController::class);
Route::get('/articles/{slug}', SiteController::class);
Route::get('/articles/{slug}/{article}', SiteController::class);