<?php

namespace App\Livewire\Modals;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

class Cancelsub extends Component
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

    public function cancelSubscription()
    {
      $order = $this->getOrder();
      $sub_type = $order->getSubscriptionType();
      $sub = $order->user->subscription($sub_type);
      Cashier::stripe()->subscriptions->update(
        $sub->stripe_id,
        ['cancel_at_period_end' => true],
      );
      $this->dispatch('openModal', 'cancelsub-accept', ['order_id' => $this->order_id]);
    }

    public function render()
    {
        $order = $this->getOrder();
        $product = $order->order_products->first()->product;

        $sub_type = $order->getSubscriptionType();
        $sub = $order->user->subscription($sub_type);
        if (!$sub) {
          Log::error('Cant cancel. Undefined subcription.', [
            'order' => $order,
            'sub_type' => $sub_type,
          ]);
          $this->dispatch('toastError', ['message' => 'Something went wrong... Please contact with administration!']);
          return ;
        }

        $sub = $sub->asStripeSubscription();
        $sub_end = Carbon::parse($sub->current_period_end)->format('d.m.Y');

        return view('livewire.modals.cancelsub', [
          'product_name' => $product->title,
          'sub_end' => $sub_end,
        ]);
    }
}
