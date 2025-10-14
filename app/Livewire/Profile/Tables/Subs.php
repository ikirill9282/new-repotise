<?php

namespace App\Livewire\Profile\Tables;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class Subs extends Component
{

    public string $user_id;

    protected $listeners = ['subs-refresh' => '$refresh'];

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

    public function completePayment(string $order_id)
    {
      Session::put('checkout', Crypt::decrypt($order_id));
      return redirect()->route('checkout.subscription');
    }

    public function render()
    {
      $user = $this->getUser();

      return view('livewire.profile.tables.subs', [
        'user' => $user,
        'subs' => $user->orders()->where('type', 'sub')->get(),
      ]);
    }
}
