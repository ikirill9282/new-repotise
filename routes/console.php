<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Admin\Page;
use App\Models\Admin\PageSection;
use App\Models\Admin\Section;
use App\Models\Admin\SectionVariables;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Carbon;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Location;
use App\Models\Options;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use App\Models\News;
use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Contracts\MultiSearchFederation;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('tt', function() {
  dd(Product::find(1)->categories->toArray());
});

Artisan::command('tt_mail', function() {
  $m = new VerifyMail([
    'name' => 'Demo',
  ]);
  $mail = Mail::to('errewer123@gmail.com')->send($m);

  dd($mail);
});

Artisan::command('tt2', function() {
  $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
  $index = $client->index('articles');
  
  // $articles = Article::all()
  //   ->map(fn($article) => [
  //     'id' => $article->id,
  //     'title' => $article->title,
  //     'subtitle' => $article->subtitle,
  //     'slug' => $article->slug,
  //     'text' => $article->text,
  //     'created_at' => $article->created_at,
  //     'updated_at' => $article->updated_at,
  //   ]);

  // $index->addDocuments($articles->toArray());
  // $index->updateDocuments($articles->toArray());
  // dd($index->getDocuments()->toArray());
  // dd($index->stats()['numberOfDocuments']);
  // $index->addDocuments($articles->toArray());

  $str = '';
  // $qs = array_map(function($w) {
  //   $query = (new SearchQuery())
  //     ->setIndexUid('articles')
  //     ->setQuery($w)
  //     ->setLimit(1000)
  //     ->setAttributesToRetrieve(['id', 'title', 'subtitle', 'slug', 'author.name'])
  //     ;
  //   return $query;

  // }, explode(' ', $str));

  // $r = $client->multiSearch($qs);

  $r = Article::search($str)
    ->options([
    'limit' => 1000,
    // 'offset' => 0,
    'attributesToRetrieve' => ['id', 'title', 'subtitle', 'slug', 'author.name', 'text'],
  ])
      // ->get();
    ->raw();
  // $r = collect($r['results'])
  //   ->flatMap(fn($item) => $item['hits']);
  dd($r);
});

Artisan::command('rl_index', function() {
  Artisan::call('scout:flush', ['model' => Product::class]);
  Artisan::call('scout:flush', ['model' => Article::class]);
  Artisan::call('scout:flush', ['model' => User::class]);
  Artisan::call('scout:flush', ['model' => Category::class]);
  Artisan::call('scout:flush', ['model' => Location::class]);

  Artisan::call('scout:import', ['model' => Product::class]);
  Artisan::call('scout:import', ['model' => Article::class]);
  Artisan::call('scout:import', ['model' => User::class]);
  Artisan::call('scout:import', ['model' => Category::class]);
  Artisan::call('scout:import', ['model' => Location::class]);

  $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
  $index = $client->index('articles')->updateSortableAttributes(['created_at']);
});