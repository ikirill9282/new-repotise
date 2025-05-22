<?php

namespace App\Traits;

use App\Models\Status;

trait HasStatus
{
  public function status()
  {
    return $this->belongsTo(Status::class);
  }
}