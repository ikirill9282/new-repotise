<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public function page()
    {
      return $this->belongsToMany(Page::class, 'page_sections');
    }

    public function variables()
    {
      return $this->hasMany(SectionVariables::class);
    }
}
