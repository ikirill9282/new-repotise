<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class News extends Model
{
  use HasAuthor, HasGallery;

  public function getGalleryClass()
  {
    return NewsGallery::class;
  }
}
