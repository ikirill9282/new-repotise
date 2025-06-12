<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Services\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Enums\Order as EnumsOrder;
use App\Models\Page;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
  public function intent(Request $request)
  {
    $transaction = Cashier::stripe()->paymentIntents->create([
      'amount' => 2000,
      'currency' => 'usd',
      'automatic_payment_methods' => ['enabled' => true],
    ]);
    Session::put('payment_intent', $transaction->id);

    return response()->json($transaction);
  }


  public function checkout(Request $request)
  {
    $page = Page::where('slug', 'cart')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $cart = new Cart();
    $order = Order::prepare($cart);

    return view("site.pages.cart", ['page' => $page, 'order' => $order]);
  }

  public function orderComplete(Request $request)
  {
    $t_session = Cashier::stripe()->paymentIntents->retrieve(
      $request->get('payment_intent'),
      []
    );

    dd($t_session);
  }

  public function order(Request $request)
  {
    $cart = new Cart();
    
    if ($cart->hasProducts()) {
      $prepared = Order::prepare($cart);
      $order = Order::create([
        'user_id' => Auth::user()->id,
        'price' => $prepared->getTotal(),
        'tax' => $prepared->getTax(),
        'status_id' => EnumsOrder::get('new'),
        'price_without_discount' => $prepared->getAmount(),
        'promocode' => $prepared->promocode?->id,
        'recipient' => ($request->has('is-gift') && $request->get('is-gift')) ? $request->get('recipient') : null,
        'recipient_message' => ($request->has('is-gift') && $request->get('is-gift')) ? $request->get('recipient_message') : null,
      ]);

      $order->products()->sync(array_map(function($item) {
        $item['product_id'] = $item['id'];
        unset($item['id']);
        return $item;
      }, $cart->getCart()['products']));

      $cart->flushCart();

      return redirect('/order/success');
    }

    return redirect('/order/error');
  }

  public function payment(Request $request, ?string $status = null)
  {
    $slug = $status ? 'payment-' . $status : 'payment';
    $page = Page::where('slug', $slug)  
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view("site.pages.$slug", [
      'page' => $page,
      'status' => $status,
    ]);
  }
}
