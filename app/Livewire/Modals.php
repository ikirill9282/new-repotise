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

    public $oneTime = ['report', 'file-description', 'social', 'contact', 'auth-second', 'backup'];

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

    public function modalHasLogo()
    {
      $arr = [
        'cart', 
        'levels', 
        'product', 
        'refund',
        'refund-accept',
        'cancelsub',
        'cancelsub-accept',
        'funds',
        'funds-success',
        'funds-error',
        'withdraw',
        'withdraw-accept',
        'change-email',
        'change-email-accept',
        'twofa',
        'twofa-accept',
        'twofa-disable',
        'twofa-disable-accept',
        'payout-method',
        'delete-account',
        'delete-account-accept',
        'message',
        'contact',
        'social',
        'donate',
        'donate-accept',
        'donate-sub-accept',
        'donate-error',
        'promocodes',
        'order',
        'edit-contacts',
        'file-description',
      ];
      return !in_array($this->modal, $arr);
    }    

    public function modalMaxWidth()
    {
      if (in_array($this->modal, ['cart', 'levels'])) return '!max-w-none';

      if (in_array($this->modal, ['promocodes'])) return '!max-w-7xl';

      if (in_array($this->modal, ['product', 'twofa'])) return '!max-w-4xl';

      if (in_array($this->modal, ['delete-account'])) return '!max-w-3xl';

      if (in_array($this->modal, [
        'refund',
        'cancelsub',
        'withdraw',
        'twofa-accept',
        'twofa-disable-accept',
        'delete-account-accept',
      ])) return '!max-w-2xl';

      if (in_array($this->modal, [
        'refund-accept',
        'cancelsub-accept',
        'withdraw-accept',
        'change-email',
        'change-email-accept',
        'twofa-disable',
        'delete-account',
        'message',
        'social',
        'donate',
      ])) return '!max-w-2xl';
      
      if (in_array($this->modal, ['funds', 'contact'])) return '!max-w-xl';

      return '';
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
