<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['source', 'user_id', 'data'];
    protected $casts = ['data' => 'array']; // опционально, чтобы хранить массив
}

