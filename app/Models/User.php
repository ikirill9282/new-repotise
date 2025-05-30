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

class User extends Authenticatable implements HasName, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Searchable, HasCart;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $appends = [
      'profile',
      'avatar',
      'name',
    ];

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
    }

    public function toSearchableArray(bool $load_options = true): array
    {
        $array = $this->toArray();

        if ($load_options) {
          $array['options'] = $this->options->toArray();
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

    public function canAccessPanel(Panel $panel): bool
    {
      return $this->hasRole('admin');
    }

    public function verify()
    {
      return $this->hasOne(UserVerify::class);
    }

    public function options()
    {
      return $this->belongsToMany(Options::class, 'user_options', 'user_id', 'option_id')->withPivot(['value']);
    }

    public function products()
    {
      return $this->hasMany(Product::class);
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
        get: fn($val) => $this->options()
          ->where([
            'type' => 'text',
            'name' => 'description',
          ])
          ->first()
          ?->pivot->value,
      );
    }

    public function avatar(): Attribute
    {
      return Attribute::make(
        get: fn() => $this->options()
          ->where([
            'type' => 'image',
            'name' => 'avatar',
          ])
          ->first()
          ?->pivot->value,
      );
    }

    public function makeProfileUrl(): string
    {
      return url("/profile");
    }

    public function makeProfileVerificationUrl(): string 
    {
      return $this->makeProfileUrl() . '/verify';
    }

    public function makeSubscribeUrl(): string
    {
      return url("/profile/subscribe/$this->profile");
    }

    public function getName(): string
    {
      return ucfirst($this->username);
    }

    public function hasFavorite(int $id, string $type)
    {
      return UserFavorite::where(['user_id' => Auth::user()->id, 'type' => $type, 'item_id' => $id])->exists();
    }

    public function getRecomendProducts(): Collection
    {
      return Product::limit(6)->orderByDesc('id')->get();
    }

    public function getRecomendAuthors(): Collection
    {
      return User::role('creator')->limit(6)->orderByDesc('id')->get();
    }

    public function sendVerificationCode(bool $seller = false)
    {
      $mail = new ConfirmRegitster($this);
      Mail::to($this->email)->send($mail);
      MailVerify::dispatch($this);
    }

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
      if ($this->verify()->where('type', 'reset')->exists()) {
        return $this->verify()->where('type', 'reset')->first()?->code;
      }

      $code = random_int(100000, 999999);
      $model = $this->verify()->firstOrCreate(['code' => $code], ['created_at' => Carbon::now()->timestamp, 'type' => 'reset']);

      return $model->code;
    }

    public function makeDefaultOptions()
    {
      foreach (Options::where('default', 1)->get() as $option) {
        $this->options()->sync([
          $option->id => ['value' => $option->getDefaultValue()],
        ], false);
      }
    }
}
