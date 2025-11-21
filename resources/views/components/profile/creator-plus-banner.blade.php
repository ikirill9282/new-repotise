@php
  $class = $attributes->get('class') ?? '';   
@endphp

<x-card size="sm" class="border-1 border-active !rounded-2xl {{ $class }} cursor-pointer hover:!border-active/80 transition" 
        x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'creator-plus' })">
  <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-3">
      <div class="flex-1">
        <div class="font-semibold text-2xl mb-3">Coming Soon: Creator+</div>
        <div class="">Unlock professional tools like Promo Codes, Email Marketing, and Unlimited Storage. Click to learn more.</div>
      </div>
    </div>
</x-card>


