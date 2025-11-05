<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserOptions extends Model
{

    protected $guarded = [];

    protected $casts = [
        'collaboration' => 'boolean',
        'return_policy_id' => 'integer',
        'creator_visible' => 'boolean',
        'show_donate' => 'boolean',
        'show_products' => 'boolean',
        'show_insights' => 'boolean',
        'notification_settings' => 'array',
    ];

    protected float $default_fee = 10;
    protected float $default_space = 0.3;
    protected float $default_sales_treshold = 100;

    public static function boot()
    {
      parent::boot();

      parent::created(function(Model $model) {
        if (empty($model->avatar)) {
          $model->update(['avatar' => '/storage/images/default_avatar.png	']);
        }
      });
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function country()
    {
      return $this->belongsTo(Country::class);
    }

    public function level()
    {
      return $this->belongsTo(Level::class);
    }

    public function getSocial(): array
    {
      return [
        'youtube' => $this->youtube,
        'tiktok' => $this->tiktok,
        'facebook' => $this->facebook,
        'instagram' => $this->instagram,
        'google' => $this->google,
        'xai' => $this->xai,
        'website' => $this->website,
        'other' => $this->other,
      ];
    }

    public static function getSocialIcons(): array
    {
      return [
        'youtube' => asset('assets/img/icons/youtube.svg'),
        'tiktok' => asset('assets/img/icons/tiktok.svg'),
        'facebook' => asset('assets/img/icons/facebook.svg'),
        'instagram' => asset('assets/img/icons/insta.svg'),
        'google' => asset('assets/img/icons/google.svg'),
        'xai' => asset('assets/img/icons/xai.svg'),
        'website' => asset('assets/img/icons/web.svg'),
        'other' => asset('assets/img/icons/web.svg'),
      ];
    }

    public static function getSocialLables(): array
    {
      return [
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'google' => 'Google',
        'xai' => 'XAI',
        'website' => 'Website',
        'other' => 'Other',
      ];
    }

    public function getFee(): float
    {
      try {
        return $this->fee ? $this->fee : $this->level->fee;
      } catch (\Exception $e) {
        Log::error('Error while get fee', ['error' => $e]);
        return $this->default_fee;
      }
    }

    public function getStorageSpace(): float
    {
      try {
        return $this->space ? $this->space : $this->level->space;
      } catch (\Exception $e) {
        Log::error('Error while get storage space', ['error' => $e]);
        return $this->default_space;
      }
    }

    public function getSaleTreshold(): float
    {
      try {
        return $this->sales_treshold ? $this->sales_treshold : $this->level->sales_treshold;
      } catch (\Exception $e) {
        Log::error('Error while get sale_treshold', ['error' => $e]);
        return $this->default_sales_treshold;
      }
    }
}
