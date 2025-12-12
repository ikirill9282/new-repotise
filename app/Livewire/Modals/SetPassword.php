<?php

namespace App\Livewire\Modals;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SetPassword extends Component
{
    public string $email;
    public array $form = [
        'password' => null,
        'password_confirmation' => null,
    ];

    public function mount(?string $email = null)
    {
        $this->email = $email ?? '';
    }

    public function submit()
    {
        $validator = Validator::make($this->form, [
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must include a mix of letters, numbers, and symbols.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password_confirmation.same' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            $validator->errors()->add('email', 'User not found.');
            throw new ValidationException($validator);
        }

        $user->update([
            'password' => Hash::make($this->form['password']),
        ]);

        $this->dispatch('openModal', 'set-password-success');
    }

    public function render()
    {
        return view('livewire.modals.set-password');
    }
}
