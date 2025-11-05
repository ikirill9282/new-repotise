<?php

namespace App\Livewire\Modals;

use App\Models\UserBackup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class Twofa extends Component
{
    protected $listeners = [
        'modal-opened' => 'handleModalOpened',
    ];

    public string $qrCodeSrc = '';
    public string $otpAuthUrl = '';
    public string $secret = '';
    public array $backupCodes = [];
    public string $code = '';
    public bool $confirmedBackup = false;

    protected array $messages = [
        'code.required' => 'Введите код из приложения.',
        'code.digits' => 'Код должен содержать 6 цифр.',
        'confirmedBackup.accepted' => 'Подтвердите, что сохранили резервные коды.',
    ];

    public function mount(): void
    {
        $this->initializePayload();
    }

    public function handleModalOpened(array $payload): void
    {
        if (($payload['modal'] ?? null) !== 'twofa') {
            return;
        }

        $this->initializePayload();
    }

    public function enable(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $this->validate([
            'code' => ['required', 'digits:6'],
            'confirmedBackup' => ['accepted'],
        ], $this->messages);

        $code = preg_replace('/\s+/', '', $validated['code']);

        if (!Google2FA::verifyKey($this->secret, $code, 4)) {
            $this->addError('code', 'Неверный код из приложения. Попробуйте снова.');
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                $user->forceFill([
                    'google2fa_secret' => Crypt::encryptString($this->secret),
                    'twofa' => 1,
                ])->save();

                $user->backup()->delete();
                foreach ($this->backupCodes as $code) {
                    $user->backup()->create(['code' => $code]);
                }
            });
        } catch (\Throwable $e) {
            Log::error('Failed to enable two-factor authentication.', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Не удалось включить 2FA. Попробуйте позже.']);
            return;
        }

        $this->reset(['code', 'confirmedBackup']);

        $this->dispatch('closeModal');
        $this->dispatch('openModal', 'twofa-accept');
        $this->dispatch('twofa-enabled');
    }

    public function render()
    {
        return view('livewire.modals.twofa', [
            'manualKey' => $this->secret,
            'qrCodeSrc' => $this->qrCodeSrc,
            'otpAuthUrl' => $this->otpAuthUrl,
        ]);
    }

    protected function initializePayload(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $this->reset(['code', 'confirmedBackup']);
        $this->resetValidation();

        $this->secret = Google2FA::generateSecretKey();

        $issuer = rawurlencode(config('app.name', 'TrekGuider'));
        $account = rawurlencode($user->email);
        $label = $issuer . '%3A' . $account;

        $this->otpAuthUrl = sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s',
            $label,
            $this->secret,
            $issuer
        );

        $this->qrCodeSrc = sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=%s',
            urlencode($this->otpAuthUrl)
        );
        $this->backupCodes = UserBackup::generate();
    }
}
