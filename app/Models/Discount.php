<?php

namespace App\Models;

use App\Enums\Action;
use App\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promocode;
use Illuminate\Support\Facades\Auth;

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

      if ($this->target == 'cart') {
        $amount = $order->getAmount();

        if (!is_null($this->percent)) {
          $discount = round($amount / 100 * $this->percent);
          $discount = ($discount > $this->max) ? $this->max : $discount;
        }
      }

      return $discount;
    }


    public function complete()
    {
      if ($this->one_time) {
        $this->update(['uses' => 0, 'active' => 0]);
        return;
      }

      if ($this->type == 'promocode') {
        if ($this->uses == 1) {
          $this->update(['uses' => 0, 'active' => 0]);
        } else {
          $this->decrement('uses');
        }

        return;
      }
    }


    public function isAvailable(): bool
    {
      if (!$this->active) {
        return false;
      }

      if ($this->type == 'promocode') {
        if ($this->one_time && $this->orders()->exists()) {
          return false;
        }

        if ($this->visibility == 'private' && Auth::check()) {
          if ($this->user_id !== Auth::user()->id) {
            return false;
          }
        }
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
      $str = preg_replace('/[+_]/is', '', $str);
      $code = "#" . strtoupper(substr($str, 0, 8));
      return static::where('code', $code)->exists() ? static::generateCode() : $code;
    }
}
