<?php

use App\Http\Controllers\SearchController;
use App\Models\Admin\SectionVariables;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use App\Models\Article;
use App\Models\News;

Route::prefix('api')->group(function() {
  Route::prefix('search')->controller(SearchController::class)->group(function() {
    Route::get('/', 'search')->name('search');
  });
  Route::get('/feed/content/{id}', function($id) {
    $vars = SectionVariables::where('section_id', 7)->get()->keyBy('name');
    $news = News::getLastNews();
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
    
    return $articles->implode('');
  });
});