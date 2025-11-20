@props([
    'title',
    'value',
    'change' => null,
    'icon' => 'heroicon-o-chart-bar',
    'format' => 'number'
])

@php
    $isPositive = $change !== null && $change >= 0;
    $changeColor = $isPositive ? 'text-green-600' : 'text-red-600';
    $changeIcon = $isPositive ? '↑' : '↓';
    
    // Check if value is numeric before formatting
    $isNumeric = is_numeric($value);
    $isString = is_string($value) && !$isNumeric;
    
    $formattedValue = match(true) {
        $isString => $value, // Return string values as-is
        $format === 'currency' => '$' . number_format((float)$value, 2),
        $format === 'percentage' => number_format((float)$value, 2) . '%',
        default => number_format((float)$value)
    };
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $formattedValue }}</p>
            @if($change !== null)
                <p class="text-sm {{ $changeColor }} mt-1">
                    {{ $changeIcon }} {{ abs($change) }}% vs previous period
                </p>
            @endif
        </div>
        <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
            <x-dynamic-component :component="$icon" class="w-6 h-6 text-primary-600" />
        </div>
    </div>
</div>

