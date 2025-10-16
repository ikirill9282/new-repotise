<?php

namespace App\Livewire\Profile\Tables;

use App\Enums\Order;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class Orders extends Component
{

    public string $user_id;

    public function mount(string $user_id)
    {
      $this->user_id = $user_id;
    }

    public function getUser(): ?User
    {
      return User::find(Crypt::decrypt($this->user_id));
    }

    public function moveCheckout(string $order_id)
    {
      $id = Crypt::decrypt($order_id);
      Session::put('checkout', $id);

      return redirect()->route('checkout');
    }

    public function render()
    {
      $user = $this->getUser();

      return view('livewire.profile.tables.orders', [
        'user' => $user,
        'orders' => $user->orders()->get(),
      ]);
    }
}
