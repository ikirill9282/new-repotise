<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Services\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Enums\Order as EnumsOrder;
use App\Helpers\CustomEncrypt;
use App\Models\Page;
use App\Models\User;
use App\Models\PaymentIntents;
use App\Jobs\CancelPaymentIntents;
use App\Mail\InviteByPurchase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Jobs\ProcessOrder;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use App\Models\Payments;
use Illuminate\Support\Facades\Crypt;
use App\Models\Subscriptions;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
  private const STRIPE_ERROR_MESSAGES = [
    'card_declined' => [
      'title' => 'Card declined (generic)',
      'message' => 'Your card was declined. Please try a different card or contact your bank for details.',
      'push' => 'Payment failed — card declined.',
    ],
    'insufficient_funds' => [
      'title' => 'Insufficient funds',
      'message' => 'Insufficient funds. Your bank declined the charge — please use another card or payment method.',
      'push' => 'Payment failed — insufficient funds.',
    ],
    'expired_card' => [
      'title' => 'Expired card',
      'message' => 'This card has expired. Please use a different card or update the expiration date.',
      'push' => 'Payment failed — expired card.',
    ],
    'incorrect_number' => [
      'title' => 'Incorrect card number',
      'message' => 'Invalid card number. Please check the card number and try again.',
      'push' => 'Invalid card number.',
    ],
    'invalid_number' => [
      'title' => 'Invalid card number (format)',
      'message' => 'The card number looks incorrect. Please re-enter it carefully or try another card.',
      'push' => 'Card number invalid.',
    ],
    'incorrect_cvc' => [
      'title' => 'Incorrect CVC',
      'message' => 'Incorrect security code (CVC). Re-enter the 3- or 4-digit code from your card.',
      'push' => 'Invalid CVC.',
    ],
    'invalid_cvc' => [
      'title' => 'Invalid CVC (format)',
      'message' => 'Security code looks invalid. Check the CVC and try again.',
      'push' => 'Invalid security code.',
    ],
    'invalid_expiry_month' => [
      'title' => 'Invalid expiry month',
      'message' => 'Invalid expiration month. Please check the card’s expiration date and try again.',
      'push' => 'Invalid expiration month.',
    ],
    'invalid_expiry_year' => [
      'title' => 'Invalid expiry year',
      'message' => 'Invalid expiration year. Please check the card’s expiration date and try again.',
      'push' => 'Invalid expiration year.',
    ],
    'incorrect_zip' => [
      'title' => 'Incorrect postal code',
      'message' => 'Billing postal code looks incorrect. Update the ZIP/postal code and try again.',
      'push' => 'Incorrect ZIP/postal code.',
    ],
    'incorrect_address' => [
      'title' => 'Incorrect billing address',
      'message' => 'Billing address doesn’t match the card. Please check the address or try another card.',
      'push' => 'Billing address mismatch.',
    ],
    'payment_intent_authentication_failure' => [
      'title' => 'Authentication failed (3DS)',
      'message' => 'Authentication failed. Your bank didn’t complete verification — try again or use a different payment method.',
      'push' => 'Authentication failed — try another card.',
    ],
    'setup_intent_authentication_failure' => [
      'title' => 'Authentication failed (3DS)',
      'message' => 'Authentication failed. Your bank didn’t complete verification — try again or use a different payment method.',
      'push' => 'Authentication failed — try another card.',
    ],
    'invoice_payment_intent_requires_action' => [
      'title' => 'Authentication required (3DS)',
      'message' => 'Your bank requires authentication. Complete the verification in the bank’s window to finish the payment.',
      'push' => 'Action required — complete bank verification.',
    ],
    'payment_intent_action_required' => [
      'title' => 'Authentication required (3DS)',
      'message' => 'Your bank requires authentication. Complete the verification in the bank’s window to finish the payment.',
      'push' => 'Action required — complete bank verification.',
    ],
    'payment_method_not_available' => [
      'title' => 'Processor unavailable',
      'message' => 'This payment method is temporarily unavailable. Please try again later or use another payment method.',
      'push' => 'Payment method unavailable — try later.',
    ],
    'payment_method_currency_mismatch' => [
      'title' => 'Currency not supported by card',
      'message' => 'This card doesn’t support transactions in [CURRENCY]. Use a card that supports this currency or change currency.',
      'push' => 'Card doesn’t support this currency.',
    ],
    'processing_error' => [
      'title' => 'Processing error',
      'message' => 'A processing error occurred. Please try again or use a different payment method. If the issue persists, contact support.',
      'push' => 'Processing error — try again.',
    ],
    'self_purchase' => [
      'title' => 'Purchase unavailable',
      'message' => 'You cannot purchase your own product. Please choose a different item.',
      'push' => 'You cannot buy your own product.',
    ],
    'payment_method_provider_timeout' => [
      'title' => 'Provider timeout',
      'message' => 'We couldn’t reach the payment provider. Please try again in a few minutes or use another payment method.',
      'push' => 'Payment timeout — try later.',
    ],
    'card_decline_rate_limit_exceeded' => [
      'title' => 'Card declined — too many attempts (soft)',
      'message' => 'Too many attempts with this card. Please wait 24 hours or try a different card. If this was a mistake, contact your bank.',
      'push' => 'Too many attempts — try later.',
    ],
    'charge_expired_for_capture' => [
      'title' => 'Authorization expired',
      'message' => 'Authorization expired. The payment authorization has timed out — please try again.',
      'push' => 'Payment authorization expired.',
    ],
    'duplicate_transaction' => [
      'title' => 'Duplicate / already submitted',
      'message' => 'This looks like a duplicate payment. Please check your recent transactions or use a different card.',
      'push' => 'Possible duplicate transaction.',
    ],
    'payment_method_provider_decline' => [
      'title' => 'Provider/issuer decline',
      'message' => 'Payment was declined by the card issuer. Try a different card or contact your bank.',
      'push' => 'Payment declined by issuer.',
    ],
    'internal_error' => [
      'title' => 'Payment failed',
      'message' => 'Something went wrong on our side. Please try again or contact support if the issue persists.',
      'push' => 'Payment failed — please try again.',
    ],
    'default' => [
      'title' => 'Payment failed',
      'message' => 'We could not process your payment. Please try again later or contact support.',
      'push' => 'Payment failed.',
    ],
  ];
  public function checkout(Request $request)
  {
    if (!Session::exists('checkout') || empty(Session::get('checkout'))) {
      return redirect('/products');
    }
    
    $order = Order::find(Session::get('checkout'));
    // $transaction = Cashier::stripe()->paymentIntents->retrieve(
    //   $order->payment_id,
    //   []
    // );
    // $page = Page::where('slug', 'checkout')
    //   ->with('config')
    //   ->first();

    // if (is_null($page)) {
    //   return (new FallbackController())($request);
    // }

    return view("site.pages.checkout", [
      'order' => $order,
      // 'page' => $page, 
      // 'transaction' => $transaction,
    ]);
  }


  public function checkoutSubscription(Request $request)
  {
    if (!Session::exists('checkout-sub') || empty(Session::get('checkout-sub'))) {
      return redirect('/products');
    }
    
    $data = Session::get('checkout-sub');
    return view("site.pages.checkout-subscription", [
      'data' => $data,
    ]);
  }

  public function success(Request $request)
  {
    $valid = $request->validate(['payment_intent' => 'required|string']);

    $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($valid['payment_intent']);
    $paymentMethod = Cashier::stripe()->paymentMethods->retrieve($paymentIntent->payment_method);

    $payment = Payments::with('paymentable')
      ->where('stripe_id', $valid['payment_intent'])
      ->first();

    if (!$payment) {
      return (new FallbackController)($request);
    }

    if ($paymentIntent->status == PaymentIntent::STATUS_SUCCEEDED) {
      $payment->update(['status' => $paymentIntent->status]);
    }

    $paymentable = $payment->paymentable;

    if ($paymentable instanceof Order) {
      return $this->renderOrderSuccess($request, $paymentable, $paymentIntent, $paymentMethod);
    }

    if ($paymentable instanceof Subscriptions) {
      return $this->renderSubscriptionSuccess($paymentable, $paymentIntent, $paymentMethod);
    }

    return (new FallbackController)($request);
  }

  public function error(Request $request)
  {
    $paymentIntent = $this->fetchPaymentIntent($request->query('payment_intent'));
    $errorPayload = $this->resolveStripeErrorPayload($request, $paymentIntent);
    $context = $this->resolvePaymentContext($request, $paymentIntent, $errorPayload);

    return view('site.pages.payment-error', [
      'page' => Page::where('slug', 'payment-error')->with('config')->first(),
      'error' => $errorPayload,
      'cart' => $context['cart'],
      'summary' => $context['summary'],
      'paymentDetails' => $context['paymentDetails'],
    ]);
  }

  protected function renderOrderSuccess(Request $request, Order $order, PaymentIntent $paymentIntent, PaymentMethod $paymentMethod)
  {
    $order->load([
      'products.preview',
      'products.types',
      'products.locations',
      'order_products.product.preview',
      'order_products.product.types',
      'order_products.product.locations',
    ]);

    ProcessOrder::dispatch($order);
    $encryptedOrderId = Crypt::encryptString((string) $order->id);

    $order->order_products->each(function ($orderProduct) use ($encryptedOrderId) {
      if ($orderProduct->refunded) {
        $orderProduct->downloadModalArgs = null;
        return;
      }

      $orderProduct->downloadModalArgs = [
        'order_product_id' => Crypt::encryptString((string) $orderProduct->id),
        'order_id' => $encryptedOrderId,
      ];
    });

    $downloadableProduct = $order->order_products->first(function ($orderProduct) {
      return !empty($orderProduct->downloadModalArgs);
    });

    $downloadModalArgs = optional($downloadableProduct)->downloadModalArgs;

    return view('site.pages.payment-success', [
      'page' => Page::where('slug', 'payment-success')->with('config')->first(),
      'user' => Auth::user() ?? null,
      'order' => $order,
      'paymentIntent' => $paymentIntent,
      'paymentMethod' => $paymentMethod,
      'downloadModalArgs' => $downloadModalArgs,
    ]);
  }

  protected function renderSubscriptionSuccess(Subscriptions $subscription, PaymentIntent $paymentIntent, PaymentMethod $paymentMethod)
  {
    $subscription->load('user');

    $product = $this->resolveSubscriptionProduct($subscription);
    $latestPayment = $subscription->payments()->latest()->first();
    $periodLabel = $this->resolveSubscriptionPeriod($subscription);

    $subscription->loadMissing('user');

    return view('site.pages.subscription-success', [
      'page' => Page::where('slug', 'payment-success')->with('config')->first(),
      'user' => Auth::user() ?? $subscription->user,
      'subscription' => $subscription,
      'paymentIntent' => $paymentIntent,
      'paymentMethod' => $paymentMethod,
      'product' => $product,
      'latestPayment' => $latestPayment,
      'periodLabel' => $periodLabel,
      'nextBillingDate' => $this->resolveNextBillingDate($subscription),
    ]);
  }

  protected function resolveSubscriptionProduct(Subscriptions $subscription): ?Product
  {
    if (!str_starts_with($subscription->type, 'plan_')) {
      return null;
    }

    $parts = explode('_', $subscription->type);
    $productId = $parts[2] ?? null;

    return $productId ? Product::with(['preview', 'types', 'locations'])->find($productId) : null;
  }

  protected function resolveSubscriptionPeriod(Subscriptions $subscription): ?string
  {
    if (!str_starts_with($subscription->type, 'plan_')) {
      return null;
    }

    $parts = explode('_', $subscription->type);
    $period = $parts[1] ?? null;

    return $period ? ucfirst($period) : null;
  }

  protected function resolveNextBillingDate(Subscriptions $subscription): ?Carbon
  {
    try {
      $stripeSub = $subscription->asStripeSubscription();
      if (!empty($stripeSub->current_period_end)) {
        return Carbon::createFromTimestamp($stripeSub->current_period_end);
      }
    } catch (\Throwable $e) {
      return null;
    }

    return null;
  }

  protected function resolveStripeErrorPayload(Request $request, ?PaymentIntent $paymentIntent = null): array
  {
    $default = $this->mapStripeError(null, null);

    if ($paymentIntent) {
      $lastError = $paymentIntent->last_payment_error ?? null;

      if ($lastError) {
        return $this->mapStripeError(
          $lastError->code ?? null,
          $lastError->decline_code ?? null,
          $paymentIntent->currency ?? null
        );
      }
    }

    $reason = $request->query('reason');
    $declineReason = $request->query('decline_reason');

    if ($reason || $declineReason) {
      return $this->mapStripeError($reason, $declineReason);
    }

    return $default;
  }

  protected function mapStripeError(?string $code, ?string $declineCode, ?string $currency = null): array
  {
    $keys = array_filter([$code, $declineCode]);
    $keys[] = 'default';

    foreach ($keys as $key) {
      if ($key && isset(self::STRIPE_ERROR_MESSAGES[$key])) {
        $message = self::STRIPE_ERROR_MESSAGES[$key];

        if (
          (isset($message['message']) && str_contains($message['message'], '[CURRENCY]')) ||
          (isset($message['push']) && str_contains($message['push'], '[CURRENCY]'))
        ) {
          $label = $currency ? strtoupper($currency) : 'this currency';
          $message['message'] = str_replace('[CURRENCY]', $label, $message['message']);
          $message['push'] = str_replace('[CURRENCY]', $label, $message['push']);
        }

        $message['code'] = $code ?? $declineCode ?? ($key === 'default' ? 'unknown_error' : $key);
        return $message;
      }
    }

    $fallback = self::STRIPE_ERROR_MESSAGES['default'];
    $fallback['code'] = $code ?? $declineCode ?? 'unknown_error';

    return $fallback;
  }

  protected function resolvePaymentContext(Request $request, ?PaymentIntent $paymentIntent, array $error): array
  {
    $cart = null;
    $summary = null;
    $paymentDetails = [
      'method' => $this->describePaymentMethod($paymentIntent),
      'order_number' => null,
      'date' => null,
      'time' => null,
      'status' => $error['title'] ?? 'Payment failed',
    ];

    $paymentModel = null;
    $paymentIntentId = $request->query('payment_intent');

    if ($paymentIntentId) {
      $paymentModel = Payments::with('paymentable')->where('stripe_id', $paymentIntentId)->first();
    }

    if ($paymentModel && $paymentModel->created_at) {
      $createdAt = $paymentModel->created_at->timezone(config('app.timezone'));
      $paymentDetails['date'] = $createdAt->format('m.d.Y');
      $paymentDetails['time'] = $createdAt->format('H:i');
    }

    $order = null;

    if ($paymentModel && $paymentModel->paymentable instanceof Order) {
      $order = $paymentModel->paymentable;
    }

    if (!$order) {
      $order = $this->resolveOrderFromSession();
    }

    if ($order) {
      $order->loadMissing([
        'products.preview',
        'products.types',
        'products.locations',
        'products.categories',
        'order_products.product',
      ]);

      $cart = [
        'products' => $order->products
          ->map(fn($product) => [
            'model' => $product,
            'count' => $product->pivot->count ?? 1,
          ])
          ->values()
          ->all(),
      ];

      $currency = strtoupper($paymentIntent?->currency ?? 'USD');
      $summary = [
        'items' => $order->order_products->sum('count'),
        'currency' => $currency,
        'subtotal' => $order->getAmount(),
        'discount' => $order->getDiscount(),
        'tax' => $order->getTax(),
        'total' => $order->getTotal(),
      ];

      $paymentDetails['order_number'] = '#' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
      if (is_null($paymentDetails['date']) || is_null($paymentDetails['time'])) {
        $orderDate = ($order->updated_at ?? $order->created_at)?->timezone(config('app.timezone'));
        if ($orderDate) {
          $paymentDetails['date'] = $paymentDetails['date'] ?? $orderDate->format('m.d.Y');
          $paymentDetails['time'] = $paymentDetails['time'] ?? $orderDate->format('H:i');
        }
      }
    }

    return compact('cart', 'summary', 'paymentDetails');
  }

  protected function resolveOrderFromSession(): ?Order
  {
    $orderId = Session::get('checkout');
    if (!$orderId) {
      return null;
    }

    return Order::with([
      'order_products.product.preview',
      'order_products.product.types',
      'order_products.product.locations',
    ])->find($orderId);
  }

  protected function fetchPaymentIntent(?string $paymentIntentId): ?PaymentIntent
  {
    if (!$paymentIntentId) {
      return null;
    }

    try {
      return Cashier::stripe()->paymentIntents->retrieve($paymentIntentId);
    } catch (\Throwable $e) {
      Log::warning('Unable to load payment intent for error page', [
        'payment_intent' => $paymentIntentId,
        'error' => $e->getMessage(),
      ]);
    }

    return null;
  }

  protected function describePaymentMethod(?PaymentIntent $paymentIntent): string
  {
    if (!$paymentIntent) {
      return 'Card payment';
    }

    if (!empty($paymentIntent->payment_method)) {
      try {
        $method = Cashier::stripe()->paymentMethods->retrieve($paymentIntent->payment_method);

        if ($method->type === 'card' && isset($method->card)) {
          $brand = ucfirst($method->card->brand ?? 'Card');
          $last4 = $method->card->last4 ?? '••••';

          return sprintf('%s •••• %s', $brand, $last4);
        }

        return Str::headline($method->type);
      } catch (\Throwable $e) {
        Log::warning('Unable to describe payment method', [
          'payment_method' => $paymentIntent->payment_method,
          'error' => $e->getMessage(),
        ]);
      }
    }

    $type = $paymentIntent->payment_method_types[0] ?? 'card';
    return Str::headline($type) . ' payment';
  }
}
