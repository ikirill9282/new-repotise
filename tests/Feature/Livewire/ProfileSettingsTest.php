<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_settings_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Settings::class)
            ->assertSuccessful();
    }
}
