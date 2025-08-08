<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\User;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth as AuthFacade;

class Auth extends Component
{
    use HasForm;

    public array $form = [
        'email' => '',
        'password' => '',
        'remember' => false,
    ];

    public function getRules()
    {
        return [
            'form.email' => 'required|email',
            'form.password' => 'required|min:6',
            'form.remember' => 'boolean',
        ];
    }

    public function getMessages()
    {
        return [
            'form.email.required' => 'Email is required.',
            'form.email.email' => 'Please enter a valid email address.',
            'form.password.required' => 'Password is required.',
            'form.password.min' => 'Password must be at least 6 characters long.',
            'form.remember.boolean' => 'Remember me must be true or false.',
        ];
    }

    public function submit()
    {
        $state = $this->getValidFormState();
        $user = User::firstWhere('email', $state['email']);
        if (!$user) {
            $this->addError('form.email', 'Invalid email or password. Please try again.');
            return false;
        }

        if (!$user->active) {
          $this->addError('form.email', 'Your account is temporarily locked. Please try again later or contact support.');
          return false;
        }
        if (AuthFacade::attempt(['email' => $state['email'], 'password' => $state['password']], $state['remember'])) {
          Session::regenerate(true);
          return redirect('/');
        }

        $this->addError('form.email', 'Invalid email or password. Please try again.');
    }

    public function render()
    {
        return view('livewire.modals.auth');
    }
}
