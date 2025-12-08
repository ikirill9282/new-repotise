<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProductModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        Livewire::test(\App\Livewire\Modals\Product::class, ['product' => $product])
            ->assertSuccessful();
    }
}
