<?php

use App\Helpers\CustomEncrypt;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Likes;

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

if (! function_exists('hash_like')) {
  function hash_like(string $type, int $id): string
  {
    return CustomEncrypt::encrypt([
      'user_id' => Auth::user()?->id ?? null,
      'type' => $type,
      'model_id' => $id,
    ]);
  }
}

if (! function_exists('hash_more')) {
  function hash_more(array $comment): string
  {
    return CustomEncrypt::encrypt([
      'id' => $comment['id'],
    ]);
  }
}

if (! function_exists('is_liked')) {
  function is_liked(string $type, int $id): string
  {
    if (!Auth::check()) return false;
    return Likes::where(['type' => $type, 'model_id' => $id, 'user_id' => Auth::user()?->id])->exists();
  }
}

if (! function_exists('enable_more')) {
  function enable_more(array $comment): string
  {
    if (!isset($comment['children_count'])) {
      return false;
    } elseif ($comment['children_count'] == 0) {
      return false;
    }

    if (!isset($comment['children'])) {
      return true;
    }

    if ($comment['children_count'] == count($comment['children'])) {
      return false;
    }

    return true;
  }
}