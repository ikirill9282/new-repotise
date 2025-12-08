<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ReportModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Report::class, [
                'type' => 'product',
                'item_id' => $product->id,
            ])
            ->assertSuccessful();
    }

    public function test_report_modal_validates_required_fields(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Report::class, [
                'type' => 'product',
                'item_id' => $product->id,
            ])
            ->set('form.text', '')
            ->call('submit')
            ->assertHasErrors(['form.text']);
    }
}
