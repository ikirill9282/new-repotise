<?php

namespace App\Livewire;

use App\Models\Discount;
use Livewire\Attributes\On;
use App\Models\Order;
use App\Models\OrderProducts;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Checkout extends Component
{
    public Order $order;

    public array $form = [
      'fullname' => null,
      'email' => null,
      'gift' => false,
      'recipient' => null,
      'recipient_message' => null
    ];

    public ?string $promocode = null;

    public function mount(Order $order)
    {
      $this->order = $order;

      if (Auth::check()) {
        $this->form['fullname'] = Auth::user()->name;
        $this->form['email'] = Auth::user()->email;
      }
      
      if (!empty($order->discount_id)) {
        $this->promocode = $order->discount->code;
      }
    }

    public function applyPromocode(): void
    {
      $this->promocode = trim($this->promocode);
      $this->validate(['promocode' => 'required|string|min:7|exists:discounts,code']);
      
      $discount = Discount::query()
        ->where('code', $this->promocode)
        ->first();
        
      if ($discount->isAvailable($this->order)) {
        $this->order->applyDiscount($discount);
        if ($this->order->cost > 0) {
          $this->updatePaymentIntent();
        }
      } else {
        $this->addError('promocode', 'Incorrect promocode');
      }
    }

    public function removePromocode(): void
    {
      $this->order->removeDiscount();
      $this->updatePaymentIntent();
    }

    public function dropProduct(int $product_id): void
    {
      DB::transaction(function() use ($product_id) {
        $this->order->order_products()->where('product_id', $product_id)->delete();
        $this->order->load('products');

        
        // $this->order->discount->isAvailable($this->order);
        if ($this->order->discount_id && !$this->order->discount->isAvailable($this->order)) {
          $this->order->removeDiscount();
          $this->promocode = null;
        }
      });
      
      if ($this->order->products->isEmpty()) {
        $this->order->delete();
        Session::forget('checkout');
      } else {
        $this->order->recalculate();
        $this->updatePaymentIntent();
      }
    }
    

    public function incrementProductCount(int $product_id): void
    {
      $product = $this->order->products->where('id', $product_id)->first();
      $product->pivot->update(['count' => ($product->pivot->count + 1)]);

      $this->order->recalculate();
      $this->updatePaymentIntent();
    }

    public function decrementProductCount(int $product_id): void
    {
      $product = $this->order->products->where('id', $product_id)->first();
      if ($product->pivot->count > 1) {
        $product->pivot->update(['count' => ($product->pivot->count - 1)]);

        $this->order->recalculate();
        $this->updatePaymentIntent();
      }
    }

    protected function updatePaymentIntent(): void
    {
      Cashier::stripe()
        ->paymentIntents
        ->update(
          $this->order->payment_id, 
          [
            'amount' => ($this->order->getTotal() * 100)
          ]
        );
    }

    public function submit()
    {
      $this->validate([
        'form.fullname' => 'required|string',
        'form.email' => 'required|email',
        'form.gift' => 'required|boolean',
        'form.recipient' => 'required_if_accepted:form.gift|nullable|email',
        'form.recipient_message' => 'required_if_accepted:form.gift|nullable|string',
      ]);

      if ($this->order->free()) {
        return redirect(route('payment-success', ['payment_intent' => $this->order->payment_id]));
      }
      $transaction = $this->order->getTransaction();
      $this->dispatch('payment-confirm', $transaction);
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
