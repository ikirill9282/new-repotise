<?php

namespace App\Search;

use App\Models\Article;
use App\Models\Category;
use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Contracts\MultiSearchFederation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class SearchClient
{

  public static function make(): Client
  {
    return static::getClient();
  }

  protected static function getClient(): Client
  {
    $host = env('MEILISEARCH_HOST') ?: config('scout.meilisearch.host', 'http://localhost:7700');
    $key = env('MEILISEARCH_KEY') ?: config('scout.meilisearch.key', '');
    
    // Ensure host is not null
    if (empty($host)) {
      $host = 'http://localhost:7700';
    }
    
    // Key can be empty string, but not null
    if ($key === null) {
      $key = '';
    }
    
    return new Client($host, $key);
  }
  /**
   * Mutliple Search for a query in Meilisearch
   *
   * @param string $query
   * @return array
   */
  public static function full(string $query): array
  {
    try {
      $client = static::getClient();
      

      $records = $client->multiSearch(
        [
        (new SearchQuery())
          ->setIndexUid('products')
          ->setQuery($query)
          ->setLimit(1000)
          ->setAttributesToRetrieve([
            'id', 
            'name', 
            'slug', 
            'title', 
            'rating',
            'created_at',
            'author',
            'price', 
            'old_price', 
            'type',  
            'preview',
            'reviews_count',
            'type',
            'location',
            'categories',
            'calcedPrice',
            'priceWithoutDiscount',
          ]),
          (new SearchQuery())
            ->setIndexUid('articles')
            ->setQuery($query)
            ->setLimit(1000)
            ->setAttributesToRetrieve([
              'id', 
              'title', 
              'subtitle', 
              'slug', 
              'author',
              'preview',
              'short',
              'created_at',
              'views',
              'tags',
            ])
            ->setSort(['created_at:desc'])
            ,
          
            (new SearchQuery())
            ->setIndexUid('users')
            ->setQuery($query)
            ->setLimit(1000)
            ->setAttributesToRetrieve([
              'id', 
              'name', 
              'username',
              'slug', 
              'profile', 
              'avatar', 
              'description',
              'followers_count',
              'created_at',
            ])
            ,
        ],
      );

      $records = collect($records['results'])
        ->filter(fn($record) => isset($record['hits']) && !empty($record['hits']));

      
      return $records->flatMap(function($record) {
        return array_map(function($row) use($record) {
          $row['index'] = $record['indexUid'];
          $row['label'] = static::getHitLabel($row);
          return $row;
        }, $record['hits']);
      })->toArray();
    } catch (\Meilisearch\Exceptions\CommunicationException $e) {
      Log::info('Meilisearch is not available, falling back to database search', [
        'query' => $query,
        'error' => $e->getMessage(),
      ]);
      // Fallback to database search if Meilisearch is unavailable
      return static::fallbackSearch($query);
    } catch (\Exception $e) {
      Log::error('Search error', [
        'query' => $query,
        'error' => $e->getMessage(),
      ]);
      return [];
    }
  }


  public static function findIn(string $query, string|array $sources, int $limit = 1000): array
  {
    try {
      $client = static::getClient();
      $result = [];

      if (is_array($sources)) {
        foreach ($sources as $source) {
          $part = $client->index($source)
            ->search($query, ['limit' => $limit])
            ->toArray();

          $part = array_map(function($elem) use ($source) {
            $elem['source'] = $source;
            return $elem;
          }, $part['hits'] ?? []);

          $result = array_merge($result, $part);
        }
      } else {
        $result = $client->index($sources)
          ->search($query, ['limit' => $limit])
          ->toArray();
        
        if (!isset($result['hits'])|| empty($result['hits'])) return [];
        $result = $result['hits'] ?? [];
      }

      $result = array_map(function($hit) {
        return [
          'id' => $hit['id'],
          'label' => static::getHitLabel($hit), 
          'slug' => $hit['slug'] ?? null, 
          'source' => $hit['source'] ?? null,
        ];
      }, $result);

      // Remove duplicates based on slug (keep first occurrence)
      $seen = [];
      $result = array_filter($result, function($item) use (&$seen) {
        $slug = $item['slug'] ?? null;
        if ($slug === null) {
          return true; // Keep items without slug
        }
        if (isset($seen[$slug])) {
          return false; // Skip duplicate
        }
        $seen[$slug] = true;
        return true;
      });
      $result = array_values($result); // Re-index array

      return $result;
    } catch (\Meilisearch\Exceptions\CommunicationException $e) {
      Log::info('Meilisearch is not available, falling back to database search', [
        'query' => $query,
        'sources' => $sources,
        'error' => $e->getMessage(),
      ]);
      // Fallback to database search if Meilisearch is unavailable
      return static::fallbackFindIn($query, $sources, $limit);
    } catch (\Exception $e) {
      Log::error('Search error in findIn', [
        'query' => $query,
        'sources' => $sources,
        'error' => $e->getMessage(),
      ]);
      return [];
    }

    $result = array_map(function($hit) {
      return [
        'id' => $hit['id'],
        'label' => static::getHitLabel($hit), 
        'slug' => $hit['slug'] ?? null, 
        'source' => $hit['source'] ?? null,
      ];
    }, $result);

    return $result;
  }

  protected static function getHitLabel(array $hit)
  {
    return (isset($hit['title']) || isset($hit['name'])) ? ($hit['title'] ?? $hit['name']) : 'null';
  }

  protected static function compare(Collection|array $records): array
  {
    $records = $records instanceof Collection ? $records : collect($records);
    $result = [];
    $key = 0;

    if ($records->isNotEmpty()) {
      do {
        $finish = true;
        foreach ($records as $record) {
          if (array_key_exists($key, $record['hits'])) {
            $row = $record['hits'][$key];
            $row['index'] = $record['indexUid'];
            $row['label'] = (isset($row['title']) || isset($row['name'])) ? ($row['title'] ?? $row['name']) : 'null';
            array_push($result, $row);
            $finish = false;
          }
        }

        $key = $finish ? false : ($key + 1);
        $finish = true;
      } while ($key);
    }

    return $result;
  }

  public static function getTagsFromItem(?array $item = null): array
  {
    if (is_null($item) || empty($item)) {
      return Category::query()
        ->whereHas('products')
        ->select(['id', 'title'])
        ->orderByDesc('id')
        ->limit(6)
        ->get()
        ->toArray();
    }
    
    if ($item['index'] == 'products') {
      $categories = (isset($item['categories']) && !empty($item['categories']))
        ? collect($item['categories'])
            ->select(['id', 'title'])
            ->map(function($category) {
              $category['type'] = 'category';
              return $category;
            })
            ->toArray()
        : [];


      $type = (isset($item['type']) && !empty($item['type']))
        ? array_map(fn($elem) => ['id' => $elem['id'], 'title' => $elem['title']], $item['type'])
        : [];

      $location = (isset($item['location']) && !empty($item['location']))
        ? array_map(fn($elem) => ['id' => $elem['id'], 'title' => $elem['title']], $item['location'])
        : [];

      $tags = array_merge($categories, $type, $location);
      return $tags;
    }

    if ($item['index'] == 'articles') {
      return (isset($item['tags']) && !empty($item['tags']))
        ? array_map(fn($tag) => ['id' => $tag['id'], 'title' => $tag['title'], 'type' => 'tag'] , $item['tags'])
        : [];
    }

    return [];
  }

  /**
   * Fallback database search when Meilisearch is unavailable
   */
  protected static function fallbackSearch(string $query): array
  {
    $results = [];
    $queryLower = mb_strtolower(trim($query));

    // Search in products
    $products = \App\Models\Product::where('status_id', '!=', \App\Enums\Status::DELETED)
      ->where(function($q) use ($queryLower) {
        $q->whereRaw('LOWER(title) LIKE ?', ["%{$queryLower}%"])
          ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$queryLower}%"]);
      })
      ->limit(100)
      ->get();

    foreach ($products as $product) {
      $results[] = [
        'id' => $product->id,
        'index' => 'products',
        'title' => $product->title,
        'name' => $product->title,
        'slug' => $product->slug,
        'label' => $product->title,
        'rating' => $product->rating,
        'created_at' => $product->created_at?->toIso8601String(),
        'author' => $product->user ? [
          'profile' => $product->user->profile,
          'avatar' => $product->user->getAvatarUrl() ?: url('/storage/images/default_avatar.png'),
          'slug' => $product->user->username,
          'username' => $product->user->username,
        ] : [
          'profile' => null,
          'avatar' => url('/storage/images/default_avatar.png'),
          'slug' => null,
          'username' => null,
        ],
        'price' => $product->price,
        'old_price' => $product->sale_price,
        'preview' => $product->preview?->image,
        'reviews_count' => $product->reviews_count,
        'calcedPrice' => $product->getPrice(),
        'priceWithoutDiscount' => $product->getPriceWithoutDiscount(),
      ];
    }

    // Search in articles
    $articles = \App\Models\Article::where('status_id', '!=', \App\Enums\Status::DELETED)
      ->where(function($q) use ($queryLower) {
        $q->whereRaw('LOWER(title) LIKE ?', ["%{$queryLower}%"])
          ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$queryLower}%"]);
      })
      ->orderByDesc('created_at')
      ->limit(100)
      ->get();

    foreach ($articles as $article) {
      $results[] = [
        'id' => $article->id,
        'index' => 'articles',
        'title' => $article->title,
        'slug' => $article->slug,
        'label' => $article->title,
        'subtitle' => null,
        'author' => $article->user ? [
          'profile' => $article->user->profile,
          'avatar' => $article->user->getAvatarUrl() ?: url('/storage/images/default_avatar.png'),
          'slug' => $article->user->username,
          'username' => $article->user->username,
        ] : [
          'profile' => null,
          'avatar' => url('/storage/images/default_avatar.png'),
          'slug' => null,
          'username' => null,
        ],
        'preview' => $article->preview?->image,
        'short' => $article->short(),
        'created_at' => $article->created_at?->toIso8601String(),
        'views' => $article->views,
        'tags' => $article->tags->map(fn($tag) => ['id' => $tag->id, 'title' => $tag->title])->toArray(),
      ];
    }

    // Search in users
    $users = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['creator', 'seller']))
      ->where(function($q) use ($queryLower) {
        $q->whereRaw('LOWER(username) LIKE ?', ["%{$queryLower}%"])
          ->orWhereRaw('LOWER(name) LIKE ?', ["%{$queryLower}%"]);
      })
      ->limit(100)
      ->get();

    foreach ($users as $user) {
      $results[] = [
        'id' => $user->id,
        'index' => 'users',
        'name' => $user->name,
        'username' => $user->username,
        'slug' => $user->username,
        'label' => $user->name,
        'profile' => $user->profile,
        'avatar' => $user->avatar,
        'description' => $user->description,
        'followers_count' => $user->followers()->count(),
        'created_at' => $user->created_at?->toIso8601String(),
      ];
    }

    return $results;
  }

  /**
   * Fallback database search for findIn method
   */
  protected static function fallbackFindIn(string $query, string|array $sources, int $limit = 1000): array
  {
    $results = [];
    $queryLower = mb_strtolower(trim($query));
    $sourcesArray = is_array($sources) ? $sources : [$sources];

    foreach ($sourcesArray as $source) {
      switch ($source) {
        case 'users':
          $users = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['creator', 'seller']))
            ->where(function($q) use ($queryLower) {
              $q->whereRaw('LOWER(username) LIKE ?', ["%{$queryLower}%"])
                ->orWhereRaw('LOWER(name) LIKE ?', ["%{$queryLower}%"]);
            })
            ->limit($limit)
            ->get();

          foreach ($users as $user) {
            $results[] = [
              'id' => $user->id,
              'label' => $user->name,
              'slug' => $user->username,
              'source' => 'users',
            ];
          }
          break;

        case 'products':
          $products = \App\Models\Product::where('status_id', '!=', \App\Enums\Status::DELETED)
            ->where(function($q) use ($queryLower) {
              $q->whereRaw('LOWER(title) LIKE ?', ["%{$queryLower}%"])
                ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$queryLower}%"]);
            })
            ->limit($limit)
            ->get();

          foreach ($products as $product) {
            $results[] = [
              'id' => $product->id,
              'label' => $product->title,
              'slug' => $product->slug,
              'source' => 'products',
            ];
          }
          break;

        case 'articles':
          $articles = \App\Models\Article::where('status_id', '!=', \App\Enums\Status::DELETED)
            ->where(function($q) use ($queryLower) {
              $q->whereRaw('LOWER(title) LIKE ?', ["%{$queryLower}%"])
                ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$queryLower}%"]);
            })
            ->limit($limit)
            ->get();

          foreach ($articles as $article) {
            $results[] = [
              'id' => $article->id,
              'label' => $article->title,
              'slug' => $article->slug,
              'source' => 'articles',
            ];
          }
          break;
      }
    }

    return $results;
  }
}