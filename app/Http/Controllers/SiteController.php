<?php

namespace App\Http\Controllers;

use App\Helpers\Search;
use App\Models\Admin\Page;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    $response_data = [
      'page' => $page,
    ];

    if ($page->slug === 'search') {
      $query = ($request->has('q') && !empty($request->get('q'))) ? $request->get('q') : null;
      $response_data['search_results'] = is_null($query) ? [] : Search::full($query);
      // dd($response_data);
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