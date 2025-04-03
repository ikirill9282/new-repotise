<?php

namespace App\Traits;

use App\Helpers\AhoCorasick;
use App\Models\Category;
use App\Models\Location;
use App\Search\SearchClient;
use Meilisearch\Contracts\DocumentsQuery;
use Meilisearch\Contracts\DocumentsResults;

trait HasKeywords
{
  protected string $keywords_target = 'text';

  public function getKeywords(): array
  {
    $keywords = $this->prepareKeywords();
    $text = $this->{$this->keywords_target};
    $kws = preg_replace('/[\s]/is', '\s', implode('|', $keywords));
    $regex = "/($kws)/is";

    preg_match_all($regex, $text, $matches);

    if (isset($matches[1])) {
      return array_unique($matches[1]);
    }

    return [];
  }

  protected function prepareKeywords(): array
  {
    $result = collect([]);
    $categories = Category::select('title')
      ->distinct()
      ->get()
      ->pluck('title')
      ->values()
      ->toArray();
    $locations = Location::select('title')
      ->distinct()
      ->get()
      ->pluck('title')
      ->values()
      ->toArray();

    $result = $result->merge($categories)->merge($locations);
    return $result->unique()->toArray();

    // $client = SearchClient::make();

    // $index = $client->index('categories');
    // $categories = $index->getDocuments(
    //   (new DocumentsQuery())
    //     ->setLimit(1000)
    // )
    //   ->getResults();
    
    // $result = array_merge($result, array_column($categories, 'title'));

    // $index = $client->index('locations');
    // $locations = $index->getDocuments(
    //   (new DocumentsQuery())
    //     ->setLimit(1000)
    // )
    //   ->getResults();

    // $result = array_merge($result, array_column($locations, 'title'));
    

    // return array_unique($result);
  }
}