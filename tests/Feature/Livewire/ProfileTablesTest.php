<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProfileTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Orders::class)
            ->assertSuccessful();
    }

    public function test_product_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Product::class)
            ->assertSuccessful();
    }

    public function test_article_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Insights::class)
            ->assertSuccessful();
    }
}
