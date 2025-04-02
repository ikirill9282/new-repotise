<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class Category extends Model
{
  use Searchable;

  public function toSearchableArray(): array
  {
    $array = $this->only('id', 'title', 'slug', 'parent_id');
    
    return $array;
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id', 'id', 'id');
  }
}
