<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasReport;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasAuthor, HasReport;

    public function reportable()
    {
      return $this->morphTo();
    }
}
