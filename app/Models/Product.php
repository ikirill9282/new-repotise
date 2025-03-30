<?php

namespace App\Models;

use App\Helpers\Collapse;
use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Scout\Searchable;

class Product extends Model
{
  use HasAuthor, HasGallery, HasPrice, Searchable;

  public function toSearchableArray(): array
  {
      $array = $this->toArray();

      $array['author'] = $this->author->toArray();
      $array['categories'] = $this->categories->toArray();
      $array['type'] = $this->type->toArray();
      $array['location'] = $this->location->toArray();

      return $array;
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
      get: fn($value) => Collapse::make($value),
    );
  }
}
