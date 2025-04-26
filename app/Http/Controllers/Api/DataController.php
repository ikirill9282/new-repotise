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

class DataController extends Controller
{

  public function feed(Request $request, $id)
  {
    $vars = SectionVariables::where('section_id', 7)->get()->keyBy('name');
    $news = Article::getLastNews();
    $articles = Article::where('id', '<', $id)
      ->when(request()->has('aid'), fn($q) => $q->where('id', '!=', request()->get('aid')))
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
    $data = CustomEncrypt::decrypt($valid['hash']);
    $comment = Comment::find($data['id']);
    $comment->getChildren();
    
    $variables = SectionVariables::whereHas('section', fn($q) => $q->where('sections.component', 'feed'))->get();

    foreach ($comment->children as $child) {
      $view = Blade::render('site.components.comment', [
        'comment' => $child->toArray(), 
        'variables' => $variables->keyBy('name'),
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
