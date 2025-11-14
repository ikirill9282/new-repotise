<?php

namespace App\Livewire\Modals;

use App\Helpers\SessionExpire;
use App\Mail\AccountDeletionCode;
use App\Models\UserVerify;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteAccount extends Component
{
    protected $listeners = [
        'modal-opened' => 'handleModalOpened',
    ];

    public string $current_password = '';
    public string $verification_code = '';
    public ?int $resendAvailableAt = null;

    protected array $messages = [
        'current_password.required' => 'Please enter your current password.',
        'verification_code.required' => 'Enter the verification code we sent to your email.',
        'verification_code.digits' => 'The verification code must contain exactly 6 digits.',
    ];

    protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'verification_code' => ['required', 'digits:6'],
        ];
    }

    public function handleModalOpened(array $payload): void
    {
        if (($payload['modal'] ?? null) !== 'delete-account') {
            return;
        }

        $this->resetForm();
        $this->syncResendTimer();
    }

    public function sendVerificationCode(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if (SessionExpire::exists('delete_account_code')) {
            $this->syncResendTimer();
            $this->dispatch('toastError', ['message' => 'A verification code was just sent. Please wait a moment before requesting another one.']);
            return;
        }

        $code = $this->generateCode();

        try {
            $user->verify()->updateOrCreate(
                ['type' => 'account_delete'],
                [
                    'code' => $code,
                    'created_at' => Carbon::now()->timestamp,
                ]
            );
            $user->load('verify');
            Mail::to($user->email)->send(new AccountDeletionCode($user, $code));
        } catch (\Throwable $e) {
            Log::error('Failed to send account deletion verification code.', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Unable to send verification code right now. Please try again later.']);
            return;
        }

        SessionExpire::set('delete_account_email', $user->email, Carbon::now()->addHour());
        SessionExpire::set('delete_account_code', $code, Carbon::now()->addMinutes(3));

        $this->syncResendTimer();

        $this->dispatch('toastSuccess', ['message' => 'Verification code sent to your email.']);
    }

    public function confirmDeletion(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $this->validate($this->rules(), $this->messages);

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Incorrect password. Please try again.',
            ]);
        }

        $verification = $user->verify()
            ->where('type', 'account_delete')
            ->first();

        if (!$verification || $verification->code !== $validated['verification_code']) {
            throw ValidationException::withMessages([
                'verification_code' => 'Invalid or expired verification code.',
            ]);
        }

        if ($this->codeExpired($verification->created_at ?? null)) {
            $user->verify()->where('type', 'account_delete')->delete();

            throw ValidationException::withMessages([
                'verification_code' => 'This verification code has expired. Please request a new one.',
            ]);
        }

        try {
            DB::transaction(function () use ($user) {
                $user->forceFill([
                    'active' => 0,
                    'deletion_requested_at' => Carbon::now(),
                    'deletion_scheduled_for' => Carbon::now()->addDays(30),
                ])->save();

                $user->verify()->where('type', 'account_delete')->delete();
            });
        } catch (\Throwable $e) {
            Log::error('Failed to schedule account deletion.', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Unable to schedule the deletion right now. Please try again later.']);
            return;
        }

        session()->forget('delete_account_code');
        session()->forget('delete_account_email');

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->resetForm();

        $this->redirectRoute('home', ['modal' => 'delete-account-accept'], navigate: true);
    }

    public function render()
    {
        return view('livewire.modals.delete-account');
    }

    public function getResendDisabledProperty(): bool
    {
        return $this->resendAvailableAt !== null
            && $this->resendAvailableAt > now()->timestamp * 1000;
    }

    public function getResendSecondsProperty(): ?int
    {
        if (!$this->resendDisabled) {
            return null;
        }

        $diff = $this->resendAvailableAt - (now()->timestamp * 1000);

        return $diff > 0 ? (int) ceil($diff / 1000) : null;
    }

    protected function syncResendTimer(): void
    {
        $expires = SessionExpire::getExpire('delete_account_code');
        $this->resendAvailableAt = $expires ? Carbon::parse($expires)->timestamp * 1000 : null;
    }

    protected function resetForm(): void
    {
        $this->reset(['current_password', 'verification_code']);
        $this->resendAvailableAt = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    protected function generateCode(): string
    {
        do {
            $code = (string) random_int(100000, 999999);
        } while (UserVerify::where('code', $code)->exists());

        return $code;
    }

    protected function codeExpired(?int $createdAt): bool
    {
        if (!$createdAt) {
            return true;
        }

        return Carbon::createFromTimestamp($createdAt)->addHour()->isPast();
    }
}
