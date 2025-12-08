<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_statistics_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\AuthorStatisticsTable::class)
            ->assertSuccessful();
    }

    public function test_fee_collection_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\FeeCollectionTable::class)
            ->assertSuccessful();
    }

    public function test_landing_pages_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\LandingPagesTable::class)
            ->assertSuccessful();
    }

    public function test_location_data_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\LocationDataTable::class)
            ->assertSuccessful();
    }

    public function test_referral_revenue_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\ReferralRevenueTable::class)
            ->assertSuccessful();
    }

    public function test_seller_onboarding_funnel_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\SellerOnboardingFunnel::class)
            ->assertSuccessful();
    }

    public function test_seller_storage_usage_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\SellerStorageUsage::class)
            ->assertSuccessful();
    }

    public function test_top_content_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\TopContentTable::class)
            ->assertSuccessful();
    }

    public function test_top_performing_content_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\TopPerformingContentTable::class)
            ->assertSuccessful();
    }

    public function test_top_sellers_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\TopSellersTable::class)
            ->assertSuccessful();
    }

    public function test_top_viewed_creator_pages_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\TopViewedCreatorPagesTable::class)
            ->assertSuccessful();
    }

    public function test_traffic_sources_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\TrafficSourcesTable::class)
            ->assertSuccessful();
    }

    public function test_user_activity_breakdown_table_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Analytics\UserActivityBreakdownTable::class)
            ->assertSuccessful();
    }
}
