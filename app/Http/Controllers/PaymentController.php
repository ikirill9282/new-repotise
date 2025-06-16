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


class PaymentController extends Controller
{
  public function intent(Request $request)
  {
    if ($request->has('checkout')) {
      $intent = CustomEncrypt::decodeUrlHash($request->get('checkout'));
      return response()->json(Cashier::stripe()->paymentIntents->retrieve(
        $intent['id'],
        []
      ));
    }

    $transaction = Cashier::stripe()->paymentIntents->create([
      'amount' => 2000,
      'currency' => 'usd',
      'automatic_payment_methods' => ['enabled' => true],
    ]);

    return response()->json($transaction);
  }


  public function checkout(Request $request)
  {
    $validaor = Validator::make($request->all(), [
      'checkout' => 'required|string',
    ]);

    if ($validaor->fails()) {
      return redirect('/?modal=cart')
        ->withErrors($validaor)
        ->withInput();
    }

    $checkout = CustomEncrypt::decodeUrlHash($request->get('checkout'));
    $transaction = Cashier::stripe()->paymentIntents->retrieve(
      $checkout['id'],
      []
    );
    $page = Page::where('slug', 'checkout')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $cart = new Cart();
    $order = Order::preparing($cart);

    // dd($transaction);
    return view("site.pages.checkout", [
      'page' => $page, 
      'order' => $order,
      'transaction' => $transaction,
    ]);
  }

  public function confirm(Request $request)
  {
    $valid = $request->validate([
      'tid' => 'required|string',
      'fullname' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'is-gift' => 'sometimes|nullable|boolean',
      'recipient' => 'required_if:is-gift,"1"|string|max:255',
      'recipient_message' => 'sometimes|nullable|string|max:255',
    ]);

    $cart = new Cart();
    $prepared = Order::preparing($cart);
    $valid['tid'] = CustomEncrypt::decodeUrlHash($valid['tid'])['id'];
    $paymentIntent = Cashier::stripe()->paymentIntents->retrieve($valid['tid']);
    
    if ($paymentIntent->metadata?->order_id ?? false)  {
      $order = Order::find($paymentIntent->metadata->order_id);
      $user = $order->user;
      $customer = $user->asStripeCustomer();
      $order = $prepared->mergePrepared($order);

    } else {
      $password = User::makePassword();
      $user = User::firstOrCreate(
        ['email' => $valid['email']],
        [
          'name' => $valid['fullname'],
          'email' => $valid['email'],
          'password' => $password,
        ]
      );
      $customer = $user->asStripeCustomer();

      $prepared->user_id = $user->id;
      $prepared->payment_id = $paymentIntent->id;
      $prepared->recipient = $valid['recipient'] ?? null;
      $prepared->recipient_message = $valid['recipient_message'] ?? null;

      $order = $prepared->savePrepared();


      Mail::to($user->email)
        ->send(new InviteByPurchase($user, $order, $password));
      
      PaymentIntents::create([
        'user_id' => $user->id,
        'stripe_id' => $paymentIntent->id,
        'status' => $paymentIntent->status,
      ]);
    }

    Cashier::stripe()->paymentIntents->update(
      $paymentIntent->id,
      [
        'amount' => $order->getTotal() * 100,
        'customer' => $customer->id,
        'metadata' => [
          'fullname' => $valid['fullname'],
          'email' => $valid['email'],
          'user_id' => $user->id,
          'order_id' => $order->id,
          'is_gift' => boolval($valid['is-gift']),
        ],
      ]
    );

    return response()->json(Cashier::stripe()->paymentIntents->retrieve(
      $paymentIntent->id,
      []
    ));
  }

  public function orderComplete(Request $request)
  {
    $paymentIntent = Cashier::stripe()->paymentIntents->retrieve(
      $request->get('payment_intent'),
      []
    );
    $order = Order::find($paymentIntent->metadata->order_id);
    if (is_null($order)) {
      return view('site.pages.payment-error', [
        'page' => Page::where('slug', 'payment-error')->with('config')->first(),
        'order' => $order,
        'paymentIntent' => $paymentIntent,
      ]);
    }
    $order->update([
      'status_id' => EnumsOrder::PAID,
    ]);
    $orderPaymentIntent = $order->paymentIntents()->where('stripe_id', $paymentIntent->id)->first();
    $orderPaymentIntent->update([
      'status' => $paymentIntent->status,
    ]);
    $paymentIntentIds = $order->paymentIntents()->where('stripe_id', '!=', $orderPaymentIntent->stripe_id)
      ->pluck('stripe_id')
      ->toArray();

    CancelPaymentIntents::dispatch($paymentIntentIds);
    (new Cart())->flushCart();

    return view('site.pages.payment-success', [
      'page' => Page::where('slug', 'payment-success')->with('config')->first(),
      'order' => $order,
      'paymentIntent' => $paymentIntent,
    ]);
  }
}
