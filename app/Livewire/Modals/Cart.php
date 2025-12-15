<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart as CartService;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;

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

    #[On('refreshCart')]
    public function refreshCart(): void
    {
      $this->prepareOrder();
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
        // Проверяем наличие подписок в корзине
        if ($cart->hasSubscriptions()) {
          $subscriptionProducts = $cart->getSubscriptionProducts();
          
          // Если в корзине только одна подписка, перенаправляем на страницу оформления подписки
          if (count($subscriptionProducts) === 1 && count($cart->getProducts()) === 1) {
            $subscriptionProduct = $subscriptionProducts[0];
            $product = Product::find($subscriptionProduct['id']);
            
            if ($product) {
              // Проверяем, что пользователь не пытается купить свой продукт
              if (Auth::check() && $product->user_id === Auth::id()) {
                $cart->removeFromCart($product->id);
                $this->prepareOrder();
                $this->dispatch('toastError', ['message' => 'You cannot purchase your own products.']);
                return;
              }
              
              // Сохраняем данные подписки в сессию и очищаем корзину
              $product_id = Crypt::encrypt($product->id);
              Session::put('checkout-sub', [
                'period' => $subscriptionProduct['subscription_period'],
                'product_id' => $product_id
              ]);
              $cart->flushCart();
              
              return redirect()->route('checkout.subscription');
            }
          } else {
            // Если в корзине несколько подписок или подписки + обычные продукты
            $this->dispatch('toastError', ['message' => 'Please process subscriptions separately from regular products.']);
            return;
          }
        }
        
        // Обычная обработка корзины для не-подписок
        $order = Order::preparing($cart);
        $blockedProductIds = [];

        if (Auth::check()) {
          foreach ($order->products as $product) {
            if ($product->user_id === Auth::id()) {
              $cart->removeFromCart($product->id);
              $blockedProductIds[] = $product->id;
            }
          }
        }

        if (!empty($blockedProductIds)) {
          $this->prepareOrder();
          $this->dispatch('toastError', ['message' => 'You cannot purchase your own products.']);
          return;
        }

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
