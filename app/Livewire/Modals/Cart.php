<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Services\Cart as CartService;

class Cart extends Component
{
    public function moveCheckout()
    {
      $cart = new CartService();
      if ($cart->hasProducts()) {
        $order = Order::preparing($cart);
        $order->user_id = Auth::user()?->id ?? 0;
        $order = $order->savePrepared();

        $cart->flushCart();
        Session::put('checkout', $order->id);

        return redirect()->route('checkout');
      }
      
      return ;
    }

    public function render()
    {
      return view('livewire.modals.cart');
    }
}
