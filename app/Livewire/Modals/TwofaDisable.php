<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwofaDisable extends Component
{
    protected $listeners = [
        'modal-opened' => 'handleModalOpened',
    ];

    public string $code = '';
    public string $backupCode = '';

    protected array $messages = [
        'code.digits' => 'Код должен состоять из 6 цифр.',
        'code.required_without' => 'Введите код из приложения или резервный код.',
        'backupCode.size' => 'Резервный код должен содержать 6 символов.',
        'backupCode.alpha_num' => 'Резервный код может содержать только буквы и цифры.',
        'backupCode.required_without' => 'Введите резервный код или код из приложения.',
    ];

    public function mount(): void
    {
        if (!Auth::user()) {
            abort(403);
        }
    }

    public function handleModalOpened(array $payload): void
    {
        if (($payload['modal'] ?? null) !== 'twofa-disable') {
            return;
        }

        $this->reset(['code', 'backupCode']);
        $this->resetValidation();
    }

    public function disable(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if (!$user->twofa) {
            $this->dispatch('toastError', ['message' => 'Двухфакторная аутентификация уже отключена.']);
            $this->dispatch('twofa-disabled');
            $this->dispatch('closeModal');
            return;
        }

        $validated = $this->validate([
            'code' => ['nullable', 'digits:6', 'required_without:backupCode'],
            'backupCode' => ['nullable', 'alpha_num', 'size:6', 'required_without:code'],
        ], $this->messages);

        $authCode = preg_replace('/\s+/', '', $validated['code'] ?? '');
        $backupCode = trim($validated['backupCode'] ?? '');

        if ($authCode !== '') {
            if (empty($user->google2fa_secret)) {
                $this->addError('code', 'Не удалось подтвердить код. Попробуйте резервный код.');
                return;
            }

            try {
                $secret = Crypt::decryptString($user->google2fa_secret);
            } catch (\Throwable $e) {
                Log::error('Failed to decrypt 2FA secret during disable.', [
                    'user_id' => $user->id ?? null,
                    'error' => $e->getMessage(),
                ]);

                $this->addError('code', 'Не удалось проверить код. Попробуйте позже или используйте резервный код.');
                return;
            }

            if (!Google2FA::verifyKey($secret, $authCode, 4)) {
                $this->addError('code', 'Неверный код из приложения.');
                return;
            }
        } else {
            $exists = $user->backup()->where('code', $backupCode)->exists();

            if (!$exists) {
                $this->addError('backupCode', 'Неверный резервный код.');
                return;
            }
        }

        try {
            DB::transaction(function () use ($user) {
                $user->forceFill([
                    'twofa' => 0,
                    'google2fa_secret' => null,
                ])->save();

                $user->backup()->delete();
            });
        } catch (\Throwable $e) {
            Log::error('Failed to disable two-factor authentication.', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Не удалось отключить 2FA. Попробуйте позже.']);
            return;
        }

        $this->reset(['code', 'backupCode']);

        $this->dispatch('closeModal');
        $this->dispatch('openModal', 'twofa-disable-accept');
        $this->dispatch('twofa-disabled');
    }

    public function openBackup(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $this->dispatch('openModal', 'backup', ['user_id' => Crypt::encrypt($user->id)]);
    }

    public function render()
    {
        return view('livewire.modals.twofa-disable');
    }
}
