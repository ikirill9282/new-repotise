<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
  
  public function cart(Request $request)
  {
    // Session::forget('cart');
    $page = $this->getPage('cart');

    $cart = Auth::user()->getCart();
    $products = Auth::user()->getCartProducts();
    
    if (isset($cart['products']) && !empty($cart['products'])) {
      $cart['products'] = array_map(function($item) use($products) {
        $product = $products->where('id', $item['id'])->first();
        
        if (is_null($product)) return null;
  
        $item['model'] = $product;
  
        return $item;
      }, $cart['products']);
    }
    return view("site.page", ['page' => $page, 'cart' => $cart]);
  }

  protected function getPage(string $name)
  {
    return Page::where('slug', $name)
      ->with('sections.variables')
      ->first();
  }
}
