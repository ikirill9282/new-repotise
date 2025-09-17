<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\SectionVariables;
use App\Models\Comment;
use Illuminate\Support\Facades\Blade;
use App\Models\Article;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Models\Page;
use App\Models\Review;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Type;

class DataController extends Controller
{

  public function feed(Request $request, $id)
  {
    $vars = Page::where('slug', 'feed')->with('config')->first()->config->keyBy('name');
    $news = Article::getLastNews();
    $aid = null;

    if (request()->has('aid')) {
      $rdata = CustomEncrypt::decodeUrlHash(request()->get('aid'));
      if (isset($rdata['id'])) $aid = $rdata['id'];
    }

    $articles = Article::where('id', '<', $id)
      ->when(!is_null($aid), fn($q) => $q->where('id', '!=', $aid))
      ->orderByDesc('id')
      ->limit(2)
      ->get()
      ->map(fn($article) => Blade::render('site.components.article_feed', [
        'variables' => $vars,
        'last_news' => $news,
        'article' => $article,
      ]));
    
    return $articles->implode("\n");
  }

  public function messages(Request $request)
  {
    $result = [];
    $valid = $request->validate([
      'resource' => 'required|string',
    ]);

    $data = CustomEncrypt::decodeUrlHash($valid['resource']);

    if ($data['type'] == 'parent') {
      $query = match($data['resource']) {
        'review' => Product::query(),
        'comment' => Article::query(),
      };
    } else {
      $query = match($data['resource']) {
        'review' => Review::query(),
        'comment' => Comment::query(),
      };
    }


    $model = $query->find($data['id']);
    $model->messagesType($data['type'])->messagesOffset($data['offset'])->getMessages();


    if ($data['type'] == 'parent') {
      $author_id = $model->author->id;
    } else {
      $author_id = match($data['resource']) {
        'review' => $model->product->author->id,
        'comment' => $model->article->author->id,
      };
    }


    $variables = Page::where('slug', 'feed')->with('config')->first()->config->keyBy('name');

    foreach ($model->messages as $message) {
      $view = Blade::render('components.chat.message', [
        'child' => $data['type'] == 'child',
        'message' => $message,
        'resource' => $data['resource'],
        'variables' => $variables,
        'author_id' => $author_id,
        'level' => $data['level'],
      ]);
      array_push($result, $view);
    }

    if ($model->getUnloadedMessagesCount() > 0) {
      $view = Blade::render('components.chat.more', [
        'resource' => \App\Helpers\CustomEncrypt::generateUrlHash([
          'id' => $model->id,
          'offset' => $model->getMessagesOffset(),
          'type' => $data['type'],
          'resource' => $data['resource'],
          'level' => $data['level'],
        ]),
        'class' => $data['type'] == 'child' ? '!flex w-full' : 'w-full text-center',
        'slot' => "Show More Replies ({$model->getLoadingMessagesCount()} of {$model->getUnloadedMessagesCount()})"
      ]);
      // dd($view);
      array_push($result, $view);
    }

    return response(implode("\n", $result));
  }

  public function favorite_author(Request $request)
  {
    $valid = $request->validate(['id' => 'required|integer']);
    $model = User::find($valid['id']);
    return response()->json(['status' => true, 'content' => Blade::render('site.components.favorite.author', ['author' => $model])]);
  }

  public function tags(Request $request)
  {
    $valid = $request->validate(['q' => 'sometimes|nullable|string']);

    return Tag::query()
      ->when(
        !empty($valid['q']),
        fn($query) => $query->where('title', 'like', "%{$valid['q']}%")
          ->orWhere('slug', 'like', "%{$valid['q']}%")
      )
      ->get()
      ->map(function($item) {
        return [
          'key' => $item->slug,
          'label' => $item->title,
        ];
      });
  }

  public function types(Request $request)
  {
    $valid = $request->validate(['q' => 'sometimes|nullable|string']);

    return Type::query()
      ->when(
        !empty($valid['q']),
        fn($query) => $query->where('title', 'like', "%{$valid['q']}%")
          ->orWhere('slug', 'like', "%{$valid['q']}%")
      )
      ->get()
      ->map(function($item) {
        return [
          'key' => $item->slug,
          'label' => $item->title,
        ];
      });
  }

  public function locations(Request $request)
  {
    $valid = $request->validate(['q' => 'sometimes|nullable|string']);

    return Location::query()
      ->when(
        !empty($valid['q']),
        fn($query) => $query->where('title', 'like', "%{$valid['q']}%")
          ->orWhere('slug', 'like', "%{$valid['q']}%")
      )
      ->get()
      ->map(function($item) {
        return [
          'key' => $item->slug,
          'label' => $item->title,
        ];
      });
  }

  public function categories(Request $request)
  {
    $valid = $request->validate(['q' => 'sometimes|nullable|string']);

    return Category::query()
      ->when(
        !empty($valid['q']),
        fn($query) => $query->where('title', 'like', "%{$valid['q']}%")
          ->orWhere('slug', 'like', "%{$valid['q']}%")
      )
      ->get()
      ->map(function($item) {
        return [
          'key' => $item->slug,
          'label' => $item->title,
        ];
      });
  }
}
