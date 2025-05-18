<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Section extends Model
{
    public function pages()
    {
      // return $this->belongsToMany(Page::class, 'page_sections')->withPivot('order');
    }

    public function variables()
    {
      return $this->hasMany(SectionVariables::class);
    }

    public function group()
    {
      return $this->hasMany(Section::class, 'parent_id');
    }

    public function copyWithVariables()
    {
      DB::beginTransaction();
      try {
        $new_section = Section::create([
          'title' => $this->title,
          'slug' => "$this->slug-copy",
          'type' => $this->type,
          'component' => $this->component,
        ]);

        $new_section->update(['slug' => "custom-{$new_section->id}", 'title' => "Custom {$new_section->id}"]);

        if (!str_contains($new_section->slug, 'custom')) {
          $new_variables = $this->variables->map(fn($var) => [
            'section_id' => $new_section->id,
            'name' => $var->name,
            'value' => $var->value,
          ])
            ->toArray();
  
          SectionVariables::query()->upsert($new_variables, ['section_id', 'name'], ['value']);
        }
      } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
      }

      DB::commit();
      return $new_section;
    }
}
