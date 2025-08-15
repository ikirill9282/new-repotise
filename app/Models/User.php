<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\Collapse;
use App\Helpers\SessionExpire;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Filament\Panel;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\HasCart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmRegitster;
use App\Events\MailVerify;
use App\Mail\ResetCode;
use App\Models\Options;
use App\Events\MailReset;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Cashier;
use App\Helpers\CustomEncrypt;
use Stripe\Identity\VerificationSession;

class User extends Authenticatable implements HasName, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Searchable, HasCart, Billable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public $appends = [
    //   'profile',
    //   'avatar',
    // ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string
    {
      return $this->username;
    }

    protected static function boot()
    {
      parent::boot();

      self::creating(function($model) {
        $username = preg_replace("/^(.*?)@.*$/is", "$1", $model->email);
        while (static::where('username', $username)->exists()) {
          $username = "$username" . random_int(0, 100);
        }

        $model->username = $username;
        $model->name = ucfirst($username);
      });

      self::created(function($model) {
        $model->resetBackup();
        $model->createOrGetStripeCustomer([
          'metadata' => [
            'user_id' => $model->id,
          ]
        ]);
        $model->options()->create(['description' => null]);
        $model->notifications()->create([
          'type' => 'info',
          'message' => 'Welcome to TrekGuider! Please, complete your profile and verify your email address.',
        ]);
      });

      self::saving(function($model) {
        if (!empty($model->original) && ($model->password !== $model->original['password'])) {
          $model->resetBackup();
        }
      });
    }

    public function toSearchableArray(bool $load_options = true): array
    {
        $array = $this->toArray();
        $array['avatar'] = $this->avatar;

        if ($load_options) {
          $array['options'] = $this->options?->toArray();
        }
        $array['description'] = $this->description;
        $array['followers_count'] = $this->loadCount('followers')->followers_count;
        
        $products = $this->products()
          ->with([
            'categories' => function ($query) {
              $query->select(['categories.id', 'categories.title']);
            },
            'location' => function ($query) {
              $query->select(['locations.id', 'locations.title']);
            },
            'type' => function ($query) {
              $query->select(['types.id', 'types.title']);
            },
          ])
          ->select(['id', 'title', 'type_id', 'location_id', 'user_id'])
          ->get()
          ->toArray();
        
        $array['products'] = collect($products)->select('id', 'title')->toArray();
        $array['categories'] = collect($products)->pluck('categories')
          ->flatten(1)
          ->select(['id', 'title'])
          ->unique('id')
          ->toArray()
        ;
        $array['types'] = collect($products)->pluck('type')
          ->unique('id')
          ->toArray()
        ;
        
        $array['locations'] = collect($products)->pluck('location')
          ->unique('id')
          ->toArray()
        ;
        
        return $array;
    }

    public function reviews()
    {
      return $this->hasMany(Review::class);
    }

    public function comments()
    {
      return $this->hasMany(Comment::class);
    }

    public function articles()
    {
      return $this->hasMany(Article::class);
    }

    public function discounts()
    {
      return $this->hasMany(Discount::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
      return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    public function funds()
    {
      return $this->hasMany(UserFunds::class);
    }

    public function notifications()
    {
      return $this->hasMany(UserNotification::class);
    }

    public function backup()
    {
      return $this->hasMany(UserBackup::class);
    }

    public function verify()
    {
      return $this->hasMany(UserVerify::class);
    }

    public function options()
    {
      return $this->hasOne(UserOptions::class);
    }

    public function products()
    {
      return $this->hasMany(Product::class);
    }

    public function orders()
    {
      return $this->hasMany(Order::class);
    }

    public function owner()
    {
      return $this->hasOneThrough(User::class, UserReferal::class, 'referal_id', 'id', 'id', 'owner_id');
    }

    public function referals()
    {
      return $this->hasManyThrough(User::class, UserReferal::class, 'owner_id', 'id', 'id', 'referal_id');
    }

    public function referal_buyers()
    {
      return $this->referals()->whereNotNull('email_verified_at')->whereHas('orders', fn($query) => $query->where('orders.status_id', '>=', 2));
    }

    public function referal_codes()
    {
      return $this->discounts()->where(['group' => 'referal', 'type' => 'promocode']);
    }

    public function referal_free_products()
    {
      return $this->discounts()->where(['group' => 'referal', 'type' => 'freeproduct']);
    }

    public function referal_income()
    {
      return $this->funds()->where(['group' => 'referal']);
    }

    public function favorite_products()
    {
      return $this->hasManyThrough(Product::class, UserFavorite::class, 'user_id', 'id', 'id', 'item_id')
        ->where('type', 'product')
        ->orderByDesc('user_favorites.created_at');
    }

    public function followers()
    {
      return $this->belongsToMany(User::class, 'followers', 'author_id', 'subscriber_id', 'id', 'id');
    }

    public function hasFollower(?int $user_id): bool
    {
      if (is_null($user_id)) return false;

      return $this->followers()->where('subscriber_id', $user_id)->exists();
    }

    public function favorite_authors()
    {
      return $this->hasManyThrough(User::class, UserFavorite::class, 'user_id', 'id', 'id', 'item_id')
        ->where('type', 'author')
        ->orderByDesc('user_favorites.created_at');
    }

    public function favoriteCount(): Attribute
    {
      return Attribute::make(
        get: fn() => $this->favorite_products()->count() + $this->favorite_authors()->count(),
      );
    }

    public function followersCount(): Attribute
    {
      return Attribute::make(
        get: fn($value) => Collapse::make($value ?? 0),
      );
    }

    public function profile(): Attribute
    {
      return Attribute::make(
        get: fn() => "@" . strtolower($this->username),
      );
    }

    public function name(): Attribute
    {
      return Attribute::make(
        get: fn() => ucfirst($this->username),
      );
    }

    public function description(): Attribute
    {
      return Attribute::make(
        get: fn($val) => $this->options?->description,
      );
    }

    public function avatar(): Attribute
    {
      return Attribute::make(
        get: fn() => $this->options?->avatar,
      );
    }

    public static function makePassword(): string
    {
      $pass = substr(trim(base64_encode(random_bytes(10)), '='), -10);
      if (!static::validatePassword($pass)) {
        return static::makePassword();
      }
      return preg_replace('/[^0-9a-zA-Z]+/is', '', $pass);
    }

    public static function validatePassword(string $password): bool
    {
      return preg_match( '/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_\-+=]{8,}$/is', $password);
    }

    public function makeProfileUrl(): string
    {
      // return $this->hasRole('creator') ? url("/profile/@$this->username") : route('profile.purchases') ;
      return url("/profile/@$this->username");
    }

    public function makeProfileVerificationUrl(): string 
    {
      return url('/profile/verify');
    }

    public function makeCompletetVerifyUrl(): string
    {
      return url('/profile/verify/complete' . '/?token=' . CustomEncrypt::generateUrlHash(['id' => $this->id]));
      // return url('/profile/verify/complete');
    }

    public function makeSubscribeUrl(): string
    {
      return url("/profile/subscribe/$this->profile");
    }

    public function makeReferalUrl(?string $source = null): string
    {
      $url = url('/referal?referal='. CustomEncrypt::generateUrlHash([
        'id' => $this->id, 
        'created_at' => $this->created_at->format('Y-m-d'),
      ], false));
      $route_url = urlencode($url);
      $title = urlencode('Discover your next adventure together!');

      return match($source) {
        'FB' => "http://www.facebook.com/share.php?u=$route_url&title=$title",
        'TW' => "https://twitter.com/intent/tweet?text=" . ($title." ".$route_url),
        'PI' => "http://pinterest.com/pin/create/link/?url=$route_url&description=$title",
        'GM' => "https://mail.google.com/mail/u/0/?ui=2&fs=1&tf=cm&su=$title&body=Link:+$route_url",
        'WA' => "https://wa.me/?text=$title $route_url",
        'TG' => "https://t.me/share/url?url=$route_url&text=$title",
        default => $url
      };
    }

    public function makeReferalProductUrl(?string $source = null, Product $product): string
    {
      $url = $product->makeUrl() . '&referal='. CustomEncrypt::generateUrlHash([
        'id' => $this->id, 
        'created_at' => $this->created_at->format('Y-m-d'),
      ], false);
      $route_url = urlencode($url);
      $title = urlencode('Discover your next adventure together!');

      return match($source) {
        'FB' => "http://www.facebook.com/share.php?u=$route_url&title=$title",
        'TW' => "https://twitter.com/intent/tweet?text=" . ($title." ".$route_url),
        'PI' => "http://pinterest.com/pin/create/link/?url=$route_url&description=$title",
        'GM' => "https://mail.google.com/mail/u/0/?ui=2&fs=1&tf=cm&su=$title&body=Link:+$route_url",
        'WA' => "https://wa.me/?text=$title $route_url",
        'TG' => "https://t.me/share/url?url=$route_url&text=$title",
        'RD' => "http://www.reddit.com/submit?url=$route_url&title=$title",
        default => $url
      };
    }

    public function makeReferalArticleUrl(?string $source = null, Article $article): string
    {
      $url = $article->makeFeedUrl() . '&referal='. CustomEncrypt::generateUrlHash([
        'id' => $this->id, 
        'created_at' => $this->created_at->format('Y-m-d'),
      ], false);
      $route_url = urlencode($url);
      $title = urlencode('Discover your next adventure together!');

      return match($source) {
        'FB' => "http://www.facebook.com/share.php?u=$route_url&title=$title",
        'TW' => "https://twitter.com/intent/tweet?text=" . ($title." ".$route_url),
        'PI' => "http://pinterest.com/pin/create/link/?url=$route_url&description=$title",
        'GM' => "https://mail.google.com/mail/u/0/?ui=2&fs=1&tf=cm&su=$title&body=Link:+$route_url",
        'WA' => "https://wa.me/?text=$title $route_url",
        'TG' => "https://t.me/share/url?url=$route_url&text=$title",
        'RD' => "http://www.reddit.com/submit?url=$route_url&title=$title",
        default => $url
      };
    }

    public function makeStripeVerificationUrl(): ?string
    {
      $verify = $this->verify()->where('type', 'stripe')->first();
      if (!$verify) return null;

      $session_verify = Cashier::stripe()->identity->verificationSessions->retrieve($verify->code);
      
      if ($session_verify['status'] === 'verified') {
        return $this->makeCompletetVerifyUrl();
      }

      return $session_verify->url;
    }

    public function getName(): string
    {
      return $this->name;
    }

    public function hasFavorite(int $id, string $type)
    {
      return UserFavorite::where(['user_id' => Auth::user()->id, 'type' => $type, 'item_id' => $id])->exists();
    }

    public function getRecomendProducts(int $limit = 6): Collection
    {
      return Product::limit($limit)->orderByDesc('id')->get();
    }

    public function getRecomendAuthors(): Collection
    {
      return User::role('creator')->limit(6)->orderByDesc('id')->get();
    }

    public function sendVerificationCode(bool $seller = false)
    {
      $mail = new ConfirmRegitster($this, $seller);
      Mail::to($this->email)->send($mail);
      MailVerify::dispatch($this);
    }

    // TODO: Rework code generation to this method.
    public function sendResetCode()
    {
      $mail = new ResetCode($this);
      Mail::to($this->email)->send($mail);
      MailReset::dispatch($this);
    }

    public function getVerifyUrl(bool $seller = false): string
    {
      return url('/auth/email/verify/?' . http_build_query(['confirm' => $this->generateVerify(), 'seller' => $seller]));
    }

    public function generateVerify(array $params = []): string
    {
      if ($this->verify()->where('type', 'email')->exists()) {
        return Crypt::encrypt(array_merge(['code' => $this->verify()->where('type', 'email')?->code], $params));
      }
      
      $code = UserVerify::genCode();
      $this->verify()->firstOrCreate(['code' => $code], ['created_at' => Carbon::now()->timestamp, 'type' => 'email']);

      return Crypt::encrypt(array_merge(['code' => $code], $params));
    }

    public function getResetCode(): int
    {
      $code = random_int(100000, 999999);
      
      if ($this->verify()->where('type', 'reset')->exists()) {
        $model = $this->verify()->where('type', 'reset')->first();
        $model->update(['code' => $code]);
      } else {
        $model = $this->verify()->firstOrCreate(
          ['code' => $code], 
          [
            'created_at' => Carbon::now()->timestamp, 
            'type' => 'reset'
          ]
        );
      }

      SessionExpire::set('reset_password_email', $this->email, Carbon::now()->addHour());
      SessionExpire::set('reset_password_code', $this->code, Carbon::now()->addMinutes(3));

      return $model->code;
    }

    public function getStripeVerify(): ?UserVerify
    {
      return $this->verify()->where('type', 'stripe')->first();
    }

    public function getStripeVerifySession(): ?VerificationSession
    {
      $verify = $this->getStripeVerify();
      if (!$verify) return null;
      
      return Cashier::stripe()->identity->verificationSessions->retrieve($verify->code);
    }

    public function resetBackup()
    {
      $this->backup()->delete();
      $codes = UserBackup::generate();
      foreach ($codes as $code) {
        $this->backup()->create(['code' => $code]);
      }
    }

    public function canWriteComment()
    {
      return Auth::check();
    }

    public function canWriteReview(Product $product, string $type = 'review'): bool
    {

      $orders = Order::query()
        ->where('orders.status_id', '>=', 1)
        ->whereHas('order_products', fn($query) => $query->where('order_products.product_id', $product->id))
        ->where(fn($query) => $query->where('orders.user_id', $this->id)->orWhere('orders.recipient', $this->email))
        // ->ddRawSql()
        ->get()
      ;

      if ($orders->isEmpty()) {
        return false;
      }

      foreach ($orders as $order) {
        $result = false;

        if ($order->gift) {
          if ($order->recipient == $this->email) {
            $result = true;
          }
        } else {
          if ($order->user_id == $this->id) {
            $result = true;
          }
        }

        if ($result) {
          return !Review::where([
            'product_id' => $product->id,
            'user_id' => $this->id,
            'parent_id' => null,
          ])
          ->exists();
        }
      }

      return false;
    }
}
