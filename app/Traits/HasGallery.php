<?php

namespace App\Traits;

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

trait HasGallery
{
  protected $deleted_gallery;

  protected static function booted()
  {
    static::deleting(function ($post) {
      $this->deleted_gallery = $this->gallery;  
    });

    static::deleted(function ($model) {
      foreach ($this->deleted_gallery as $gallery_item) {
        $path = str_ireplace('storage', 'public', $gallery_item->image);
        Storage::delete($path);
      }
    });
  }

  public function gallery()
  {
    return $this->hasMany(Gallery::class, 'model_id')->where('type', $this->table);
  }

  public function preview()
  {
    return $this->hasOne(Gallery::class, 'model_id')->where('preview', 1)->where('type', $this->table);
  }
}