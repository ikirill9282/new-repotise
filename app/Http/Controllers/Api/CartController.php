<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use App\Helpers\SessionExpire;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Promocode;
use App\Services\Cart;

class CartController extends Controller
{
    public function push(Request $request)
    {
      $valid = $request->validate(['item' => 'required|string']);
      try {
        $cart = new Cart('cart');
        $product_data = CustomEncrypt::decodeUrlHash($valid['item']);
        // $cart_data = Auth::user()->getCart();
        $cart_data = $cart->getCart();

        if (!isset($cart_data['products'])) $cart_data['products'] = [];
        if (!in_array($product_data['id'], array_column($cart_data['products'], 'id'))) {
          $cart_data['products'][] = [
            'id' => $product_data['id'],
            'count' => 1,
          ];
        }

        SessionExpire::saveCart('cart', $cart_data);
        $cart->loadCart();

      } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Something went wrong...'], 502);
      }

      $order = Order::prepare($cart->getCart());
      
      return response()->json(['status' => 'success', 'products_count' => $order->getCount()]);
    }

    public function count(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
        'count' => 'required|integer',
      ]);
      $cart = new Cart();
      $valid['item'] = CustomEncrypt::decodeUrlHash($valid['item']);
      SessionExpire::setCartItemCount('cart', $valid['item']['id'], $valid['count']);
      $cart->loadCart();

      $order = Order::prepare(Auth::user()->getCart());

      return response()->json([
        'status' => 'success', 
        'count' => Auth::user()->getCartCount(),
        'costs' => $order->getCosts(),
      ]);
    }

    public function remove(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
      ]);
      $cart = new Cart();
      $item = CustomEncrypt::decodeUrlHash($valid['item']);
      $cart->removeFromCart($item['id']);
      $order = Order::prepare($cart->getCart());
      
      return response()->json([
        'status' => 'success',
        'count' => $cart->getCartCount(),
        'costs' => $order->getCosts(),
      ]);
    }

    public function promocode(Request $request)
    {
      $valid = $request->validate(['promocode' => 'required|string']);
      $promocode = Promocode::where('code', $valid['promocode'])->first();
      if (!$promocode || !$promocode->active) {
        return response()->json(['status' => 'error', 'message' => 'Promocode doesn\'t exists!']);
      }

      Auth::user()->applyPromocode($promocode);
      $order = Order::prepare(Auth::user()->getCart());
      
      return response()->json([
        'status' => 'success',
        'costs' => $order->getCosts(),
      ]);
    }
}
