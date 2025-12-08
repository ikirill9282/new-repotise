<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Article;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_view_article(): void
    {
        $author = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status_id' => \App\Enums\Status::ACTIVE,
        ]);

        $this->browse(function (Browser $browser) use ($article) {
            $browser->visit("/insights/{$article->slug}")
                ->assertSee($article->title)
                ->assertSee($article->content);
        });
    }

    public function test_user_can_browse_insights(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/insights')
                ->assertSee('Insights');
        });
    }
}
