<?php

namespace App\Jobs;

use App\Mail\GiftRecipient;
use App\Mail\GiftBuyerRegistered;
use App\Mail\GiftBuyerGuest;
use App\Models\Order;
use App\Models\Gift;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class DeliveryGift implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    // public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(
      public Order $order
    )
    {
    }

    public function uniqueId()
    {
      return $this->order->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      $gift = $this->order->gift;
      if (!$gift) {
        return;
      }

      $buyer = $this->order->buyer ?? $this->order->user;
      
      // Письмо покупателю
      // Проверяем, был ли пользователь создан недавно (в течение последних 5 минут) - значит гость
      $isGuestBuyer = $buyer->created_at->isAfter(now()->subMinutes(5)) && !$buyer->email_verified_at;
      
      if ($isGuestBuyer) {
        // Покупатель - гость → письмо №2
        Mail::to($buyer->email)->send(new GiftBuyerGuest($buyer, $this->order, $gift));
      } else {
        // Покупатель - зарегистрированный → письмо №1
        Mail::to($buyer->email)->send(new GiftBuyerRegistered($buyer, $this->order, $gift));
      }

      // Письмо получателю → письмо №3
      $recipientUser = User::where('email', $gift->recipient_email)->first();
      if (!$recipientUser) {
        // Создаем пользователя для получателя, но без пароля (установит сам)
        $recipientUser = User::create([
          'email' => $gift->recipient_email,
          'username' => preg_replace("/^(.*?)@.*$/is", "$1", $gift->recipient_email),
          'password' => User::makePassword(), // Временный пароль, нужно будет установить свой
        ]);
      }

      Mail::to($gift->recipient_email)->send(new GiftRecipient($recipientUser, $this->order, $gift));
    }
}
