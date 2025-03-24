<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public function pages()
    {
      return $this->belongsToMany(Page::class, 'page_sections');
    }

    public function variables()
    {
      return $this->hasMany(SectionVariables::class);
    }

    public function group()
    {
      return $this->hasMany(Section::class, 'parent_id');
    }
}
