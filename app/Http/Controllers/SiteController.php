<?php

namespace App\Http\Controllers;

use App\Search\SearchClient;
use App\Models\Admin\Page;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\User;
use App\Models\SearchQueries;

class SiteController extends Controller
{

  public function __invoke(Request $request, string $title = 'home', ?string $article = null)
  {
    $page = Page::where('slug', $title)
      ->with('sections.variables')
      ->first();

    if (is_null($page)) {
      throw new NotFoundHttpException('Not found');
    }


    // dd(Article::find(3)->toSearchableArray());

    $response_data = [
      'page' => $page,
    ];

    if ($page->slug === 'search') {
      $query = ($request->has('q') && !empty($request->get('q'))) ? $request->get('q') : null;
      $response_data['search_results'] = is_null($query) ? [] : SearchClient::full($query);
      $response_data['tags'] = SearchClient::getTagsFromItem($response_data['search_results'][0] ?? []);
      if (!is_null($query)) {
        SearchQueries::create([
          'text' => $query,
          'found' => count($response_data['search_results']),
        ]);
      }
    }

    return view("site.page", $response_data);
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