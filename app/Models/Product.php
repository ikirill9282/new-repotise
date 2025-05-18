<?php

namespace App\Models;

use App\Helpers\Collapse;
use App\Helpers\CustomEncrypt;
use App\Helpers\Slug;
use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use App\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

class Product extends Model
{
  use HasAuthor, HasGallery, Searchable, HasFactory;

  public function toSearchableArray(): array
  {
    $this->load('author', 'categories', 'type', 'location', 'preview')->loadCount('reviews');

    $array = $this->toArray();

    $array['author'] = $this->author->only('profile', 'name', 'avatar', 'description');
    $array['categories'] = $this->categories->select(['id', 'parent_id', 'title'])->toArray();
    $array['type'] = $this->type->only(['id', 'title', 'slug']);
    $array['location'] = $this->location->only(['id', 'title', 'slug']);
    $array['preview'] = $this->preview?->image ?? '';
    $array['reviews_count'] = $this->reviews_count;

    return $array;
  }

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {

      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
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
      get: fn($value) => ($value == 0) ? 0 : Collapse::make($value),
    );
  }
  public function countComments(): int
  {
    return $this->reviews_count ?? 0;
  }

  // public function price(): Attribute
  // {
  //   return Attribute::make(
  //     get: fn($val) => number_format($val),
  //   );
  // }

  // public function oldPrice(): Attribute
  // {
  //   return Attribute::make(
  //     get: fn($val) => number_format($val),
  //   );
  // }

  public function makeUrl()
  {
    return url("/products/{$this->location->slug}/$this->slug?pid=" . CustomEncrypt::generateUrlHash(['id' => $this->id]));
  }

  public function getAllReviews(?int $limit = 10)
  {
    $this->limit = $limit;
    $this->comments_loading = true;
    $this->level = 0;

    $this->comments = Review::query()
      ->where('product_id', $this->id)
      ->whereNull('parent_id')
      ->with('likes', function($query) {
        $query->with('author')->orderByDesc('id')->limit(4);
      })
      // ->with('likes.author')
      ->when(!is_null($limit), fn($q) => $q->limit($limit))
      ->withCount('likes')
      ->with('author')
      ->get(); 

    
    foreach ($this->comments as $key => &$comment) {
      if ($this->comments_loading && $comment->children()->exists()) {
        $comment->children = $this->getChildren($comment);
      }
    }

    $this->comments = $this->comments->toArray();
    return $this;
  }


  public function getChildren($review, int $max_level = 1)
  {
    $this->level++;
    $review->load('likes.author', 'author');
    $review->loadCount('likes', 'children');

    if ($this->level > $max_level) {
      $this->level = 0;
      return;
    }

    $review->load('children');

    foreach ($review->children as &$child) {
        if ($child->children()->exists()) {
          $this->getChildren($child);
        } else {
          $child->load('likes.author', 'author');
          $child->loadCount('likes');
          $this->level = 0;
        }
    }
  }


  public function getTogetherProducts(int $limit = 10, array $includes = []): Collection
  {
    $products = $this->query()
      ->where('id', '!=', $this->id)
      ->whereHas('location', fn($q) => $q->where('slug', $this->location->slug))
      ->orWhereHas('type', fn($q) => $q->where('slug', $this->type->slug))
      ->limit($limit)
      ->get();

    while($products->count() < $limit) {
      $products = $products->collect()->merge($products)->slice(0, $limit);
    }

    return $products;
  }

  public static function getTrendingProducts(int $limit = 10, array $includes = []): Collection
  {
    $products = \App\Models\Product::query()
      ->when(
        !empty($includes),
        fn($query) => $query->whereIn('id', $includes)->orWhere('id', '>', 0),
      )
      ->with('preview', 'location', 'categories', 'type')
      ->withCount(['reviews' => function($query) {
        $query->whereNull('parent_id');
      }])
      ->limit($limit)
      ->get();

    while($products->count() < $limit) {
      $products = $products->collect()->merge($products)->slice(0, $limit);
    }

    return $products;
  }

  public static function findByPid(string $pid): ?Product
  {
    $rdata = CustomEncrypt::decodeUrlHash($pid);
    $id = isset($rdata['id']) ? $rdata['id'] : null;

    return static::where('id', $id)->with('author')->withCount('reviews')->first();
  }

  public static function getAnalogs(int $product_id = null)
  {
    return static::latest()->limit(10)->get();
  }
}
