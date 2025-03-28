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