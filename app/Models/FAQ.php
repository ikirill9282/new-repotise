<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class FAQ extends Model
{
    protected $table = 'faq';

    protected static function boot()
    {
        parent::boot();

        $purify = function (FAQ $model): void {
            // Questions are plain text; answers may contain limited HTML from RichEditor.
            if (($model->type ?? null) === 'question') {
                $model->text = strip_tags(Purifier::clean($model->text));
            } else {
                $model->text = Purifier::clean($model->text);
            }
        };

        static::creating($purify);
        static::updating($purify);
    }

    public function answer()
    {
      return $this->hasOne(FAQ::class, 'parent_id');
    }

    public function question()
    {
      return $this->belongsTo(FAQ::class, 'id', 'parent_id');
    }
}
