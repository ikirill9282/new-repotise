<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProfileAdditionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_level_benefits_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\LevelBenefits::class)
            ->assertSuccessful();
    }

    public function test_social_aside_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\SocialAside::class)
            ->assertSuccessful();
    }
}
