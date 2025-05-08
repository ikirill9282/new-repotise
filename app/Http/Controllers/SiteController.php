<?php

namespace App\Http\Controllers;

use App\Helpers\SessionExpire;
use App\Search\SearchClient;
use App\Models\Admin\Page;
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

class SiteController extends Controller
{
  public function __invoke(Request $request, string $slug = 'home')
  {
    // Session::forget('cart');
    // dd(SessionExpire::getCart('cart'));
    $params = $request->route()->parameters();
    $page = Page::where('slug', $slug)
      ->with('sections.variables')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }
    
    $response_data = [
      'page' => $page,
    ];

    if ($page->slug === 'search') {
      $response_data = array_merge($response_data, $this->getSearchData($request));
    }

    if ($page->slug === 'feed') {
      $response_data = array_merge($response_data, $this->getFeedData($request));
    }

    if ($page->slug === 'favorites') {
      if (!Auth::check()) return redirect('/');
      $response_data = array_merge($response_data, $this->getFavoriteData($request));
    }

    if ($page->slug === 'products') {
      try {
        $response_data = array_merge($response_data, $this->getProductsData($request));
        
      } catch (ItemNotFoundException $e) {
        return (new FallbackController())->__invoke($request);
      }
    }

    return view("site.page", $response_data);
  }

  public function getFeedData(Request $request): array
  {
    
    $id = null;
    if (request()->has('aid')) {
      $rdata = CustomEncrypt::decodeUrlHash(request()->get('aid'));
      if (isset($rdata['id'])) $id = $rdata['id'];
    }

    $response_data = [];
    $response_data['articles'] = Article::when($id, fn($q) => $q->where('id', '!=', $id))->orderByDesc('id')->limit(3)->get()->all();
    $response_data['last_news'] = Article::getLastNews();
    $response_data['first_article'] = ($id) ? Article::find($id) : null;

    if ($response_data['first_article']) $response_data['first_article']->updateViews();
    
    return $response_data;
  }

  public function getSearchData(Request $request): array
  {
    $query = ($request->has('q') && !empty($request->get('q'))) ? $request->get('q') : null;
    $response_data = [];
    $response_data['search_results'] = is_null($query) ? [] : SearchClient::full($query);
    $response_data['tags'] = SearchClient::getTagsFromItem($response_data['search_results'][0] ?? []);
    if (!is_null($query)) {
      SearchQueries::create([
        'text' => $query,
        'found' => count($response_data['search_results']),
      ]);
    }

    return $response_data;
  }

  public function getFavoriteData(Request $request): array
  {
    return [];
  }

  public function getProductsData(Request $request): array
  {
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
    
    // FOR TEST
    // $paginator = $query->get();
    // while($paginator->count() < 20 && $paginator->isNotEmpty()) {
    //   $paginator = $paginator->collect()->merge($paginator)->slice(0, 20);
    // }

    return ['paginator' => $paginator];
  }
}