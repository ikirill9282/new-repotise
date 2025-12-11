<?php

namespace App\Console\Commands;

use App\Services\MediaOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class OptimizeAllImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-all {--disk=public : Storage disk to optimize} {--path=images : Path to optimize} {--batch=50 : Number of images to process per batch} {--force : Force re-optimization of already optimized images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all existing images on the server';

    /**
     * Execute the console command.
     */
    public function handle(MediaOptimizer $optimizer)
    {
        $disk = $this->option('disk');
        $path = $this->option('path');
        $batchSize = (int) $this->option('batch');
        $force = $this->option('force');

        $this->info("Starting image optimization...");
        $this->info("Disk: {$disk}, Path: {$path}, Batch size: {$batchSize}");

        $storage = Storage::disk($disk);
        $absolutePath = $storage->path($path);

        if (!File::exists($absolutePath)) {
            $this->error("Path does not exist: {$absolutePath}");
            return 1;
        }

        // Получаем все изображения
        $images = $this->getImageFiles($absolutePath);
        $totalImages = count($images);

        if ($totalImages === 0) {
            $this->info("No images found to optimize.");
            return 0;
        }

        $this->info("Found {$totalImages} images to optimize.");
        
        if (!$this->confirm("Do you want to proceed?", true)) {
            $this->info("Optimization cancelled.");
            return 0;
        }

        $bar = $this->output->createProgressBar($totalImages);
        $bar->start();

        $optimized = 0;
        $skipped = 0;
        $errors = 0;
        $totalSaved = 0;
        $reasons = [];

        // Обрабатываем изображения батчами
        $batches = array_chunk($images, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            foreach ($batch as $imagePath) {
                try {
                    $relativePath = str_replace($storage->path(''), '', $imagePath);
                    $relativePath = ltrim($relativePath, '/\\');
                    
                    // Оптимизируем и получаем результат
                    $result = $optimizer->optimize($disk, $relativePath, ['force' => $force]);
                    
                    if ($result['success'] && isset($result['optimized']) && $result['optimized']) {
                        // Изображение успешно оптимизировано
                        $optimized++;
                        if (isset($result['saved']) && $result['saved'] > 0) {
                            $totalSaved += $result['saved'];
                        }
                    } else {
                        // Изображение пропущено
                        $skipped++;
                        $reason = $result['reason'] ?? 'unknown';
                        $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->warn("Error optimizing {$imagePath}: " . $e->getMessage());
                }
                
                $bar->advance();
            }

            // Небольшая пауза между батчами чтобы не перегружать сервер
            if ($batchIndex < count($batches) - 1) {
                usleep(100000); // 0.1 секунды
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Выводим статистику
        $this->info("Optimization completed!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total images', $totalImages],
                ['Optimized', $optimized],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Space saved', $this->formatBytes($totalSaved)],
            ]
        );

        // Показываем причины пропуска, если есть
        if (!empty($reasons)) {
            $this->newLine();
            $this->info("Reasons for skipping:");
            foreach ($reasons as $reason => $count) {
                $reasonText = match($reason) {
                    'webp_not_supported' => 'WebP not supported by GD',
                    'format_not_supported' => 'Format not supported',
                    'already_optimized' => 'Already optimized',
                    'not_an_image' => 'Not an image file',
                    'file_not_found' => 'File not found',
                    default => ucfirst(str_replace('_', ' ', $reason)),
                };
                $this->line("  - {$reasonText}: {$count}");
            }
        }

        return 0;
    }

    /**
     * Get all image files recursively
     */
    protected function getImageFiles(string $path): array
    {
        $images = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $files = File::allFiles($path);

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $allowedExtensions)) {
                $images[] = $file->getPathname();
            }
        }

        return $images;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
