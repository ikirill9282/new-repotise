<?php 

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait HasAuthor
{
  protected $append = [
    'short',
  ];

  public function author()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function short(int $symbols = 200)
  {
    return trim(mb_substr($this->text, 0, $symbols) . '...');
  }
}