<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProfileTablesAdditionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_analytics_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ArticleAnalytics::class)
            ->assertSuccessful();
    }

    public function test_donation_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Donation::class)
            ->assertSuccessful();
    }

    public function test_donation_analytics_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\DonationAnalytics::class)
            ->assertSuccessful();
    }

    public function test_payout_analytics_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\PayoutAnalytics::class)
            ->assertSuccessful();
    }

    public function test_product_analytics_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ProductAnalytics::class)
            ->assertSuccessful();
    }

    public function test_profile_article_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ProfileArticle::class)
            ->assertSuccessful();
    }

    public function test_profile_product_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ProfileProduct::class)
            ->assertSuccessful();
    }

    public function test_profile_referal_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ProfileReferal::class)
            ->assertSuccessful();
    }

    public function test_profile_refunds_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ProfileRefunds::class)
            ->assertSuccessful();
    }

    public function test_profile_reviews_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\ProfileReviews::class)
            ->assertSuccessful();
    }

    public function test_referal_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Referal::class)
            ->assertSuccessful();
    }

    public function test_refunds_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Refunds::class)
            ->assertSuccessful();
    }

    public function test_reviews_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Reviews::class)
            ->assertSuccessful();
    }

    public function test_sales_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Sales::class)
            ->assertSuccessful();
    }

    public function test_sales_analytics_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\SalesAnalytics::class)
            ->assertSuccessful();
    }

    public function test_subs_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables\Subs::class)
            ->assertSuccessful();
    }

    public function test_tables_component_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\Tables::class)
            ->assertSuccessful();
    }
}
