<?php

namespace App\Http\Controllers;

use App\Helpers\SessionExpire;
use App\Search\SearchClient;
use App\Models\Admin\Page;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\User;
use App\Models\SearchQueries;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class SiteController extends Controller
{

  public function __invoke(Request $request, string $title = 'home', ?string $article = null)
  {
    $page = Page::where('slug', $title)
      ->with('sections.variables')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }
    
    $response_data = [
      'page' => $page,
    ];

    if ($page->slug === 'search') {
      $response_data = array_merge($response_data, $this->getSearchData($request));
    }

    if ($page->slug === 'feed') {
      $response_data = array_merge($response_data, $this->getFeedData($request));
    }

    return view("site.page", $response_data);
  }

  public function getFeedData(Request $request)
  {
    $id = ($request->has('aid') && filter_var($request->get('aid'), FILTER_VALIDATE_INT)) ? $request->get('aid') : null;
    $response_data = [];
    $response_data['articles'] = Article::when($id, fn($q) => $q->where('id', '!=', $id))->orderByDesc('id')->limit(3)->get()->all();
    $response_data['last_news'] = Article::getLastNews();
    $response_data['first_article'] = ($id) ? Article::find($id) : null;

    $this->updateViews($response_data['first_article'] ?? $response_data['articles'][0]);

    return $response_data;
  }

  public function getSearchData(Request $request)
  {
    $query = ($request->has('q') && !empty($request->get('q'))) ? $request->get('q') : null;
    $response_data = [];
    $response_data['search_results'] = is_null($query) ? [] : SearchClient::full($query);
    $response_data['tags'] = SearchClient::getTagsFromItem($response_data['search_results'][0] ?? []);
    if (!is_null($query)) {
      SearchQueries::create([
        'text' => $query,
        'found' => count($response_data['search_results']),
      ]);
    }

    return $response_data;
  }

  public function updateViews(Article $article) {
    $session_key = "v_article:" . $article->id;
    SessionExpire::check($session_key, function($key) use($article) {
      $article->increment('views');
    });
  }

  // public function main(Request $request)
  // {
  //   return view('main');
  // }

  // public function articles(Request $request)
  // {
  //   return view('site.pages.articles');
  // }

  // public function news(Request $request)
  // {
  //   return view('site.pages.news');
  // }
}