<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Page;

class FallbackController extends Controller
{
  public function __invoke(Request $request)
  {
    return response()->view("site.page", [
      'page' => Page::firstWhere('slug', '404')
    ]);
  }
}
