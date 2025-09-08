@props([
  'title' => null,
])

<div class="!mb-15">
  <div class="flex justify-start items-start sm:items-center flex-col sm:flex-row gap-2 !mb-10">
      <x-profile.title>{{ $title }}</x-profile.title>
      {{ $titleSlot ?? '' }}
  </div>
  {{ $slot }}
</div>