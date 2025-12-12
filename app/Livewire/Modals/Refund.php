<?php

namespace App\Livewire\Modals;

use App\Models\OrderProducts;
use App\Models\RefundRequest;
use App\Models\Gift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Refund extends Component
{
    public array $form = [
        'reason' => null,
        'details' => null,
    ];

    public ?OrderProducts $orderProduct = null;

    public ?string $errorMessage = null;

    public array $reasonOptions = [
        'not_received' => 'I did not receive the product',
        'not_as_described' => 'The product is not as described',
        'broken_or_corrupt' => 'Files are broken or inaccessible',
        'unauthorised_purchase' => 'This was not an authorised purchase',
        'other' => 'Other',
    ];

    public function mount(?string $order_product_id = null, ?string $order_id = null): void
    {
        if (!$order_product_id) {
            $this->errorMessage = 'Order data is missing. Please try again later.';
            return;
        }

        try {
            $orderProductId = (int) Crypt::decryptString($order_product_id);
            $orderId = $order_id ? (int) Crypt::decryptString($order_id) : null;
        } catch (\Throwable $e) {
            $this->errorMessage = 'We could not verify this purchase. Please refresh the page and try again.';
            return;
        }

        $orderProduct = OrderProducts::with(['order.gift', 'product.author', 'refundRequest'])
            ->whereKey($orderProductId)
            ->when($orderId, fn($query) => $query->where('order_id', $orderId))
            ->first();

        if (!$orderProduct || !$orderProduct->order) {
            $this->errorMessage = 'You do not have permission to request a refund for this product.';
            return;
        }

        $order = $orderProduct->order;
        
        // Проверяем права доступа
        $hasAccess = false;
        if ($order->is_gift_order && $order->gift) {
            // Для подарков: доступ есть только у buyer_user_id
            $hasAccess = $order->buyer_user_id === Auth::id();
            
            // Для подарков рефанд доступен только если gift.status = created
            if ($hasAccess && $order->gift->status !== \App\Models\Gift::STATUS_CREATED) {
                $this->errorMessage = 'Refunds are not available for claimed gifts.';
                return;
            }
        } else {
            // Для обычных заказов: доступ есть у user_id
            $hasAccess = $order->user_id === Auth::id();
        }

        if (!$hasAccess) {
            $this->errorMessage = 'You do not have permission to request a refund for this product.';
            return;
        }

        if ($orderProduct->refundRequest) {
            $this->errorMessage = 'A refund request has already been submitted for this product.';
            return;
        }

        $this->orderProduct = $orderProduct;
    }

    public function submit(): void
    {
        if (!$this->orderProduct) {
            throw ValidationException::withMessages([
                'form.reason' => 'Unable to submit refund request at this time.',
            ]);
        }

        if ($this->orderProduct->refundRequest) {
            throw ValidationException::withMessages([
                'form.reason' => 'A refund request has already been submitted for this product.',
            ]);
        }

        $validator = Validator::make(
            $this->form,
            [
                'reason' => 'required|in:' . implode(',', array_keys($this->reasonOptions)),
                'details' => 'nullable|string|max:1000',
            ],
            [
                'reason.required' => 'Please select a refund reason.',
                'reason.in' => 'Please choose a valid refund reason.',
                'details.max' => 'Please keep your message under 1000 characters.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $valid = $validator->validated();

        $order = $this->orderProduct->order;
        $buyerId = $order->is_gift_order ? ($order->buyer_user_id ?? Auth::id()) : Auth::id();
        
        RefundRequest::create([
            'order_id' => $this->orderProduct->order_id,
            'order_product_id' => $this->orderProduct->id,
            'buyer_id' => $buyerId,
            'seller_id' => $this->orderProduct->product->author->id ?? null,
            'status' => 'pending',
            'reason' => $valid['reason'],
            'details' => $valid['details'],
        ]);

        $this->dispatch('orders:refresh');
        $this->dispatch('openModal', 'refund-accept');
    }

    public function render()
    {
        return view('livewire.modals.refund', [
            'orderProduct' => $this->orderProduct,
            'errorMessage' => $this->errorMessage,
            'reasonOptions' => $this->reasonOptions,
        ]);
    }
}
