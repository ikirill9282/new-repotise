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
    
    $order = Order::whereHas(
        'payments', 
        fn($query) => $query->where('stripe_id', $valid['payment_intent'])
      )
      ->with([
        'products.preview',
        'products.types',
        'products.locations',
        'order_products.product.preview',
        'order_products.product.types',
        'order_products.product.locations',
      ])
      ->first();

    if (!$order) {
      return (new FallbackController)($request);
    }

    $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($valid['payment_intent']);
    $paymentMethod = Cashier::stripe()->paymentMethods->retrieve($paymentIntent->payment_method);

    if ($paymentIntent->status == PaymentIntent::STATUS_SUCCEEDED) {
      Payments::query()
        ->where('stripe_id', $paymentIntent->id)
        ->update(['status' => $paymentIntent->status]);
    }
    
    // $order->update(['status_id' => EnumsOrder::PAID]);
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

  public function error(Request $request)
  {
    return view('site.pages.payment-error', [
      'page' => Page::where('slug', 'payment-error')->with('config')->first(),
    ]);
  }
}
