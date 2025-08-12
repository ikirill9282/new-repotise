@props([
  'resource' => null,
  'class' => '',
])

<div class="show-more inline-block text-gray mt-4 {{ $class }}">
  <span 
    class="border-b border-dashed pb-0.5 replies-button transition hover:cursor-pointer hover:text-active " 
    data-resource="{{ $resource }}"
    {{ $attributes }}
  >
    {{ $slot ?? '' }}
  </span>
</div>