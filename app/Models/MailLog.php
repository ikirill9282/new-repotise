<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    public function user()
    {
      return $this->belongsTo(User::class, 'recipient', 'email');
    }
    public function author()
    {
      return $this->belongsTo(User::class, 'recipient', 'email');
    }
}
