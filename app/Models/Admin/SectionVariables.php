<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SectionVariables extends Model
{
    public function section()
    {
      return $this->belongsTo(Section::class);
    }

    public function value(): Attribute
    {
      return Attribute::make(
        get: fn($value) => (!empty($value) && json_validate($value)) ? json_decode($value, true): $value,
        set: fn($value) => is_array($value) ? json_encode(array_values($value)) : $value,
      );
    }
}
