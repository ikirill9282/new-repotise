@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <x-card size="sm" class="">
      <div class="flex justify-between items-start !gap-3 sm:!gap-0 sm:items-center flex-col sm:flex-row mb-12">
        <h2 class="font-bold text-2xl">Products & Subscriptions</h2>
        <div x-data="{}" class="flex justify-center items-center gap-2">
          <x-btn href="{{ route('profile.products.create') }}" class="!px-4 sm:!px-8 !py-1.5 text-nowrap" >Add Product</x-btn>
          <x-btn x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'promocodes' })" class="!px-4 sm:!px-8 !py-1.5 text-nowrap"  outlined>Promo Codes</x-btn>
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