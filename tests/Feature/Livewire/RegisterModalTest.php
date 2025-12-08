<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class RegisterModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\Register::class)
            ->assertSuccessful();
    }

    public function test_register_modal_validates_email(): void
    {
        Livewire::test(\App\Livewire\Modals\Register::class)
            ->set('form.email', 'invalid-email')
            ->call('register')
            ->assertHasErrors(['form.email']);
    }
}
