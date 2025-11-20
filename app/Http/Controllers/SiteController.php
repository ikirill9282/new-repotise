<?php

namespace App\Http\Controllers;

use App\Enums\Order as EnumsOrder;
use App\Enums\Status;
use App\Helpers\SessionExpire;
use App\Search\SearchClient;
use App\Models\Page;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SearchQueries;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Helpers\CustomEncrypt;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Support\ItemNotFoundException;
use App\Models\Order;
use App\Models\Policies;
use App\Services\Cart;
use Exception;
use Illuminate\Support\Facades\Crypt;
use PDO;
use Illuminate\Support\Facades\DB;

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

  public function creators(Request $request)
  {
    $page = Page::where('slug', 'creators')
      ->with('config')
      ->first();

    $tags = User::whereHas('roles', fn($query) => $query->where('name', 'creator'))
      ->orderByDesc('id')
      ->select('username')
      ->get()
      ->pluck('username')
      ->map(fn($val) => ['title' => "@$val"]);
    
    $valid = $request->validate([
      'creator' => 'sometimes|nullable|string',
      'followers_min' => 'sometimes|nullable|integer', // Always in filters
      'followers_max' => 'sometimes|nullable|integer', // Always in filters
      'platforms' => 'sometimes|nullable|array',
      'langs' => 'sometimes|nullable|array',
      'countries' => 'sometimes|nullable|array',
      'collaboration' => 'sometimes|nullable|integer',
      'q' => 'sometimes|nullable|string',
      'sort' => 'sometimes|nullable|string',
    ]);

    $sortValue = $valid['sort'] ?? 'name_asc';
    $sort = match($sortValue) {
      'name_asc' => ['username', 'asc'],
      'name_desc' => ['username', 'desc'],
      'followers_desc' => ['followers_count', 'desc'],
      default => ['username', 'asc'],
    };

    $query = User::query()
      ->whereHas('roles', fn($query) => $query->whereIn('name', ['creator', 'seller']))
      ->withCount('followers')
      ->when(
        isset($valid['creator']),
        fn($query) => $query->where('username', str_ireplace('@', '', $valid['creator']))
      )
      ->when(
        isset($valid['followers_min']) && ($valid['followers_min'] > 0),
        fn($query) => $query
          ->having('followers_count', '>', $valid['followers_min'])
          ->having('followers_count', '<', $valid['followers_max'])
      )
      ->when(
        isset($valid['platforms']),
        function($query) use($valid) {
          $query->whereHas('options', function($subquery) use ($valid) {
            foreach ($valid['platforms'] as $platform) {
              $subquery->whereNotNull($platform);
            }
          });
        }
      )
      ->when(
        isset($valid['langs']),
        function($query) use($valid) {
          $langs = Language::select('id')->whereIn('name', $valid['langs'])->pluck('id')->toArray();
          $query->whereHas('options', fn($subquery) => $subquery->whereIn('language_id', $langs));
        }
      )
      ->when(
        isset($valid['countries']),
        function($query) use($valid) {
          $langs = Country::select('id')->whereIn('name', $valid['countries'])->pluck('id')->toArray();
          $query->whereHas('options', fn($subquery) => $subquery->whereIn('country_id', $langs));
        }
      )
      ->when(
        isset($valid['collaboration']),
        fn($query) => $query->whereHas('options', fn($subquery) => $subquery->where('collaboration', $valid['collaboration']))
      )
      ->when(
        isset($valid['q']),
        function($query) use ($valid) {
          $client = new SearchClient();
          $creators = $client->findIn($valid['q'], 'users', 5000);
          $creators_ids = array_column($creators, 'id');

          if (empty($creators_ids)) {
            $query->whereRaw('1 = 0');
            return;
          }

          $query->whereIn('id', $creators_ids);
        },
      )
      ->orderBy(...$sort)
      ;

    return view('site.pages.creators', [
      'page' => $page,
      'tags' => $tags,
      'creators' => $query->paginate(50)->appends(['sort' => $sortValue]),
      'sortOption' => $sortValue,
    ]);
  }

  public function insights(Request $request)
  {
    $page = Page::where('slug', 'insights')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $valid = $request->validate([
      'author' => 'sometimes|nullable|string',
      'sort' => 'sometimes|nullable|string|in:rating,popular,newest,oldest',
    ]);

    $newsPerPage = 10;
    $newsQuery = Article::query()
      ->whereHas('author', fn($query) => $query->where('id', 0))
      ->orderByDesc('id');

    $newsPaginator = (clone $newsQuery)->paginate($newsPerPage, ['*'], 'news_page');
    $newsTotal = (clone $newsQuery)->count();

    $sortOption = $valid['sort'] ?? 'rating';

    $articlesQuery = Article::query()
      ->when(
        isset($valid['author']),
        fn($q) => $q->whereHas('author', fn($sq) => $sq->where('users.username', str_ireplace('@', '', $valid['author']))),
      )
      ->withCount('likes');

    $articlesQuery = match ($sortOption) {
      'popular' => $articlesQuery->orderByDesc('views')->orderByDesc('likes_count'),
      'newest' => $articlesQuery->orderByDesc('created_at'),
      'oldest' => $articlesQuery->orderBy('created_at'),
      default => $articlesQuery->orderByDesc('likes_count')->orderByDesc('views'),
    };

    return view('site.pages.insights', [
      'page' => $page,
      'articles' => $articlesQuery->paginate(9)->appends(['sort' => $sortOption]),
      'news' => $newsPaginator,
      'newsTotal' => $newsTotal,
      'sortOption' => $sortOption,
    ]);
  }

  public function insightsNews(Request $request)
  {
    $valid = $request->validate([
      'page' => 'sometimes|nullable|integer|min:1',
      'per_page' => 'sometimes|nullable|integer|min:1|max:50',
    ]);

    $page = $valid['page'] ?? 1;
    $perPage = $valid['per_page'] ?? 10;

    $news = Article::query()
      ->whereHas('author', fn($query) => $query->where('id', 0))
      ->orderByDesc('id')
      ->paginate($perPage, ['*'], 'page', $page);

    $html = view('site.pages.insights.partials.news-items', [
      'items' => $news->items(),
    ])->render();

    return response()->json([
      'html' => $html,
      'next_page' => $news->hasMorePages() ? $news->currentPage() + 1 : null,
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
        ->whereHas('author.roles', fn($q) => $q->whereIn('name', ['creator', 'customer']))
        ->inRandomOrder()
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

    $valid = $request->validate([
      'products_sort' => 'sometimes|nullable|string|in:rating,popular,newest,oldest,price_high,price_low,price_desc,price_asc',
      'creators_sort' => 'sometimes|nullable|string|in:name_asc,name_desc,followers_desc',
    ]);

    $productsSortRaw = $valid['products_sort'] ?? 'rating';
    $productsSort = match ($productsSortRaw) {
      'price_desc' => 'price_high',
      'price_asc' => 'price_low',
      default => $productsSortRaw,
    };
    $creatorsSort = $valid['creators_sort'] ?? 'name_asc';

    $page = Page::where('slug', 'favorites')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $user = Auth::user();

    $favoriteProducts = $user->favorite_products()
      ->select('products.*', 'user_favorites.created_at as favorited_at')
      ->with(['preview', 'types', 'categories', 'locations'])
      ->withCount(['reviews' => fn($query) => $query->whereNull('parent_id')])
      ->get();

    $favoriteProducts = match ($productsSort) {
      'price_low' => $favoriteProducts
        ->sortBy(fn($product) => $product->getPrice())
        ->values(),
      'price_high' => $favoriteProducts
        ->sortByDesc(fn($product) => $product->getPrice())
        ->values(),
      'rating' => $favoriteProducts
        ->sortByDesc(fn($product) => (float) ($product->rating ?? 0))
        ->values(),
      'popular' => $favoriteProducts
        ->sortByDesc(fn($product) => (int) ($product->views ?? 0))
        ->values(),
      'newest' => $favoriteProducts
        ->sortByDesc(fn($product) => $product->published_at ?? $product->created_at ?? $product->favorited_at)
        ->values(),
      'oldest' => $favoriteProducts
        ->sortBy(fn($product) => $product->published_at ?? $product->created_at ?? $product->favorited_at)
        ->values(),
      default => $favoriteProducts
        ->sortByDesc(fn($product) => (float) ($product->rating ?? 0))
        ->values(),
    };

    $favoriteAuthors = $user->favorite_authors()
      ->select('users.*', 'user_favorites.created_at as favorited_at')
      ->with('options')
      ->withCount('followers')
      ->get();

    // Получаем статьи авторов вместо их профилей
    $favoriteAuthorsIds = $favoriteAuthors->pluck('id')->toArray();
    $favoriteArticles = collect();
    
    if (!empty($favoriteAuthorsIds)) {
      $favoriteArticles = \App\Models\Article::query()
        ->whereIn('user_id', $favoriteAuthorsIds)
        // ->where('status_id', \App\Enums\Status::ACTIVE) // Временно убрано для отображения всех статей
        ->with(['preview', 'author' => function($query) {
          $query->withCount('followers');
        }])
        ->withCount('likes')
        ->orderByDesc('published_at')
        ->orderByDesc('created_at')
        ->get();
    }

    $favoriteArticles = match ($creatorsSort) {
      'followers_desc' => $favoriteArticles
        ->sortByDesc(fn($article) => (int) ($article->author->followers_count ?? 0))
        ->values(),
      'name_desc' => $favoriteArticles
        ->sortByDesc(fn($article) => strtolower($article->author->username ?? $article->author->name ?? ''))
        ->values(),
      'name_asc' => $favoriteArticles
        ->sortBy(fn($article) => strtolower($article->author->username ?? $article->author->name ?? ''))
        ->values(),
      default => $favoriteArticles
        ->sortByDesc(fn($article) => $article->published_at ?? $article->created_at)
        ->values(),
    };

    return view('site.pages.favorites', [
      'page' => $page,
      'favoriteProducts' => $favoriteProducts,
      'favoriteAuthors' => $favoriteAuthors,
      'favoriteArticles' => $favoriteArticles,
      'productsSort' => $productsSort,
      'creatorsSort' => $creatorsSort,
    ]);
  }

  public function search(Request $request)
  {
    $page = Page::where('slug', 'search')
      ->with('config')
      ->first();

    if (is_null($page)) {
      return (new FallbackController())($request);
    }

    $valid = $request->validate([
      'sort' => 'sometimes|nullable|string|in:rating,popular,newest,oldest,price_high,price_low,price_desc,price_asc',
    ]);

    $query = ($request->has('q') && !empty($request->get('q'))) ? $request->get('q') : null;
    $sortOption = $valid['sort'] ?? 'relevance';

    $search_results = is_null($query) ? [] : SearchClient::full($query);

    // При сортировке по relevance ставим полные совпадения заголовка первыми
    if (!empty($search_results) && $sortOption === 'relevance' && !is_null($query)) {
      $queryLower = mb_strtolower(trim($query));
      $exactMatches = [];
      $otherResults = [];
      
      foreach ($search_results as $item) {
        // Для разных типов результатов используем разные поля для заголовка
        $title = match($item['index'] ?? '') {
          'products' => mb_strtolower(trim($item['title'] ?? $item['name'] ?? '')),
          'articles' => mb_strtolower(trim($item['title'] ?? '')),
          'users' => mb_strtolower(trim($item['name'] ?? $item['profile'] ?? '')),
          default => mb_strtolower(trim($item['title'] ?? $item['name'] ?? '')),
        };
        
        // Проверяем полное совпадение заголовка с поисковым запросом
        if ($title === $queryLower) {
          $exactMatches[] = $item;
        } else {
          $otherResults[] = $item;
        }
      }
      
      // Сначала полные совпадения, потом остальные результаты
      $search_results = array_merge($exactMatches, $otherResults);
    }

    if (!empty($search_results) && $sortOption !== 'relevance') {
      $sortResolver = function(array $item) use ($sortOption) {
        return match ($sortOption) {
          'newest' => isset($item['created_at']) ? Carbon::parse($item['created_at'])->timestamp : null,
          'oldest' => isset($item['created_at']) ? Carbon::parse($item['created_at'])->timestamp : null,
          'popular' => match ($item['index']) {
            'products' => $item['reviews_count'] ?? $item['rating'] ?? null,
            'users' => $item['followers_count'] ?? null,
            'articles' => $item['views'] ?? null,
            default => null,
          },
          default => null,
        };
      };

      $collection = collect($search_results)->map(function ($item, $index) {
        $item['_original_index'] = $index;
        return $item;
      });

      $sortable = $collection->filter(fn($item) => !is_null($sortResolver($item)));
      $unsortable = $collection->reject(fn($item) => !is_null($sortResolver($item)));

      $descendingSorts = ['newest', 'popular'];

      $sorted = in_array($sortOption, $descendingSorts, true)
        ? $sortable->sortByDesc(fn($item) => $sortResolver($item))
        : $sortable->sortBy(fn($item) => $sortResolver($item));

      $search_results = $sorted
        ->concat($unsortable->sortBy('_original_index'))
        ->values()
        ->map(function ($item) {
          unset($item['_original_index']);
          return $item;
        })
        ->all();
    }

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
      'sortOption' => $sortOption,
    ]);
  }

  public function allPolicies(Request $request)
  {
    return view('site.pages.policies-all', [
      'models' => Policies::select('title', 'slug')->get(),
    ]);
  }

  public function policies(Request $request, string $slug)
  {
    if (is_null($slug)) {
      
    } else {
    }
    $content = Policies::where('slug', $slug)->first()?->content ?? '';
    return view('site.pages.policies', ['content' => $content]);
  }

  public function product(Request $request, string $product)
  {
    if (!request()->has('pid')) {
      return redirect("/search/?q=$product");
    }

    if (!Auth::check() && $request->has('referal') && is_string($request->get('referal'))) {
      SessionExpire::set('referal', $request->get('referal'), Carbon::now()->addHours(24));
    }

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
      'author' => 'sometimes|nullable|string',
      'q' => 'sometimes|nullable|string',
      'sort' => 'sometimes|nullable|string|in:rating,popular,newest,oldest,price_high,price_low,price_desc,price_asc',
    ]);

    $sortOptionRaw = $valid['sort'] ?? 'rating';
    unset($valid['sort']);

    if (isset($valid['categories'])) {
      $valid['categories'] = is_null($valid['categories']) ? null : explode(',', $valid['categories']);
    }
    if (isset($valid['locations'])) {
      $valid['locations'] = is_null($valid['locations']) ? null : explode(',', $valid['locations']);
    }
    
    $valid = array_filter($valid, fn($item) => !is_null($item));

    $query = Product::query()
      ->where('status_id', Status::ACTIVE)
      ->withCount([
        'reviews as reviews_count' => fn($q) => $q->whereNull('parent_id'),
      ])
      ->withAvg([
        'reviews as avg_rating' => fn($q) => $q->whereNull('parent_id')
      ], 'rating')
      ->when(
        isset($valid['rating']) && $valid['rating'] > 0,
        fn($q) => $q->whereExists(function($sq) use ($valid) {
          $sq->selectRaw('1')
             ->from('reviews')
             ->whereColumn('reviews.product_id', 'products.id')
             ->whereNull('reviews.parent_id')
             ->groupBy('reviews.product_id')
             ->havingRaw('AVG(rating) >= ?', [$valid['rating']]);
        }),
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
        fn($q) => $q->whereHas('types', fn($sq) => $sq->where('slug', $valid['type'])),
      )
      ->when(
        isset($valid['locations']),
        fn($q) => $q->whereHas('locations', fn($sq) => $sq->whereIn('locations.slug', $valid['locations'])),
      )
      ->when(
        isset($valid['author']),
        fn($q) => $q->whereHas('author', fn($sq) => $sq->where('users.username', str_ireplace('@', '', $valid['author']))),
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
        fn($q) => $q->whereHas('locations', fn($sq) => $sq->where('locations.slug', $params['country'])),
      )
    ;

    $sortOption = match ($sortOptionRaw) {
      'price_desc' => 'price_high',
      'price_asc' => 'price_low',
      default => $sortOptionRaw,
    };

    $paginator = (match ($sortOption) {
      'price_high' => $query
        ->orderByRaw('(price - COALESCE(sale_price, 0)) DESC')
        ->orderByDesc('id'),
      'price_low' => $query
        ->orderByRaw('(price - COALESCE(sale_price, 0)) ASC')
        ->orderByDesc('id'),
      'popular' => $query
        ->orderByDesc(DB::raw('COALESCE(views, 0)'))
        ->orderByDesc('reviews_count')
        ->orderByDesc('id'),
      'newest' => $query
        ->orderByDesc(DB::raw('COALESCE(published_at, created_at, updated_at)'))
        ->orderByDesc('id'),
      'oldest' => $query
        ->orderBy(DB::raw('COALESCE(published_at, created_at, updated_at)'))
        ->orderByDesc('id'),
      default => $query
        ->orderByDesc(DB::raw('COALESCE((SELECT AVG(rating) FROM reviews WHERE reviews.product_id = products.id AND reviews.parent_id IS NULL), 0)'))
        ->orderByDesc('reviews_count')
        ->orderByDesc('id'),
    })->paginate(20);

    return view('site.pages.products', [
      'page' => $page,
      'paginator' => $paginator,
      'sortOption' => $sortOption,
    ]);
  }

  public function sellers(Request $request)
  {
    if (!Auth::check() && $request->has('referal') && is_string($request->get('referal'))) {
      SessionExpire::set('referal', $request->get('referal'), Carbon::now()->addHours(24));
    }

    // Redirect sellers to dashboard
    if (Auth::check() && Auth::user()->hasRole('creator')) {
      return redirect()->route('profile.dashboard');
    }

    $page = Page::where('slug', 'sellers')
      ->with('config')
      ->first();
    
    return view('site.pages.sellers', [
      'page' => $page,
    ]);
  }

  public function referal(Request $request)
  {
    if (!Auth::check() && $request->has('referal') && is_string($request->get('referal'))) {
      SessionExpire::set('referal', $request->get('referal'), Carbon::now()->addHours(24));
    }

    $page = Page::where('slug', 'referal')
      ->with('config')
      ->first();

    return view('site.pages.referal', [
      'page' => $page,
    ]);
  }

  public function gift(Request $request)
  {
    try {
      $order_id = Crypt::decrypt($request->get('h'));
    } catch (\Exception $e) {
      return redirect('/');
    }

    $order = Order::find($order_id);
    
    $page = Page::where('slug', 'gift')
      ->with('config')
      ->first();
    
    return view('site.pages.gift', [
      'page' => $page,
      'order' => $order,
    ]);
  }
  
  public function investments(Request $request)
  {
    $page = Page::where('slug', 'investments')
      ->with('config')
      ->first();

    return view('site.pages.investments', [
      'page' => $page,
    ]);
  }
}