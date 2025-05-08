<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use App\Helpers\SessionExpire;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function push(Request $request)
    {
      $valid = $request->validate(['item' => 'required|string']);
      try {
        $product_data = CustomEncrypt::decodeUrlHash($valid['item']);
        $cart_data = Auth::user()->getCart();

        if (!isset($cart_data['products'])) $cart_data['products'] = [];
        if (!in_array($product_data['id'], array_column($cart_data['products'], 'id'))) {
          $cart_data['products'][] = [
            'id' => $product_data['id'],
            'count' => 1,
          ];
        }

        SessionExpire::saveCart('cart', $cart_data);
      } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Something went wrong...'], 502);
      }

      return response()->json(['status' => 'success', 'products_count' => count($cart_data['products'])]);
    }

    public function count(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
        'count' => 'required|integer',
      ]);
      $valid['item'] = CustomEncrypt::decodeUrlHash($valid['item']);
      SessionExpire::setCartItemCount('cart', $valid['item']['id'], $valid['count']);
      Auth::user()->loadCart();

      return response()->json([
        'status' => 'success', 
        'count' => Auth::user()->getCartCount(),
        'costs' => [
          'subtotal' => number_format(Auth::user()->getCartAmount()),
        ],
      ]);
    }

    public function remove(Request $request)
    {
      $valid = $request->validate([
        'item' => 'required|string',
      ]);
      $item = CustomEncrypt::decodeUrlHash($valid['item']);
      Auth::user()->removeFromCart($item['id']);

      return response()->json([
        'status' => 'success',
        'count' => Auth::user()->getCartCount(),
        'costs' => [
          'subtotal' => number_format(Auth::user()->getCartAmount()),
        ],
      ]);
    }
}
