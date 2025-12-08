<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProductFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_form_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\Product::class)
            ->assertSuccessful();
    }

    public function test_product_form_validates_required_fields(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\Product::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title']);
    }
}
