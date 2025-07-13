<?php

namespace App\Http\Controllers;

use App\Enums\Order as EnumsOrder;
use App\Helpers\SessionExpire;
use App\Search\SearchClient;
use App\Models\Page;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\User;
use App\Models\SearchQueries;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Helpers\CustomEncrypt;
use Illuminate\Support\ItemNotFoundException;
use App\Models\Order;
use App\Services\Cart;
use Exception;

class SiteController extends Controller
{

  public function home(Request $request)
  {
    $page = Page::where('slug', 'home')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view('site.pages.home', ['page' => $page]);
  }

  public function insights(Request $request)
  {
    $page = Page::where('slug', 'insights')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view('site.pages.insights', [
      'page' => $page,
      'articles' => Article::query()
        ->orderByDesc('id')
        ->paginate(9),
    ]);
  }

  public function feed(Request $request, string $slug)
  {
    $page = Page::where('slug', 'feed')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    if (request()->has('aid')) {
      $rdata = CustomEncrypt::decodeUrlHash(request()->get('aid'));
      if (isset($rdata['id'])) $id = $rdata['id'];
    }

    $first_article = (isset($id)) ? Article::find($id) : null;
    if ($first_article) $first_article->updateViews();

    return view('site.pages.feed', [
      'page' => $page,
      'articles' => Article::query()
        ->when(isset($id), fn($q) => $q->where('id', '!=', $id))
        ->orderByDesc('id')
        ->limit(3)
        ->get()
        ->all(),
      'last_news' => Article::getLastNews(),
      'first_article' => $first_article,
    ]);
  }

  public function helpCenter(Request $request)
  {
    $page = Page::where('slug', 'help-center')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view('site.pages.help-center', ['page' => $page]);
  }

  public function favorites(Request $request)
  {
    if (!Auth::check()) return redirect('/');

    $page = Page::where('slug', 'favorites')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view('site.pages.favorites', ['page' => $page]);
  }

  public function search(Request $request)
  {
    $page = Page::where('slug', 'search')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $query = ($request->has('q') && !empty($request->get('q'))) ? $request->get('q') : null;
    $search_results = is_null($query) ? [] : SearchClient::full($query);
    $tags = SearchClient::getTagsFromItem($search_results[0] ?? []);

    if (!is_null($query)) {
      SearchQueries::create([
        'text' => $query,
        'found' => count($search_results),
      ]);
    }

    return view('site.pages.search', [
      'page' => $page,
      'search_results' => $search_results,
      'tags' => $tags,
      'query' => $query,
    ]);
  }

  public function policies(Request $request, ?string $slug = null)
  {
    $page = Page::where('slug', ($slug ?? 'all-policies'))
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    return view('site.pages.custom-page', ['page' => $page]);
  }

  public function product(Request $request, string $country, string $product)
  {
    $page = Page::where('slug', 'product')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $product = Product::findByPid(request()->get('pid'));

    if (!$product) {
      return (new FallbackController())($request);
    }

    return view('site.pages.product', [
      'page' => $page,
      'product' => $product,
    ]);
  }
  
  public function products(Request $request)
  {
    $page = Page::where('slug', 'products')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $params = $request->route()->parameters();

    if (array_key_exists('product', $params) && $request->has('pid')) {
      $response_data['page'] = Page::where('slug', 'product')->with('sections.variables')->first();
      $response_data['product'] = Product::findByPid(request()->get('pid'));

      if (!$response_data['product']) {
        throw new ItemNotFoundException('Product Undefined');
      }
      
      return $response_data;
    }

    $valid = $request->validate([
      'rating' => 'sometimes|nullable|integer',
      'price' => 'sometimes|nullable|array',
      'price.min' => 'integer',
      'price.max' => 'integer',
      'categories' => 'sometimes|nullable|string',
      'locations' => 'sometimes|nullable|string',
      'sale' => 'sometimes|nullable|integer',
      'type' => 'sometimes|nullable|string',
      'q' => 'sometimes|nullable|string',
    ]);

    if (isset($valid['categories'])) {
      $valid['categories'] = is_null($valid['categories']) ? null : explode(',', $valid['categories']);
    }
    if (isset($valid['locations'])) {
      $valid['locations'] = is_null($valid['locations']) ? null : explode(',', $valid['locations']);
    }
    
    $valid = array_filter($valid, fn($item) => !is_null($item));

    $query = Product::query()
      ->when(
        isset($valid['rating']),
        fn($q) => $q->where('rating', '>=', $valid['rating']),
      )
      ->when(
        isset($valid['price']['min']),
        fn($q) => $q->where('price', '>=', $valid['price']['min']),
      )
      ->when(
        isset($valid['price']['max']),
        fn($q) => $q->where('price', '<=', $valid['price']['max']),
      )
      ->when(
        isset($valid['categories']),
        fn($q) => $q->whereHas('categories', fn($sq) => $sq->whereIn('categories.slug', $valid['categories'])),
      )
      ->when(
        isset($valid['type']),
        fn($q) => $q->whereHas('type', fn($sq) => $sq->where('slug', $valid['type'])),
      )
      ->when(
        isset($valid['locations']),
        fn($q) => $q->whereHas('location', fn($sq) => $sq->whereIn('locations.slug', $valid['locations'])),
      )
      ->when(
        isset($valid['q']),
        function($q) use ($valid) {
          $client = new SearchClient();
          $products = $client->findIn($valid['q'], 'products', 5000);
          $product_ids = array_column($products, 'id');

          $q->whereIn('id', $product_ids);
        },
      )
      ->when(
        array_key_exists('country', $params),
        fn($q) => $q->whereHas('location', fn($sq) => $sq->where('locations.slug', $params['country'])),
      )
    ;

    $paginator = $query->paginate(20);

    return view('site.pages.products', [
      'page' => $page,
      'paginator' => $paginator,
    ]);
  }

  public function invite(Request $request)
  {
    $page = Page::where('slug', 'invite')
      ->with('config')
      ->first();
    
    return view('site.pages.invite', [
      'page' => $page,
    ]);
  }

  public function referal(Request $request)
  {
    $page = Page::where('slug', 'referal')
      ->with('config')
      ->first();
    
    return view('site.pages.referal', [
      'page' => $page,
    ]);
  }
}