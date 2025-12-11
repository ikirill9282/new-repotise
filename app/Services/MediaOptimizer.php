<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class MediaOptimizer
{
    protected OptimizerChain $optimizer;

    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->optimizer = OptimizerChainFactory::create();
        $this->imageManager = new ImageManager(['driver' => config('media.image.driver', 'gd')]);
    }

    public function optimize(string $disk, string $path, array $options = []): void
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            return;
        }

        $absolutePath = $storage->path($path);

        $mime = mime_content_type($absolutePath) ?: '';

        if (str_starts_with($mime, 'image/')) {
            $this->optimizeImage($absolutePath, $options);
        }
    }

    protected function optimizeImage(string $absolutePath, array $options = []): void
    {
        $maxWidth = $options['max_width'] ?? config('media.image.max_width', 1200);
        $maxHeight = $options['max_height'] ?? config('media.image.max_height', 1200);
        $quality = $options['quality'] ?? config('media.image.quality', 60);
        $forceOptimize = $options['force'] ?? true; // Всегда оптимизировать для сжатия

        // Проверяем MIME тип файла
        $mime = mime_content_type($absolutePath) ?: '';
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        // Если это WebP и драйвер GD (который не поддерживает WebP), пропускаем оптимизацию
        if (($mime === 'image/webp' || $extension === 'webp') && config('media.image.driver', 'gd') === 'gd') {
            // Проверяем, поддерживает ли GD WebP
            if (!function_exists('imagecreatefromwebp')) {
                // GD не поддерживает WebP, просто пропускаем оптимизацию
                // WebP уже оптимизированный формат, так что это нормально
                return;
            }
        }

        try {
            $image = $this->imageManager->make($absolutePath);
            $originalSize = filesize($absolutePath);
            $needsResize = $image->width() > $maxWidth || $image->height() > $maxHeight;

            // Всегда применяем оптимизацию, даже если размер уже правильный (для сжатия)
            if ($needsResize) {
                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Применяем ориентацию и сохраняем с оптимизированным качеством
            $image->orientate()->save($absolutePath, $quality);

            // Дополнительная оптимизация через Spatie Image Optimizer
            try {
                $this->optimizer->optimize($absolutePath);
            } catch (\Throwable $exception) {
                // Silently ignore optimizer failures; resized image is already saved.
            }

            // Логируем результат оптимизации
            $newSize = filesize($absolutePath);
            if ($originalSize > $newSize) {
                $saved = $originalSize - $newSize;
                $percent = round(($saved / $originalSize) * 100, 2);
                Log::info('Image optimized', [
                    'path' => $absolutePath,
                    'original_size' => $originalSize,
                    'new_size' => $newSize,
                    'saved' => $saved,
                    'percent' => $percent . '%',
                ]);
            }
        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            // Если формат не поддерживается (например, WebP в GD без поддержки), просто пропускаем
            Log::warning('Image optimization skipped: ' . $e->getMessage(), [
                'path' => $absolutePath,
                'mime' => $mime,
            ]);
        } catch (\Throwable $exception) {
            // Логируем другие ошибки, но не прерываем выполнение
            Log::error('Image optimization error: ' . $exception->getMessage(), [
                'path' => $absolutePath,
                'mime' => $mime,
                'exception' => $exception,
            ]);
        }
    }
}

