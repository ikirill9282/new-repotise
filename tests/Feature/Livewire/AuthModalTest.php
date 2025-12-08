<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use Illuminate\Support\Facades\Hash;

class AuthModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\Auth::class)
            ->assertSuccessful();
    }

    public function test_auth_modal_validates_email(): void
    {
        Livewire::test(\App\Livewire\Modals\Auth::class)
            ->set('form.email', 'invalid-email')
            ->call('prepareEmail')
            ->assertHasErrors(['form.email']);
    }

    public function test_auth_modal_requires_email(): void
    {
        Livewire::test(\App\Livewire\Modals\Auth::class)
            ->set('form.email', '')
            ->call('prepareEmail')
            ->assertHasErrors(['form.email']);
    }
}
