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

        {{-- Key Metrics Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-filament-pages-analytics::metric-card
                title="Total Visits"
                :value="$this->getTotalVisits()"
                :change="$this->getTotalVisitsChange()"
                icon="heroicon-o-chart-bar"
            />
            <x-filament-pages-analytics::metric-card
                title="Unique Visitors"
                :value="$this->getUniqueVisitors()"
                :change="$this->getUniqueVisitorsChange()"
                icon="heroicon-o-users"
            />
            <x-filament-pages-analytics::metric-card
                title="Pageviews"
                :value="$this->getPageviews()"
                :change="$this->getPageviewsChange()"
                icon="heroicon-o-eye"
            />
            <x-filament-pages-analytics::metric-card
                title="Avg. Session Duration"
                :value="$this->getAvgSessionDuration()"
                :change="$this->getAvgSessionDurationChange()"
                icon="heroicon-o-clock"
            />
            <x-filament-pages-analytics::metric-card
                title="Bounce Rate"
                :value="$this->getBounceRate()"
                :change="$this->getBounceRateChange()"
                icon="heroicon-o-arrow-uturn-left"
            />
        </div>

        {{-- Visits Trend Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Visits Trend</h3>
            <x-filament-pages-analytics::line-chart
                :data="$this->getVisitsTrendData()"
                x-axis="date"
                y-axis="sessions"
            />
        </div>

        {{-- Traffic Sources Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Traffic Sources</h3>
            @livewire('analytics.traffic-sources-table')
        </div>

        {{-- Top Landing Pages Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Top Landing Pages</h3>
            @livewire('analytics.landing-pages-table')
        </div>

        {{-- Top Content Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Top Content by Views</h3>
            @livewire('analytics.top-content-table')
        </div>

        {{-- Location Data Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Location Data</h3>
            @livewire('analytics.location-data-table')
        </div>
    </div>
</x-filament-panels::page>

