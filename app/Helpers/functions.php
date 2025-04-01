<?php 
use Illuminate\Support\Collection;

if (! function_exists('print_var')) {
  function print_var($name, Collection|array|null $resource = null)
  {
    if (empty($resource)) {
      return 'null';
    }
    try {
      return ($resource->has($name) ? $resource->get($name)?->value ?? '' : '');
    } catch (\Error $e) {
    }
  }
}

if (! function_exists('print_key')) {
  function print_key($key, array|null $resource = null)
  {
    if (is_null($resource)) {
      return 'null';
    }
    try {
      return array_key_exists($key, $resource) ? $resource[$key] : 'null';
    } catch (\Error $e) {
      return 'null';
    }
  }
}

if (! function_exists('rating_images')) {
  function rating_images(float|int $rating)
  {
    $result = [];
    $rating_parts = explode('.', strval($rating));
    
    if (!isset($rating_parts[1])) {
      $result = array_fill(0, $rating, asset('/assets/img/star1.svg'));
    } else {
      $result = array_fill(0, $rating_parts[0], asset('/assets/img/star1.svg'));
      array_push($result, asset('/assets/img/star2.svg'));
    }

    if (count($result) < 5) {
      $empty_stars = array_fill(count($result), (5 - count($result)), asset('/assets/img/star3.svg'));
      $result = array_merge($result, $empty_stars);
    }

    return $result;
  }
}