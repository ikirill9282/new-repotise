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
use App\Models\Product;
use Exception;

class CartController extends Controller
{
    public function push(Request $request)
    {
      $valid = $request->validate(['item' => 'required|string']);
      try {
        $cart = new Cart();
        $product_id = CustomEncrypt::getId($valid['item']);
        $product = Product::find($product_id);
        if (!$product) {
          return response()->json([
            'status' => 'error',
            'message' => 'Selected product is unavailable.',
          ], 404);
        }

        if (Auth::check() && $product->user_id === Auth::id()) {
          return response()->json([
            'status' => 'error',
            'message' => 'You cannot purchase your own product.',
          ], 422);
        }

        $cart->addProduct($product_id, 1);

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
      $product_id = CustomEncrypt::getId($valid['item']);
      $product = Product::find($product_id);
      if (!$product) {
        return response()->json([
          'status' => 'error',
          'message' => 'Selected product is unavailable.',
        ], 404);
      }

      if (Auth::check() && $product->user_id === Auth::id()) {
        return response()->json([
          'status' => 'error',
          'message' => 'You cannot purchase your own product.',
        ], 422);
      }

      $cart->addProduct($product_id, $valid['count']);
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
      $cart->removeProduct($id);
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
      
      return response()->json([
        'status' => 'success',
        'costs' => $order->getCosts(),
      ]);
    }
}
