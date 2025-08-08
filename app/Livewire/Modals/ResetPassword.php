<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;

class ResetPassword extends Component
{
    use HasForm;

    public array $form = [
      'email' => null,
    ];
    
    public function getRules()
    {
        return [
            'form.email' => 'required|email|exists:users,email',
        ];
    }

    public function getMessages()
    {
        return [
            'form.email.required' => 'The email field is required.',
            'form.email.email' => 'The email must be a valid email address.',
            'form.email.exists' => 'The email does not exist.',
        ];
    }

    public function submit()
    {
        $state = $this->getValidFormState();
        $user = User::firstWhere('email', $state['email']);
        $user->sendResetCode();
        $this->dispatch('openModal', 'reset-password-confirm', ['email' => $user->email]);
    }

    public function render()
    {
        return view('livewire.modals.reset-password');
    }
}
