<?php

namespace App\Helpers;

use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Contracts\MultiSearchFederation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class Search
{
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
          ->setIndexUid('articles')
          ->setQuery($query)
          ->setLimit(1000)
          ->setAttributesToRetrieve(['id', 'title', 'subtitle', 'slug', 'author.name'])
          ->setSort(['created_at:desc'])
          ,
        (new SearchQuery())
          ->setIndexUid('users')
          ->setQuery($query)
          ->setLimit(20)
          ->setAttributesToRetrieve(['id', 'name', 'slug']),
        (new SearchQuery())
          ->setIndexUid('products')
          ->setQuery($query)
          ->setLimit(20)
          ->setAttributesToRetrieve(['id', 'name', 'slug', 'title', 'author', 'pritce', 'old_price', 'type']),
      ],
    );
    $records = collect($records['results'])
      ->filter(fn($record) => isset($record['hits']) && !empty($record['hits']));

    return static::compare($records);
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
}