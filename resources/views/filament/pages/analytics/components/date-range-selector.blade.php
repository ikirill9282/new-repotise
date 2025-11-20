@php
    use Illuminate\Support\Carbon;
    
    $presets = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'this_month' => 'This Month',
        'last_90_days' => 'Last 90 Days',
        'this_year' => 'This Year',
        'custom' => 'Custom Range',
    ];
    
    $selectedPreset = request()->get('date_preset', 'last_30_days');
    $startDate = request()->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
    $endDate = request()->get('end_date', Carbon::now()->format('Y-m-d'));
@endphp

<div x-data="{ 
    preset: '{{ $selectedPreset }}',
    startDate: '{{ $startDate }}',
    endDate: '{{ $endDate }}',
    showCustom: false
}" class="flex flex-wrap items-center gap-4">
    <div class="flex gap-2 flex-wrap">
        @foreach($presets as $key => $label)
            <button
                type="button"
                @click="preset = '{{ $key }}'; showCustom = false; updateDateRange('{{ $key }}')"
                :class="preset === '{{ $key }}' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
    
    <div x-show="preset === 'custom' || showCustom" class="flex gap-2 items-center">
        <input
            type="date"
            x-model="startDate"
            @change="updateCustomDateRange()"
            class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
        <span class="text-gray-500">to</span>
        <input
            type="date"
            x-model="endDate"
            @change="updateCustomDateRange()"
            class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
    </div>
</div>

<script>
function updateDateRange(preset) {
    const params = new URLSearchParams(window.location.search);
    params.set('date_preset', preset);
    
    if (preset === 'custom') {
        // Keep custom dates if already set
    } else {
        const dates = getPresetDates(preset);
        params.set('start_date', dates.start);
        params.set('end_date', dates.end);
    }
    
    window.location.search = params.toString();
}

function updateCustomDateRange() {
    const params = new URLSearchParams(window.location.search);
    params.set('date_preset', 'custom');
    params.set('start_date', document.querySelector('[x-model="startDate"]').value);
    params.set('end_date', document.querySelector('[x-model="endDate"]').value);
    window.location.search = params.toString();
}

function getPresetDates(preset) {
    const today = new Date();
    let start, end;
    
    switch(preset) {
        case 'today':
            start = end = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            start = end = yesterday.toISOString().split('T')[0];
            break;
        case 'last_7_days':
            start = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            end = today.toISOString().split('T')[0];
            break;
        case 'last_30_days':
            start = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            end = today.toISOString().split('T')[0];
            break;
        case 'this_month':
            start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            end = today.toISOString().split('T')[0];
            break;
        case 'last_90_days':
            start = new Date(today.getTime() - 90 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            end = today.toISOString().split('T')[0];
            break;
        case 'this_year':
            start = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            end = today.toISOString().split('T')[0];
            break;
        default:
            start = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            end = today.toISOString().split('T')[0];
    }
    
    return { start, end };
}
</script>

