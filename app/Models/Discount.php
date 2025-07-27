<?php

namespace App\Models;

use App\Enums\Action;
use App\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promocode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Discount extends Model
{
    use HasAuthor;

    public static function boot()
    {
      parent::boot();

      static::creating(function($model) {
        $model->code = static::generateCode();
        if (empty($model->end)) $model->end = Carbon::today()->modify('+1 month')->format('Y-m-d H:i:s');
      });
    }

    public function author()
    {
      return $this->belongsTo(User::class, 'author_id');
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function orders()
    {
      return $this->hasMany(Order::class);
    }

    public function sendToUsers()
    {
      foreach ($this->users as $user) {
        Mail::to($user->email)->send(new Promocode($this));
      }
    }

    public function calcOrderDiscount(Order $order): int
    {
      $discount = 0;

      if ($this->type == 'promocode') {
        if ($this->target == 'cart') {
          $amount = $order->getAmount();

          if (!is_null($this->percent)) {
            $discount = round($amount / 100 * $this->percent, 2);
            $discount = ($discount > $this->max) ? $this->max : $discount;
          }
        }
      }

      if ($this->type == 'freeproduct') {
        // if ($this->group == 'referal') {
          $res = [];
          foreach ($order->order_products as $op) {
            if ($op->price > 50) continue;
            if ($op->product->author->id > 0 && $op->price > 25) continue;
            
            $res[$op->product_id] = $op->price;
          }
          $cost = max($res);
          $product_id = array_search($cost, $res);
          $product = $order->order_products->where('product_id', $product_id)->first();

          return $product->price;
        // }
      }

      return $discount;
    }


    public function complete()
    {
      $this->increment('attempts');
      if ($this->limit == $this->attempts) {
        $this->update(['active' => 0]);
      }

      // if ($this->type == 'promocode') {
      //   if ($this->limit == 1) {
      //     $this->update(['attempts' => 1, 'active' => 0]);
      //   } else {
      //     $this->increment('attempts');
      //     if ($this->limit == $this->attempts) {}
      //   }
      // }

      // if ($this->type == 'freeproduct') {
      //   $this->update(['attempts' => 1, 'active' => 0]);
      // }
    }


    public function isAvailable(Order $order): bool
    {
      if (!$this->active) {
        return false;
      }

      if ($this->visibility == 'private' && $this->user_id !== $order->user_id) {
        return false;
      }

      if (Carbon::today()->gt(Carbon::parse($this->end))) {
        return false;
      }
      if ($this->limit == 0 || $this->limit == $this->attempts) {
        return false;
      }

      if ($this->type == 'promocode') {
      }

      if ($this->type == 'freeproduct') {
        // dd($order->order_products);
        foreach ($order->order_products as $op) {
          if ($op->product->author->id === 0 && $op->price < 50) {
            return true;
          } elseif ($op->price < 25) {
            return true;
          }
        }

        return false;
      }

      return true;
    }

    public static function createForUsers(array $users, array $data): void
    {
      foreach ($users as $user) {
        $user = is_object($user) ? $user : User::find($user);
        $formatted = array_merge(['user_id' => $user->id], $data);
        $promocode = static::create($formatted);
        Mail::to($user->email)->send(new Promocode($promocode));
        History::info()->create([
          'type' => 'referal',
          'user_id' => $user->id,
          'action' => Action::REFERAL_PROMOCODE_CREATED,
          'message' => 'Create promocode by referal registration.',
          'value' => $promocode->code,
        ]);
      }
    }

    public static function generateCode(): string
    {
      $str = str_shuffle(trim(base64_encode(random_bytes(10)), '='));
      $str = preg_replace('/[^a-zA-Z]/is', '', $str);
      $code = "#" . strtoupper(substr($str, 0, 8));
      return static::where('code', $code)->exists() ? static::generateCode() : $code;
    }
}
