<?php

namespace App\Helpers;

use Illuminate\Routing\Route;
use App\Models\Page;
use App\Models\Product;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Support\Facades\Route as FacadesRoute;
use Illuminate\Support\Facades\URL;
use App\Models\Location;

class Breadcrumbs
{
  public static function make(Route $route, ?string $current_name = null, ?array $exclude = null)
  {
    $request_params = $route->parameters();
    $steps = ['home'];
    $uri = $route->uri;

    // dd($uri);
    if ($route->uri === 'livewire/update') {
      $url = parse_url(request()->header('referer'));
      $uri = $url['path'];
    }

    if ($uri === '{slug}') {
      $steps[] = $route->parameter('slug');
    } else {
      $steps = array_merge($steps, array_filter(explode('/', $uri), function($step) {
        return !str_contains($step, '{') && !str_contains($step, '}') && !empty($step);
      }));
    }

    if (!is_null($exclude)) {
      $steps = array_filter($steps, fn($step) => !in_array($step, $exclude));
    }

    $steps = array_flip($steps);
    $pages = Page::select('title', 'slug')->whereIn('slug', array_keys($steps))->get();

    foreach ($steps as $name => $value) {
      $page = $pages->firstWhere('slug', $name);
      unset($steps[$name]);
      if ($page) $steps[$page->title] = $page->url;
    };


    if (isset($request_params['country']) && !empty($request_params['country'])) {
      $location = Location::where('slug', $request_params['country'])->first();
      $steps[$request_params['country']] = $location->makeUrl();
    }

    if (isset($request_params['slug']) && ($request_params['slug'] == 'products')) {
      if (isset($request_params['product']) && request()->has('pid')) {
        $product = Product::findByPid(request()->get('pid'));
        $steps[$product->location->title] = $product->location->makeUrl();
      }
    }

    if (!is_null($current_name)) {
      $steps[$current_name] = URL::full();
    }

    return $steps;
  }
}