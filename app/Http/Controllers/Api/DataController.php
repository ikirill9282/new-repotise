<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\SectionVariables;
use App\Models\Comment;
use Illuminate\Support\Facades\Blade;
use App\Models\Article;
use App\Models\User;
use App\Models\Page;

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

  public function comments(Request $request)
  {
    $result = [];
    $valid = $request->validate([
      'hash' => 'required|string',
    ]);
    $id = CustomEncrypt::getId($valid['hash']);
    $comment = Comment::find($id);
    $comment->getChildren();
    
    $variables = Page::where('slug', 'feed')->with('config')->first()->config->keyBy('name');

    foreach ($comment->children as $child) {
      $view = Blade::render('site.components.comments.comment', [
        'comment' => $child->toArray(), 
        'variables' => $variables,
        'class' => 'answers border_none_block',
      ]);
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
}
