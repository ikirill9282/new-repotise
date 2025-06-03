<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\MailLog;
use Illuminate\Support\Facades\Http;

class CheckMailgunLog extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:check-mailgun-log';

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
    $url = 'https://api.mailgun.net/v1/analytics/logs';
    $query = [
      'start' => Carbon::today()->toRfc2822String(),
      'end' => Carbon::today()->endOfDay()->toRfc2822String(),
      'include_subaccounts' => true,
      'pagination' => [
        'limit' => 100,
      ]
    ];
    $req = fn($data) => Http::withBasicAuth('api', env('MAILGUN_API_KEY'))
      ->withBody(json_encode($data));

    $data = [];
    do {
      $resp = $req($query)->post($url);
      $resp = $resp->json();
      $data = array_merge($data, ($resp['items'] ?? []));
      if (isset($resp['pagination']) && isset($resp['pagination']['next'])) {
        $query['pagination']['next'] = $resp['pagination']['next'];
      }
    } while (isset($resp['pagination']) && isset($resp['pagination']['next']));

    // dd(collect($data)->sortByDesc('@timestamp'));
    // dd($data);
    foreach (MailLog::where('status', 'new')->get() as $mailLog) {
      $found = [];
      foreach ($data as $item) {
        $messageId = preg_replace('/^(.*?)@.*$/is', "$1", $item['message']['headers']['message-id']);
        if ($messageId == $mailLog->message_id) {
          $found[] = $item;
        }
      }

      // dd($mailLog->message_id);
      $mail = collect($found)->sortByDesc('@timestamp')->first();
      if ($mail) $mailLog->update(['status' => $mail['event'], 'mailgun_id' => $mail['id']]);
    }
  }
}
