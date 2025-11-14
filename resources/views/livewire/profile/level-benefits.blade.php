<x-card size="sm" :class="$cardClass">
    <div class="text-2xl mb-5">Your Level & Benefits</div>

    @if(!$state)
        <div class="text-gray text-sm">Sign in to view your current level details.</div>
    @else
        <div class="mb-4">
            <div class="text-gray mb-1">Your Current Level:</div>
            <div class="bg-light flex justify-start items-center gap-2 px-3 py-3.5 rounded-lg">
                <div class="">
                    @includeIf($state['current_level_icon'], ['width' => 25, 'height' => 25])
                </div>
                <div class="font-semibold text-2xl">{{ $state['current_level_label'] }}</div>
            </div>
        </div>

        <div class="flex flex-col gap-1 mb-4 text-sm">
            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                    <div class="text-gray">Your Commission Rate:</div>
                    <div class="text-nowrap">{{ $state['commission_label'] }}</div>
                    <x-tooltip message="Commission rate for product sales. Donations have a fixed 5% fee." />
                </div>
                <div class="md:ml-auto opacity-0">
                    <x-points active="0" />
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                    <div class="text-gray">Storage Limit:</div>
                    <div class="text-nowrap">{{ $state['storage_label'] }}</div>
                    <x-tooltip message="Current storage usage compared to your available storage capacity." />
                </div>
                <div class="md:ml-auto">
                    <x-points :active="$state['storage_points']" />
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                    <div class="text-gray">Referral Bonus Period:</div>
                    <div class="text-nowrap">{{ $state['bonus_label'] }}</div>
                    <x-tooltip message="Thanks to the referral program, you are enjoying a special commission rate of 4% and Level 3 storage for the first 30 days. Reach the sales target within this period to permanently keep these enhanced benefits." />
                </div>
                <div class="md:ml-auto opacity-0">
                    <x-points active="0" />
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                    <div class="text-gray">Progress to Next Level:</div>
                    <div class="text-nowrap">{{ $state['progress_label'] }}</div>
                    <x-tooltip message="Track your progress towards unlocking the benefits of the next seller level. Reach the required sales volume to automatically upgrade and enjoy lower commission rates and increased storage." />
                </div>
                <div class="md:ml-auto">
                    <x-points :active="$state['progress_points']" />
                </div>
            </div>
        </div>

        <div class="text-gray mb-4 text-sm">
            {{ $state['message'] }}
        </div>

        <div x-data="{}" class="">
            <x-link x-on:click.prevent="() => Livewire.dispatch('openModal', { modalName: 'levels' })">Learn More</x-link>
        </div>
    @endif
</x-card>

