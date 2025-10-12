<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use App\Models\Order;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Cashier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutSubscription extends Component
{

    public string $order_id;

    public array $form = [
      'username' => null,
      'email' => null,
      'paymentMethod' => null,
    ];

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;
      $this->form = [
        'username' => Auth::user()?->username,
        'email' => Auth::user()?->email,
      ];
    }

    public function getOrder(): ?Order
    {
      return Order::where('id', Crypt::decrypt($this->order_id))
        ->with('user', 'order_products.product')
        ->first();
    }

    #[On('makeSubscription')]
    public function onMakeSubscription(string $pm_id)
    {
      $order = $this->getOrder();

      DB::beginTransaction();
      try {
        if ($order->user_id == 0) {
          $pwd = User::makePassword();
          $user = User::firstOrCreate(
            ['email' => $this->form['email']],
            [
              'username' => $this->form['username'],
              'password' => $pwd,
            ]
          );
          $user->sendPassword($pwd);
          $user->sendVerificationCode();
        } else {
          $user = $order->user;
        }
      } catch (\Exception $e) {
        Log::critical('Cant create new user for subscription', [
          'data' => $this->form,
          'error' => $e,
        ]);
        DB::rollBack();
        $this->dispatch('toastError', ['message' => 'Something went wrong... Please contact with administration!']);
        return ;
      }

      DB::commit();
      $user->addPaymentMethod($pm_id);
      $order_product = $order->order_products->first();

      try {
        $price_id = $order_product->product->subprice->getPeriodId($order->sub_period);
        $sub_name = $order->getSubscriptionType();
        $sub = $user->newSubscription($sub_name, $price_id)->create($pm_id, [
          'metadata' => [
            'order_id' => $order->id,
          ]
        ]);
      } catch (\Exception $e) {
        $this->dispatch('toastError', ['message' => 'Something went wrong ... Please contact with administration!']);
        Log::critical('Subscription error', [
          'order' => $order,
          'error' => $e,
        ]);
        return ;
      }

      $payment_id = $sub->latestInvoice()->payment_intent;
      $order->update([
        'payment_id' => $payment_id,
        'user_id' => $user->id,
      ]);
      
      $url = route('payment-success') . '/?payment_intent=' . $payment_id;
      return redirect($url);
    }

    public function checkValidtion()
    {
      $validator = Validator::make($this->form, [
        'username' => 'required|string',
        'email' => 'required|email',
        'paymentMethod' => 'sometimes|nullable|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }
      $valid = $validator->validated();

      return ['action' => isset($valid['paymentMethod']) ? $valid['paymentMethod'] : 'create'];
    }

    public function render()
    {
      $order = $this->getOrder();
      $paymentMethods = $order->user_id !== 0 ? $order->user->paymentMethods() : null;

      return view('livewire.checkout-subscription', [
        'order' => $this->getOrder(),
        'intent' => Cashier::stripe()->setupIntents->create([
          'payment_method_types' => ['card'],
        ]),
        'user' => $order->user,
        'paymentMethods' => $paymentMethods,
      ]);
    }
}
