<div>
    <div class="font-bold text-xl mb-4 pt-10 md:pt-0">Creator Levels & Benefits</div>
    <div class="py-8 border-t-1 border-b-1 border-gray/25">
        <div class="grid grid-cols-1 sm:grid-cols-2 sm:grid-rows-2 gap-4 mb-6">
            @foreach ($this->levels as $level)
                @php
                    $icon = match ($level->id) {
                        1 => 'icons.star',
                        2 => 'icons.graph-rise',
                        3 => 'icons.thumb',
                        default => null,
                    };

                    $class = match ($level->id) {
                        3 => '!w-7 !h-7',
                        default => '',
                    };

                    $text = match ($level->id) {
                        1 => 'Starting Level',
                        2 => 'Reach $100 in sales to unlock',
                        3 => 'Reach $300 in sales to unlock',
                    };
                @endphp
                <div class="p-2.5 rounded-lg border-1 border-gray/25">
                    <div class="font-bold text-2xl flex items-center gap-2 w-full bg-light rounded-lg p-3 mb-4">
                        <span class="text-yellow">
                            @includeIf($icon, ['width' => 25, 'height' => 25, 'class' => $class])
                        </span>
                        <div class="text-nowrap">Level {{ $level->id }}: {{ $level->title }}</div>
                    </div>
                    <ol>
                        <li class="!list-disc">Platform Fee: {{ $level->fee }}%</li>
                        <li class="!list-disc">Storage Limit: {{ $level->getSpace() }}</li>
                        <li class="!list-disc">Starting Level</li>
                    </ol>
                </div>
            @endforeach

            <div class="p-2.5 rounded-lg border-1 border-gray/25">
                <div class="font-bold text-2xl flex items-center gap-2 w-full bg-light rounded-lg p-3 mb-4">
                    <span class="text-red">@include('icons.gem', ['width' => 25, 'height' => 25])</span>
                    <div class="text-nowrap">Level 4: Exclusive</div>
                </div>
                <ol>
                    <li class="!list-disc">Platform Fee: Exclusive Rate</li>
                    <li class="!list-disc">Storage Limit: Unlimited</li>
                </ol>
            </div>

        </div>
        <div class="max-w-2xl">
            <ol class="m-0">
                <li class="!list-disc">All new creators start with a 30-day Level 3 bonus period. To maintain Level 3
                    benefits, reach $300 in sales within the first 30 days</li>
                <li class="!list-disc">Levels are permanent. Once you reach a level, you will never be downgraded.</li>
                <li class="!list-disc">Special conditions apply for creators referred through the Referral Program.
                    Learn More</li>
            </ol>
        </div>
    </div>
    <div class="flex justify-srtart items-center gap-3 mt-6">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="basis-1/3" outlined>Cancel</x-btn>
      <x-btn href="{{ route('sellers') . '#levels' }}" class="grow">Learn More</x-btn>
    </div>
</div>
