<?php

namespace App\Traits;

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HasGallery
{
  protected $deleted_gallery;

  protected static function booted()
  {
    static::deleting(function ($model) {
      $model->deleted_gallery = $model->gallery;
    });

    static::deleted(function ($model) {
      foreach ($model->deleted_gallery as $gallery_item) {
        $path = str_ireplace('/storage/', '', $gallery_item->image);
        Storage::disk('public')->delete($path);
        $gallery_item->delete();
      }
    });
  }

  public function gallery()
  {
    return $this->hasMany(Gallery::class, 'model_id')
      ->where('type', $this->table)
      ->whereNull('expires_at')
      ;
  }

  public function preview()
  {
    return $this->hasOne(Gallery::class, 'model_id')
      ->where('preview', 1)
      ->where('type', $this->table)
      ->whereNull('expires_at')
      ;
  }

  public function copyGallery($newRecord, $type)
  {
    foreach ($this->gallery as $item) {
      $src = str_ireplace('/storage/', '', $item->image);
      $name = preg_replace('/^.*?\/*([a-zA-Z0-9-_]+\.\w+)$/is', "$1", $src);
      $path = Storage::disk('public')->path($src);
      $file = new UploadedFile($path, $name);
      $dst = '/images/' . trim(base64_encode(microtime()), '=') . '.' . $file->getFileInfo()->getExtension();

      Storage::disk('public')->copy($src, $dst);

      $newRecord->gallery()->create([
        'image' => "/storage$dst",
        'preview' => $item->preview,
        'type' => $type,
      ]);
    }
  }
}