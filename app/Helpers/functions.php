<?php

use App\Helpers\CustomEncrypt;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Likes;
use Illuminate\Support\Facades\Crypt;

if (! function_exists('print_var')) {
  function print_var($name, Collection|array|null $resource = null)
  {
    if (empty($resource)) {
      return null;
    }
    try {
      return ($resource->has($name) ? $resource->get($name)?->value ?? null : null);
    } catch (\Error $e) {
      return null;
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
    return CustomEncrypt::generateUrlHash([
      'user_id' => Auth::user()?->id ?? null,
      'type' => $type,
      'model_id' => $id,
    ]);
  }
}

if (! function_exists('hash_more')) {
  function hash_more(array $comment): string
  {
    return CustomEncrypt::generateUrlHash([
      'id' => $comment['id'],
    ]);
  }
}

if (! function_exists('is_liked')) {
  function is_liked(string $type, string|int $id): string
  {
    if (!Auth::check()) return false;
    return Likes::where(['type' => $type, 'model_id' => $id, 'user_id' => Auth::user()?->id])->exists();
  }
}

if (! function_exists('enable_more')) {
  function enable_more(array $comment): bool
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

if (! function_exists('currency')) {
  function currency(int|float|null $value): ?string
  {
    if (is_null($value)) return null;
    
    $decimalPart = strstr((string)$value, '.');
    $decimals = $decimalPart !== false ? 2 : 0;

    return '$' . number_format($value, $decimals, '.', ',');
  }
}

if (! function_exists('money')) {
  /**
   * Format money amount with currency symbol and proper formatting
   * Uses platform currency from config
   * 
   * @param int|float|null $amount
   * @param int $decimals
   * @return string|null
   */
  function money(int|float|null $amount, int $decimals = 2): ?string
  {
    if (is_null($amount)) {
      return null;
    }
    
    $currency = config('cashier.currency', 'usd');
    $formatted = number_format((float) $amount, $decimals, '.', ',');
    
    $symbol = match(strtoupper($currency)) {
      'USD' => '$',
      'EUR' => '€',
      'GBP' => '£',
      default => strtoupper($currency) . ' ',
    };
    
    return $symbol . $formatted;
  }
}

if (! function_exists('settings')) {
  /**
   * Get or set system settings
   * 
   * @param string|null $key
   * @param mixed $default
   * @return mixed|array
   */
  function settings(?string $key = null, $default = null)
  {
    if ($key === null) {
      return \App\Models\SystemSetting::allAsArray();
    }

    return \App\Models\SystemSetting::get($key, $default);
  }
}

if (! function_exists('ga4_measurement_id')) {
  /**
   * Get GA4 Measurement ID (G-XXXXXXXXXX) for frontend tracking
   * First tries to get from Integration model, then from env/config
   * 
   * @return string|null
   */
  function ga4_measurement_id(): ?string
  {
    try {
      // Try to get from Integration model first
      $integration = \App\Models\Integration::where('name', 'ga4')
        ->where('status', \App\Models\Integration::STATUS_ACTIVE)
        ->first();
      
      if ($integration && $integration->getConfig('measurement_id')) {
        return $integration->getConfig('measurement_id');
      }
    } catch (\Exception $e) {
      // Fallback to config if Integration doesn't exist or error
    }
    
    // Fallback to config/env
    return config('services.ga4.measurement_id');
  }
}

if (! function_exists('stripe_key')) {
  /**
   * Get Stripe Publishable Key
   * First tries to get from Integration model, then from env/config
   * 
   * @return string|null
   */
  function stripe_key(): ?string
  {
    try {
      // Try to get from Integration model first
      $integration = \App\Models\Integration::where('name', 'stripe')
        ->where('status', \App\Models\Integration::STATUS_ACTIVE)
        ->first();
      
      if ($integration && $integration->getConfig('api_key')) {
        return $integration->getConfig('api_key');
      }
    } catch (\Exception $e) {
      // Fallback to config if Integration doesn't exist or error
    }
    
    // Fallback to config/env
    return config('services.stripe.key') ?? config('cashier.key');
  }
}

if (! function_exists('stripe_secret')) {
  /**
   * Get Stripe Secret Key
   * First tries to get from Integration model, then from env/config
   * 
   * @return string|null
   */
  function stripe_secret(): ?string
  {
    try {
      // Try to get from Integration model first
      $integration = \App\Models\Integration::where('name', 'stripe')
        ->where('status', \App\Models\Integration::STATUS_ACTIVE)
        ->first();
      
      if ($integration && $integration->getConfig('secret_key')) {
        return $integration->getConfig('secret_key');
      }
    } catch (\Exception $e) {
      // Fallback to config if Integration doesn't exist or error
    }
    
    // Fallback to config/env
    return config('services.stripe.secret') ?? config('cashier.secret');
  }
}

if (! function_exists('stripe_webhook_secret')) {
  /**
   * Get Stripe Webhook Secret
   * First tries to get from Integration model, then from env/config
   * 
   * @return string|null
   */
  function stripe_webhook_secret(): ?string
  {
    try {
      // Try to get from Integration model first
      $integration = \App\Models\Integration::where('name', 'stripe')
        ->where('status', \App\Models\Integration::STATUS_ACTIVE)
        ->first();
      
      if ($integration && $integration->getConfig('webhook_secret')) {
        return $integration->getConfig('webhook_secret');
      }
    } catch (\Exception $e) {
      // Fallback to config if Integration doesn't exist or error
    }
    
    // Fallback to config/env
    return config('services.stripe.webhook_secret') ?? config('cashier.webhook.secret');
  }
}