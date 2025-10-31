@extends('layouts.site')

@section('content')
    <x-profile.wrap colClass="overflow-hidden">
        <div class="dashboard-content max-w-full">
            <x-profile.complete-verify class="mb-4" />
            {{-- <x-profile.resend-verify class="mb-4" /> --}}

            <div class="flex flex-col lg:flex-row justify-start items-stretch gap-3 mb-4">
                <div class="basis-1/2">
                    @livewire('profile.balances', ['class' => 'h-full'])
                </div>
                <div class="basis-1/2">
                    <x-card size="sm" class="h-full">
                        <div class="text-2xl mb-5">Your Level & Benefits</div>
                        <div class="mb-4">
                            <div class="text-gray mb-1">Your Current Level:</div>
                            <div class="bg-light flex justify-start items-center gap-2 px-3 py-3.5 rounded-lg">
                                <div class="">@include('icons.thumb')</div>
                                <div class="font-semibold text-2xl">Level 3: Pro</div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1 mb-4 text-sm">
                            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                                    <div class="text-gray">Your Commission Rate:</div>
                                    <div class="text-nowrap">10%</div>
                                    <x-tooltip message="tooltip" />
                                </div>
                                <div class="md:ml-auto opacity-0">
                                  <x-points active="3" />
                                </div>
                            </div>
                            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                                    <div class="text-gray">Storage Limit:</div>
                                    <div class="text-nowrap">0 MB / 1GB</div>
                                    <x-tooltip message="tooltip" />
                                </div>
                                <div class="md:ml-auto">
                                  <x-points active="3" />
                                </div>
                            </div>
                            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                                    <div class="text-gray">Referral Bonus Period:</div>
                                    <div class="text-nowrap">30 days left</div>
                                    <x-tooltip message="tooltip" />
                                </div>
                                <div class="md:ml-auto opacity-0">
                                  <x-points active="3" />
                                </div>
                            </div>
                            <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-2">
                                <div class="col-span-2 relative flex justify-between items-center gap-2 pe-6">
                                    <div class="text-gray">Progress to Next Level:</div>
                                    <div class="text-nowrap">$258 / $300</div>
                                    <x-tooltip message="tooltip" />
                                </div>
                                <div class="md:ml-auto">
                                  <x-points active="3" />
                                </div>
                            </div>
                        </div>

                        <div class="text-gray mb-4 text-sm">
                          You need $[оставшийся объем] more in sales to reach Level [следующий уровень] and unlock [сниженная комиссия и увеличенный объем хранения].
                        </div>
                        
                        <div x-data="{}" class="">
                          <x-link x-on:click.prevent="() => Livewire.dispatch('openModal', { modalName: 'levels' })">Learn More</x-link>
                        </div>

                    </x-card>
                </div>
            </div>

            <x-card size="sm">
              @livewire('profile.tables', [
                'tables' => [
                  [
                    'name' => 'sales',
                    'title' => 'Sales Snapshot',
                  ],
                  [
                    'name' => 'product',
                    'title' => 'Product Performance',
                  ],
                  [
                    'name' => 'insights',
                    'title' => 'Content Insights',
                  ],
                  [
                    'name' => 'donation',
                    'title' => 'Donation Summary',
                  ],
                  [
                    'name' => 'refunds',
                    'title' => 'Refunds Summary',
                  ],
                  [
                    'name' => 'reviews',
                    'title' => 'Recent Reviews',
                  ],
                  [
                    'name' => 'referal',
                    'title' => 'Referral Program Summary',
                  ],
                ],
                'active' => 'sales',
              ])
            </x-card>
        </div>
    </x-profile.wrap>
@endsection
