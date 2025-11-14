<?php

namespace App\Jobs;

use App\Services\MediaOptimizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OptimizeMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $disk,
        protected string $path,
        protected array $options = []
    ) {
    }

    public function handle(MediaOptimizer $optimizer): void
    {
        $optimizer->optimize($this->disk, $this->path, $this->options);
    }
}

