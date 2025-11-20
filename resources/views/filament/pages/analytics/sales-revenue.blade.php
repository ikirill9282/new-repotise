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

        {{-- Key Revenue Metrics Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <x-filament-pages-analytics::metric-card
                title="Total GMV"
                :value="$this->getTotalGMV()"
                :change="$this->getTotalGMVChange()"
                icon="heroicon-o-currency-dollar"
                format="currency"
            />
            <x-filament-pages-analytics::metric-card
                title="Net Platform Revenue"
                :value="$this->getNetPlatformRevenue()"
                :change="$this->getNetPlatformRevenueChange()"
                icon="heroicon-o-banknotes"
                format="currency"
            />
            <x-filament-pages-analytics::metric-card
                title="Product Sales GMV"
                :value="$this->getProductSalesGMV()"
                :change="$this->getProductSalesGMVChange()"
                icon="heroicon-o-shopping-bag"
                format="currency"
            />
            <x-filament-pages-analytics::metric-card
                title="Subscription GMV"
                :value="$this->getSubscriptionGMV()"
                :change="$this->getSubscriptionGMVChange()"
                icon="heroicon-o-arrow-path"
                format="currency"
            />
            <x-filament-pages-analytics::metric-card
                title="Donation GMV"
                :value="$this->getDonationGMV()"
                :change="$this->getDonationGMVChange()"
                icon="heroicon-o-heart"
                format="currency"
            />
            <x-filament-pages-analytics::metric-card
                title="Total Orders"
                :value="$this->getTotalOrders()"
                :change="$this->getTotalOrdersChange()"
                icon="heroicon-o-shopping-cart"
            />
            <x-filament-pages-analytics::metric-card
                title="Average Order Value"
                :value="$this->getAOV()"
                :change="$this->getAOVChange()"
                icon="heroicon-o-calculator"
                format="currency"
            />
            <x-filament-pages-analytics::metric-card
                title="Referral Revenue"
                :value="$this->getReferralRevenue()"
                :change="$this->getReferralRevenueChange()"
                icon="heroicon-o-user-plus"
                format="currency"
            />
        </div>

        {{-- Revenue Trend Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Revenue Trend</h3>
            <x-filament-pages-analytics::revenue-trend-chart
                :data="$this->getRevenueTrendData()"
            />
        </div>

        {{-- Fee Collection Log Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Fee Collection Log</h3>
            @livewire('analytics.fee-collection-table')
        </div>

        {{-- Referral Revenue Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Referral Revenue Details</h3>
            @livewire('analytics.referral-revenue-table')
        </div>
    </div>
</x-filament-panels::page>

