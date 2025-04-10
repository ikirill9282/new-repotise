<?php

use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\SearchController;
use App\Models\Admin\SectionVariables;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use App\Models\Article;


Route::prefix('api')->group(function() {
  Route::prefix('search')->controller(SearchController::class)->group(function() {
    Route::get('/', 'search')->name('search');
  });


  Route::prefix('/feedback')->controller(FeedbackController::class)->group(function() {
    Route::get('/views', 'views');
  });

  Route::get('/feed/content/{id}', function($id) {
    $vars = SectionVariables::where('section_id', 7)->get()->keyBy('name');
    $news = Article::getLastNews();
    $articles = Article::where('id', '<', $id)
      ->when(request()->has('aid'), fn($q) => $q->where('id', '!=', request()->get('aid')))
      ->orderByDesc('id')
      ->limit(2)
      ->get()
      ->map(fn($article) => Blade::render('site.components.article_feed', [
        'variables' => $vars,
        'last_news' => $news,
        'article' => $article,
      ]));
    
    return $articles->implode("\n");
  });
});