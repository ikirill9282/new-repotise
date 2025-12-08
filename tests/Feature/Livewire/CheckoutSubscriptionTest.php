<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class CheckoutSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_subscription_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'subscription' => true,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\CheckoutSubscription::class, ['product' => $product])
            ->assertSuccessful();
    }
}
