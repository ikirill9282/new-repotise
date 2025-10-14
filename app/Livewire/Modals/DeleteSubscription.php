<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use App\Models\Order;

class DeleteSubscription extends Component
{
    public string $order_id;

    public function mount(string $order_id)
    {
      $this->order_id = $order_id;
    }

    public function getOrder(): ?Order
    {
      return Order::find(Crypt::decrypt($this->order_id));
    }

    public function deleteSubscription()
    {
      $this->getOrder()?->delete();
      $this->dispatch('subs-refresh');
      $this->dispatch('openModal', 'delete-subscription-accept');
    }

    public function render()
    {
        return view('livewire.modals.delete-subscription');
    }
}
