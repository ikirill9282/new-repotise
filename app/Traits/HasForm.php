<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

trait HasForm
{
    public function getValidFormState(): ?array
    {
        return $this->validate()['form'] ?? null;
    }

    public function resetForm()
    {
        $this->form = array_fill_keys(array_keys($this->form), null);
        $this->resetValidation();
    }

    public function getFormFields(Model $model, array $exclude = []): array
    {
      if ($model->exists) {
        return collect($model->getAttributes())
          ->filter(fn($val, $key) => !in_array($key, $exclude))
          ->toArray();
      } else {
        $fields = Schema::getColumnListing($model->getTable());
        $fields = collect($fields)
          ->filter(fn($item) => !in_array($item, $exclude))
          ->values()
          ->toArray();

        return array_fill_keys($fields, null);
      }
    }

}