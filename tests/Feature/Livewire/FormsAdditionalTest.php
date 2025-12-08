<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class FormsAdditionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_us_form_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Forms\ContactUs::class)
            ->assertSuccessful();
    }

    public function test_contact_us_form_validates_required_fields(): void
    {
        Livewire::test(\App\Livewire\Forms\ContactUs::class)
            ->set('form.name', '')
            ->set('form.email', '')
            ->set('form.message', '')
            ->call('submit')
            ->assertHasErrors(['form.name', 'form.email', 'form.message']);
    }

    public function test_invest_form_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\Invest::class)
            ->assertSuccessful();
    }

    public function test_invest_form_validates_required_fields(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\Invest::class)
            ->set('form.amount', '')
            ->call('submit')
            ->assertHasErrors(['form.amount']);
    }

    public function test_product_media_form_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\ProductMedia::class, ['product' => $product])
            ->assertSuccessful();
    }
}
