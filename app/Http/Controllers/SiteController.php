<?php

namespace App\Http\Controllers;

use App\Models\Admin\Page;
use Illuminate\Http\Request;
use App\Models\Admin\Section;
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

    return view("site.page", ['page' => $page]);
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