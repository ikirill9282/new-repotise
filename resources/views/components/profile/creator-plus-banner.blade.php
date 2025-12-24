@php
  $class = $attributes->get('class') ?? '';   
@endphp

<x-card
  size="sm"
  x-data="{}"
  class="border-1 border-active !rounded-2xl {{ $class }} cursor-pointer hover:!border-active/80 transition overflow-hidden"
  style="background:
    radial-gradient(1200px circle at 0% 0%, rgba(255, 92, 77, 0.18), transparent 55%),
    radial-gradient(900px circle at 100% 0%, rgba(74, 163, 255, 0.14), transparent 55%),
    linear-gradient(135deg, rgba(255,255,255,0.0), rgba(255,255,255,0.0));
  "
  x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'creator-plus' })"
>
  <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-3">
      <div class="flex-1">
        <div class="font-semibold text-2xl mb-3">Coming Soon: Creator+</div>
        <div class="">Unlock professional tools like Promo Codes, Email Marketing, and Unlimited Storage. Click to learn more.</div>
      </div>
    </div>
</x-card>




