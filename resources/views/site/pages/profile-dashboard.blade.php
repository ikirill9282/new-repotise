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
                    @livewire('profile.level-benefits', ['class' => 'h-full'])
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
