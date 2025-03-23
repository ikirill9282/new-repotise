<?php 

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
    return mb_substr($this->text, 0, $symbols) . '...';
  }
}