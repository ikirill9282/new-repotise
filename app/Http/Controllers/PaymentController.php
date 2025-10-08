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
use PhpParser\Node\Expr\Cast\Object_;

class PaymentController extends Controller
{
  public function checkout(Request $request)
  {
    if (!Session::exists('checkout') || empty(Session::get('checkout'))) {
      return redirect('/products');
    }
    
    $order = Order::find(Session::get('checkout'));
    $transaction = Cashier::stripe()->paymentIntents->retrieve(
      $order->payment_id,
      []
    );
    $page = Page::where('slug', 'checkout')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view("site.pages.checkout", [
      'page' => $page, 
      'order' => $order,
      'transaction' => $transaction,
    ]);
  }

  public function success(Request $request)
  {
    $valid = $request->validate(['payment_intent' => 'required|string']);
    $order = Order::where('payment_id', $valid['payment_intent'])->first();

    if ($order->free()) {
      $paymentIntent = null;
      $paymentMethod = 'Free';
      $order->cancelTransaction('Used free product promocode.');
    } else {
      $paymentIntent = $order->getTransaction();
      $paymentMethod = Cashier::stripe()->paymentMethods->retrieve($paymentIntent->payment_method);
    }
    
    ProcessOrder::dispatch($order);
    
    return view('site.pages.payment-success', [
      'page' => Page::where('slug', 'payment-success')->with('config')->first(),
      'user' => Auth::user() ?? null,
      'order' => $order,
      'paymentIntent' => $paymentIntent,
      'paymentMethod' => $paymentMethod,
    ]);
  }

  public function error(Request $request)
  {
    return view('site.pages.payment-error', [
      'page' => Page::where('slug', 'payment-error')->with('config')->first(),
    ]);
  }
}
