<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\OrderProducts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class Product extends Component
{
    public ?OrderProducts $orderProduct = null;

    public ?string $errorMessage = null;

    public function mount(?string $order_product_id = null, ?string $order_id = null): void
    {
        if (!$order_product_id) {
            $this->errorMessage = 'Product data is unavailable.';
            return;
        }

        try {
            $orderProductId = Crypt::decryptString($order_product_id);
            $orderId = $order_id ? Crypt::decryptString($order_id) : null;
        } catch (\Throwable $e) {
            $this->errorMessage = 'Invalid download request.';
            return;
        }

        $orderProduct = OrderProducts::with(['order', 'product.files', 'product.links', 'product.author'])
            ->whereKey($orderProductId)
            ->when($orderId, fn($query) => $query->where('order_id', $orderId))
            ->first();

        if (!$orderProduct || !$orderProduct->order || $orderProduct->order->user_id !== Auth::id()) {
            $this->errorMessage = 'You do not have access to this product.';
            return;
        }

        if ($orderProduct->refunded) {
            $this->errorMessage = 'Access to this product was removed after the refund was approved.';
            return;
        }

        $this->orderProduct = $orderProduct;
    }

    public function render()
    {
        return view('livewire.modals.product', [
            'orderProduct' => $this->orderProduct,
            'errorMessage' => $this->errorMessage,
        ]);
    }
}
