<?php

namespace App\Livewire\Modals;

use App\Traits\HasForm;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\HtmlString;

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
            'form.email.required' => 'Please enter your email address.',
            'form.email.email' => 'Please enter a valid email address.',
            'form.email.exists' => new HtmlString('Email address not found. Please check your email or <a href="#" onclick="Livewire.dispatch(\'openModal\', { modalName: \'auth\' }); return false;" class="text-active underline sign-in-res">Sign Up</a> instead?'),
        ];
    }

    public function submit()
    {
        $messages = [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => new HtmlString('Email address not found. Please check your email or <a href="#" onclick="Livewire.dispatch(\'openModal\', { modalName: \'auth\' }); return false;" class="text-active underline sign-in-res">Sign Up</a> instead?'),
        ];

        $validator = Validator::make(
            $this->form,
            [
                'email' => 'required|email|exists:users,email',
            ],
            $messages
        );
        if ($validator->fails()) {
          throw new ValidationException($validator);
        }
        $valid = $validator->validated();

        $user = User::firstWhere('email', $valid['email']);
        $user->sendResetCode();
        $this->dispatch('openModal', 'reset-password-confirm', ['email' => $user->email]);
    }

    public function render()
    {
        return view('livewire.modals.reset-password');
    }
}
