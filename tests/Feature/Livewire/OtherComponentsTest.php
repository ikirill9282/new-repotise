<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class OtherComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_invite_calculator_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\InviteCalculator::class)
            ->assertSuccessful();
    }

    public function test_modals_component_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals::class)
            ->assertSuccessful();
    }

    public function test_product_subscribe_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'subscription' => true,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductSubscribe::class, ['product' => $product])
            ->assertSuccessful();
    }

    public function test_user_notify_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\UserNotify::class)
            ->assertSuccessful();
    }
}
