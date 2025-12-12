<?php

namespace App\Livewire;

use App\Models\Gift;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Enums\Order as EnumsOrder;
use App\Mail\GiftClaimed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ClaimGift extends Component
{
    public string $token;
    public ?Gift $gift = null;

    public function mount(string $token)
    {
        $this->token = $token;
        $this->gift = Gift::where('token', $token)
            ->with(['order.order_products.product.preview', 'order.buyer', 'buyer'])
            ->first();
    }

    public function claim()
    {
        if (!Auth::check()) {
            $this->dispatch('openModal', ['modalName' => 'auth']);
            return;
        }

        if (!$this->gift || !$this->gift->canBeClaimed()) {
            return redirect()->route('gift', ['token' => $this->token]);
        }

        $user = Auth::user();

        // Проверяем, что email совпадает или это тот же пользователь (разрешаем если покупатель и получатель один и тот же)
        if ($user->email !== $this->gift->recipient_email && $this->gift->buyer_user_id !== $user->id) {
            $this->addError('email', 'This gift was sent to a different email address.');
            return;
        }

        DB::transaction(function () use ($user) {
            // Создаем новый Order для получателя со статусом PAID
            $originalOrder = $this->gift->order;
            $newOrder = Order::create([
                'user_id' => $user->id,
                'buyer_user_id' => $user->id,
                'status_id' => EnumsOrder::PAID,
                'cost' => $originalOrder->cost,
                'discount_amount' => $originalOrder->discount_amount ?? 0,
                'tax' => $originalOrder->tax ?? 0,
                'cost_without_discount' => $originalOrder->cost_without_discount,
                'cost_without_tax' => $originalOrder->cost_without_tax,
                'is_gift_order' => false,
            ]);

            // Копируем order_products
            foreach ($originalOrder->order_products as $orderProduct) {
                OrderProducts::create([
                    'order_id' => $newOrder->id,
                    'product_id' => $orderProduct->product_id,
                    'count' => $orderProduct->count,
                    'price' => $orderProduct->price,
                    'sale_price' => $orderProduct->sale_price,
                    'total' => $orderProduct->total,
                    'total_without_discount' => $orderProduct->total_without_discount,
                    'discount' => $orderProduct->discount ?? 0,
                    'payment_fee' => $orderProduct->payment_fee ?? 0,
                    'seller_reward' => $orderProduct->seller_reward ?? 0,
                    'referal_reward' => $orderProduct->referal_reward ?? 0,
                    'platform_reward' => $orderProduct->platform_reward ?? 0,
                ]);
            }

            // Обновляем Gift
            $this->gift->update([
                'recipient_user_id' => $user->id,
                'status' => Gift::STATUS_CLAIMED,
                'claimed_at' => now(),
            ]);

            // Отправляем письмо покупателю
            $buyer = $this->gift->buyer;
            if ($buyer) {
                Mail::to($buyer->email)->send(
                    new GiftClaimed($buyer, $originalOrder, $this->gift)
                );
            }
        });

        return redirect()->route('profile.purchases');
    }

    public function render()
    {
        return view('livewire.claim-gift', [
            'gift' => $this->gift,
        ]);
    }
}
