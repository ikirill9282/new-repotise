<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use App\Models\Order;
use App\Models\Payments;
use App\Models\Product;
use App\Models\Subscriptions;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Cashier;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\PaymentMethod;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\PaymentIntent as StripePaymentIntent;


class CheckoutSubscription extends Component
{

    public array $form = [
      'username' => null,
      'email' => null,
      'paymentMethod' => null,
    ];

    public string $product_id;
    public string $period;

    public int $discount = 0;

    public int $tax = 0;

    public function mount(string $product_id, string $period)
    {
      $this->form = [
        'username' => Auth::user()?->username,
        'email' => Auth::user()?->email,
      ];
      $this->product_id = $product_id;
      $this->period = $period;
    }

    public function getProduct(): ?Product
    {
      return Product::where('id', Crypt::decrypt($this->product_id))
        ->with('subprice')
        ->first();
    }

    #[On('makeSubscription')]
    public function onMakeSubscription(string $pm_id)
    {

      $product = $this->getProduct();

      if (!$product) {
        Log::critical('Subscription product not found', [
          'product_id' => $this->product_id,
          'period' => $this->period,
        ]);
        $this->dispatch('toastError', ['message' => 'Subscription product is unavailable.']);
        return;
      }

      $subprice = $product->subprice;
      if (!$subprice) {
        Log::critical('Subscription plan missing pricing', [
          'product_id' => $product->id,
          'period' => $this->period,
        ]);
        $this->dispatch('toastError', ['message' => 'Subscription plan is not configured.']);
        return;
      }

      $price_id = $subprice->getPeriodId($this->period);
      if (!$price_id) {
        try {
          $product->publishInStripe();
          $product->refresh();
          $subprice = $product->subprice;
          $price_id = $subprice?->getPeriodId($this->period);
        } catch (\Throwable $e) {
          Log::critical('Failed to publish product in Stripe', [
            'product_id' => $product->id,
            'period' => $this->period,
            'error' => $e->getMessage(),
          ]);
        }
      }

      if (!$price_id) {
        Log::critical('Subscription price id not configured', [
          'product_id' => $product->id,
          'period' => $this->period,
          'stripe_data' => $subprice?->stripe_data,
        ]);
        $this->dispatch('toastError', ['message' => 'Subscription price is not configured for this period.']);
        return;
      }

      DB::beginTransaction();
      try {
        if (Auth::guest()) {
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
          $user = Auth::user();
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

      $paymentMethod = $this->addUserPaymentMethod($user, $pm_id);

      $subscription = $user->subscription($this->makeSubscriptionType());
      $paymentIntent = null;

      if ($subscription && $subscription->hasIncompletePayment()) {
        $paymentIntent = $subscription->latestPayment()->asStripePaymentIntent();
        
        $paymentIntent = Cashier::stripe()->paymentIntents->confirm($paymentIntent->id, [
          // 'payment_method' => $pm_id,
        ]);

        if (in_array($paymentIntent->status, ['requires_action', 'requires_confirmation'])) {
          $this->dispatch('requires-action', [
            'clientSecret' => $paymentIntent->client_secret,
            'paymentMethod' => $paymentMethod->id,
          ]);

        } elseif ($paymentIntent->status === 'succeeded') {
          return $this->paymentResult('success', $paymentIntent->id);
        } else {
          return $this->paymentResult('error', $paymentIntent->id);
        }
      }

      try {
        $sub_name = $this->makeSubscriptionType();
        $sub = $user->newSubscription($sub_name, $price_id)->create($pm_id);
        $paymentIntent = $this->addPayment($sub, $user);

      } catch (\Exception $e) {
        $sub = $user->subscription($this->makeSubscriptionType());
        $paymentIntent = $this->addPayment($sub, $user);

        if ($paymentIntent && in_array($paymentIntent->status, ['requires_action', 'requires_confirmation'])) {
          $this->dispatch('requires-action', [
            'clientSecret' => $paymentIntent->client_secret,
            'paymentMethod' => $pm_id,
          ]);
          return ;
        }

        $this->dispatch('toastError', ['message' => 'Something went wrong ... Please contact with administration!']);
        Log::critical('Subscription error', [
          'order' => $sub,
          'error' => $e,
        ]);

        return ;
      }
      
      return $this->paymentResult('success', $paymentIntent?->id ?? 'subscription');
    }

    public function addPayment($sub, $user): ?StripePaymentIntent
    {
      if (!$sub) {
        return null;
      }

      $latestPayment = $sub->latestPayment();
      if (!$latestPayment) {
        return null;
      }

      $paymentIntent = $latestPayment->asStripePaymentIntent();

      if (!Payments::where('stripe_id', $paymentIntent->id)->exists()) {
        $subscriptionModel = $sub instanceof Subscriptions ? $sub : Subscriptions::find($sub->id);

        $subscriptionModel?->payments()->create([
          'user_id' => $user->id,
          'stripe_id' => $paymentIntent->id,
          'status' => $paymentIntent->status,
          'amount' => $paymentIntent->amount / 100, // cents!
        ]);
      }

      return $paymentIntent;
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

    public function makeSubscriptionType()
    {
      return "plan_{$this->period}_" . Crypt::decrypt($this->product_id);
    }

    public function checkValidation()
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

    public function paymentResult(string $result, string $paymentIntentId)
    {
      $route = $result === 'success' ? 'subscription.success' : "payment.$result";
      $url = route($route) . '/?payment_intent=' . $paymentIntentId;
      return redirect($url);
    }

    public function render()
    {
      $product = $this->getProduct();
      $paymentMethods = Auth::check() ? Auth::user()->paymentMethods() : null;

      return view('livewire.checkout-subscription', [
        'intent' => Cashier::stripe()->setupIntents->create([
          'payment_method_types' => ['card'],
        ]),
        'product' => $product,
        'paymentMethods' => $paymentMethods,
      ]);
    }
}
