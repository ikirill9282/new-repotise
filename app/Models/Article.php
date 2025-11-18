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
use App\Traits\HasReport;
use App\Traits\HasStatus;
use Illuminate\Support\Facades\Crypt;
use Mews\Purifier\Facades\Purifier;

class Article extends Model
{
  use HasAuthor, HasGallery, Searchable, HasKeywords, HasStatus, HasMessages, HasReport;

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
      $model->title = str_replace('&amp;', '&', $model->title);

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
      $model->title = str_replace('&amp;', '&', $model->title);

      // SLUG
      if ($model->isDirty('title')) {
          $model->generateSlug();
      }
    });

    self::updated(function($model) {
      $model->searchable();
    });
  }

  public function toSearchableArray(): array
  {
      $array = $this->toArray();

      $array['tags'] = $this->tags->select(['id', 'title'])->toArray();
      $array['preview'] = $this->preview?->image ?? '';
      $array['author'] = array_merge(
        $this->author->only('profile', 'name', 'avatar', 'description'),
        ['username' => $this->author->username, 'slug' => $this->author->username]
      );
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

  public function getAnalogs(?array $excludeIds = null)
  {
    $excludeIds = $excludeIds ?? [];
    $excludeIds[] = $this->id; // Всегда исключаем текущую статью
    
    $tags = $this->tags->pluck('id')->values()->toArray();
    
    // Сначала ищем по тегам
    if (!empty($tags)) {
      $query = Article::query()
        ->whereHas('tags', fn($query) => $query->whereIn('tags.id', $tags))
        ->whereNotIn('id', $excludeIds)
        ->whereHas('author.roles', fn($q) => $q->where('name', ['creator', 'customer']))
        ->with('author', 'preview')
        ->orderByDesc('id')
        ->limit($this->amountAnalogs * 2); // Берем больше, чтобы было из чего выбрать
      
      $analogs = $query->get()->collect();
      
      // Если нашли достаточно по тегам, возвращаем
      if ($analogs->count() >= $this->amountAnalogs) {
        $this->analogs = $analogs->slice(0, $this->amountAnalogs);
        return $this;
      }
    } else {
      $analogs = collect();
    }
    
    // Если не хватает, ищем по похожим словам в названии
    $titleWords = array_filter(
      explode(' ', preg_replace('/[^\w\s]/u', ' ', mb_strtolower($this->title))),
      fn($word) => mb_strlen($word) > 3
    );
    
    if (!empty($titleWords)) {
      $titleQuery = Article::query()
        ->whereNotIn('id', array_merge($excludeIds, $analogs->pluck('id')->toArray()))
        ->whereHas('author.roles', fn($q) => $q->where('name', ['creator', 'customer']))
        ->with('author', 'preview');
      
      // Ищем статьи, где в названии есть хотя бы одно слово из текущей статьи
      $titleQuery->where(function($q) use ($titleWords) {
        foreach (array_slice($titleWords, 0, 3) as $word) { // Берем первые 3 слова
          $q->orWhere('title', 'LIKE', "%{$word}%");
        }
      });
      
      $titleAnalogs = $titleQuery
        ->orderByDesc('id')
        ->limit($this->amountAnalogs * 2)
        ->get()
        ->collect();
      
      $analogs = $analogs->merge($titleAnalogs);
    }
    
    // Если все еще не хватает, берем любые статьи (кроме исключенных)
    if ($analogs->count() < $this->amountAnalogs) {
      $fallbackQuery = Article::query()
        ->whereNotIn('id', array_merge($excludeIds, $analogs->pluck('id')->toArray()))
        ->whereHas('author.roles', fn($q) => $q->where('name', ['creator', 'customer']))
        ->with('author', 'preview')
        ->orderByDesc('id')
        ->limit($this->amountAnalogs - $analogs->count());
      
      $fallbackAnalogs = $fallbackQuery->get()->collect();
      $analogs = $analogs->merge($fallbackAnalogs);
    }
    
    // Если все еще не хватает, дублируем существующие (старый способ)
    while ($analogs->count() < $this->amountAnalogs && $analogs->count() > 0) {
      $analogs = $analogs->merge($analogs->all());
    }
    
    $this->analogs = $analogs->slice(0, $this->amountAnalogs)->unique('id')->values();

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

  public function makeShareUrl(?string $source = null)
  {
    $url = $this->makeFeedUrl();
    $route_url = urlencode($url);
    $title = urlencode('Discover your next adventure together!');

    return match($source) {
      'FB' => "http://www.facebook.com/share.php?u=$route_url&title=$title",
      'TW' => "https://twitter.com/intent/tweet?text=" . ($title." ".$route_url),
      'PI' => "http://pinterest.com/pin/create/link/?url=$route_url&description=$title",
      'GM' => "https://mail.google.com/mail/u/0/?ui=2&fs=1&tf=cm&su=$title&body=Link:+$route_url",
      'WA' => "https://wa.me/?text=$title $route_url",
      'TG' => "https://t.me/share/url?url=$route_url&text=$title",
      'RD' => "https://www.reddit.com/submit?url=$url&title=$title",
      default => $url
    };
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
      ->whereHas('author', fn($query) => $query->where('id', 0))
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
