<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use App\Models\Order;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CheckoutSubscription extends Component
{

    public string $order_id;

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;
    }

    public function getOrder(): ?Order
    {
      return Order::where('id', Crypt::decrypt($this->order_id))
        ->with('user', 'order_products.product')
        ->first();
    }

    #[On('makeSubscription')]
    public function onMakeSubscription($intent)
    {
      $order = $this->getOrder();
      $order_product = $order->order_products->first();
      $order->user->addPaymentMethod($intent['payment_method']);

      try {
        $price_id = $order_product->product->subprice->getPeriodId($order->sub_period);
        $sub_name = 'plan_' . $order->sub_period . '_' . $order_product->product->id;
        $order->user->newSubscription($sub_name, $price_id)->create($intent['payment_method']);
      } catch (\Exception $e) {
        $this->dispatch('toastError', ['message' => 'Something went wrong ... Please contact with administration!']);
        Log::critical('Subscription error', [
          'order' => $order,
          'error' => $e,
        ]);
        return ;
      }
    }

    public function render()
    {
      $order = $this->getOrder();
      $pm = $order->user->paymentMethods();
      // dd($pm);
      return view('livewire.checkout-subscription', [
        'order' => $this->getOrder(),
        'intent' => $order->user->createSetupIntent(),
        'user' => $order->user,
      ]);
    }
}
