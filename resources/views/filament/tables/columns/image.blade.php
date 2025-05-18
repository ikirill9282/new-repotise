@php
    $record = $getRecord();   
@endphp

<x-filament::avatar
    src="{{ url($record->preview->image) }}"
    alt="{{ $record->name}}"
    :circular="false"
    size="w-12 h-12"
/>