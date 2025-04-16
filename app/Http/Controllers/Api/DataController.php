<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CustomEncrypt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\SectionVariables;
use App\Models\Comment;
use Illuminate\Support\Facades\Blade;

class DataController extends Controller
{
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
}
