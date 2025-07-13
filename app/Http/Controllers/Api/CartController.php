<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use App\Helpers\SessionExpire;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Services\Cart;
use Exception;

class CartController extends Controller
{
    public function push(Request $request)
    {
      $valid = $request->validate(['item' => 'required|string']);
      try {
        $cart = new Cart('cart');
        $product_data = CustomEncrypt::decodeUrlHash($valid['item']);
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

      $order = Order::preparing($cart);
      
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

      $order = Order::preparing($cart);

      return response()->json([
        'status' => 'success', 
        'count' => $cart->getCartCount(),
        'costs' => $order->getCosts(),
      ]);
    }

    public function remove(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
      ]);
      $cart = new Cart();
      $id = CustomEncrypt::getId($valid['item']);
      $cart->removeFromCart($id);
      $order = Order::preparing($cart);
      
      return response()->json([
        'status' => 'success',
        'count' => $cart->getCartCount(),
        'costs' => $order->getCosts(),
      ]);
    }

    public function promocode(Request $request)
    {
      $valid = $request->validate([
        'promocode' => 'required|string|exists:discounts,code',
      ]);

      $discount = Discount::where('code', trim($valid['promocode']))->first();
      if ($discount->visibility == 'private') {
        if (!Auth::check() || !$discount->user->id === Auth::user()->id) {
          throw new Exception('Incorrect promocode.');
        }
      }
      $cart = new Cart();
      $cart->applyPromocode($discount);
      $order = Order::preparing($cart);
      
      // dd($order->getCosts());
      return response()->json([
        'status' => 'success',
        'costs' => $order->getCosts(),
      ]);
    }
}
