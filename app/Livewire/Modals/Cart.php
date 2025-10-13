<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Services\Cart as CartService;

class Cart extends Component
{
    public array|Order $order;
    // public array|CartService $cart;

    public function mount()
    {
      $this->prepareOrder();
    }

    public function prepareOrder()
    {
      $cart = $this->getCart();
      $this->order = Order::preparing($cart);
    }

    public function getCart()
    {
      return new CartService();
    }

    // public function incrementProductCount(int $product_id): void
    // {
    //   $product = $this->order->products->where('id', $product_id)->first();
    //   $product->pivot->update(['count' => ($product->pivot->count + 1)]);
      
    //   $this->order->recalculate();
    // }

    // public function decrementProductCount(int $product_id): void
    // {
    //   $product = $this->order->products->where('id', $product_id)->first();
    //   if ($product->pivot->count > 1) {
    //     $product->pivot->update(['count' => ($product->pivot->count - 1)]);
    //     $this->order->recalculate();
    //   }
    // }


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
