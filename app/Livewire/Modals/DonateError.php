<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class DonateError extends Component
{
    public string $message = '';

    public string $errorCode = '';

    public ?int $seller_id = null;

    public function mount(?string $message = null, ?string $errorCode = null, ?int $seller_id = null): void
    {
        $this->message = $message ?? 'We couldn\'t process your payment. Please check your payment information and try again, or use a different payment method.';
        $this->errorCode = $errorCode ?? '';
        $this->seller_id = $seller_id ?? (int) request()->query('seller_id', 0);
    }

    public function render()
    {
        return view('livewire.modals.donate-error');
    }
}
