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
      if(!Auth::check()) {
        $this->dispatch('openModal', 'auth');
        return ;
      }

      $product = $this->getProduct();
      $cost = match($period) {
        'month' => $product->subprice->getMonthSum(),
        'quarter' => $product->subprice->getQuarterSum(),
        'year' => $product->subprice->getYearSum(),
      };
      $costWithoutDiscount = match($period) {
        'month' => $product->subprice->getMonthSumWithoutDiscount(),
        'quarter' => $product->subprice->getQuarterSumWithoutDiscount(),
        'year' => $product->subprice->getYearSumWithoutDiscount(),
      };
      $discount = match($period) {
        'month' => round(($product->getPrice() / 100 * $product->subprice->month), 2),
        'quarter' => round(($product->getPrice() / 100 * $product->subprice->quarter), 2),
        'year' => round(($product->getPrice() / 100 * $product->subprice->year), 2),
      };

      // TODO: add discounts result
      dd($discount);

      $order = new Order();
      $order->user_id = Auth::user()?->id;
      $order->status_id = EnumsOrder::NEW;
      $order->cost = $cost;
      $order->sub = 1;
      $order->sub_period = $period;
      $order->cost_without_discount = $costWithoutDiscount;
      $order->cost_without_tax = $costWithoutDiscount;
      $order->save();

      $order->order_products()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'price' => $product->price,
        'sale_price' => $product->sale_price,
        'count' => 1,
        'total' => $cost,
        'total_without_discount' => $costWithoutDiscount,
      ]);

      Session::put('checkout', $order->id);
      return redirect()->route('checkout.subscription');
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
