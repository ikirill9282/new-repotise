@php
  $record = $getRecord();
@endphp
<div class="flex items-center gap-2">
    <x-filament::avatar
        :src="url($record->author->avatar)"
        :alt="$record->author->name"
        :label="$record->author->name"
    />
    <div>
        <p class="text-sm font-medium text-gray-900 dark:text-white">
            {{ $record->author->name }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $record->author->email }}
        </p>
    </div>
</div>