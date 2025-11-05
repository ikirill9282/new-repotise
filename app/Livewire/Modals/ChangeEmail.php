<?php

namespace App\Livewire\Modals;

use App\Mail\EmailChangeVerification;
use App\Models\EmailChange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Livewire\Component;

class ChangeEmail extends Component
{
    protected $listeners = [
        'modal-opened' => 'handleModalOpened',
    ];

    public string $current_password = '';
    public string $email = '';

    protected array $messages = [
        'current_password.required' => 'Current password is required.',
        'email.required' => 'Please enter your new email address.',
        'email.email' => 'Enter a valid email address.',
        'email.unique' => 'This email is already taken.',
    ];

    protected function rules(): array
    {
        $userId = Auth::id();

        return [
            'current_password' => ['required', 'string'],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
        ];
    }

    public function submit(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $this->validate();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Incorrect password.',
            ]);
        }

        if ($validated['email'] === $user->email) {
            throw ValidationException::withMessages([
                'email' => 'This is already your current email address.',
            ]);
        }

        EmailChange::where('user_id', $user->id)->delete();

        $token = Str::random(64);

        EmailChange::create([
            'user_id' => $user->id,
            'new_email' => $validated['email'],
            'token' => hash('sha256', $token),
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'profile.settings.email.verify',
            now()->addHour(),
            [
                'token' => $token,
            ]
        );

        try {
            Mail::to($validated['email'])->send(new EmailChangeVerification($user, $verificationUrl));
        } catch (\Throwable $e) {
            Log::error('Failed to send email change verification.', [
                'user_id' => $user->id,
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Failed to send verification email. Please try again.']);

            return;
        }

        $this->reset(['current_password', 'email']);

        $this->dispatch('closeModal');
        $this->dispatch('toastSuccess', ['message' => 'Verification link sent to your new email.']);
    }

    public function handleModalOpened(array $payload): void
    {
        if (($payload['modal'] ?? null) !== 'change-email') {
            return;
        }

        $this->reset(['current_password', 'email']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.modals.change-email');
    }
}
