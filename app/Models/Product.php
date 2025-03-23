<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
  use HasAuthor, HasGallery, HasPrice;

  public function getGalleryClass()
  {
    return ProductGallery::class;
  }

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id', 'id', 'id');
  }

  public function type()
  {
    return $this->belongsTo(Type::class);
  }

  public function location()
  {
    return $this->belongsTo(Location::class);
  }

  public function reviews()
  {
    return $this->hasMany(Review::class);
  }

  public function prepareRatingImages()
  {
    $result = [];
    $rating_parts = explode('.', strval($this->rating));
    
    if (!isset($rating_parts[1])) {
      $result = array_fill(0, $this->rating, asset('/assets/img/star1.svg'));
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

  public function reviewsCount(): Attribute
  {
    return Attribute::make(
      get: function($value) {
        if ($value > 1000000000) {
          return round($value / 1000000000, 2) . 'kkk';
        } elseif ($value > 1000000) {
          return round($value / 1000000, 2) . 'kk';
        } elseif ($value > 1000) {
          return $value / 1000 . 'k';
        } else {
          return $value;
        }
      }
    );
  }
}
