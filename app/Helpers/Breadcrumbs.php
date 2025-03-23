<?php

namespace App\Helpers;

use Illuminate\Routing\Route;
use App\Models\Admin\Page;
use Illuminate\Support\Facades\Route as FacadesRoute;
use Illuminate\Support\Facades\URL;

class Breadcrumbs
{
  public static function make(Route $route, ?string $current_name = null, ?array $exclude = null)
  {
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

    if (!is_null($current_name)) {
      $steps[$current_name] = URL::full();
    }
    return $steps;
  }
}