<?php

namespace App\Livewire\Modals;

use App\Models\Gift;
use App\Mail\GiftRecipient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ResendGift extends Component
{
    public ?Gift $gift = null;
    public ?string $errorMessage = null;

    public function mount(?string $gift_id = null): void
    {
        if (!$gift_id) {
            $this->errorMessage = 'Gift data is missing.';
            return;
        }

        try {
            $giftId = (int) Crypt::decryptString($gift_id);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Invalid gift reference.';
            return;
        }

        $gift = Gift::with(['order.order_products.product', 'buyer'])
            ->whereKey($giftId)
            ->first();

        if (!$gift || $gift->buyer_user_id !== Auth::id()) {
            $this->errorMessage = 'You do not have permission to resend this gift.';
            return;
        }

        if ($gift->status !== Gift::STATUS_CREATED) {
            $this->errorMessage = 'This gift can no longer be resent.';
            return;
        }

        $this->gift = $gift;
    }

    public function resend(): void
    {
        if (!$this->gift || $this->gift->status !== Gift::STATUS_CREATED) {
            return;
        }

        $recipientUser = User::where('email', $this->gift->recipient_email)->first();
        if (!$recipientUser) {
            // Создаем пользователя если его нет
            $recipientUser = User::create([
                'email' => $this->gift->recipient_email,
                'username' => preg_replace("/^(.*?)@.*$/is", "$1", $this->gift->recipient_email),
                'password' => User::makePassword(),
            ]);
        }

        Mail::to($this->gift->recipient_email)->send(
            new GiftRecipient($recipientUser, $this->gift->order, $this->gift)
        );

        $this->dispatch('toastSuccess', [
            'message' => 'Gift email has been resent to ' . $this->gift->recipient_email . '.'
        ]);

        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.modals.resend-gift');
    }
}
