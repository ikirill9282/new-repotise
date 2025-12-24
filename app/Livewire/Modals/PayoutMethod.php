<?php

namespace App\Livewire\Modals;

use App\Helpers\SessionExpire;
use App\Mail\PayoutMethodConfirmationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PayoutMethod extends Component
{
    public string $code = '';
    public ?int $resend = null;

    public function mount(): void
    {
        $this->ensureCodeSent();
    }

    public function ensureCodeSent(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        if (SessionExpire::exists('payout_method_code_cooldown')) {
            $this->resend = (int) SessionExpire::get('payout_method_code_cooldown');
            return;
        }

        $this->sendCode();
    }

    public function sendCode(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $code = random_int(100000, 999999);

        $user->verify()->updateOrCreate(
            ['type' => 'payout_method'],
            ['code' => (string) $code, 'created_at' => Carbon::now()->timestamp],
        );

        // cooldown 1 minute for resend
        SessionExpire::set('payout_method_code_cooldown', (string) Carbon::now()->timestamp, Carbon::now()->addMinutes(1));
        $this->resend = Carbon::now()->timestamp;

        try {
            Mail::to($user->email)->send(new PayoutMethodConfirmationCode($user, $code));
        } catch (\Throwable $e) {
            // ignore mail errors; user can retry
        }
    }

    public function confirm(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $verify = $user->verify()->where('type', 'payout_method')->first();
        if (!$verify || (string) $verify->code !== (string) $this->code) {
            $this->dispatch('toastError', [
                'heading' => 'Verification Failed',
                'message' => 'Invalid verification code. Please check the code and try again.',
            ]);
            return;
        }

        // mark verified for this session
        session()->put('payout_method_verified', true);

        // proceed to add payment method
        $this->dispatch('openModal', 'payment-method');
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.modals.payout-method');
    }
}
