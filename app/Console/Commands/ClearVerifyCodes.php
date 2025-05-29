<?php

namespace App\Console\Commands;

use App\Models\UserVerify;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ClearVerifyCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-verify-codes';

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
      foreach (UserVerify::all() as $verify) {
        $alive = Carbon::now()->timestamp - Carbon::parse($verify->created_at)->timestamp;
        if ($alive >= 3600) $verify->delete();
      }
    }
}
