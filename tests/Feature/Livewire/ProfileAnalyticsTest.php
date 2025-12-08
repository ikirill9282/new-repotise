<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProfileAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Analytics::class)
            ->assertSuccessful();
    }

    public function test_balances_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Balances::class)
            ->assertSuccessful();
    }

    public function test_edit_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Edit::class)
            ->assertSuccessful();
    }

    public function test_page_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Page::class)
            ->assertSuccessful();
    }
}
