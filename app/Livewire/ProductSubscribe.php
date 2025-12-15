<?php

namespace App\Livewire;

use App\Enums\Order as EnumsOrder;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use App\Services\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProductSubscribe extends Component
{
    public string $product_id;

    public function mount(string $product_id)
    {
      $this->product_id = $product_id;
    }

    public function moveCheckout(string $period)
    {
      $product = $this->getProduct();
      
      if (!$product) {
        $this->dispatch('toastError', ['message' => 'Product not found.']);
        return;
      }

      if (Auth::check() && $product->user_id === Auth::id()) {
        $this->dispatch('toastError', ['message' => 'You cannot purchase your own product.']);
        return;
      }

      $cart = new Cart();
      $product_id = Crypt::decrypt($this->product_id);
      $cart->addProduct($product_id, 1, $period);
      
      $this->dispatch('refreshCart');
      $this->dispatch('openModal', ['modalName' => 'cart']);
      $this->dispatch('toastSuccess', ['message' => 'Subscription added to cart.']);
    }

    public function getProduct(): ?Product
    {
      return Product::find(Crypt::decrypt($this->product_id));
    }

    public function render()
    {
      return view('livewire.product-subscribe', [
        'product' => $this->getProduct(),
      ]);
    }
}
