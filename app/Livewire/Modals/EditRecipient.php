<?php

namespace App\Livewire\Modals;

use App\Models\Gift;
use App\Mail\GiftRecipient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditRecipient extends Component
{
    public ?Gift $gift = null;
    public ?string $errorMessage = null;
    public string $recipient_email = '';

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
            $this->errorMessage = 'You do not have permission to edit this gift.';
            return;
        }

        if ($gift->status !== Gift::STATUS_CREATED) {
            $this->errorMessage = 'This gift can no longer be edited.';
            return;
        }

        $this->gift = $gift;
        $this->recipient_email = $gift->recipient_email;
    }

    public function save(): void
    {
        if (!$this->gift || $this->gift->status !== Gift::STATUS_CREATED) {
            return;
        }

        $validator = Validator::make(
            ['recipient_email' => $this->recipient_email],
            [
                'recipient_email' => 'required|email|different:' . $this->gift->recipient_email,
            ],
            [
                'recipient_email.required' => 'Please enter recipient email.',
                'recipient_email.email' => 'Please enter a valid email address.',
                'recipient_email.different' => 'Please enter a different email address.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $oldEmail = $this->gift->recipient_email;
        $newEmail = $this->recipient_email;

        // Обновляем recipient_email
        $this->gift->update([
            'recipient_email' => $newEmail,
        ]);

        // Обновляем также в order для совместимости
        $this->gift->order->update([
            'recipient' => $newEmail,
        ]);

        // Отправляем письмо на новый email
        $recipientUser = User::where('email', $newEmail)->first();
        if (!$recipientUser) {
            $recipientUser = User::create([
                'email' => $newEmail,
                'username' => preg_replace("/^(.*?)@.*$/is", "$1", $newEmail),
                'password' => User::makePassword(),
            ]);
        }

        Mail::to($newEmail)->send(
            new GiftRecipient($recipientUser, $this->gift->order, $this->gift)
        );

        $this->dispatch('toastSuccess', [
            'message' => 'Recipient email updated. We\'ve sent the gift to ' . $newEmail . '.'
        ]);

        $this->dispatch('closeModal');
        $this->dispatch('orders:refresh');
    }

    public function render()
    {
        return view('livewire.modals.edit-recipient');
    }
}
