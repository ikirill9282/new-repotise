<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class Tables extends Component
{
    public $tables;

    public $activeTable;

    public $sortable = false;
    public $sorting = null;
    public $args = [];

    public function mount(
      array $tables = [], 
      ?string $active = null, 
      ?string $sortable = null,
      ?array $args = [],
    )
    {
      $this->tables = $tables;
      $this->activeTable = $active;
      $this->sortable = $sortable ?? false;
      $this->args = $args;
    }

    public function setActive(string $name)
    {
      $table = $this->getTableByName($name);
      if (isset($table['href']) && !empty($table['href'])) {
        return redirect($table['href']);
      }

      $this->activeTable = $name;
      $this->dispatch('tableChanged', $this->activeTable);
    }

    public function getTableName()
    {
      if (str_contains($this->activeTable, 'products-')) {
        return "profile.tables.profile-product";
      }
      if (str_contains($this->activeTable, 'articles-')) {
        return "profile.tables.profile-article";
      }
      return "profile.tables.". $this->activeTable;
    }

    public function getTableByName(string $name)
    {
      foreach ($this->tables as $table) {
        if ($table['name'] == $name) {
          return $table;
        }
      }
    }

    public function render()
    {
      return view('livewire.profile.tables');
    }
}
