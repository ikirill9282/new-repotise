<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
  use HasAuthor, HasGallery;

  protected Collection|array $all_comments = [];

  protected int $amountAnalogs = 6;

  public array|Collection $comments = [];

  public static function selectShort()
  {
    return static::query()->select(['id', 'title', 'user_id']);
  }
  
  public function tags()
  {
    return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id', 'id', 'id');
  }

  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'article');
  }

  public function getFullComments(int $limit = 10): Article
  {
    $this->comments_limit = $limit;
    $this->comments_loading = true;

    $this->comments = Comment::query()
      ->where('article_id', $this->id)
      ->whereNull('parent_id')
      ->with('likes', function($query) {
        $query->with('author')->orderByDesc('id')->limit(4);
      })
      // ->with('likes.author')
      ->withCount('likes')
      ->with('author')
      ->get();
    
    foreach ($this->comments as $key => &$comment) {
      if ($this->comments_loading && $comment->children()->exists()) {
        $comment->children = $this->getChildren($comment);
      }
    }

    $this->comments = $this->comments->toArray();
    // dd($this->comments, $this->countComments());
    return $this;
  }

  public function getChildren($comment)
  {
    if ($this->countComments() > $this->comments_limit) {
      $this->comments_loading = false;
      return;
    }

    $comment->load('children', 'likes.author', 'author');
    $comment->loadCount('likes');

    foreach ($comment->children as &$child) {
        $this->getChildren($child);
    }
  }

  public function countComments(array|Collection|null $array = null)
  {
    if (!isset($this->comments) || empty($this->comments)) return 0;

    $target = is_null($array) ? collect($this->comments)->toArray() : $array;
    $count = count($target);

    foreach ($target as $comment) {
      if (!empty($comment['children'])) {
        $count += $this->countComments($comment['children']);
      }
    }

    return $count;
  }

  public function getAnalogs()
  {
    $tags = $this->tags->pluck('id')->values()->toArray();
    if (empty($tags)) {
      $query = Article::query()
        ->orderByDesc('id')
        ->limit($this->amountAnalogs)
      ;
    } else {
      $query = Article::query()
        ->whereHas('tags', fn($query) => $query->whereIn('tags.id', $tags))
        ->with('author', 'preview')
      ;
    }

    $analogs = $query->get()->collect();
    while ($analogs->count() < $this->amountAnalogs) {
      $analogs = $analogs->merge($analogs->all());
    }
    $this->analogs = $analogs->slice(0, $this->amountAnalogs);

    return $this;
  }

  public function getLikes()
  {
    $this->load([
      'likes' => function($query) {
        $query->with('author')->orderByDesc('id')->limit(3);
      },
    ])
    ->loadCount('likes');

    return $this;
  }

  public function makeFeedUrl()
  {
    return url("insights/feed/$this->slug?aid=$this->id");
  }

  public function setAmountAnalogs(int $amount)
  {
    $this->amountAnalogs = $amount;
  }
}
