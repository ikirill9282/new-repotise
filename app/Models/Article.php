<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use App\Traits\HasKeywords;
use App\Helpers\Slug;
use App\Helpers\SessionExpire;

class Article extends Model
{
  use HasAuthor, HasGallery, Searchable, HasKeywords;

  protected Collection|array $all_comments = [];

  protected int $amountAnalogs = 6;

  public array|Collection $comments = [];

  public static function selectShort()
  {
    return static::query()->select(['id', 'title', 'user_id', 'annotation']);
  }
  
  public function tags()
  {
    return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id', 'id', 'id');
  }

  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'article');
  }


  public function toSearchableArray(): array
  {
      $array = $this->toArray();

      $array['tags'] = $this->tags->select(['id', 'title'])->toArray();
      $array['preview'] = $this->preview?->image ?? '';
      $array['author'] = $this->author->only('profile', 'name', 'avatar', 'description');
      $array['short'] = $this->short();
      $array['keywords'] = $this->getKeywords();

      return $array;
  }

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });

    self::updating(function ($model) {
        if ($model->isDirty('title')) {
            $model->generateSlug();
        }
    });
  }

  public function short(int $symbols = 200)
  {
    return trim(mb_substr($this->text, 0, $symbols) . '...');
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  public function getFullComments(?int $limit = null): Article
  {
    $this->comments_limit = $limit;
    $this->comments_loading = true;
    $this->level = 0;

    $this->comments = Comment::query()
      ->where('article_id', $this->id)
      ->whereNull('parent_id')
      ->with('likes', function($query) {
        $query->with('author')->orderByDesc('id')->limit(4);
      })
      // ->with('likes.author')
      ->when(!is_null($limit), fn($q) => $q->limit($limit))
      ->withCount('likes')
      ->with('author')
      ->get();
    
    foreach ($this->comments as $key => &$comment) {
      if ($this->comments_loading && $comment->children()->exists()) {
        $comment->children = $this->getChildren($comment);
      }
    }

    $this->comments = $this->comments->toArray();
    return $this;
  }

  public function getChildren($comment, int $max_level = 1)
  {
    $this->level++;
    $comment->load('likes.author', 'author');
    $comment->loadCount('likes', 'children');

    // if (!is_null($this->comments_limit) && $this->countComments() > $this->comments_limit) {
    //   $this->comments_loading = false;
    //   return;
    // }

    if ($this->level > $max_level) {
      $this->level = 0;
      return;
    }

    $comment->load('children');

    foreach ($comment->children as &$child) {
        if ($child->children()->exists()) {
          $this->getChildren($child);
        } else {
          $child->load('likes.author', 'author');
          $child->loadCount('likes');
          $this->level = 0;
        }
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
        ->limit($this->amountAnalogs)
      ;
    } else {
      $query = Article::query()
        ->whereHas('tags', fn($query) => $query->whereIn('tags.id', $tags))
        ->with('author', 'preview')
      ;
    }

    $analogs = $query->whereHas('author.roles', fn($q) => $q->where('name', ['creator', 'customer']))
      ->where('id', '!=', $this->id)
      ->orderByDesc('id')
      ->get()
      ->collect();

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

  public function updateViews()
  {
    $session_key = "v_article:" . $this->id;
    SessionExpire::check($session_key, function($key) {
      $this->increment('views');
    });
  }

  public static function getLastNews(int|string $maximum_models = 4)
  {
    $last_news = static::query()
      ->whereHas('author', fn($query) => $query->whereHas('roles', fn($q) => $q->where('name', 'admin')))
      ->when(($maximum_models != '*'), fn($query) => $query->limit($maximum_models))
      ->orderByDesc('id')
      ->get();
    
    while ($last_news->count() < $maximum_models) {
      $last_news = $last_news->collect()->merge($last_news)->slice(0, $maximum_models);
    }

    return $last_news;
  }
}
