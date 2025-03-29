<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\Collapse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $appends = [
      'profile',
      'avatar',
    ];

    protected static function boot()
    {
      parent::boot();

      self::creating(function($model) {
        $username = preg_replace("/^(.*?)@.*$/is", "$1", $model->email);
        while (static::where('username', $username)->exists()) {
          $username = "$username" . random_int(0, 100);
        }

        $model->username = $username;
      });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function followersCount(): Attribute
    {
      return Attribute::make(
        get: fn($value) => Collapse::make($value),
      );
    }

    public function profile(): Attribute
    {
      return Attribute::make(
        get: fn() => "@" . $this->username,
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
          ->where('name', 'avatar')
          ->first()
          ?->pivot->value,
      );
    }

    public function followers()
    {
      return $this->belongsToMany(User::class, 'followers', 'author_id', 'subscriber_id', 'id', 'id');
    }

    public function getFilamentName(): string
    {
      return $this->username;
    }

    public function makeProfileUrl(): string
    {
      return url("/profiles/" . $this->profile());
    }

    public function getName(): string
    {
      return ucfirst($this->username);
    }

    public function options()
    {
      return $this->belongsToMany(Options::class, 'user_options', 'user_id', 'option_id')->withPivot(['value']);
    }

    // public function profile()
    // {
    //   return "@$this->username";
    // }

    // public function avatar()
    // {
    //   return $this->options()->where('type', 'image')->where('name', 'avatar');
    // }
}
