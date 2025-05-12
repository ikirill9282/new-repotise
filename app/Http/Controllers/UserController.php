<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
  
  public function cart(Request $request)
  {
    // Session::forget('cart');
    $page = $this->getPage('cart');

    $cart = Auth::user()->getCart();
    $order = Order::prepare($cart);
    
    // $products = Auth::user()->getCartProducts();
    
    // if ($order?->products && !empty($order->products)) {
    //   $order->products = array_map(function($item) use($products) {
    //     $product = $products->where('id', $item['id'])->first();
        
    //     if (is_null($product)) return null;
  
    //     $item['model'] = $product;
  
    //     return $item;
    //   }, $order->products);
    // }

    return view("site.page", ['page' => $page, 'order' => $order]);
  }

  public function order(Request $request)
  {
    $cart = Auth::user()->getCart();
    if (isset($cart['products']) && !empty($cart['products'])) {
      $prepared = Order::prepare($cart);
      $order = Order::create([
        'user_id' => Auth::user()->id,
        'price' => $prepared->getTotal(),
        'tax' => $prepared->getTax(),
        'price_without_discount' => $prepared->getAmount(),
        'promocode' => $prepared->promocode?->id,
        'recipient' => ($request->has('is-gift') && $request->get('is-gift')) ? $request->get('recipient') : null,
        'recipient_message' => ($request->has('is-gift') && $request->get('is-gift')) ? $request->get('recipient_message') : null,
      ]);

      $order->products()->sync(array_map(function($item) {
        $item['product_id'] = $item['id'];
        unset($item['id']);
        return $item;
      }, $cart['products']));

      Auth::user()->flushCart();
    }
  }

  protected function getPage(string $name)
  {
    return Page::where('slug', $name)
      ->with('sections.variables')
      ->first();
  }
}
