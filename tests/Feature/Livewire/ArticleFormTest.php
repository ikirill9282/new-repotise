<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ArticleFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_form_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\Article::class)
            ->assertSuccessful();
    }

    public function test_article_form_validates_required_fields(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Forms\Article::class)
            ->set('fields.title', '')
            ->call('save')
            ->assertHasErrors(['fields.title']);
    }
}
