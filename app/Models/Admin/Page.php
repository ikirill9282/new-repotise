<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
  
  protected $append = ['url'];

  public function sections()
  {
    return $this->hasManyThrough(Section::class, PageSection::class, 'page_id', 'id', 'id', 'section_id');
  }

  public function url(): Attribute
  {
    return Attribute::make(
      get: fn() => url($this->slug),
    );
  }
}
