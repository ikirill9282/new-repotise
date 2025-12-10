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
      try {
        $src = str_ireplace('/storage/', '', $item->image);
        
        // Проверяем существование исходного файла
        if (!Storage::disk('public')->exists($src)) {
          continue;
        }
        
        $name = preg_replace('/^.*?\/*([a-zA-Z0-9-_]+\.\w+)$/is', "$1", $src);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $dst = '/images/' . trim(base64_encode(microtime() . uniqid()), '=') . '.' . $extension;

        // Копируем файл
        if (!Storage::disk('public')->copy($src, $dst)) {
          continue;
        }

        // Создаем запись в галерее (без запуска оптимизации, файлы уже оптимизированы)
        $newRecord->gallery()->create([
          'user_id' => $newRecord->user_id,
          'model_id' => $newRecord->id,
          'image' => "/storage$dst",
          'preview' => $item->preview,
          'type' => $type,
          'placement' => $item->placement ?? 'site',
          'size' => $item->size ?? 0,
        ]);
      } catch (\Exception $e) {
        // Пропускаем файлы с ошибками и продолжаем
        \Illuminate\Support\Facades\Log::warning('Error copying gallery item', [
          'item_id' => $item->id,
          'error' => $e->getMessage(),
        ]);
        continue;
      }
    }
  }
}