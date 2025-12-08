<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class FallbackController extends Controller
{
  public function __invoke(Request $request)
  {
    $page = Page::where('slug', '404')->with('config')->first();
    
    return response()->view("site.pages.404", [
      'page' => $page ?? (object)['variables' => null],
    ]);
  }
}
