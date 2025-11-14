<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class Tables extends Component
{
    public $tables;

    public $activeTable;

    public $sortable = false;
    public ?string $sorting = null;
    public $args = [];
    public array $sortingOptions = [];

    public function mount(
      array $tables = [], 
      ?string $active = null, 
      ?string $sortable = null,
      ?array $args = [],
      ?string $defaultSorting = null,
      array $sortingOptions = [],
    )
    {
      $this->tables = $tables;
      $this->activeTable = $active;
      $this->sortable = $sortable ?? false;
      $this->args = $args;
      $this->sortingOptions = $sortingOptions;

      if ($this->sortable && !empty($this->sortingOptions)) {
        if (!is_null($defaultSorting) && array_key_exists($defaultSorting, $this->sortingOptions)) {
          $this->sorting = $defaultSorting;
        } else {
          $this->sorting = array_key_first($this->sortingOptions);
        }
      }
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

    public function updatedSorting(?string $value): void
    {
      $this->dispatch('sortingChanged', $value);
    }

    public function render()
    {
      return view('livewire.profile.tables');
    }
}
