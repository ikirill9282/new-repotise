<?php

namespace App\Models;

use App\Enums\Status;
use App\Helpers\CustomEncrypt;
use App\Traits\HasAuthor;
use App\Traits\HasGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use App\Traits\HasKeywords;
use App\Helpers\Slug;
use App\Helpers\SessionExpire;
use App\Traits\HasMessages;
use App\Traits\HasStatus;
use Illuminate\Support\Facades\Crypt;
use Mews\Purifier\Facades\Purifier;

class Article extends Model
{
  use HasAuthor, HasGallery, Searchable, HasKeywords, HasStatus, HasMessages;

  protected Collection|array $all_comments = [];

  protected int $amountAnalogs = 6;

  public array|Collection $comments = [];

  public static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      
      // PURIFY
      $model->title = Purifier::clean($model->title);
      $model->text = Purifier::clean($model->text);
      $model->seo_title = Purifier::clean($model->seo_title);
      $model->seo_text = Purifier::clean($model->seo_text);

      // SLUG
      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });

    self::updating(function ($model) {
      
      // PURIFY
      $model->title = Purifier::clean($model->title);
      $model->text = Purifier::clean($model->text);
      $model->seo_title = Purifier::clean($model->seo_title);
      $model->seo_text = Purifier::clean($model->seo_text);

      // SLUG
      if ($model->isDirty('title')) {
          $model->generateSlug();
      }
    });
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

  public function getText(): string
  {
    return "<div class='user-custom-text'>$this->text</div>";
  }

  public function messages()
  {
    return $this->hasMany(Comment::class)->orderByDesc('id');
  }
  
  public function tags()
  {
    return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id', 'id', 'id')->where('status_id', '!=', Status::DELETED);
  }

  public function likes()
  {
    return $this->hasMany(Likes::class, 'model_id')->where('type', 'article');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function short(int $symbols = 200)
  {
    $str = $this->getText();
    $str = preg_replace('/(<img.*?>)/is', '', $str);
    $str = trim(mb_substr($str, 0, $symbols) . '...');
    $str = preg_replace('/(?:<p>\s*<br\s*\/?>\s*<\/p>)/i', '', $str);

    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    libxml_clear_errors();

    $res = $dom->saveHTML();
    
    return $res;

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
        $query->with('author.options')->orderByDesc('id')->limit(4);
      })
      // ->with('likes.author')
      ->when(!is_null($limit), fn($q) => $q->limit($limit))
      ->withCount('likes')
      ->with('author.options')
      ->orderByDesc('created_at')
      ->get()
      // ->map(function($comment) {
      //   $comment->author->load('options');
      //   return $comment;
      // })
      ;
    
    foreach ($this->comments as $key => &$comment) {
      if ($comment->children()->exists()) {
        $comment->getChildren($comment);
      }
    }
    
    // dd($this->comments->toArray());
    $this->comments = $this->comments->toArray();
    
    return $this;
  }

  public function getChildren($comment, int $max_level = 1)
  {
    $this->level++;

    $comment->load('likes.author', 'author.options');
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
          $child->getChildren($child);
        } else {
          $child->load('likes.author', 'author.options');
          $child->loadCount('likes');
          $this->level = 0;
        }
    }
  }

  public function getFullCommentsCount()
  {
    return Comment::where('article_id', $this->id)->count();
  }

  public function countComments(array|Collection|null $array = null)
  {
    if (is_null($array) && (!isset($this->comments) || empty($this->comments))) return 0;

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
    return url("insights/$this->slug?aid=" . CustomEncrypt::generateUrlHash(['id' => $this->id]));
  }

  public function makeEditUrl()
  {
    return route('profile.articles.create') . '?aid=' . Crypt::encrypt($this->id);
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
      ->whereHas('author', fn($query) => $query->whereHas('roles', fn($q) => $q->where('name', 'system')))
      // ->whereHas('author', fn($query) => $query->whereHas('roles', fn($q) => $q->where('name', 'admin')))
      ->when(($maximum_models != '*'), fn($query) => $query->limit($maximum_models))
      ->orderByDesc('id')
      // ->ddRawSql()
      ->get();
    
    
    while ($last_news->count() > 0 && $last_news->count() < $maximum_models) {
      $last_news = $last_news->collect()->merge($last_news)->slice(0, $maximum_models);
    }

    return $last_news;
  }
}
