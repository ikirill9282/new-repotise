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
        ->setLimit(20)
        ->setAttributesToRetrieve([
          'id', 
          'name', 
          'slug', 
          'title', 
          'rating',
          'author',
          'price', 
          'old_price', 
          'type',  
          'preview',
          'reviews_count',
          'type',
          'location',
          'categories',
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
            'tags',
          ])
          ->setSort(['created_at:desc'])
          ,
        
          (new SearchQuery())
          ->setIndexUid('users')
          ->setQuery($query)
          ->setLimit(20)
          ->setAttributesToRetrieve([
            'id', 
            'name', 
            'slug', 
            'profile', 
            'avatar', 
            'description',
            'followers_count',
          ])
          ,
      ],
    );

    $records = collect($records['results'])
      ->filter(fn($record) => isset($record['hits']) && !empty($record['hits']));

    return $records->toArray();
    // return static::compare($records);
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
        ? [['id' => $item['type']['id'], 'title' => $item['type']['title'], 'type' => 'type']]
        : [];
      $location = (isset($item['location']) && !empty($item['location']))
        ? [['id' => $item['location']['id'], 'title' => $item['location']['title'], 'type' => 'location']]
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