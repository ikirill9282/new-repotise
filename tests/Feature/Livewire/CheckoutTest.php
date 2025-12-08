<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Order;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Checkout::class, ['order_id' => $order->id])
            ->assertSuccessful();
    }

    public function test_checkout_validates_required_fields(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Checkout::class, ['order_id' => $order->id])
            ->set('form.fullname', '')
            ->set('form.email', '')
            ->call('confirm')
            ->assertHasErrors(['form.fullname', 'form.email']);
    }
}
