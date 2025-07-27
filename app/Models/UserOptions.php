<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserOptions extends Model
{

    protected float $default_fee = 10;
    protected float $default_space = 0.3;
    protected float $default_sales_treshold = 100;

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function level()
    {
      return $this->belongsTo(Level::class);
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
