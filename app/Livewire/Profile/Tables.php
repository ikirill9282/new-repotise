<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class Tables extends Component
{
    public $tables;

    public $activeTable;

    public $sortable = false;
    public $sorting = null;

    public function mount(
      array $tables = [], 
      ?string $active = null, 
      ?string $sortable = null
    )
    {
      $this->tables = $tables;
      $this->activeTable = $active;
      $this->sortable = $sortable ?? false;
    }

    public function setActive(string $name)
    {
      $this->activeTable = $name;
    }

    public function getTableName()
    {
      if (str_contains($this->activeTable, 'products-')) {
        return "profile.tables.profile-product";
      }
      return "profile.tables.". $this->activeTable;
    }

    public function render()
    {
      return view('livewire.profile.tables');
    }
}
