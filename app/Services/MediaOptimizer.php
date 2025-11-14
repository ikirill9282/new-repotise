<?php

namespace App\Services;

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
        $maxWidth = $options['max_width'] ?? config('media.image.max_width', 2560);
        $maxHeight = $options['max_height'] ?? config('media.image.max_height', 2560);
        $quality = $options['quality'] ?? config('media.image.quality', 85);

        $image = $this->imageManager->make($absolutePath);

        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $image->orientate()->save($absolutePath, $quality);

        try {
            $this->optimizer->optimize($absolutePath);
        } catch (\Throwable $exception) {
            // Silently ignore optimizer failures; resized image is already saved.
        }
    }
}

