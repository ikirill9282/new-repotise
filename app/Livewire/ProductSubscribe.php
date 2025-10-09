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
      
      $cart = new Cart('cart');
      $cart->addProduct($product->id);
      $cost = match($period) {
        'month' => $product->getMonthSum(),
        'quarter' => $product->getQuarterSum(),
        'year' => $product->getYearSum(),
      };

      $order = new Order();
      $order->user_id = Auth::user()?->id ?? 0;
      $order->status_id = EnumsOrder::NEW;
      $order->cost = $cost;
      $order->cost_without_discount = $product->price;
      $order->cost_without_tax = $cost;
      $order->save();

      $order->order_products()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'price' => $cost,
        'sale_price' => $product->sale_price,
        'count' => 1,
        'total' => $cost,
        'total_without_discount' => $cost,
      ]);

      Session::put('checkout', $order->id);
      return redirect()->route('checkout');
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
