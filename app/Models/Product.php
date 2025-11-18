<?php

namespace App\Models;

use App\Helpers\Collapse;
use App\Helpers\CustomEncrypt;
use App\Helpers\Slug;
use App\Services\StripeClient;
use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use App\Traits\HasMessages;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Laravel\Scout\Searchable;
use Mews\Purifier\Facades\Purifier;


class Product extends Model
{
  use HasAuthor, HasGallery, Searchable, HasFactory, HasStatus, HasMessages;

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      // PURIFY
      $model->title = Purifier::clean($model->title);
      $model->text = Purifier::clean($model->text);
      $model->seo_title = Purifier::clean($model->seo_title);
      $model->seo_text = Purifier::clean($model->seo_text);
      $model->title = str_replace('&amp;', '&', $model->title);

      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });

    self::created(function($model) {

      // Push to Stripe
      // $stripe_client = new StripeClient();
      // $stripe_product = $stripe_client->createProduct($model);

      if ($model->subscription) {
        if (!$model->subprice()->exists()) {
          $model->subprice()->create();
        }
        // $stripe_client->createPrices($model, $stripe_product);
      }
    });

    self::updating(function ($model) {
      // PURIFY
      $model->title = Purifier::clean($model->title);
      $model->text = Purifier::clean($model->text);
      $model->seo_title = Purifier::clean($model->seo_title);
      $model->seo_text = Purifier::clean($model->seo_text);
      $model->title = str_replace('&amp;', '&', $model->title);

      if ($model->isDirty('title')) {
        $model->generateSlug();
      }
    });

    self::updated(function($model) {
      $model->searchable();
      
    });
  }

  public function publishInStripe()
  {
    $stripe_client = new StripeClient();
    $stripe_product = $stripe_client->createProduct($this);
    if ($this->subscription) {
      $stripe_client->createPrices($this, $stripe_product);
    } else {
      $stripe_client->createPrice($this, $stripe_product);
    }
  }

  public function toSearchableArray(): array
  {
    $this->load('author', 'categories', 'types', 'locations', 'preview')->loadCount('reviews');

    $array = $this->toArray();

    $array['author'] = array_merge(
      $this->author->only('profile', 'name', 'avatar', 'description'),
      ['username' => $this->author->username, 'slug' => $this->author->username]
    );
    $array['categories'] = $this->categories->select(['id', 'parent_id', 'title'])->toArray();
    $array['type'] = $this->types->select(['id', 'title', 'slug'])->toArray();
    $array['location'] = $this->locations->select(['id', 'title', 'slug'])->toArray();
    $array['preview'] = $this->preview?->image ?? '';
    $array['reviews_count'] = $this->reviews_count;
    $array['calcedPrice'] = $this->getPrice();
    $array['priceWithoutDiscount'] = $this->getPriceWithoutDiscount();

    return $array;
  }

  private function generateSlug(bool $salt = false)
  {
    $this->slug = Slug::makeEn($this->title) . ($salt ? random_int(0, 10000) : '');
    if (Product::where('slug', $this->slug)->exists()) {
      return $this->generateSlug(true);
    }
  }

  public function files()
  {
    return $this->hasMany(ProductFiles::class)->whereNull('expires_at');
  }

  public function links()
  {
    return $this->hasMany(ProductLinks::class);
  }

  public function subprice()
  {
    return $this->hasOne(Subprice::class);
  }
  
  public function categories()
  {
    return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id', 'id', 'id');
  }

  public function types()
  {
    return $this->belongsToMany(Type::class, ProductTypes::class, 'product_id', 'type_id', 'id', 'id');
  }

  public function locations()
  {
    return $this->belongsToMany(Location::class, ProductLocations::class, 'product_id', 'location_id', 'id', 'id');
  }

  public function reviews()
  {
    return $this->hasMany(Review::class);
  }

  public function messages()
  {
    return $this->hasMany(Review::class)->orderByDesc('id');
  }

  public function favorite()
  {
    return $this->belongsToMany(User::class, UserFavorite::class, 'item_id', 'user_id', 'id', 'id')->where('type', 'product');
  }

  public function getPrice()
  {
    return $this->price - $this->sale_price;
  }

  public function getPriceWithoutDiscount()
  {
    if ($this->getPrice() == $this->price) return null;

    return $this->price;
  }

  public function getText(): string
  {
    return "<div class='user-custom-text'>$this->text</div>";
  }

  public function prepareRatingImages()
  {
    // Если отзывов нет - показываем 0 звезд (все пустые)
    if (($this->reviews_count ?? 0) == 0) {
      return rating_images(0);
    }
    
    // Если отзывы есть - вычисляем средний рейтинг из отзывов
    $avgRating = $this->reviews()
      ->whereNull('parent_id')
      ->avg('rating');
    
    return rating_images($avgRating ?? 0);
  }

  public function reviewsCount(): Attribute
  {
    $reviews = $this->messages()->whereNull('parent_id')->count();
    return Attribute::make(
      get: fn($value) => ($reviews == 0) ? 0 : Collapse::make($reviews),
    );
  }

  public function countComments(): int
  {
    return $this->reviews_count ?? 0;
  }

  public function makeUrl()
  {
    return url("/products/$this->slug?pid=" . CustomEncrypt::generateUrlHash(['id' => $this->id]));
  }

  public function makeEditUrl()
  {
    return route('profile.products.create') . '?pid=' . Crypt::encrypt($this->id);
  }

  public function makeEditMediaUrl()
  {
    return route('profile.products.create.media') . '?pid=' . Crypt::encrypt($this->id);
  }


  public function makeShareUrl(?string $source = null)
  {
    $url = $this->makeUrl();
    $route_url = urlencode($url);
    $title = urlencode('Discover your next adventure together!');

    return match($source) {
      'FB' => "http://www.facebook.com/share.php?u=$route_url&title=$title",
      'TW' => "https://twitter.com/intent/tweet?text=" . ($title." ".$route_url),
      'PI' => "http://pinterest.com/pin/create/link/?url=$route_url&description=$title",
      'GM' => "https://mail.google.com/mail/u/0/?ui=2&fs=1&tf=cm&su=$title&body=Link:+$route_url",
      'WA' => "https://wa.me/?text=$title $route_url",
      'TG' => "https://t.me/share/url?url=$route_url&text=$title",
      'RD' => "https://www.reddit.com/submit?url=$url&title=$title",
      default => $url
    };
  }

  public function getAllReviews(?int $limit = 10)
  {
    $this->limit = $limit;
    $this->messages_loading = true;
    $this->level = 0;

    $this->messages = Review::query()
      ->where('product_id', $this->id)
      ->whereNull('parent_id')
      ->with('likes', function($query) {
        $query->with('author.options')->orderByDesc('id')->limit(4);
      })
      // ->with('likes.author')
      ->when(!is_null($limit), fn($q) => $q->limit($limit))
      ->withCount('likes')
      ->with('author.options')
      ->get();

    // dd($this->messages);
    
    foreach ($this->messages as $key => &$comment) {
      if ($this->messages_loading && $comment->children()->exists()) {
        $this->getChildren($comment);
      }
    }

    // $this->messages = $this->messages->toArray();
    
    return $this;
  }

  public function getChildren($review, int $max_level = 1)
  {
    $this->level++;
    $review->load('likes.author.options', 'author.options');
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
          $child->load('likes.author.options', 'author.options');
          $child->loadCount('likes');
          $this->level = 0;
        }
    }
  }

  public function getTogetherProducts(int $limit = 10, array $includes = []): Collection
  {
    $products = $this->query()
      ->where('id', '!=', $this->id)
      ->whereHas('locations', fn($q) => $q->whereIn('locations.id', $this->locations->pluck('id')))
      ->orWhereHas('types', fn($q) => $q->whereIn('types.id', $this->types->pluck('id')))
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
      ->with('preview', 'locations', 'categories', 'types')
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

    return static::where('id', $id)->with('author.options')->withCount('reviews')->first();
  }

  public static function getAnalogs(int $product_id = null)
  {
    return static::latest()->limit(10)->get();
  }
}
