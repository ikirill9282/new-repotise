<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Checkout extends Component
{
    // 4000 0027 6000 3184 - 3d secure
    public string $order_id;
    
    public bool $requiresAction = false;

    public string $clientSecret;

    public array $form = [
      'fullname' => null,
      'email' => null,
      'gift' => false,
      'recipient' => null,
      'recipient_message' => null
    ];

    // public ?string $promocode = null;

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;

      if (Auth::check()) {
        $this->form['fullname'] = Auth::user()->name;
        $this->form['email'] = Auth::user()->email;
      }

      $setupIntent = Cashier::stripe()->setupIntents->create([
        'payment_method_types' => ['card'],
      ]);
      $this->clientSecret = $setupIntent->client_secret;
      
      // if (!empty($order->discount_id)) {
      //   $this->promocode = $order->discount->code;
      // }
    }

    // public function applyPromocode(): void
    // {
    //   $this->promocode = trim($this->promocode);
    //   $this->validate(['promocode' => 'required|string|min:7|exists:discounts,code']);
      
    //   $discount = Discount::query()
    //     ->where('code', $this->promocode)
    //     ->first();
        
    //   if ($discount->isAvailable($this->order)) {
    //     $this->order->applyDiscount($discount);
    //     if ($this->order->cost > 0) {
    //       $this->updatePaymentIntent();
    //     }
    //   } else {
    //     $this->addError('promocode', 'Incorrect promocode');
    //   }
    // }

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
          // $this->promocode = null;
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
      $order = $this->getOrder();
      $product = $order->products->where('id', $product_id)->first();

      $new_count = $product->pivot->count + 1;
      $product->pivot->update(['count' => $new_count]);
      // $this->order->recalculate();
      // $this->updatePaymentIntent();
    }

    public function decrementProductCount(int $product_id): void
    {
      $order = $this->getOrder();
      $product = $order->products->where('id', $product_id)->first();
      if ($product->pivot->count > 1) {
        $new_count = $product->pivot->count - 1;
        $product->pivot->update(['count' => $new_count]);
        // $this->order->recalculate();
        // $this->updatePaymentIntent();
      }
    }

    // protected function updatePaymentIntent(): void
    // {
    //   Cashier::stripe()
    //     ->paymentIntents
    //     ->update(
    //       $this->order->payment_id, 
    //       [
    //         'amount' => ($this->order->getTotal() * 100)
    //       ]
    //     );
    // }

    public function checkValidtion()
    {
      $validator = Validator::make($this->form, [
        'fullname' => 'required|string',
        'email' => 'required|email',
        'gift' => 'required|boolean',
        'recipient' => 'required_if_accepted:form.gift|nullable|email',
        'recipient_message' => 'required_if_accepted:form.gift|nullable|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
        return false;
      }

      return true;
    }

    #[On('makePayment')]
    public function onMakePayment(string $pm_id)
    { 

      DB::beginTransaction();
      try {
        $order = $this->getOrder();
        if (!Auth::check()) {
          $pwd = User::makePassword();
          $user = User::firstOrCreate(
            ['email' => $this->form['email']],
            [
              'username' => $this->form['fullname'],
              'password' => $pwd,
            ]
          );

          $order->update(['user_id' => $user->id]);
          $user->sendPassword($pwd);
          $user->sendVerificationCode();
        } else {
          $user = $order->user;
        }
        if ($this->form['gift'] && $this->form['recipient'] !== $order->user->email) {
          $order->update([
            'gift' => 1,
            'recipient' => $this->form['recipient'],
            'recipient_message' => $this->form['recipient_message'],
          ]);
        }

        $user->addPaymentMethod($pm_id);
        $paymentIntent = Cashier::stripe()->paymentIntents->create([
          'customer' => $user->stripe_id,
          'amount' => $order->getTotal() * 100, // cents!
          'currency' => 'usd',
          'payment_method' => $pm_id,
          'confirmation_method' => 'manual',
          'confirm' => true,
          'return_url' => route('payment.success'),
          'metadata' => [
            'order_id' => $order->id,
          ],
        ]);

      } catch (\Exception $e) {
        Log::critical('Error while payement creation', [
          'order' => $order,
          'error' => $e,
        ]);
        DB::rollBack();
        $this->dispatch('toastError', ['message' => 'Something went wrong ... Please contact with administration!']);
        return ;
      }
      DB::commit();

      $order->update(['payment_id' => $paymentIntent->id]);
      if ($paymentIntent->status === 'requires_action') {
          $this->requiresAction = true;
          $this->clientSecret = $paymentIntent->client_secret;
          $this->dispatch('requires-action', ['clientSecret' => $this->clientSecret]);
      } elseif ($paymentIntent->status === 'succeeded') {
          $url = route('payment.success') . '/?payment_intent=' . $paymentIntent->id;
          return redirect($url);
      } else {
          return redirect()->route('payment.error');
      }
    }

    public function getOrder(): ?Order
    {
      return Order::where('id', Crypt::decrypt($this->order_id))
        ->with('user', 'order_products.product')
        ->first();
    }

    public function render()
    {
      $order = $this->getOrder();
      $paymentMethods = $order->user_id !== 0 ? $order->user->paymentMethods() : null;

      return view('livewire.checkout', [
        'order' => $order,
        'user' => $order->user,
        'paymentMethods' => $paymentMethods,
      ]);
    }
}
