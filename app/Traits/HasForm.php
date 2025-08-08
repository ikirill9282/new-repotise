<?php

namespace App\Traits;


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
}