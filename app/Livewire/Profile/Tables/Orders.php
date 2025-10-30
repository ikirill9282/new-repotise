<?php

namespace App\Livewire\Profile\Tables;

use App\Enums\Order;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

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

    #[On('orders:refresh')]
    public function refreshOrders(): void
    {
        $this->dispatch('$refresh');
    }

    public function openProductModal(string $encryptedOrderProductId, string $encryptedOrderId): void
    {
        $this->dispatch(
            'openModal',
            modalName: 'product',
            args: [
                'order_product_id' => $encryptedOrderProductId,
                'order_id' => $encryptedOrderId,
            ],
        );
    }

    public function openRefundModal(string $encryptedOrderProductId, string $encryptedOrderId): void
    {
        $this->dispatch(
            'openModal',
            modalName: 'refund',
            args: [
                'order_product_id' => $encryptedOrderProductId,
                'order_id' => $encryptedOrderId,
            ],
        );
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
        'orders' => $user->orders()
          ->with([
            'order_products.product.preview',
            'order_products.refundRequest',
          ])
          ->get(),
      ]);
    }
}
