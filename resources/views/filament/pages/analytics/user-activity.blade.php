<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Date Range Selector --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <x-filament::section>
                <x-slot name="heading">
                    Date Range
                </x-slot>
                <x-filament-pages-analytics::date-range-selector />
            </x-filament::section>
        </div>

        {{-- Key User Metrics Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament-pages-analytics::metric-card
                title="Total Active Users"
                :value="$this->getTotalActiveUsers()"
                :change="$this->getTotalActiveUsersChange()"
                icon="heroicon-o-users"
            />
            <x-filament-pages-analytics::metric-card
                title="New Registrations"
                :value="$this->getNewRegistrations()"
                :change="$this->getNewRegistrationsChange()"
                icon="heroicon-o-user-plus"
            />
            <x-filament-pages-analytics::metric-card
                title="Total Buyers"
                :value="$this->getTotalBuyers()"
                :change="$this->getTotalBuyersChange()"
                icon="heroicon-o-shopping-cart"
            />
            <x-filament-pages-analytics::metric-card
                title="Total Active Sellers"
                :value="$this->getTotalActiveSellers()"
                :change="$this->getTotalActiveSellersChange()"
                icon="heroicon-o-briefcase"
            />
            <x-filament-pages-analytics::metric-card
                title="Stripe-Active Sellers"
                :value="$this->getStripeActiveSellers()"
                icon="heroicon-o-check-circle"
            />
            <x-filament-pages-analytics::metric-card
                title="Sellers Pending Verification"
                :value="$this->getSellersPendingVerification()"
                icon="heroicon-o-clock"
            />
            <x-filament-pages-analytics::metric-card
                title="User Retention Rate"
                :value="$this->getUserRetentionRate()"
                :change="$this->getUserRetentionRateChange()"
                icon="heroicon-o-arrow-path"
                format="percentage"
            />
        </div>

        {{-- New User Registration Trend Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">New User Registration Trend</h3>
            <x-filament-pages-analytics::registration-trend-chart
                :data="$this->getRegistrationTrendData()"
            />
        </div>

        {{-- User Activity Breakdown Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Activity Breakdown by Role</h3>
            @livewire('analytics.user-activity-breakdown-table')
        </div>

        {{-- Top Sellers Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Top Sellers</h3>
            @livewire('analytics.top-sellers-table')
        </div>

        {{-- Seller Storage Usage --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Seller Storage Usage</h3>
            @livewire('analytics.seller-storage-usage')
        </div>

        {{-- Top Viewed Creator Pages --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Top Viewed Creator Pages</h3>
            @livewire('analytics.top-viewed-creator-pages-table')
        </div>

        {{-- Seller Onboarding Funnel --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Seller Onboarding Funnel</h3>
            @livewire('analytics.seller-onboarding-funnel')
        </div>
    </div>
</x-filament-panels::page>

