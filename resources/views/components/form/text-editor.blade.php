@props([
  'label' => '',
  'placeholder' => '',
])
        
<div class="relative !text-sm sm:!text-base">
  <div class="!mb-2 text-gray">{{ $label }}</div>
  <div 
    class="quill-editor !bg-light !border-none !rounded-lg min-h-36 grid grid-cols-1 !pr-4" 
    data-placeholder="{{ $placeholder }}"
  ></div>
  <x-tooltip class="!right-3 !top-32 xs:!top-25" message="tooltip"></x-tooltip>
</div>