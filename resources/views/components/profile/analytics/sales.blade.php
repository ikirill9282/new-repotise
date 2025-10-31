@props(['stats' => []])

<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
        <div class="text-gray">Total Orders:</div>
        <div class="text-nowrap relative !pr-6">
            <span>{{ number_format($stats['total_orders'] ?? 0) }}</span>
            <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
        </div>
    </div>
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
        <div class="text-gray">Projected Recurring Revenue:</div>
        <div class="text-nowrap relative !pr-6">
            <span>{{ currency($stats['recurring_revenue'] ?? 0) }}</span>
            <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
        </div>
    </div>
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
        <div class="text-gray">Refund Rate:</div>
        <div class="text-nowrap relative !pr-6">
            <span>{{ number_format($stats['refund_rate'] ?? 0, 2) }}%</span>
            <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
        </div>
    </div>
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
        <div class="text-gray">Average Order Value:</div>
        <div class="text-nowrap relative !pr-6">
            <span>{{ currency($stats['average_order_value'] ?? 0) }}</span>
            <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
        </div>
    </div>
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
        <div class="text-gray">Product Conversion Rate:</div>
        <div class="text-nowrap relative !pr-6">
            <span>{{ number_format($stats['conversion_rate'] ?? 0, 2) }}%</span>
            <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
        </div>
    </div>
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
        <div class="text-gray">Referral Income Earned:</div>
        <div class="text-nowrap relative !pr-6">
            <span>{{ currency($stats['referral_income'] ?? 0) }}</span>
            <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
        </div>
    </div>
</div>
