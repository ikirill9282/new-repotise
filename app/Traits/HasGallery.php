<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasGallery
{
  abstract public function getGalleryClass();

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
    return $this->hasMany($this->getGalleryClass());
  }

  public function preview()
  {
    return $this->hasOne($this->getGalleryClass())->where('preview', 1);
  }
}