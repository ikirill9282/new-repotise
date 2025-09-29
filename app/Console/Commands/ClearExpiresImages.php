<?php

namespace App\Console\Commands;

use App\Models\Gallery;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearExpiresImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expires-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $gallery = Gallery::where('expires_at', '<=', Carbon::now())->get();
      foreach ($gallery as $item) {
        $path = str_ireplace('/storage/', '', $item->image);
        Storage::disk('public')->delete($path);
        $item->delete();
      }
    }
}
