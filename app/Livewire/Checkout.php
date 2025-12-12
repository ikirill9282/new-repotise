<?php

namespace App\Livewire;

use App\Jobs\CancelPaymentIntents;
use App\Models\Order;
use App\Models\Payments;
use App\Models\Discount;
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
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
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

    public ?string $promocode = null;

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;

      if (Auth::check()) {
        $this->form['fullname'] = Auth::user()->name;
        $this->form['email'] = Auth::user()->email;
      }

      // Инициализируем promocode из заказа, если есть
      $order = $this->getOrder();
      if ($order->discount && $order->discount->type === 'promocode') {
        $this->promocode = $order->discount->code;
      }

      $success_payment = $order->getSuccessPayment();
      if ($success_payment) {
        return $this->paymentResult('success', $success_payment->stripe_id);
      }

      $setupIntent = Cashier::stripe()->setupIntents->create([
        'payment_method_types' => ['card'],
      ]);
      $this->clientSecret = $setupIntent->client_secret;
    }

    public function applyPromocode(): void
    {
      $this->validate([
        'promocode' => 'required|string',
      ]);

      $order = $this->getOrder();
      $discount = Discount::where('code', $this->promocode)
        ->where('type', 'promocode')
        ->where('active', true)
        ->first();

      if (!$discount) {
        $this->addError('promocode', 'Invalid promo code.');
        return;
      }

      if (!$discount->isAvailable($order)) {
        $this->addError('promocode', 'This promo code is not available for your order.');
        return;
      }

      $order->applyDiscount($discount);
      $this->updatePaymentIntent();
      $this->resetErrorBag('promocode');
    }

    public function removePromocode(): void
    {
      $this->order->removeDiscount();
      $this->promocode = null;
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
      $order = $this->getOrder();
      
      // Проверка возможности подарка
      if ($this->form['gift']) {
        if ($this->hasSubscriptions($order)) {
          throw new ValidationException(
            Validator::make([], [])->errors()->add('gift', 'Gifts are not available for subscription products.')
          );
        }
        
        if ($this->hasOnlyFreeProducts($order)) {
          throw new ValidationException(
            Validator::make([], [])->errors()->add('gift', 'Sending gifts is only available for paid products.')
          );
        }
      }

      $validator = Validator::make($this->form, [
        'fullname' => 'required|string',
        'email' => 'required|email',
        'gift' => 'required|boolean',
        'recipient' => 'required_if_accepted:form.gift|nullable|email',
        'recipient_message' => 'required_if_accepted:form.gift|nullable|string|max:150',
        'payment_method' => 'sometimes|nullable|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
        return false;
      }

      $valid = $validator->validated();
      return ['action' => isset($valid['payment_method']) ? $valid['payment_method'] : 'create'];
    }

    protected function hasSubscriptions(Order $order): bool
    {
      $order->loadMissing('order_products.product');
      return $order->order_products->contains(function ($orderProduct) {
        return $orderProduct->product && $orderProduct->product->subscription;
      });
    }

    protected function hasOnlyFreeProducts(Order $order): bool
    {
      $order->loadMissing('order_products.product');
      return $order->order_products->every(function ($orderProduct) {
        if (!$orderProduct->product) {
          return false;
        }
        $price = $orderProduct->product->price ?? 0;
        $salePrice = $orderProduct->product->sale_price ?? 0;
        return ($price - $salePrice) <= 0;
      });
    }

    #[On('makePayment')]
    public function onMakePayment(string $pm_id)
    { 
    $order = $this->getOrder();
    if (!$order) {
      return redirect()->route('payment.error', ['reason' => 'internal_error']);
    }

    if (Auth::check() && $this->orderContainsSelfProduct($order, Auth::user())) {
      return redirect()->route('payment.error', ['reason' => 'self_purchase']);
    }

      DB::beginTransaction();
      try {
        $buyerUser = null;
        
        // Создание/поиск покупателя (buyer_user_id)
        if (!Auth::check()) {
          $buyerUser = User::firstOrCreate(
            ['email' => $this->form['email']],
            [
              'username' => $this->form['fullname'],
              'password' => User::makePassword(),
            ]
          );
        } else {
          $buyerUser = Auth::user();
        }

        // Если покупатель был создан, отправляем письмо №5 (User Created)
        // Письмо будет отправлено через систему email-шаблонов с триггером USER_CREATED
        if (!Auth::check() && $buyerUser->wasRecentlyCreated) {
          // Отправка письма происходит автоматически через систему уведомлений
          // Не отправляем пароль напрямую - пользователь установит его через попап
        }

        // Обработка подарка
        $recipientUser = null;
        if ($this->form['gift'] && !empty($this->form['recipient']) && $this->form['recipient'] !== $buyerUser->email) {
          // Находим или создаем пользователя для получателя подарка
          $recipientUser = User::firstOrCreate(
            ['email' => $this->form['recipient']],
            [
              'username' => explode('@', $this->form['recipient'])[0],
              'password' => User::makePassword(),
            ]
          );

          // Присваиваем заказ получателю подарка
          $order->update([
            'user_id' => $recipientUser->id,
            'gift' => 1,
            'recipient' => $this->form['recipient'],
            'recipient_message' => $this->form['recipient_message'] ?? null,
          ]);

          $user = $recipientUser; // Используем получателя для проверок
        } else {
          // Обычная покупка - присваиваем заказ покупателю
          $order->update([
            'user_id' => $buyerUser->id,
            'gift' => 0,
            'recipient' => null,
            'recipient_message' => null,
          ]);

          $user = $buyerUser;
        }

      if ($this->orderContainsSelfProduct($order, $user)) {
        DB::rollBack();
        return redirect()->route('payment.error', ['reason' => 'self_purchase']);
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
          $metadata = [
            'order_id' => $order->id,
          ];
          
          // Сохраняем email покупателя в метаданных для подарков
          if ($order->gift == 1) {
            $metadata['buyer_email'] = $this->form['email'];
          }
          
          $paymentIntent = Cashier::stripe()->paymentIntents->create([
            'customer' => $user->stripe_id,
            'amount' => $order->getTotal() * 100, // cents!
            'currency' => 'usd',
            'payment_method' => $paymentMethod->id,
            'confirmation_method' => 'automatic',
            'confirm' => true,
            'return_url' => route('payment.success'),
            'metadata' => $metadata,
          ]);

          $order->payments()->create([
            'user_id' => $order->user_id,
            'stripe_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount / 100, // !cents
          ]);
        }

      } catch (CardException $e) {
        // Обработка ошибок карты от Stripe
        $errorCode = $e->getStripeCode();
        $declineCode = $e->getDeclineCode();
        
        Log::warning('Stripe card error during payment', [
          'order_id' => $order->id ?? null,
          'error_code' => $errorCode,
          'decline_code' => $declineCode,
          'message' => $e->getMessage(),
        ]);
        
        DB::rollBack();
        
        // Если есть PaymentIntent, сохраняем его для отображения ошибки
        if (isset($paymentIntent) && $paymentIntent->id) {
          return redirect()->route('payment.error', [
            'payment_intent' => $paymentIntent->id,
            'reason' => $errorCode,
            'decline_reason' => $declineCode,
          ]);
        }
        
        return redirect()->route('payment.error', [
          'reason' => $errorCode ?? 'card_declined',
          'decline_reason' => $declineCode,
        ]);
      } catch (RateLimitException | InvalidRequestException | AuthenticationException | ApiConnectionException | ApiErrorException $e) {
        // Обработка других ошибок Stripe API
        Log::error('Stripe API error during payment', [
          'order_id' => $order->id ?? null,
          'error_type' => get_class($e),
          'message' => $e->getMessage(),
        ]);
        
        DB::rollBack();
        
        if (isset($paymentIntent) && $paymentIntent->id) {
          return redirect()->route('payment.error', [
            'payment_intent' => $paymentIntent->id,
            'reason' => 'internal_error',
          ]);
        }
        
        return redirect()->route('payment.error', ['reason' => 'internal_error']);
      } catch (\Exception $e) {
        // Обработка всех остальных ошибок
        Log::critical('Error while payment creation', [
          'order_id' => $order->id ?? null,
          'error_type' => get_class($e),
          'message' => $e->getMessage(),
          'trace' => $e->getTraceAsString(),
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
      $paymentMethods = collect([]); // По умолчанию пустая коллекция
      
      $canGift = true;
      $giftDisabledReason = null;
      
      if ($order) {
        if ($this->hasSubscriptions($order)) {
          $canGift = false;
          $giftDisabledReason = 'Gifts are not available for subscription products.';
        } elseif ($this->hasOnlyFreeProducts($order)) {
          $canGift = false;
          $giftDisabledReason = 'Sending gifts is only available for paid products.';
        }
      }
      
      if ($order && $order?->user_id && $order?->user) {
        try {
          $paymentMethods = $order->user->paymentMethods();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
          // Если клиент не найден в Stripe, очищаем stripe_id
          if (str_contains($e->getMessage(), 'No such customer')) {
            Log::warning('Stripe customer not found, clearing stripe_id', [
              'user_id' => $order->user->id,
              'stripe_id' => $order->user->stripe_id,
            ]);
            
            $order->user->update(['stripe_id' => null]);
            // $paymentMethods уже пустая коллекция
          } else {
            // Другие ошибки Stripe - логируем
            Log::error('Error fetching payment methods from Stripe', [
              'user_id' => $order->user->id,
              'error' => $e->getMessage(),
            ]);
          }
        } catch (\Exception $e) {
          // Обработка всех остальных ошибок
          Log::error('Unexpected error fetching payment methods', [
            'user_id' => $order->user->id ?? null,
            'error' => $e->getMessage(),
          ]);
        }
      }

      return view('livewire.checkout', [
        'order' => $order,
        'user' => $order?->user,
        'paymentMethods' => $paymentMethods,
        'canGift' => $canGift,
        'giftDisabledReason' => $giftDisabledReason,
      ]);
    }

  protected function orderContainsSelfProduct(Order $order, User $user): bool
  {
    $order->loadMissing('order_products.product');

    return $order->order_products->contains(function ($orderProduct) use ($user) {
      return $orderProduct->product && $orderProduct->product->user_id === $user->id;
    });
  }
}
