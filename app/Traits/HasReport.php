<?php

namespace App\Traits;

use App\Models\Report;

trait HasReport
{
  public function reports()
  {
    return $this->morphMany(Report::class, 'reportable');
  }
}