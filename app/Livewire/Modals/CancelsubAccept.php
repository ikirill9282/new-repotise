<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\Order;
use Illuminate\Support\Carbon;

class CancelsubAccept extends Component
{

    public string $order_id;

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;
    }

    public function getOrder(): ?Order
    {
      return Order::find(Crypt::decrypt($this->order_id));
    }

    public function render()
    {
      $order = $this->getOrder();
      $product = $order->order_products->first()->product;

      $sub_type = $order->getSubscriptionType();
      $sub = $order->user->subscription($sub_type)->asStripeSubscription();
      $sub_end = Carbon::parse($sub->current_period_end)->format('d.m.Y');

      return view('livewire.modals.cancelsub-accept', [
        'product_name' => $product->title,
        'sub_end' => $sub_end,
      ]);
    }
}
