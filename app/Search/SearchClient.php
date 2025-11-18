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
    return new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
  }
  /**
   * Mutliple Search for a query in Meilisearch
   *
   * @param string $query
   * @return array
   */
  public static function full(string $query): array
  {
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
    return static::compare($records);
  }


  public static function findIn(string $query, string|array $sources, int $limit = 1000): array
  {
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
}