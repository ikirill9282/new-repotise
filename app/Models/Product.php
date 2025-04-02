<?php

namespace App\Models;

use App\Helpers\Collapse;
use App\Helpers\Slug;
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
      $this->load('author', 'categories', 'type', 'location', 'preview')->loadCount('reviews');

      $array = $this->toArray();

      $array['author'] = $this->author->only('profile', 'name', 'avatar', 'description');
      $array['categories'] = $this->categories->select(['id', 'parent_id', 'title'])->toArray();
      $array['type'] = $this->type->only(['id', 'title']);
      $array['location'] = $this->location->only(['id', 'title']);
      $array['preview'] = $this->preview?->image ?? '';
      $array['reviews_count'] = $this->reviews_count;

      return $array;
  }


  protected static function boot()
  {
    parent::boot();

    self::creating(function($model) {
      if (empty($model->slug)) {
        $model->slug = Slug::makeEn($model->title);
      }
    });
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
    return rating_images($this->rating);
  }

  public function reviewsCount(): Attribute
  {
    return Attribute::make(
      get: fn($value) => Collapse::make($value),
    );
  }
}
