<?php

namespace App\Jobs;

use App\Mail\GiftRecipient;
use App\Mail\GiftBuyerRegistered;
use App\Mail\GiftBuyerGuest;
use App\Models\Order;
use App\Models\Gift;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
      // Проверяем, является ли заказ подарком
      if ($this->order->gift != 1) {
        return;
      }

      // Получаем запись Gift через связь
      $gift = Gift::where('order_id', $this->order->id)->first();
      if (!$gift) {
        Log::error('Gift record not found for order', ['order_id' => $this->order->id]);
        return;
      }

      // Получаем покупателя из Gift
      $buyer = $gift->buyer;
      if (!$buyer) {
        Log::error('Buyer not found for gift', ['gift_id' => $gift->id, 'order_id' => $this->order->id]);
        return;
      }
      
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
