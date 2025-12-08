<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ModalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\ResetPassword::class)
            ->assertSuccessful();
    }

    public function test_reset_password_confirm_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\ResetPasswordConfirm::class)
            ->assertSuccessful();
    }

    public function test_change_email_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\ChangeEmail::class)
            ->assertSuccessful();
    }

    public function test_delete_account_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DeleteAccount::class)
            ->assertSuccessful();
    }

    public function test_payment_method_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\PaymentMethod::class)
            ->assertSuccessful();
    }

    public function test_promocodes_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Promocodes::class)
            ->assertSuccessful();
    }

    public function test_twofa_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Twofa::class)
            ->assertSuccessful();
    }

    public function test_donate_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Donate::class, ['product' => $product])
            ->assertSuccessful();
    }
}
