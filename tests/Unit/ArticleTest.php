<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_can_be_created(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Article',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'user_id' => $user->id,
        ]);
    }

    public function test_article_slug_is_auto_generated(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Article Title',
            'slug' => null,
        ]);

        $this->assertNotNull($article->slug);
        $this->assertNotEmpty($article->slug);
    }

    public function test_article_has_author_relationship(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $article->author);
        $this->assertEquals($user->id, $article->author->id);
    }

    public function test_article_can_get_short_description(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'text' => str_repeat('a', 500),
        ]);

        $short = $article->short(100);
        
        $this->assertNotEmpty($short);
    }

    public function test_article_has_feed_url(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-article',
        ]);

        $url = $article->makeFeedUrl();
        
        $this->assertStringContainsString('insights', $url);
        $this->assertStringContainsString('test-article', $url);
    }

    public function test_article_can_have_tags(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $tag = \App\Models\Tag::factory()->create();

        $article->tags()->attach($tag->id);

        $this->assertTrue($article->tags()->exists());
        $this->assertEquals(1, $article->tags()->count());
    }

    public function test_article_can_have_comments(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $comment = \App\Models\Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
        ]);

        // messages() filters by status, so we need to refresh and check
        $article->refresh();
        $this->assertTrue($article->messages()->exists());
    }

    public function test_article_can_get_text(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'text' => 'Test content',
        ]);

        $text = $article->getText();
        
        $this->assertStringContainsString('user-custom-text', $text);
        $this->assertStringContainsString('Test content', $text);
    }

    public function test_article_can_update_views(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'views' => 0,
        ]);

        $initialViews = $article->views;
        $article->updateViews();
        $article->refresh();

        $this->assertGreaterThanOrEqual($initialViews, $article->views);
    }
}
