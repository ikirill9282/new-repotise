@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <x-card class="">
      <div class="flex justify-between items-center mb-12">
        <h2 class="font-bold text-2xl">Products & Subscriptions</h2>
        <div class="flex justify-center items-center gap-2">
          <x-btn class="!px-8 !py-1.5 text-nowrap" >Add Product</x-btn>
          <x-btn class="!px-8 !py-1.5 text-nowrap"  outlined>Promo Codes</x-btn>
        </div>
      </div>
      @livewire('profile.tables', [
        'active' => 'products-active',
        'sortable' => true,
        'tables' => [
          [
            'name' => "products-active",
            'title' => "Active (". $user->products()->where('status_id', 1)->count() .")",
          ],
          [
            'name' => 'products-draft',
            'title' => "Draft (". $user->products()->where('status_id', 2)->count() .")",
          ],
          [
            'name' => 'products-pending',
            'title' => "Pending Review (". $user->products()->where('status_id', 3)->count() .")",
          ]
        ]
      ])
    </x-card>
  </x-profile.wrap>
@endsection