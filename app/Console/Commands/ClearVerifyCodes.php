<?php

namespace App\Console\Commands;

use App\Models\UserVerify;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        if (!Carbon::parse($verify->created_at)->addHour()->isFuture()) {
          $verify->delete();
          Log::info('Verify code cleared: ' . $verify->code . ' for user: ' . $verify->user->email);
        }
      }
    }
}
