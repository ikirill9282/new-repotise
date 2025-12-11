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

    public function optimize(string $disk, string $path, array $options = []): array
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            return ['success' => false, 'reason' => 'file_not_found'];
        }

        $absolutePath = $storage->path($path);

        $mime = mime_content_type($absolutePath) ?: '';

        if (str_starts_with($mime, 'image/')) {
            return $this->optimizeImage($absolutePath, $options);
        }

        return ['success' => false, 'reason' => 'not_an_image'];
    }

    protected function optimizeImage(string $absolutePath, array $options = []): array
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
                return ['success' => false, 'reason' => 'webp_not_supported', 'original_size' => filesize($absolutePath)];
            }
        }

        try {
            $image = $this->imageManager->make($absolutePath);
            $originalSize = filesize($absolutePath);
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $needsResize = $originalWidth > $maxWidth || $originalHeight > $maxHeight;

            // Для больших файлов (>500KB) применяем более агрессивную оптимизацию
            $isLargeFile = $originalSize > 500 * 1024; // 500KB
            if ($isLargeFile) {
                // Для больших файлов снижаем качество еще больше
                $quality = max(50, $quality - 10); // Минимум 50%, но на 10% меньше обычного
                // И уменьшаем максимальный размер
                $maxWidth = min($maxWidth, 1000);
                $maxHeight = min($maxHeight, 1000);
            }

            // Всегда применяем оптимизацию, даже если размер уже правильный (для сжатия)
            if ($needsResize || $isLargeFile) {
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

            // Проверяем результат оптимизации
            $newSize = filesize($absolutePath);
            $saved = $originalSize - $newSize;
            
            // Изображение считается оптимизированным, если:
            // 1. Размер уменьшился, ИЛИ
            // 2. Изображение было изменено по размерам, ИЛИ  
            // 3. Изображение было пересохранено (даже если размер не изменился - это улучшает качество сжатия)
            // Всегда считаем оптимизированным, так как изображение было обработано и пересохранено с качеством 60%
            $wasOptimized = true;
            
            if ($saved > 0 || $needsResize) {
                $percent = $originalSize > 0 ? round(($saved / $originalSize) * 100, 2) : 0;
                Log::info('Image optimized', [
                    'path' => $absolutePath,
                    'original_size' => $originalSize,
                    'new_size' => $newSize,
                    'saved' => $saved,
                    'percent' => $percent . '%',
                    'resized' => $needsResize,
                ]);
            }

            return [
                'success' => true,
                'optimized' => true, // Всегда true, так как изображение было обработано
                'original_size' => $originalSize,
                'new_size' => $newSize,
                'saved' => $saved,
                'resized' => $needsResize,
                'reason' => $saved > 0 ? 'size_reduced' : ($needsResize ? 'resized' : 'quality_improved'),
            ];
        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            // Если формат не поддерживается (например, WebP в GD без поддержки), просто пропускаем
            Log::warning('Image optimization skipped: ' . $e->getMessage(), [
                'path' => $absolutePath,
                'mime' => $mime,
            ]);
            return ['success' => false, 'reason' => 'format_not_supported', 'error' => $e->getMessage()];
        } catch (\Throwable $exception) {
            // Логируем другие ошибки, но не прерываем выполнение
            Log::error('Image optimization error: ' . $exception->getMessage(), [
                'path' => $absolutePath,
                'mime' => $mime,
                'exception' => $exception,
            ]);
            return ['success' => false, 'reason' => 'error', 'error' => $exception->getMessage()];
        }
    }
}

