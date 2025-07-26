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
      if ($this->type == 'promocode') {
        if ($this->uses == 1) {
          $this->update(['uses' => 0, 'active' => 0]);
        } else {
          $this->decrement('uses');
        }
      }

      if ($this->type == 'freeproduct') {
        $this->update(['uses' => 0, 'active' => 0]);
      }
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
      if ($this->uses == 0 || $this->uses == $this->orders()->whereNot('id', $order->id)->count()) {
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


    // public function applyOrder(Order $order)
    // {
    //   DB::transaction(function() use($order) {
    //     $order->update(['discount_id' => $this->id]);
    //     $order->update([
    //       'discount_amount' => $order->getDiscount(),
    //       'cost' => $order->getTotal(),
    //       'tax' => $order->getTax(),
    //     ]);

    //     $max_discount = $order->discount_amount;
    //     $get_dicsount = fn($val) => $val > $max_discount ? $max_discount : $val;

    //     if ($this->type == 'promocode') {

    //       if ($this->target == 'cart') {
    //         $discount_per_product = round($max_discount / $order->order_products->count(), 2);
    //         foreach ($order->order_products as $product) {
    //           $discount = $get_dicsount($discount_per_product);
    //           $product->update([
    //             'discount' => $discount,
    //             'price' => ($product->price - $discount),
    //             'price_without_discount' => $product->price,
    //             'total' => $product->getTotal() - $discount,
    //             'total_without_discount' => $product->getTotal(),
    //           ]);
    //           $max_discount -= $discount;
    //         }
    //       }
    //     }

    //     if ($this->type == 'freeproduct') {
    //       if ($this->target == 'cart') {
    //         foreach ($order->order_products as $op) {
    //           if ($op->price > 50) continue;
    //           if ($op->product->author->id > 0 && $op->price > 25) continue;
              
    //           $res[$op->product_id] = $op->price;
    //         }
    //         $cost = max($res);
    //         $product_id = array_search($cost, $res);
    //         $product = $order->order_products->where('product_id', $product_id)->first();
    //         $max_discount = $product->product->author->id > 0 ? 25 : 50;
    //         $dis = $max_discount > $product->price ? $product->price : $max_discount;
    //         $product->update([
    //           'discount' => $dis,
    //           'total' => $product->getTotal() - $dis,
    //           'total_without_discount' => $product->getTotal(),
    //         ]);
    //       }
    //     }
    //   });
    // }

    // public function removeOrder(Order $order)
    // {
    //   DB::transaction(function() use($order) {
    //     $order->update([
    //       'discount_id' => null,
    //       'discount_amount' => 0,
    //       'cost' => $order->cost_without_discount,
    //     ]);

    //     if ($this->target == 'cart') {
    //       foreach ($order->order_products as $product) {
    //         $product->update([
    //           'discount' => 0,
    //           'price' => $product->price_without_discount,
    //           'total' => $product->getTotalWithotDiscount(),
    //           'total_without_discount' => $product->getTotalWithotDiscount(),
    //         ]);
    //       }
    //     }
    //   });
    // }
    
}
