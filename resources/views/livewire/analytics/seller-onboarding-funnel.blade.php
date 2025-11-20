<div>
    <div class="space-y-4">
        @foreach($funnel as $step)
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium">{{ $step['step'] }}</span>
                        <span class="text-sm text-gray-600">{{ number_format($step['count']) }} ({{ number_format($step['percentage'], 2) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div
                            class="bg-primary-600 h-4 rounded-full transition-all"
                            style="width: {{ $step['percentage'] }}%"
                        ></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

