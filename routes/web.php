<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use Illuminate\Http\Request;


// Route::controller(SiteController::class)->group(function() {
//   Route::get('/')->name('main');
//   Route::get('/{slug}');
// });

Route::get('/', SiteController::class)->name('home');
Route::get('/{slug}', SiteController::class);
Route::get('/insights/{slug}', SiteController::class);
Route::get('/insights/{slug}/{article}', SiteController::class);