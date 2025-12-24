<?php

namespace App\Models;

use App\Helpers\Slug;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class Type extends Model
{

  protected static function boot()
  {
    parent::boot();

    self::creating(function ($model) {

      $model->title = Purifier::clean($model->title);
      $model->title = str_replace('&amp;', '&', $model->title);

      if (!isset($model->slug) || empty($model->slug)) {
        $model->generateSlug();
      }
    });

    self::updating(function ($model) {
      $model->title = Purifier::clean($model->title);
      $model->title = str_replace('&amp;', '&', $model->title);
      
      if ($model->isDirty('title')) {
        $model->generateSlug();
      }
    });
  }

  private function generateSlug()
  {
    $this->slug = Slug::makeEn($this->title);
  }

  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, ProductTypes::class, 'type_id', 'product_id', 'id', 'id');
  }

  /**
   * Преобразует название типа в единственное число
   */
  public function getSingularTitle(): string
  {
    $title = trim($this->title);
    
    // Специальные случаи, которые не подчиняются общим правилам
    $specialCases = [
      // Exact labels expected on "Create Product" page
      'Travel Guides' => 'Travel Guide',
      'Creator Guides' => 'Creator Guide',
      'Itineraries' => 'Itinerary',
      'Audio guides' => 'Audio Guide',
      'Video guides' => 'Video Guide',
      'E-books' => 'E-book',
    ];
    
    if (isset($specialCases[$title])) {
      return $specialCases[$title];
    }
    
    // Правила преобразования множественного числа в единственное
    // Оканчивается на -ies -> -y (например, "Stories" -> "Story")
    if (preg_match('/ies$/i', $title)) {
      return preg_replace('/ies$/i', 'y', $title);
    }
    
    // Оканчивается на -es после согласной -> удаляем -es (например, "Boxes" -> "Box")
    // Но не трогаем слова, оканчивающиеся на -ses, -ches, -shes, -xes, -zes
    if (preg_match('/^(.+[^sxzc])es$/i', $title, $matches)) {
      return $matches[1];
    }
    
    // Оканчивается на -s (но не на -ss, -us, -is) -> удаляем -s
    if (preg_match('/^(.+[^sui])s$/i', $title, $matches)) {
      return $matches[1];
    }
    
    // Если не подошло ни одно правило, возвращаем оригинал
    return $title;
  }
}
