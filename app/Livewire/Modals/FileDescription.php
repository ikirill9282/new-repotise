<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class FileDescription extends Component
{

    public string $filename;
    public string $key;

    public string $description;

    public function mount(string $filename = '', string $key = '', string $description = '')
    {
      $this->filename = $filename;
      $this->key = $key;
      $this->description = $description;
    }

    public function submit()
    {
      $this->dispatch('fileDescriptionUpdated', ['key' => $this->key, 'description' => $this->description]);
      $this->dispatch('closeModal');
    }

    public function render()
    {
      return view('livewire.modals.file-description');
    }
}
