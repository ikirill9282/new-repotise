<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Modals extends Component
{
    public ?string $modal = null;    
    public $isVisible = false;
    public $inited = false;
    public $args = [];

    public $oneTime = ['report'];

    public function mount()
    {
      $this->modal = request()->get('modal', null);
      if ($this->modal) {
          $this->isVisible = true;
          $this->inited = true;
          $this->startShowAnimation();
      } else {
          $this->isVisible = false;
      }
    }

    #[On('openModal')]
    public function openModal($modalName, $args = [])
    {
      $this->args = $args;
      $this->modal = $modalName;
      $this->inited = true;
      $this->startShowAnimation();
      if (!in_array($modalName, $this->oneTime)) {
        $this->dispatch('modal-opened', ['modal' => $modalName]);
      }
    }

    #[On('closeModal')]
    public function closeModal()
    {
        $this->isVisible = false;
        $this->dispatch('modal-closing-clean-url');
        $this->dispatch('modalClosing');
    }

    public function startShowAnimation()
    {
        $this->isVisible = true;
    }

    public function finalizeClose()
    {
        $this->modal = false;
    }

    

    public function render()
    {
        return view('livewire.modals', [
          'modal' => $this->modal,
          'isVisible' => $this->isVisible,
          'args' => $this->args,
        ]);
    }
}
