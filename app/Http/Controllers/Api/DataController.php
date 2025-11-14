<?php

namespace App\Http\Controllers\Api;

use App\Enums\Status;
use App\Helpers\Collapse;
use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\SectionVariables;
use App\Models\Comment;
use Illuminate\Support\Facades\Blade;
use App\Models\Article;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Location;
use App\Models\User;
use App\Models\Page;
use App\Models\Review;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Type;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Jobs\OptimizeMedia;

class DataController extends Controller
{

  public function uploadImage(Request $request)
  {
    $request->validate(['image' => 'required|image|mimes:jpeg,jpg,png,gif,webp']);
    $image = $request->file('image');
    
    try {
      $path = $image->store('images', 'public');
      OptimizeMedia::dispatch('public', $path);

      $gallery = Gallery::create([
        'model_id' => 0,
        'user_id' => $request->user()->id,
        'type' => 'articles',
        'image' => "/storage/$path",
        'placement' => 'text',
        'size' => Collapse::bytesToMegabytes($image->getSize()),
        'expires_at' => Carbon::now()->addHours(1),
      ]);

      return response()->json(['status' => 'success', 'message' => '', 'path' => $gallery->image]);

    } catch (\Exception $e) {
      if ($path && Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
      }
      Log::error('Error while saving TMP image for article', [
        'error' => $e->getMessage() . ' at ' . $e->getFile() . ' on line ' . $e->getLine(),
      ]);
      return response()->json(['status' => 'error', 'message' => 'Something went wrong...']);
    }
  }
  
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
      ->where(function ($query) use($valid) {
          $query->when(
            !empty($valid['q']),
            fn($subquery) => $subquery->where('title', 'like', "%{$valid['q']}%")
              ->orWhere('slug', 'like', "%{$valid['q']}%")
          );
      })
      ->where('status_id', '!=', Status::DELETED)
      // ->ddRawSql()
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
