<?php

namespace App\Livewire;

use App\Jobs\CancelPaymentIntents;
use App\Models\Order;
use App\Models\Payments;
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
use Laravel\Cashier\PaymentMethod;
use Livewire\Attributes\On;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use App\Enums\Order as EnumsOrder;
use App\Jobs\ProcessOrder;

class Checkout extends Component
{
    // 4000 0027 6000 3184 - 3d secure
    // 4000 0000 0000 0002 - error
    // 4000 0000 0000 9995 - no money

    public string $order_id;
    
    public bool $requiresAction = false;

    public string $clientSecret;

    public array $form = [
      'fullname' => null,
      'email' => null,
      'gift' => false,
      'recipient' => null,
      'recipient_message' => null,
      'payment_method' => null,
    ];

    // public ?string $promocode = null;

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;

      if (Auth::check()) {
        $this->form['fullname'] = Auth::user()->name;
        $this->form['email'] = Auth::user()->email;
      }

      $success_payment = $this->getOrder()->getSuccessPayment();
      if ($success_payment) {
        return $this->paymentResult('success', $success_payment->stripe_id);
      }

      $setupIntent = Cashier::stripe()->setupIntents->create([
        'payment_method_types' => ['card'],
      ]);
      $this->clientSecret = $setupIntent->client_secret;
    }

    public function removePromocode(): void
    {
      $this->order->removeDiscount();
      $this->updatePaymentIntent();
    }

    public function dropProduct(int $product_id): void
    {
      $order = $this->getOrder();

      DB::transaction(function() use ($product_id, $order) {
        $order->order_products()->where('product_id', $product_id)->delete();
        $order->load('products');

        if ($order->discount_id && !$order->discount->isAvailable($order)) {
          $order->removeDiscount();
        }
      });
      
      if ($order->products->isEmpty()) {
        $order->delete();
        Session::forget('checkout');
      } else {
        $order->recalculate();
      }
    }
    

    public function incrementProductCount(int $product_id): void
    {
      $order = $this->getOrder();
      $order_product = $order->order_products->where('product_id', $product_id)->first();

      $new_count = $order_product->count + 1;

      $order_product->count = $new_count;
      $order_product->update([
        'count' => $new_count,
        'total' => $order_product->getTotal(),
        'total_without_discount' => $order_product->getTotalWithoutDiscount(),
      ]);
    }

    public function decrementProductCount(int $product_id): void
    {
      $order = $this->getOrder();
      $order_product = $order->order_products->where('product_id', $product_id)->first();
      if ($order_product->count > 1) {
        $new_count = $order_product->count - 1;

        $order_product->count = $new_count;
        $order_product->update([
          'count' => $new_count,
          'total' => $order_product->getTotal(),
          'total_without_discount' => $order_product->getTotalWithoutDiscount(),
        ]);
      }
    }

    public function checkValidtion()
    {
      $validator = Validator::make($this->form, [
        'fullname' => 'required|string',
        'email' => 'required|email',
        'gift' => 'required|boolean',
        'recipient' => 'required_if_accepted:form.gift|nullable|email',
        'recipient_message' => 'required_if_accepted:form.gift|nullable|string',
        'payment_method' => 'sometimes|nullable|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
        return false;
      }

      $valid = $validator->validated();
      return ['action' => isset($valid['payment_method']) ? $valid['payment_method'] : 'create'];
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
        
        $paymentMethod = $this->addUserPaymentMethod($user, $pm_id);
        $paymentIntent = null;
        $need_creation = false;

        if ($order->hasIncompletePayment()) {
          $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($order->getLatestPayment()->stripe_id);

          if ($paymentIntent->payment_method == $pm_id) {
            if ($paymentIntent->status == 'requires_payment_method') {
              Cashier::stripe()->paymentIntents->update($paymentIntent->id, [
                'payment_method' => $paymentMethod->id,
              ]);
              $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($order->payment_id);
            }
          } else {
            $need_creation = true;
            CancelPaymentIntents::dispatch([$paymentIntent->id]);
          }

        } else {
          $need_creation = true;
        }

        if ($need_creation) {
          $paymentIntent = Cashier::stripe()->paymentIntents->create([
            'customer' => $user->stripe_id,
            'amount' => $order->getTotal() * 100, // cents!
            'currency' => 'usd',
            'payment_method' => $paymentMethod->id,
            'confirmation_method' => 'automatic',
            'confirm' => true,
            'return_url' => route('payment.success'),
            'metadata' => [
              'order_id' => $order->id,
            ],
          ]);

          $order->payments()->create([
            'user_id' => $order->user_id,
            'stripe_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount / 100, // !cents
          ]);
        }

      } catch (\Exception $e) {
        Log::critical('Error while payement creation', [
          'order' => $order ?? null,
          'error' => $e,
        ]);
        DB::rollBack();
        return redirect()->route('payment.error', ['reason' => 'internal_error']);
      }
      DB::commit();

      if (in_array($paymentIntent->status, ['requires_action', 'requires_confirmation'])) {
          $this->requiresAction = true;
          $this->clientSecret = $paymentIntent->client_secret;
          $this->dispatch('requires-action', [
            'clientSecret' => $this->clientSecret,
            'paymentMethod' => $paymentMethod->id,
          ]);

      } elseif ($paymentIntent->status === 'succeeded') {
          return $this->paymentResult('success', $paymentIntent->id);
      } else {
          return $this->paymentResult('error', $paymentIntent->id);
      }
    }

    public function addUserPaymentMethod(User $user, string $pm_id): PaymentMethod|StripePaymentMethod
    {
      if (empty($user->stripe_id)) {
        $user->createOrGetStripeCustomer();
      }

      $paymentMethod = Cashier::stripe()->paymentMethods->retrieve($pm_id);
      $pmType = $paymentMethod->type;

      $newFingerprint = $paymentMethod->card->fingerprint;

      $existingMethods = Cashier::stripe()->paymentMethods->all([
        'customer' => $user->stripe_id,
        'type' => $pmType,
      ]);

      $pm = null;
      if ($pmType === 'card') {
          $newFingerprint = $paymentMethod->card->fingerprint;

          foreach ($existingMethods->data as $method) {
              if ($method->card->fingerprint === $newFingerprint) {
                  $pm = $method;
                  break;
              }
          }
      } elseif ($pmType === 'sepa_debit') {
          $newBankDetails = $paymentMethod->sepa_debit;
          foreach ($existingMethods->data as $method) {
              $existingBankDetails = $method->sepa_debit;
              if (
                  $existingBankDetails->last4 === $newBankDetails->last4 &&
                  $existingBankDetails->bank_code === $newBankDetails->bank_code
              ) {
                  $pm = $method;
                  break;
              }
          }
      }

      if (is_null($pm)) {
        $user->addPaymentMethod($paymentMethod->id);
        $pm = $paymentMethod;
      }

      return $pm;
    }

    public function getOrder(): ?Order
    {
      return Order::where('id', Crypt::decrypt($this->order_id))
        ->with('user', 'order_products.product')
        ->first();
    }

    public function paymentResult(string $result, string $paymentIntentId)
    {
      $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($paymentIntentId);
      Payments::where('stripe_id', $paymentIntent->id)->update(['status' => $paymentIntent->status]);
      
      if ($paymentIntent->status == PaymentIntent::STATUS_SUCCEEDED) {
        $order = $this->getOrder();
        $order->update(['status_id' => EnumsOrder::PAID]);
        ProcessOrder::dispatch($order);
      }

      $url = route("payment.$result") . '/?payment_intent=' . $paymentIntentId;
      return redirect($url);
    }

    public function render()
    {
      $order = $this->getOrder();
      $paymentMethods = ($order && $order?->user_id) !== 0 ? $order?->user->paymentMethods() : null;

      return view('livewire.checkout', [
        'order' => $order,
        'user' => $order?->user,
        'paymentMethods' => $paymentMethods,
      ]);
    }
}
