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


class PaymentController extends Controller
{
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
    return view('site.pages.payment-error', [
      'page' => Page::where('slug', 'payment-error')->with('config')->first(),
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
      $orderProduct->downloadModalArgs = [
        'order_product_id' => Crypt::encryptString((string) $orderProduct->id),
        'order_id' => $encryptedOrderId,
      ];
    });

    $downloadModalArgs = $order->order_products->first()->downloadModalArgs ?? null;

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
}
