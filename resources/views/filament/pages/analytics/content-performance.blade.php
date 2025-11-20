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

        {{-- Content Performance Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <x-filament-pages-analytics::metric-card
                title="Total Content Views"
                :value="$this->getTotalContentViews()"
                :change="$this->getTotalContentViewsChange()"
                icon="heroicon-o-eye"
            />
            <x-filament-pages-analytics::metric-card
                title="Unique Content Views"
                :value="$this->getUniqueContentViews()"
                :change="$this->getUniqueContentViewsChange()"
                icon="heroicon-o-users"
            />
            <x-filament-pages-analytics::metric-card
                title="Avg. Time on Content"
                :value="$this->getAvgTimeOnContent()"
                :change="$this->getAvgTimeOnContentChange()"
                icon="heroicon-o-clock"
            />
            <x-filament-pages-analytics::metric-card
                title="New Content Published"
                :value="$this->getNewContentPublished()"
                :change="$this->getNewContentPublishedChange()"
                icon="heroicon-o-document-plus"
            />
            <x-filament-pages-analytics::metric-card
                title="Total Approved Comments"
                :value="$this->getTotalApprovedComments()"
                :change="$this->getTotalApprovedCommentsChange()"
                icon="heroicon-o-chat-bubble-left-right"
            />
            <x-filament-pages-analytics::metric-card
                title="Comment Engagement Rate"
                :value="$this->getCommentEngagementRate()"
                :change="$this->getCommentEngagementRateChange()"
                icon="heroicon-o-chart-bar"
                format="percentage"
            />
        </div>

        {{-- Top Performing Content Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Top Performing Content</h3>
            @livewire('analytics.top-performing-content-table')
        </div>

        {{-- Author Statistics Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Author Statistics</h3>
            @livewire('analytics.author-statistics-table')
        </div>
    </div>
</x-filament-panels::page>

